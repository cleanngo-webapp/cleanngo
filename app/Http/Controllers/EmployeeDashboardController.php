<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    /**
     * Display the employee dashboard with job assignments and progress
     * 
     * This method shows the employee their daily job assignments,
     * completed work, and pending tasks
     */
    public function index()
    {
        $employeeId = Auth::user()?->employee?->id;
        
        if (!$employeeId) {
            // If no employee record, redirect or show empty dashboard
            return view('employee.dashboard', [
                'jobsAssignedToday' => 0,
                'jobsCompletedToday' => 0,
                'pendingJobs' => 0,
                'todayJobs' => collect()
            ]);
        }
        
        $today = Carbon::today();
        
        // Get jobs assigned to this employee today
        $jobsAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereDate('bookings.scheduled_start', $today)
            ->count();
        
        // Get jobs completed by this employee today
        $jobsCompletedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->where('bookings.status', 'completed')
            ->whereDate('bookings.completed_at', $today)
            ->count();
        
        // Get pending jobs assigned to this employee
        $pendingJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereIn('bookings.status', ['pending', 'confirmed'])
            ->count();
        
        // Get today's job details for the employee
        $todayJobs = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('addresses', 'bookings.address_id', '=', 'addresses.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select(
                'bookings.id',
                'bookings.code',
                'bookings.status',
                'bookings.scheduled_start',
                'bookings.scheduled_end',
                'bookings.notes',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'addresses.line1 as street_address',
                'addresses.city',
                'services.name as service_name',
                'services.duration_minutes'
            )
            ->where('booking_staff_assignments.employee_id', $employeeId)
            ->whereDate('bookings.scheduled_start', $today)
            ->orderBy('bookings.scheduled_start')
            ->get();
        
        return view('employee.dashboard', compact(
            'jobsAssignedToday',
            'jobsCompletedToday', 
            'pendingJobs',
            'todayJobs'
        ));
    }
}
