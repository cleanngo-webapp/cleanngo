<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'b.id', 'b.code', 'b.status', 'b.scheduled_start',
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
        $today = Carbon::today();
        
        // Get jobs assigned to this employee today or currently in progress (excluding completed jobs)
        $jobsAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where('bookings.status', '!=', 'completed') // Exclude completed jobs
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
        
        // Get pending jobs assigned to this employee
        $pendingJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereIn('bookings.status', ['pending', 'confirmed'])
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
}


