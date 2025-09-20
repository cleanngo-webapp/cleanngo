<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->leftJoin('booking_staff_assignments as bsa', 'bsa.booking_id', '=', 'b.id')
            ->leftJoin('employees as e', 'e.id', '=', 'bsa.employee_id')
            ->leftJoin('users as eu', 'eu.id', '=', 'e.user_id')
            ->leftJoin('addresses as a', function($join) {
                $join->on('a.user_id', '=', 'u.id')
                     ->where('a.is_primary', '=', 1);
            })
            ->leftJoin('payment_proofs as pp', function($join) {
                $join->on('pp.booking_id', '=', 'b.id')
                     ->whereRaw('pp.id = (SELECT MAX(id) FROM payment_proofs WHERE booking_id = b.id)');
            })
            ->select([
                'b.id', 'b.code', 'b.scheduled_start', 'b.status', 'b.address_id',
                's.name as service_name',
                DB::raw("CONCAT(u.first_name,' ',u.last_name) as customer_name"),
                DB::raw('u.phone as customer_phone'),
                DB::raw("CONCAT(eu.first_name,' ',eu.last_name) as employee_name"),
                DB::raw('e.user_id as employee_user_id'),
                DB::raw('bsa.employee_id as assigned_employee_id'),
                DB::raw("COALESCE(a.line1,'') as address_line1"),
                DB::raw("COALESCE(a.city,'') as address_city"),
                DB::raw("COALESCE(a.province,'') as address_province"),
                DB::raw('a.latitude as address_latitude'),
                DB::raw('a.longitude as address_longitude'),
                DB::raw('pp.id as payment_proof_id'),
                DB::raw('pp.status as payment_status'),
                DB::raw("CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END as payment_approved"),
            ]);

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

        // Dashboard statistics for the cards
        $totalBookings = DB::table('bookings')->count();
        $todayBookings = DB::table('bookings')
            ->whereDate('scheduled_start', today())
            ->count();
        $activeServices = DB::table('bookings')
            ->where('status', 'in_progress')
            ->count();
        $completedJobsToday = DB::table('bookings')
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        return view('admin.bookings', [
            'bookings' => $bookings,
            'customers' => $customers,
            'employees' => $employees,
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
            'employee_user_id' => 'nullable|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'summary' => 'nullable|string',
        ]);

        $customerId = DB::table('customers')->where('user_id', $data['user_id'])->value('id');
        if (!$customerId) {
            $customerId = DB::table('customers')->insertGetId([
                'user_id' => $data['user_id'],
                'customer_code' => $this->generateCode('C'),
            ]);
        }

        $addressId = DB::table('addresses')
            ->where('user_id', $data['user_id'])
            ->orderByDesc('is_primary')->orderBy('id')->value('id');

        $start = \Carbon\Carbon::parse($data['date'].' '.$data['time']);
        $code = $this->generateCode('B');
        $bookingId = DB::table('bookings')->insertGetId([
            'code' => $code,
            'customer_id' => $customerId,
            'address_id' => $addressId,
            'service_id' => DB::table('services')->where('name','General')->value('id') ?? DB::table('services')->insertGetId([
                'name' => 'General', 'description' => 'Manual entry', 'base_price_cents' => 0, 'duration_minutes' => 60, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()
            ]),
            'scheduled_start' => $start,
            'status' => 'pending',
            'notes' => $data['summary'] ?? null,
            'base_price_cents' => 0,
            'total_due_cents' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!empty($data['employee_user_id'])) {
            $employeeId = DB::table('employees')->where('user_id', $data['employee_user_id'])->value('id');
            if ($employeeId) {
                DB::table('booking_staff_assignments')->updateOrInsert(
                    ['booking_id' => $bookingId, 'employee_id' => $employeeId],
                    ['role' => 'cleaner', 'assigned_at' => now(), 'assigned_by' => Auth::id()]
                );
            }
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
            // Confirm the booking - change status to confirmed
            // This will trigger the boot() method and send notifications
            $booking->status = 'confirmed';
            $booking->save();
            
            return back()->with('status', 'Booking confirmed successfully. You can now assign employees and change status.');
        } else {
            // Cancel the booking - delete it completely
            // Manually trigger notification before deletion since the booking will be deleted
            $oldStatus = $booking->status;
            $newStatus = 'cancelled';
            
            // Manually trigger notification before deletion
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyBookingStatusChanged($booking, $oldStatus, $newStatus);
            
            // Delete related records first
            $booking->staffAssignments()->delete();
            $booking->bookingItems()->delete();
            $booking->delete();
            
            return back()->with('status', 'Booking cancelled and removed.');
        }
    }

    public function assignEmployee(Request $request, $bookingId)
    {
        $request->validate(['employee_user_id' => 'required|exists:users,id']);
        
        // Use Eloquent model to check booking
        $booking = \App\Models\Booking::find($bookingId);
        if (!$booking) {
            return back()->withErrors(['assign' => 'Booking not found.']);
        }
        
        if ($booking->status !== 'confirmed') {
            if ($booking->status === 'cancelled') {
                return back()->withErrors(['assign' => 'Cannot assign employees to cancelled bookings.']);
            }
            return back()->withErrors(['assign' => 'Booking must be confirmed before assigning employees.']);
        }
        
        // Get employee using Eloquent model
        $employee = \App\Models\Employee::where('user_id', $request->employee_user_id)->first();
        if (!$employee) {
            return back()->withErrors(['assign' => 'Employee not found.']);
        }
        
        // Do not allow reassignment once any employee is assigned to this booking
        $alreadyAssigned = $booking->staffAssignments()->exists();
        if ($alreadyAssigned) {
            return back()->withErrors(['assign' => 'An employee is already assigned to this booking and cannot be changed.']);
        }
        
        // Create the assignment using Eloquent model
        // The notification will be triggered automatically by the model's boot() method
        \App\Models\BookingStaffAssignment::create([
            'booking_id'   => $bookingId,
            'employee_id'  => $employee->id,
            'role'         => 'cleaner',
            'assigned_at'  => now(),
            'assigned_by'  => Auth::id(),
        ]);
        
        return back()->with('status', 'Employee assigned.');
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
}

?>


