<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'scheduled_start');
        $sortOrder = $request->get('sortOrder', 'desc');

        $query = DB::table('bookings as b')
            ->leftJoin('customers as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('services as s', 's.id', '=', 'b.service_id')
            ->leftJoin('addresses as a', function($join) {
                $join->on('a.user_id', '=', 'u.id')
                     ->where('a.is_primary', '=', 1);
            })
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('pp.booking_id', '=', 'b.id')
                     ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
            })
            ->select([
                'b.id', 'b.code', 'b.scheduled_start', 'b.status', 'b.address_id', 'b.booking_photos',
                's.name as service_name',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw('u.phone as customer_phone'),
                DB::raw("COALESCE(a.line1,'') as address_line1"),
                DB::raw("COALESCE(a.city,'') as address_city"),
                DB::raw("COALESCE(a.province,'') as address_province"),
                DB::raw('a.latitude as address_latitude'),
                DB::raw('a.longitude as address_longitude'),
                DB::raw('pp.id as payment_proof_id'),
                DB::raw('pp.status as payment_status'),
                DB::raw("CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END as payment_approved"),
            ])
            ->whereNotIn('b.status', ['completed', 'cancelled']); // Exclude completed and cancelled bookings

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(u.first_name,' ',u.last_name)"), 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(eu.first_name,' ',eu.last_name)"), 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $allowedSorts = ['scheduled_start', 'customer_name', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $sortColumn = $sort === 'customer_name' ? DB::raw("CONCAT(u.first_name,' ',u.last_name)") : "b.{$sort}";
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderByDesc('b.scheduled_start');
        }

        $bookings = $query->paginate(15)->appends($request->query());

        // Get assigned employees for each booking
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        $assignedEmployees = collect();
        if (!empty($bookingIds)) {
            $employeeAssignments = DB::table('booking_staff_assignments as bsa')
                ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->whereIn('bsa.booking_id', $bookingIds)
                ->select('bsa.booking_id', 'u.first_name', 'u.last_name', 'u.id as user_id')
                ->orderBy('u.first_name')
                ->get();
            
            $assignedEmployees = $employeeAssignments->groupBy('booking_id');
        }

        // For modal dropdowns
        $customers = DB::table('users')->where('role','customer')->orderBy('first_name')->orderBy('last_name')->get(['id','first_name','last_name']);
        $employees = DB::table('users')->where('role','employee')->orderBy('first_name')->orderBy('last_name')->get(['id','first_name','last_name']);

        // Pull booking item summaries and detailed lines for receipts
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        $itemsByBooking = collect();
        $receiptData = [];
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                // Summary label
                $label = trim(($r->item_type ?? 'item') . ' x ' . (int)($r->quantity ?? 0));
                $itemsByBooking[$r->booking_id] = isset($itemsByBooking[$r->booking_id])
                    ? ($itemsByBooking[$r->booking_id] . ', ' . $label)
                    : $label;
                // Detailed lines
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

        // Build locations payload for map modal
        $locationsData = collect($bookings->items())->mapWithKeys(function($b){
            $addrParts = array_filter([$b->address_line1 ?? null, $b->address_city ?? null, $b->address_province ?? null]);
            return [
                $b->id => [
                    'address' => implode(', ', $addrParts),
                    'lat' => $b->address_latitude,
                    'lng' => $b->address_longitude,
                    'phone' => $b->customer_phone,
                ]
            ];
        })->all();

        // Dashboard statistics for the cards (only active bookings)
        $totalBookings = DB::table('bookings')->whereNotIn('status', ['completed', 'cancelled'])->count();
        $todayBookings = DB::table('bookings')
            ->whereDate('scheduled_start', today())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $activeServices = DB::table('bookings')
            ->where('status', 'in_progress')
            ->count();
        $completedJobsToday = DB::table('bookings')
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        // Handle AJAX requests for table refresh
        if ($request->ajax()) {
            return view('admin.bookings', [
                'bookings' => $bookings,
                'customers' => $customers,
                'employees' => $employees,
                'assignedEmployees' => $assignedEmployees,
                'itemSummaries' => $itemsByBooking,
                'locationsData' => $locationsData,
                'receiptData' => $receiptData,
                'totalBookings' => $totalBookings,
                'todayBookings' => $todayBookings,
                'activeServices' => $activeServices,
                'completedJobsToday' => $completedJobsToday,
                'search' => $search,
                'sort' => $sort,
                'sortOrder' => $sortOrder,
            ]);
        }

        return view('admin.bookings', [
            'bookings' => $bookings,
            'customers' => $customers,
            'employees' => $employees,
            'assignedEmployees' => $assignedEmployees,
            'itemSummaries' => $itemsByBooking,
            'locationsData' => $locationsData,
            'receiptData' => $receiptData,
            'totalBookings' => $totalBookings,
            'todayBookings' => $todayBookings,
            'activeServices' => $activeServices,
            'completedJobsToday' => $completedJobsToday,
            'search' => $search,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'summary' => 'nullable|string',
            'total' => 'nullable|numeric|min:0',
            'items_json' => 'nullable|string',
            'booking_photos' => 'nullable|array|max:10',
            'booking_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per file
        ]);

        $customerId = DB::table('customers')->where('user_id', $data['user_id'])->value('id');
        if (!$customerId) {
            $customerId = DB::table('customers')->insertGetId([
                'user_id' => $data['user_id'],
                'customer_code' => $this->generateCode('C'),
            ]);
        }

        // Check if the customer has an address before creating the booking
        $addressId = DB::table('addresses')
            ->where('user_id', $data['user_id'])
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->value('id');
            
        // If no address exists for this customer, return an error
        if (!$addressId) {
            $customerName = DB::table('users')
                ->where('id', $data['user_id'])
                ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
                ->value('name');
                
            $errorMessage = "Cannot create booking for {$customerName}. The customer does not have an address yet. Please ask the customer to add an address first.";
            
            // Handle AJAX requests with JSON response for SweetAlert
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'no_address'
                ], 422);
            }
            
            return back()->withErrors(['address' => $errorMessage]);
        }

        $start = \Carbon\Carbon::parse($data['date'].' '.$data['time']);
        $code = $this->generateCode('B');
        
        // Calculate total amount
        $totalCents = isset($data['total']) ? (int) round($data['total'] * 100) : 0;
        
        $bookingId = DB::table('bookings')->insertGetId([
            'code' => $code,
            'customer_id' => $customerId,
            'address_id' => $addressId,
            'service_id' => DB::table('services')->where('name','General')->value('id') ?? DB::table('services')->insertGetId([
                'name' => 'General', 'description' => 'Manual entry', 'base_price_cents' => 0, 'duration_minutes' => 60, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()
            ]),
            'scheduled_start' => $start,
            'status' => 'confirmed', // Admin bookings are automatically confirmed
            'notes' => $data['summary'] ?? null,
            'base_price_cents' => $totalCents,
            'total_due_cents' => $totalCents,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Handle employee assignments if provided
        if (!empty($data['employee_ids'])) {
            // First validate all employees for ongoing conflicts before creating any assignments
            $conflictErrors = [];
            $validEmployeeIds = [];
            
            foreach ($data['employee_ids'] as $employeeUserId) {
                $employeeId = DB::table('employees')->where('user_id', $employeeUserId)->value('id');
                if ($employeeId) {
                    // Check if employee has ongoing bookings before assignment
                    $ongoingConflict = $this->checkEmployeeOngoingConflict($employeeId);
                    if ($ongoingConflict) {
                        $employeeName = DB::table('users')
                            ->where('id', $employeeUserId)
                            ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
                            ->value('name');
                        $conflictErrors[] = "{$employeeName}: {$ongoingConflict}";
                    } else {
                        $validEmployeeIds[] = $employeeId;
                    }
                }
            }
            
            // If there are conflicts, return error
            if (!empty($conflictErrors)) {
                // Delete the booking that was just created since we can't assign employees
                DB::table('bookings')->where('id', $bookingId)->delete();
                
                $errorMessage = "Cannot assign employees due to ongoing booking conflicts:\n" . implode("\n", $conflictErrors);
                
                // Handle AJAX requests with JSON response for SweetAlert
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'error_type' => 'employee_conflict'
                    ], 422);
                }
                
                return back()->withErrors(['employee_assignment' => $errorMessage]);
            }
            
            // Create assignments for valid employees
            foreach ($validEmployeeIds as $employeeId) {
                // Check if assignment already exists (prevent duplicates)
                $existingAssignment = DB::table('booking_staff_assignments')
                    ->where('booking_id', $bookingId)
                    ->where('employee_id', $employeeId)
                    ->first();
                
                if (!$existingAssignment) {
                    // Insert new assignment - this will make the employee appear in the table
                    DB::table('booking_staff_assignments')->insert([
                        'booking_id' => $bookingId,
                        'employee_id' => $employeeId,
                        'role' => 'cleaner',
                        'assigned_at' => now(),
                        'assigned_by' => Auth::id()
                    ]);
                }
            }
        }

        // Handle photo uploads if provided
        $photoPaths = [];
        if ($request->hasFile('booking_photos')) {
            foreach ($request->file('booking_photos') as $photo) {
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('booking-photos', $filename, 'public');
                $photoPaths[] = $path;
            }
            
            // Update the booking with photo paths
            if (!empty($photoPaths)) {
                DB::table('bookings')
                    ->where('id', $bookingId)
                    ->update(['booking_photos' => json_encode($photoPaths)]);
            }
        }

        // Create booking items if provided
        $items = [];
        if (!empty($data['items_json'])) {
            $decoded = json_decode($data['items_json'], true);
            if (is_array($decoded)) { 
                $items = $decoded; 
            }
        }
        
        // Map item types to service IDs (same as customer controller)
        $serviceMapping = [
            // Sofa items
            'sofa_1' => 'Sofa Mattress Deep Cleaning',
            'sofa_2' => 'Sofa Mattress Deep Cleaning',
            'sofa_3' => 'Sofa Mattress Deep Cleaning',
            'sofa_4' => 'Sofa Mattress Deep Cleaning',
            'sofa_5' => 'Sofa Mattress Deep Cleaning',
            'sofa_6' => 'Sofa Mattress Deep Cleaning',
            'sofa_7' => 'Sofa Mattress Deep Cleaning',
            'sofa_8' => 'Sofa Mattress Deep Cleaning',
            
            // Mattress items
            'mattress_single' => 'Mattress Deep Cleaning',
            'mattress_double' => 'Mattress Deep Cleaning',
            'mattress_king' => 'Mattress Deep Cleaning',
            'mattress_california' => 'Mattress Deep Cleaning',
            
            // Car items
            'car_sedan' => 'Home Service Car Interior Detailing',
            'car_suv' => 'Home Service Car Interior Detailing',
            'car_van' => 'Home Service Car Interior Detailing',
            'car_coaster' => 'Home Service Car Interior Detailing',
            
            // Area-based services
            'carpet_sqft' => 'Carpet Deep Cleaning',
            'post_construction_sqm' => 'Post Construction Cleaning',
            'disinfect_sqm' => 'Home/Office Disinfection',
            'glass_sqft' => 'Glass Cleaning',
            'house_cleaning_sqm' => 'House Cleaning',
            'curtain_cleaning_yard' => 'Curtain Cleaning',
        ];
        
        foreach ($items as $item) {
            $qty = (int)($item['qty'] ?? 0);
            $unit = (float)($item['unitPrice'] ?? 0); // Keep as float to preserve decimals like 101.67
            $sqm = isset($item['areaSqm']) ? (float)$item['areaSqm'] : null;
            $line = (int)round(($sqm ? $sqm * $qty * $unit : $qty * $unit) * 100); // Convert pesos to cents
            
            // Get the correct service ID for this item type
            $itemType = $item['type'] ?? null;
            $serviceName = $serviceMapping[$itemType] ?? 'General';
            $itemService = DB::table('services')->where('name', $serviceName)->first();
            
            // Fallback to General service if specific service not found
            if (!$itemService) {
                $itemService = DB::table('services')->where('name', 'General')->first();
            }
            
            // Create booking item
            DB::table('booking_items')->insert([
                'booking_id' => $bookingId,
                'service_id' => $itemService->id,
                'item_type' => $itemType,
                'quantity' => $qty,
                'area_sqm' => $sqm,
                'unit_price_cents' => ($unit * 100),
                'line_total_cents' => $line,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'booking_id' => $bookingId,
                'booking_code' => $code,
                'employees_assigned' => !empty($data['employee_ids']) ? count($data['employee_ids']) : 0,
                'photos_uploaded' => $request->hasFile('booking_photos') ? count($request->file('booking_photos')) : 0
            ]);
        }

        return back()->with('status', 'Booking created');
    }

    public function updateStatus(Request $request, $bookingId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,confirmed,cancelled,completed',
            'bypass_payment_proof' => 'sometimes|boolean'
        ]);
        
        // Use Eloquent model to trigger notifications
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking) {
            return back()->withErrors(['status' => 'Booking not found.']);
        }
        
        // Only allow status changes for confirmed bookings
        if ($booking->status !== 'confirmed') {
            return back()->withErrors(['status' => 'Status can only be changed for confirmed bookings.']);
        }
        
        // Only allow in_progress, completed, and cancelled for confirmed bookings
        if (!in_array($request->status, ['in_progress', 'completed', 'cancelled'])) {
            return back()->withErrors(['status' => 'Only In Progress, Completed, and Cancelled statuses are allowed for confirmed bookings.']);
        }
        
        if ($request->status === 'cancelled') {
            // Delete the booking when cancelled as requested
            // Note: This will trigger the model's deleting event, but we need to manually trigger notification
            // since the booking will be deleted before the updated event can fire
            $oldStatus = $booking->status;
            $newStatus = 'cancelled';
            
            // Manually trigger notification before deletion
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyBookingStatusChanged($booking, $oldStatus, $newStatus);
            
            // Delete related records first
            $booking->staffAssignments()->delete();
            $booking->bookingItems()->delete();
            $booking->delete();
            
            return back()->with('status','Booking cancelled and removed');
        }
        
        // If status is completed and bypass_payment_proof is set, handle payment proof
        if ($request->status === 'completed' && $request->has('bypass_payment_proof')) {
            // Check if there's already a payment proof for this booking
            $existingProof = \App\Models\PaymentProof::where('booking_id', $bookingId)
                ->where('status', 'approved')
                ->first();
            
            if (!$existingProof) {
                // Create an approved payment proof to bypass the requirement
                \App\Models\PaymentProof::create([
                    'booking_id' => $bookingId,
                    'amount' => $booking->total_due_cents / 100, // Convert cents to dollars
                    'payment_method' => 'admin_bypass',
                    'status' => 'approved',
                    'admin_notes' => 'Payment proof bypassed by admin - status changed to completed',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
                
                // Also update the booking's payment status to 'paid' to trigger payroll notifications
                $booking->payment_status = 'paid';
                $booking->payment_method = 'admin_bypass';
                $booking->amount_paid_cents = $booking->total_due_cents;
            }
            
            // Set completed_at timestamp
            $booking->completed_at = now();
        }
        
        // Update the booking status using Eloquent model to trigger notifications
        $booking->status = $request->status;
        $booking->save(); // This will trigger the boot() method and send notifications
        
        $statusMessage = $request->status === 'completed' && $request->has('bypass_payment_proof') 
            ? 'Booking marked as completed with payment proof bypassed'
            : 'Booking status updated successfully';
            
        return back()->with('status', $statusMessage);
    }

    /**
     * Cancel a confirmed booking with reason
     */
    public function cancel(Request $request, $bookingId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        // Use Eloquent model to check booking
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.'
                ], 404);
            }
            return back()->withErrors(['cancel' => 'Booking not found.']);
        }
        
        // Only allow cancellation of confirmed bookings
        if ($booking->status !== 'confirmed') {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only confirmed bookings can be cancelled.'
                ], 400);
            }
            return back()->withErrors(['cancel' => 'Only confirmed bookings can be cancelled.']);
        }
        
        $reason = $request->reason;
        
        // Set the cancellation reason for the model to use in notifications
        $booking->setCancellationReason($reason);
        
        // Update status to cancelled to trigger notifications via model boot method
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->save();
        
        // Delete related records first
        $booking->staffAssignments()->delete();
        $booking->bookingItems()->delete();
        $booking->delete();
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully. Customer and assigned employees have been notified.',
                'booking_code' => $booking->code
            ]);
        }
        
        return back()->with('status', 'Booking cancelled successfully. Customer and assigned employees have been notified.');
    }

    /**
     * Handle booking confirmation or cancellation from pending status
     * This is the new workflow where pending bookings must be confirmed or cancelled first
     */
    public function confirm(Request $request, $bookingId)
    {
        $request->validate(['action' => 'required|in:confirm,cancel']);
        
        // Use Eloquent model to trigger notifications
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking || $booking->status !== 'pending') {
            return back()->withErrors(['confirm' => 'Booking not found or not in pending status.']);
        }
        
        if ($request->action === 'confirm') {
            // Get the selected employee from the request
            $selectedEmployeeUserId = $request->get('employee_user_id');
            
            // Confirm the booking - change status to confirmed
            $booking->status = 'confirmed';
            $booking->save();
            
            // If an employee was selected, create the assignment
            $employeeName = null;
            if ($selectedEmployeeUserId) {
                $employee = \App\Models\Employee::where('user_id', $selectedEmployeeUserId)->first();
                if ($employee) {
                    // Check if employee has ongoing bookings before assignment
                    $ongoingConflict = $this->checkEmployeeOngoingConflict($employee->id);
                    if ($ongoingConflict) {
                        return back()->withErrors(['confirm' => $ongoingConflict]);
                    }

                    // Check if assignment already exists (prevent duplicates)
                    $existingAssignment = \App\Models\BookingStaffAssignment::where('booking_id', $bookingId)
                        ->where('employee_id', $employee->id)
                        ->first();
                    
                    if (!$existingAssignment) {
                        \App\Models\BookingStaffAssignment::create([
                            'booking_id'   => $bookingId,
                            'employee_id'  => $employee->id,
                            'role'         => 'cleaner',
                            'assigned_at'  => now(),
                            'assigned_by'  => Auth::id(),
                        ]);
                    }
                    
                    $employeeName = $employee->user->first_name . ' ' . $employee->user->last_name;
                }
            }
            
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                $message = $employeeName ? 
                    "Booking confirmed successfully! {$employeeName} has been assigned." : 
                    'Booking confirmed successfully!';
                    
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'booking_code' => $booking->code,
                    'employee_name' => $employeeName
                ]);
            }
            
            $statusMessage = $employeeName ? 
                "Booking confirmed successfully. {$employeeName} has been assigned." : 
                'Booking confirmed successfully.';
                
            return back()->with('status', $statusMessage);
        } else {
            // Cancel the booking - delete it completely
            $reason = $request->get('reason', 'No reason provided');
            
            // Set the cancellation reason for the model to use in notifications
            $booking->setCancellationReason($reason);
            
            // Update status to cancelled to trigger notifications via model boot method
            $booking->status = 'cancelled';
            $booking->cancelled_at = now();
            $booking->save();
            
            $bookingCode = $booking->code;
            
            // Delete related records first
            $booking->staffAssignments()->delete();
            $booking->bookingItems()->delete();
            $booking->delete();
            
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking cancelled successfully. Customer and assigned employees have been notified.',
                    'booking_code' => $bookingCode
                ]);
            }
            
            return back()->with('status', 'Booking cancelled successfully. Customer and assigned employees have been notified.');
        }
    }

    public function assignEmployee(Request $request, $bookingId)
    {
        try {
            Log::info('Employee assignment attempt', [
                'booking_id' => $bookingId,
                'employee_user_id' => $request->employee_user_id,
                'user_id' => Auth::id()
            ]);
            
            $request->validate([
                'employee_user_id' => 'nullable|exists:users,id'
            ]);
            
            // Use Eloquent model to check booking
            $booking = \App\Models\Booking::find($bookingId);
            if (!$booking) {
                return back()->withErrors(['assign' => 'Booking not found.']);
            }
            
            if ($booking->status !== 'confirmed' && $booking->status !== 'pending') {
                if ($booking->status === 'cancelled') {
                    return back()->withErrors(['assign' => 'Cannot assign employees to cancelled bookings.']);
                }
                return back()->withErrors(['assign' => 'Booking must be confirmed or pending before assigning employees.']);
            }
            
            // Get employee using Eloquent model (only if employee_user_id is provided)
            $employee = null;
            if (!empty($request->employee_user_id)) {
                Log::info('Looking up employee', ['user_id' => $request->employee_user_id]);
                $employee = \App\Models\Employee::with('user')->where('user_id', $request->employee_user_id)->first();
                Log::info('Employee lookup result', [
                    'found' => $employee ? true : false,
                    'employee_id' => $employee ? $employee->id : null,
                    'has_user' => $employee && $employee->user ? true : false
                ]);
                if (!$employee) {
                    return back()->withErrors(['assign' => 'Employee not found.']);
                }

                // Check if employee has ongoing bookings before assignment
                $ongoingConflict = $this->checkEmployeeOngoingConflict($employee->id);
                if ($ongoingConflict) {
                    return back()->withErrors(['assign' => $ongoingConflict]);
                }
            }
            
            // Handle reassignment - update existing assignment or create new one
            $existingAssignment = $booking->staffAssignments()->first();
            
            if ($existingAssignment) {
                if (empty($request->employee_user_id)) {
                    // Remove assignment if empty value selected
                    DB::table('booking_staff_assignments')
                        ->where('booking_id', $bookingId)
                        ->where('employee_id', $existingAssignment->employee_id)
                        ->delete();
                } else {
                    // Update existing assignment using DB::table due to composite primary key
                    DB::table('booking_staff_assignments')
                        ->where('booking_id', $bookingId)
                        ->where('employee_id', $existingAssignment->employee_id)
                        ->update([
                            'employee_id' => $employee->id,
                            'assigned_at' => now(),
                            'assigned_by' => Auth::id(),
                        ]);
                }
            } else if (!empty($request->employee_user_id) && $employee) {
                // Create new assignment only if employee is selected and found
                // Use DB::table insert instead of Eloquent create due to composite primary key
                DB::table('booking_staff_assignments')->insert([
                    'booking_id'   => $bookingId,
                    'employee_id'  => $employee->id,
                    'role'         => 'cleaner',
                    'assigned_at'  => now(),
                    'assigned_by'  => Auth::id(),
                ]);
            }
            
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee assigned successfully!',
                    'booking_code' => $booking->code,
                    'employee_name' => ($employee && $employee->user) ? $employee->user->first_name . ' ' . $employee->user->last_name : null
                ]);
            }
            
            return back()->with('status', 'Employee assigned.');
            
        } catch (\Exception $e) {
            Log::error('Employee assignment error: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while assigning employee: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['assign' => 'An error occurred while assigning employee.']);
        }
    }

    /**
     * Get employee availability for assignment modal
     */
    public function getEmployeeAvailability(Request $request, $bookingId)
    {
        try {
            $scheduledTime = $request->get('time');
            if (!$scheduledTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scheduled time is required'
                ], 400);
            }

            $bookingTime = \Carbon\Carbon::parse($scheduledTime);
            
            // Get all employees
            $employees = DB::table('employees as e')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->select('e.id as employee_id', 'u.id as user_id', 'u.first_name', 'u.last_name')
                ->orderBy('u.first_name')
                ->orderBy('u.last_name')
                ->get();

            $conflicts = [];
            
            // Check for scheduling conflicts
            foreach ($employees as $employee) {
                $conflictReason = $this->checkEmployeeConflict($employee->employee_id, $bookingTime);
                if ($conflictReason) {
                    $conflicts[] = [
                        'employee_id' => $employee->employee_id,
                        'conflict_reason' => $conflictReason
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'employees' => $employees,
                'conflicts' => $conflicts
            ]);

        } catch (\Exception $e) {
            Log::error('Employee availability check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check employee availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign multiple employees to a booking
     */
    public function assignEmployees(Request $request, $bookingId)
    {
        try {
            $request->validate([
                'employee_ids' => 'required|array|min:1',
                'employee_ids.*' => 'exists:users,id'
            ]);

            $booking = \App\Models\Booking::find($bookingId);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            if ($booking->status !== 'confirmed' && $booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking must be confirmed or pending before assigning employees'
                ], 400);
            }

            $bookingTime = \Carbon\Carbon::parse($booking->scheduled_start);
            $assignedEmployees = [];
            $conflictErrors = [];

            // Check for conflicts before assigning
            foreach ($request->employee_ids as $userId) {
                $employee = \App\Models\Employee::where('user_id', $userId)->first();
                if (!$employee) {
                    continue;
                }

                $conflictReason = $this->checkEmployeeConflict($employee->id, $bookingTime);
                if ($conflictReason) {
                    $conflictErrors[] = "Employee {$employee->user->first_name} {$employee->user->last_name}: {$conflictReason}";
                } else {
                    $assignedEmployees[] = $employee;
                }
            }

            if (!empty($conflictErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some employees have scheduling conflicts',
                    'conflicts' => $conflictErrors
                ], 400);
            }

            // Remove existing assignments
            DB::table('booking_staff_assignments')->where('booking_id', $bookingId)->delete();

            // Create new assignments
            foreach ($assignedEmployees as $employee) {
                DB::table('booking_staff_assignments')->insert([
                    'booking_id' => $bookingId,
                    'employee_id' => $employee->id,
                    'role' => 'cleaner',
                    'assigned_at' => now(),
                    'assigned_by' => Auth::id(),
                ]);
            }

            $employeeNames = array_map(function($emp) {
                return $emp->user->first_name . ' ' . $emp->user->last_name;
            }, $assignedEmployees);

            return response()->json([
                'success' => true,
                'message' => 'Employees assigned successfully: ' . implode(', ', $employeeNames),
                'assigned_employees' => $employeeNames
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple employee assignment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign employees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if an employee has a scheduling conflict
     */
    private function checkEmployeeConflict($employeeId, $bookingTime)
    {
        // First check if employee has any in_progress booking (regardless of time)
        $inProgressBooking = DB::table('booking_staff_assignments as bsa')
            ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', 'in_progress')
            ->first();

        if ($inProgressBooking) {
            $ongoingTime = \Carbon\Carbon::parse($inProgressBooking->scheduled_start);
            return "Currently has an ongoing booking {$inProgressBooking->code} (in progress) that started at " . 
                   $ongoingTime->format('g:i A') . " on " . 
                   $ongoingTime->format('M j, Y') . ". Cannot assign to new bookings while current booking is in progress.";
        }

        // Check for confirmed bookings on the same day that haven't been completed
        $confirmedBooking = DB::table('booking_staff_assignments as bsa')
            ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', 'confirmed')
            ->whereDate('b.scheduled_start', $bookingTime->toDateString())
            ->first();

        if ($confirmedBooking) {
            $confirmedTime = \Carbon\Carbon::parse($confirmedBooking->scheduled_start);
            return "Already assigned to confirmed booking {$confirmedBooking->code} at " . 
                   $confirmedTime->format('g:i A') . " on " . 
                   $confirmedTime->format('M j, Y') . ". Cannot assign to multiple bookings on the same day.";
        }

        // Check for other status bookings at the same time (excluding completed/cancelled)
        $conflictingBooking = DB::table('booking_staff_assignments as bsa')
            ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', '!=', 'completed')
            ->where('b.status', '!=', 'cancelled')
            ->where('b.status', '!=', 'in_progress') // Exclude in_progress as it's handled above
            ->where('b.status', '!=', 'confirmed') // Exclude confirmed as it's handled above
            ->whereDate('b.scheduled_start', $bookingTime->toDateString())
            ->whereTime('b.scheduled_start', $bookingTime->format('H:i:s'))
            ->first();

        if ($conflictingBooking) {
            $conflictTime = \Carbon\Carbon::parse($conflictingBooking->scheduled_start);
            return "Already assigned to booking {$conflictingBooking->code} at " . 
                   $conflictTime->format('g:i A') . " on " . 
                   $conflictTime->format('M j, Y');
        }

        return null;
    }

    /**
     * Check if an employee has any ongoing booking (confirmed or in_progress status)
     * This is a separate method for checking ongoing conflicts without time consideration
     */
    private function checkEmployeeOngoingConflict($employeeId)
    {
        // Check for in_progress bookings (regardless of time)
        $inProgressBooking = DB::table('booking_staff_assignments as bsa')
            ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', 'in_progress')
            ->first();

        if ($inProgressBooking) {
            $ongoingTime = \Carbon\Carbon::parse($inProgressBooking->scheduled_start);
            return "Employee currently has an ongoing booking {$inProgressBooking->code} (in progress) that started at " . 
                   $ongoingTime->format('g:i A') . " on " . 
                   $ongoingTime->format('M j, Y') . ". Cannot assign to new bookings while current booking is in progress.";
        }

        // Check for confirmed bookings (any confirmed booking prevents new assignments)
        $confirmedBooking = DB::table('booking_staff_assignments as bsa')
            ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
            ->where('bsa.employee_id', $employeeId)
            ->where('b.status', 'confirmed')
            ->first();

        if ($confirmedBooking) {
            $confirmedTime = \Carbon\Carbon::parse($confirmedBooking->scheduled_start);
            return "Employee currently has a confirmed booking {$confirmedBooking->code} at " . 
                   $confirmedTime->format('g:i A') . " on " . 
                   $confirmedTime->format('M j, Y') . ". Cannot assign to new bookings while employee has confirmed work.";
        }

        return null;
    }

    private function generateCode(string $prefix): string
    {
        $year = date('Y');
        for ($i=0; $i<1000; $i++) {
            $code = $prefix.$year.str_pad((string)random_int(0,999), 3, '0', STR_PAD_LEFT);
            $exists = $prefix==='B' ? DB::table('bookings')->where('code',$code)->exists() : DB::table('customers')->where('customer_code',$code)->exists();
            if (!$exists) return $code;
        }
        return $prefix.$year.substr((string)microtime(true), -3);
    }


    /**
     * Get employee availability for manual booking modal
     */
    public function getEmployeeAvailabilityForManualBooking(Request $request)
    {
        try {
            $scheduledTime = $request->get('time');
            if (!$scheduledTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scheduled time is required'
                ], 400);
            }

            $bookingTime = \Carbon\Carbon::parse($scheduledTime);
            
            // Get all employees
            $employees = DB::table('employees as e')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->select('e.id as employee_id', 'u.id as user_id', 'u.first_name', 'u.last_name')
                ->orderBy('u.first_name')
                ->orderBy('u.last_name')
                ->get();

            $conflicts = [];
            
            // Check for scheduling conflicts
            foreach ($employees as $employee) {
                $conflictReason = $this->checkEmployeeConflict($employee->employee_id, $bookingTime);
                if ($conflictReason) {
                    $conflicts[] = [
                        'employee_id' => $employee->employee_id,
                        'user_id' => $employee->user_id,
                        'conflict_reason' => $conflictReason
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'employees' => $employees,
                'conflicts' => $conflicts
            ]);

        } catch (\Exception $e) {
            Log::error('Employee availability check for manual booking error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check employee availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking summary for admin view
     */
    public function getSummary($bookingId)
    {
        $booking = DB::table('bookings')
            ->where('id', $bookingId)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
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
            'html' => $html
        ]);
    }

    /**
     * Get booking location for admin view
     */
    public function getLocation($bookingId)
    {
        $booking = DB::table('bookings')
            ->where('id', $bookingId)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
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
     * Get booking photos for admin view
     */
    public function getPhotos($bookingId)
    {
        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
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
     * Display completed and cancelled bookings
     */
    public function completed(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status'); // Filter by completed or cancelled
        $sort = $request->get('sort', 'scheduled_start');
        $sortOrder = $request->get('sortOrder', 'desc');

        $query = DB::table('bookings as b')
            ->leftJoin('customers as c', 'c.id', '=', 'b.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('services as s', 's.id', '=', 'b.service_id')
            ->leftJoin('addresses as a', function($join) {
                $join->on('a.user_id', '=', 'u.id')
                     ->where('a.is_primary', '=', 1);
            })
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('pp.booking_id', '=', 'b.id')
                     ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
            })
            ->select([
                'b.id', 'b.code', 'b.scheduled_start', 'b.status', 'b.address_id', 'b.booking_photos', 'b.completed_at', 'b.updated_at',
                's.name as service_name',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw('u.phone as customer_phone'),
                DB::raw("COALESCE(a.line1,'') as address_line1"),
                DB::raw("COALESCE(a.city,'') as address_city"),
                DB::raw("COALESCE(a.province,'') as address_province"),
                DB::raw('a.latitude as address_latitude'),
                DB::raw('a.longitude as address_longitude'),
                DB::raw('pp.id as payment_proof_id'),
                DB::raw('pp.status as payment_status'),
                DB::raw("CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END as payment_approved"),
            ])
               ->where(function($query) use ($status) {
                   if ($status && in_array($status, ['completed', 'cancelled'])) {
                       $query->where('b.status', $status);
                   } else {
                       $query->where('b.status', 'completed'); // Default to completed if no status filter
                   }
               });

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('b.code', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(u.first_name,' ',u.last_name)"), 'like', "%{$search}%")
                  ->orWhereExists(function($subQuery) use ($search) {
                      $subQuery->select(DB::raw(1))
                               ->from('booking_staff_assignments as bsa')
                               ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
                               ->join('users as eu', 'eu.id', '=', 'e.user_id')
                               ->whereColumn('bsa.booking_id', 'b.id')
                               ->where(DB::raw("CONCAT(eu.first_name,' ',eu.last_name)"), 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $allowedSorts = ['scheduled_start', 'customer_name', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $sortColumn = $sort === 'customer_name' ? DB::raw("CONCAT(u.first_name,' ',u.last_name)") : "b.{$sort}";
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->orderByDesc('b.scheduled_start');
        }

        $bookings = $query->paginate(15)->appends($request->query());

        // Get assigned employees for each booking
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        $assignedEmployees = collect();
        if (!empty($bookingIds)) {
            $employeeAssignments = DB::table('booking_staff_assignments as bsa')
                ->join('employees as e', 'e.id', '=', 'bsa.employee_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->whereIn('bsa.booking_id', $bookingIds)
                ->select('bsa.booking_id', 'u.first_name', 'u.last_name', 'u.id as user_id')
                ->orderBy('u.first_name')
                ->get();
            
            $assignedEmployees = $employeeAssignments->groupBy('booking_id');
        }

        // Pull booking item summaries and detailed lines for receipts
        $bookingIds = collect($bookings->items())->pluck('id')->all();
        $itemsByBooking = collect();
        $receiptData = [];
        if (!empty($bookingIds)) {
            $rows = DB::table('booking_items')
                ->whereIn('booking_id', $bookingIds)
                ->orderBy('booking_id')
                ->get(['booking_id','item_type','quantity','area_sqm','unit_price_cents','line_total_cents']);
            $grouped = [];
            foreach ($rows as $r) {
                // Summary label
                $label = trim(($r->item_type ?? 'item') . ' x ' . (int)($r->quantity ?? 0));
                $itemsByBooking[$r->booking_id] = isset($itemsByBooking[$r->booking_id])
                    ? ($itemsByBooking[$r->booking_id] . ', ' . $label)
                    : $label;
                // Detailed lines
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

        // Build locations payload for map modal
        $locationsData = collect($bookings->items())->mapWithKeys(function($b){
            $addrParts = array_filter([$b->address_line1 ?? null, $b->address_city ?? null, $b->address_province ?? null]);
            return [
                $b->id => [
                    'address' => implode(', ', $addrParts),
                    'lat' => $b->address_latitude,
                    'lng' => $b->address_longitude,
                    'phone' => $b->customer_phone,
                ]
            ];
        })->all();

        // Statistics for completed bookings page
        $totalCompleted = DB::table('bookings')->where('status', 'completed')->count();
        $monthlyCompleted = DB::table('bookings')
            ->where('status', 'completed')
            ->whereYear('completed_at', now()->year)
            ->whereMonth('completed_at', now()->month)
            ->count();
        $totalCancelled = DB::table('bookings')->where('status', 'cancelled')->count();

        // Handle AJAX requests for table refresh
        if ($request->ajax()) {
            return view('admin.completedbookings', [
                'bookings' => $bookings,
                'assignedEmployees' => $assignedEmployees,
                'itemSummaries' => $itemsByBooking,
                'locationsData' => $locationsData,
                'receiptData' => $receiptData,
                'totalCompleted' => $totalCompleted,
                'monthlyCompleted' => $monthlyCompleted,
                'totalCancelled' => $totalCancelled,
                'search' => $search,
                'status' => $status,
                'sort' => $sort,
                'sortOrder' => $sortOrder,
            ]);
        }

        return view('admin.completedbookings', [
            'bookings' => $bookings,
            'assignedEmployees' => $assignedEmployees,
            'itemSummaries' => $itemsByBooking,
            'locationsData' => $locationsData,
            'receiptData' => $receiptData,
            'totalCompleted' => $totalCompleted,
            'monthlyCompleted' => $monthlyCompleted,
            'totalCancelled' => $totalCancelled,
            'search' => $search,
            'status' => $status,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
        ]);
    }
}

?>


