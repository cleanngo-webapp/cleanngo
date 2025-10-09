<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class EmployeeJobsController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            $empty = DB::table('bookings')->whereRaw('1=0')->paginate(15);
            return view('employee.jobs', [
                'bookings' => $empty,
                'locationsData' => [],
                'receiptData' => [],
                'search' => '',
                'sort' => 'date',
                'sortOrder' => 'desc',
                'jobsAssignedToday' => 0,
                'jobsCompletedOverall' => 0,
                'pendingJobs' => 0
            ]);
        }

        // Get search and sort parameters
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'date'); // 'date' or 'customer'
        $sortOrder = $request->get('sort_order', 'desc'); // 'asc' or 'desc'

        // Build the base query
        $query = DB::table('bookings as b')
            ->leftJoin('customers as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('booking_staff_assignments as a', 'a.booking_id', '=', 'b.id')
            ->leftJoin('addresses as primary_addr', 'primary_addr.id', '=', 'c.default_address_id')
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('pp.booking_id', '=', 'b.id')
                     ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
            })
            ->where('a.employee_id', $employeeId)
            ->select([
                'b.id', 'b.code', 'b.status', 'b.scheduled_start', 'b.booking_photos',
                'b.equipment_borrowed_by', 'b.equipment_borrowed_at',
                'b.job_started_by', 'b.job_started_at',
                'b.job_completed_by', 'b.job_completed_at',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw('u.phone as customer_phone'),
                DB::raw("COALESCE(primary_addr.line1,'') as address_line1"),
                DB::raw("COALESCE(primary_addr.barangay,'') as address_barangay"),
                DB::raw("COALESCE(primary_addr.city,'') as address_city"),
                DB::raw("COALESCE(primary_addr.province,'') as address_province"),
                'primary_addr.latitude', 'primary_addr.longitude',
                DB::raw("CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END as payment_approved"),
                DB::raw('pp.id as payment_proof_id'),
                DB::raw('pp.status as payment_status'),
                // Check if equipment has been borrowed for this booking by this employee
                DB::raw("CASE WHEN EXISTS(SELECT 1 FROM inventory_transactions it JOIN inventory_items ii ON it.inventory_item_id = ii.id WHERE it.employee_id = a.employee_id AND it.booking_id = b.id AND it.transaction_type = 'borrow') THEN 1 ELSE 0 END as equipment_borrowed"),
            ]);

        // Apply search logic - search across relevant fields
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere('u.first_name', 'like', "%{$search}%")
                  ->orWhere('u.last_name', 'like', "%{$search}%")
                  ->orWhere('u.phone', 'like', "%{$search}%")
                  ->orWhere('b.status', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(u.first_name, ' ', u.last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply sorting with order
        if ($sort === 'customer') {
            $query->orderBy('u.first_name', $sortOrder)
                  ->orderBy('u.last_name', $sortOrder);
        } else {
            // Default sort by date
            $query->orderBy('b.scheduled_start', $sortOrder);
        }

        // Only show confirmed and in_progress bookings to employees
        $query->whereIn('b.status', ['confirmed', 'in_progress']);

        $bookings = $query->paginate(15);

        // Build map payload
        $locationsData = collect($bookings->items())->mapWithKeys(function ($b) {
            $parts = array_filter([$b->address_line1 ?? null, $b->address_barangay ?? null, $b->address_city ?? null, $b->address_province ?? null]);
            return [
                $b->id => [
                    'address' => implode(', ', $parts),
                    'lat' => $b->latitude,
                    'lng' => $b->longitude,
                    'phone' => $b->customer_phone,
                ]
            ];
        })->all();

        // Build receipt payload with same structure as admin controller
        $receiptData = [];
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                // Detailed lines with same structure as admin
                $grouped[$r->booking_id][] = [
                    'item_type' => $r->item_type,
                    'quantity' => (int)($r->quantity ?? 0),
                    'area_sqm' => $r->area_sqm !== null ? (float)$r->area_sqm : null,
                    'unit_price' => $r->unit_price_cents !== null ? ((int)$r->unit_price_cents)/100 : null,
                    'line_total' => $r->line_total_cents !== null ? ((int)$r->line_total_cents)/100 : null,
                ];
            }
            foreach ($grouped as $bid => $lines) {
                $total = 0.0;
                foreach ($lines as $ln) { $total += (float)($ln['line_total'] ?? 0); }
                $receiptData[$bid] = [ 'lines' => $lines, 'total' => $total ];
            }
        }

        // Calculate job statistics for the cards
        $today = Carbon::now()->setTimezone('Asia/Manila')->startOfDay();
        
        // Get jobs assigned to this employee today or currently in progress (excluding completed jobs)
        $jobsAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereIn('bookings.status', ['confirmed', 'in_progress']) // Only confirmed and in_progress jobs
            ->where(function($query) use ($today) {
                $query->whereDate('bookings.scheduled_start', $today)
                      ->orWhere('bookings.status', 'in_progress');
            })
            ->count();
        
        // Get all completed jobs by this employee (overall, not just today)
        $jobsCompletedOverall = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where('bookings.status', 'completed')
            ->count();
        
        // Get pending jobs assigned to this employee (only confirmed jobs are visible to employees)
        $pendingJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereIn('bookings.status', ['confirmed', 'in_progress'])
            ->count();

        return view('employee.jobs', [
            'bookings' => $bookings,
            'locationsData' => $locationsData,
            'receiptData' => $receiptData,
            'search' => $search,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
            'jobsAssignedToday' => $jobsAssignedToday,
            'jobsCompletedOverall' => $jobsCompletedOverall,
            'pendingJobs' => $pendingJobs
        ]);
    }

    public function start(Request $request, int $bookingId)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) { return back(); }
        
        // Use Eloquent model to trigger notifications
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking) { return back(); }
        
        // Ensure this employee is assigned to the booking
        $assigned = $booking->staffAssignments()
            ->where('employee_id', $employeeId)
            ->exists();
        if (!$assigned) { return back(); }
        
        // Update booking status using Eloquent model to trigger notifications
        $booking->status = 'in_progress';
        $booking->job_started_by = $employeeId;
        $booking->job_started_at = now();
        $booking->save(); // This will trigger the boot() method and send notifications
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Job started successfully!',
                'booking_id' => $bookingId
            ]);
        }
        
        return back()->with('status', 'Job started');
    }

    public function complete(Request $request, int $bookingId)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) { return back(); }
        
        // Use Eloquent model to trigger notifications
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking) { return back(); }
        
        // Ensure this employee is assigned to the booking
        $assigned = $booking->staffAssignments()
            ->where('employee_id', $employeeId)
            ->exists();
        if (!$assigned) { return back(); }
        
        // Check if payment proof is approved
        $paymentApproved = \App\Models\PaymentProof::where('booking_id', $bookingId)
            ->where('status', 'approved')
            ->exists();
            
        if (!$paymentApproved) {
            return back()->withErrors(['error' => 'Payment proof must be approved before completing the job.']);
        }
        
        // Update booking status using Eloquent model to trigger notifications
        $booking->status = 'completed';
        $booking->completed_at = now();
        $booking->job_completed_by = $employeeId;
        $booking->job_completed_at = now();
        $booking->save(); // This will trigger the boot() method and send notifications
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Job completed successfully!',
                'booking_id' => $bookingId
            ]);
        }
        
        return back()->with('status', 'Job marked as completed');
    }


    /**
     * Get booking summary for employee view
     */
    public function getSummary($bookingId)
    {
        $user = Auth::user();
        
        // Verify the employee is assigned to this booking
        $booking = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->where('b.id', $bookingId)
            ->where('e.user_id', $user->id)
            ->select('b.*')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you are not assigned to this booking'
            ], 404);
        }

        // Get booking items
        $items = DB::table('booking_items')
            ->where('booking_id', $bookingId)
            ->get();

        // Get customer information from users table
        $customer = DB::table('customers')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->where('customers.id', $booking->customer_id)
            ->select('users.first_name', 'users.last_name', 'users.phone')
            ->first();

        // Get assigned employees
        $assignedEmployees = DB::table('booking_staff_assignments')
            ->join('employees', 'booking_staff_assignments.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->where('booking_staff_assignments.booking_id', $bookingId)
            ->select(DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"), 'users.phone')
            ->get();

        // Get booking photos from the booking_photos column
        $photos = [];
        if ($booking->booking_photos) {
            $photoPaths = json_decode($booking->booking_photos, true);
            if (is_array($photoPaths)) {
                foreach ($photoPaths as $photoPath) {
                    $photos[] = [
                        'url' => asset('storage/' . $photoPath),
                        'filename' => basename($photoPath)
                    ];
                }
            }
        }

        // Generate summary HTML
        $html = view('components.booking-summary', [
            'booking' => $booking,
            'items' => $items,
            'customer' => $customer,
            'assignedEmployees' => $assignedEmployees
        ])->render();

        return response()->json([
            'success' => true,
            'summary' => $booking,
            'html' => $html,
            'photos' => $photos
        ]);
    }

    /**
     * Get booking location for employee view
     */
    public function getLocation($bookingId)
    {
        $user = Auth::user();
        
        // Verify the employee is assigned to this booking
        $booking = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->where('b.id', $bookingId)
            ->where('e.user_id', $user->id)
            ->select('b.*')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you are not assigned to this booking'
            ], 404);
        }

        // Get customer information
        $customer = DB::table('customers')
            ->where('id', $booking->customer_id)
            ->first();

        // Get primary address with coordinates
        $address = null;
        if ($customer && $customer->user_id) {
            $address = DB::table('addresses')
                ->where('user_id', $customer->user_id)
                ->where('is_primary', true)
                ->first();
        }

        // Build full address from address components
        $fullAddress = 'No address provided';
        if ($address) {
            $addressParts = array_filter([
                $address->line1,
                $address->line2,
                $address->barangay,
                $address->city,
                $address->province,
                $address->postal_code
            ]);
            $fullAddress = implode(', ', $addressParts);
        }

        return response()->json([
            'success' => true,
            'location' => [
                'lat' => $address->latitude ?? null,
                'lng' => $address->longitude ?? null,
                'address' => $fullAddress
            ]
        ]);
    }

    /**
     * Get booking photos for employee view (only if assigned)
     */
    public function getPhotos($bookingId)
    {
        $user = Auth::user();

        // Verify the employee is assigned to this booking
        $booking = DB::table('bookings as b')
            ->join('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->where('b.id', $bookingId)
            ->where('e.user_id', $user->id)
            ->select('b.*')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you are not assigned to this booking'
            ], 404);
        }

        $photos = [];
        if (!empty($booking->booking_photos)) {
            $photoPaths = json_decode($booking->booking_photos, true);
            if (is_array($photoPaths)) {
                foreach ($photoPaths as $photoPath) {
                    $photos[] = [
                        'url' => asset('storage/' . ltrim($photoPath, '/')),
                        'filename' => basename($photoPath),
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'count' => count($photos),
            'photos' => $photos,
        ]);
    }

    /**
     * Get available inventory items for equipment borrowing
     */
    public function getAvailableInventory()
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            // Get active inventory items with their available quantities
            $items = InventoryItem::active()
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category' => $item->category,
                        'item_code' => $item->item_code,
                        'available_quantity' => $item->available_quantity,
                        'is_returnable' => $item->isReturnableItem(),
                        'is_consumable' => $item->isConsumableItem(),
                    ];
                })
                ->filter(function ($item) {
                    return $item['available_quantity'] > 0; // Only show items with available stock
                });

            return response()->json([
                'success' => true,
                'items' => $items->values(), // Reset array keys
                'message' => $items->count() . ' equipment items available'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Borrow equipment for a booking
     */
    public function borrowEquipment(Request $request, $bookingId)
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            // Validate booking ownership
            $booking = Booking::find($bookingId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Ensure this employee is assigned to the booking
            $assigned = $booking->staffAssignments()
                ->where('employee_id', $employeeId)
                ->exists();
            if (!$assigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this booking'
                ], 403);
            }

            // Parse equipment JSON data
            $equipmentJson = $request->input('equipment');
            if (is_string($equipmentJson)) {
                $equipmentData = json_decode($equipmentJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ['equipment' => ['Invalid equipment data format']]
                    );
                }
            } else {
                $equipmentData = $equipmentJson;
            }

            $request->validate([
                'equipment' => 'required',
            ], [
                'equipment.required' => 'Equipment data is required',
            ]);

            // Manual validation of equipment array structure
            if (!is_array($equipmentData) || empty($equipmentData)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['equipment' => ['Equipment list must not be empty']]
                );
            }

            foreach ($equipmentData as $index => $item) {
                if (!isset($item['id']) || !is_numeric($item['id'])) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ["equipment.{$index}.id" => ['Equipment ID is required and must be numeric']]
                    );
                }
                
                if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] < 1) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ["equipment.{$index}.quantity" => ['Quantity must be at least 1']]
                    );
                }

                // Check if inventory item exists
                if (!InventoryItem::where('id', $item['id'])->exists()) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], []),
                        ["equipment.{$index}.id" => ['Selected equipment item not found']]
                    );
                }
            }
            $borrowedItems = [];
            $errors = [];

            DB::beginTransaction();

            foreach ($equipmentData as $equipmentItem) {
                $inventoryItem = InventoryItem::find($equipmentItem['id']);
                $quantity = (int) $equipmentItem['quantity'];

                // Check if enough inventory is available
                if ($inventoryItem->available_quantity < $quantity) {
                    $errors[] = "Not enough {$inventoryItem->name} available. Only {$inventoryItem->available_quantity} left.";
                    continue;
                }

                // Create inventory transaction record
                $transaction = InventoryTransaction::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'employee_id' => $employeeId,
                    'booking_id' => $bookingId,
                    'transaction_type' => 'borrow',
                    'quantity' => $quantity,
                    'transaction_at' => now(),
                    'expected_return_date' => $inventoryItem->isReturnableItem() ? $booking->scheduled_end : null,
                    'notes' => "Borrowed for booking #{$booking->code}"
                ]);

                // Update the actual inventory quantity
                $originalQuantity = $inventoryItem->quantity;
                $inventoryItem->quantity = $inventoryItem->quantity - $quantity;
                $inventoryItem->save(); // This will trigger status update via the model's boot method
                
                Log::info("Equipment borrowed: {$inventoryItem->name} - {$originalQuantity} to {$inventoryItem->quantity} (borrowed: {$quantity})");

                $borrowedItems[] = [
                    'item' => $inventoryItem->name,
                    'category' => $inventoryItem->category,
                    'quantity' => $quantity,
                    'transaction_id' => $transaction->id,
                    'is_returnable' => $inventoryItem->isReturnableItem(),
                ];
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Some items could not be borrowed',
                    'errors' => $errors
                ], 422);
            }

            // Update booking to track who borrowed equipment
            $booking->equipment_borrowed_by = $employeeId;
            $booking->equipment_borrowed_at = now();
            $booking->save();

            DB::commit();

            // Send one consolidated notification for all borrowed items
            if (!empty($borrowedItems)) {
                $employee = \App\Models\Employee::find($employeeId);
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyEquipmentBorrowedBatch($booking, $employee, $borrowedItems);
            }

            return response()->json([
                'success' => true,
                'message' => 'Equipment borrowed successfully for booking #' . $booking->code,
                'borrowed_items' => $borrowedItems,
                'booking_id' => $bookingId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to borrow equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return equipment when booking is completed
     * This will be called automatically from the Booking model's boot method
     */
    public function returnEquipment($bookingId, $employeeId = null)
    {
        try {
            $booking = Booking::find($bookingId);
            if (!$booking) {
                return false;
            }

            // If employee ID is not provided, get all employees assigned to this booking
            $employeesQuery = $booking->staffAssignments();
            if ($employeeId) {
                $employeesQuery = $employeesQuery->where('employee_id', $employeeId);
            }
            
            $employeeIds = $employeesQuery->pluck('employee_id');

            $returnedItems = [];
            DB::beginTransaction();

            foreach ($employeeIds as $empId) {
                // Find all borrowed equipment for this employee and booking
                $borrowedTransactions = InventoryTransaction::where('employee_id', $empId)
                    ->where('booking_id', $bookingId)
                    ->borrow()
                    ->with('inventoryItem')
                    ->get();

                foreach ($borrowedTransactions as $transaction) {
                    // Check if this item needs to be returned (only returnable items)
                    if (!$transaction->inventoryItem->isReturnableItem()) {
                        continue; // Skip consumables
                    }

                    // Check the current borrowed quantity vs returned quantity for this transaction pattern
                    $currentBorrowed = InventoryTransaction::where('employee_id', $empId)
                        ->where('booking_id', $bookingId)
                        ->where('inventory_item_id', $transaction->inventory_item_id)
                        ->borrow()
                        ->sum('quantity');

                    $currentReturned = InventoryTransaction::where('employee_id', $empId)
                        ->where('booking_id', $bookingId)
                        ->where('inventory_item_id', $transaction->inventory_item_id)
                        ->return()
                        ->sum('quantity');

                    $remainingToReturn = $currentBorrowed - $currentReturned;

                    if ($remainingToReturn > 0) {
                        $returnedItem = InventoryItem::find($transaction->inventory_item_id);
                        
                        // Create return transaction
                        InventoryTransaction::create([
                            'inventory_item_id' => $transaction->inventory_item_id,
                            'employee_id' => $empId,
                            'booking_id' => $bookingId,
                            'transaction_type' => 'return',
                            'quantity' => $remainingToReturn,
                            'transaction_at' => now(),
                            'notes' => "Automatically returned when booking #{$booking->code} was completed"
                        ]);

                        // Update the actual inventory quantity (add back to inventory)
                        $originalQuantity = $returnedItem->quantity;
                        $returnedItem->quantity = $returnedItem->quantity + $remainingToReturn;
                        $returnedItem->save(); // This will trigger status update via the model's boot method
                        
                        Log::info("Equipment returned: {$transaction->inventoryItem->name} - {$originalQuantity} to {$returnedItem->quantity} (returned: {$remainingToReturn})");

                        $returnedItems[] = [
                            'employee_id' => $empId,
                            'item' => $transaction->inventoryItem->name,
                            'quantity' => $remainingToReturn,
                        ];
                    }
                }
            }

            DB::commit();
            return $returnedItems;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error returning equipment for booking ' . $bookingId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get borrowed items for a specific booking
     */
    public function getBorrowedItems($bookingId)
    {
        try {
            // Check employee authentication first - use Auth::user() to get the authenticated user
            $user = Auth::user();
            if (!$user || !$user->employee) {
                Log::error('Employee not authenticated for borrowed items request');
                return response()->json([
                    'success' => false,
                    'message' => 'Employee authentication required'
                ], 401);
            }
            
            $employeeId = $user->employee->id;
            Log::info("Employee {$employeeId} requesting borrowed items for booking {$bookingId}");
            
            // Check if employee is assigned to this booking
            $booking = Booking::find($bookingId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            $isAssigned = $booking->staffAssignments()
                ->where('employee_id', $employeeId)
                ->exists();
                
            if (!$isAssigned) {
                Log::warning("Employee {$employeeId} not assigned to booking {$bookingId}");
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this booking'
                ], 403);
            }
            
            // Get all borrow transactions for this booking by this employee
            $transactions = InventoryTransaction::where('employee_id', $employeeId)
                ->where('booking_id', $bookingId)
                ->where('transaction_type', 'borrow')
                ->with(['inventoryItem'])
                ->get();
            
            $borrowedItems = [];
            foreach ($transactions as $transaction) {
                $itemData = $transaction->inventoryItem;
                $borrowedItems[] = [
                    'name' => $itemData->name,
                    'category' => $itemData->category,
                    'item_code' => $itemData->item_code,
                    'quantity' => $transaction->quantity,
                    'is_returnable' => $itemData->isReturnableItem(),
                    'notes' => $transaction->notes,
                    'borrowed_at' => $transaction->transaction_at
                ];
            }
            
            return response()->json([
                'success' => true,
                'items' => $borrowedItems,
                'booking_code' => $booking->code,
                'borrowed_count' => count($borrowedItems)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Employee borrowed items error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Get employee ID safely for debug info
            $employeeId = null;
            $user = Auth::user();
            if ($user && $user->employee) {
                $employeeId = $user->employee->id;
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch borrowed items: ' . $e->getMessage(),
                'debug' => [
                    'booking_id' => $bookingId,
                    'employee_id' => $employeeId,
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ]
            ], 500);
        }
    }

    /**
     * Get updated table data for AJAX table updates
     */
    public function getTableData()
    {
        $employeeId = Auth::user()?->employee?->id;
        if (!$employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        try {
            // Get all bookings assigned to this employee with equipment borrowed status
            $bookings = Booking::with(['customer', 'staffAssignments.employee'])
                ->whereHas('staffAssignments', function ($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId);
                })
                ->selectRaw('
                    bookings.*,
                    CASE WHEN EXISTS(
                        SELECT 1 FROM inventory_transactions 
                        WHERE inventory_transactions.booking_id = bookings.id 
                        AND inventory_transactions.transaction_type = "borrow"
                    ) THEN 1 ELSE 0 END as equipment_borrowed
                ')
                ->orderBy('scheduled_start', 'desc')
                ->get();

            // Calculate statistics
            $today = \Carbon\Carbon::now()->setTimezone('Asia/Manila')->startOfDay();
            $jobsAssignedToday = $bookings->filter(function ($booking) use ($today) {
                $scheduledDate = \Carbon\Carbon::parse($booking->scheduled_start, 'Asia/Manila');
                return $scheduledDate->isSameDay($today) || $booking->status === 'in_progress';
            })->count();

            $jobsCompletedOverall = $bookings->where('status', 'completed')->count();
            $pendingJobs = $bookings->whereIn('status', ['confirmed', 'in_progress'])->count();

            // Generate the table HTML
            $tableHtml = view('employee.partials.jobs-table-body', [
                'bookings' => $bookings
            ])->render();

            return response()->json([
                'success' => true,
                'tableHtml' => $tableHtml,
                'statistics' => [
                    'jobsAssignedToday' => $jobsAssignedToday,
                    'jobsCompletedOverall' => $jobsCompletedOverall,
                    'pendingJobs' => $pendingJobs
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get table data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Payment status polling method removed - no longer needed
}


