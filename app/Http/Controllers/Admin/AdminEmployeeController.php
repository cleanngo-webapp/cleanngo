<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        // Get search and sort parameters
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'employee_id'); // 'employee_id' or 'name'
        $sortOrder = $request->get('sort_order', 'asc'); // 'asc' or 'desc'

        // Get statistics for the dashboard cards
        $employeesAssignedToday = DB::table('booking_staff_assignments')
            ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
            ->whereDate('bookings.scheduled_start', $today)
            ->distinct('booking_staff_assignments.employee_id')
            ->count('booking_staff_assignments.employee_id');

        $completedJobsToday = DB::table('bookings')
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();

        $todayBookings = DB::table('bookings')
            ->whereDate('scheduled_start', $today)
            ->count();

        // Build the base query
        $query = DB::table('users')
            ->leftJoin('employees', 'employees.user_id', '=', 'users.id')
            ->where('users.role', 'employee')
            ->select([
                'users.id as user_id',
                'users.first_name',
                'users.last_name',
                'users.username',
                'users.phone',
                'employees.id as employee_id',
                'employees.employee_code',
                'employees.contact_number',
                'employees.employment_status',
                'employees.is_active',
            ])
            ->selectSub(function ($q) {
                $q->from('booking_staff_assignments as bsa')
                  ->whereColumn('bsa.employee_id', 'employees.id')
                  ->selectRaw('count(*)');
            }, 'total_bookings')
            ->selectSub(function ($q) use ($today) {
                $q->from('booking_staff_assignments as bsa')
                  ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
                  ->whereDate('b.scheduled_start', $today)
                  ->whereColumn('bsa.employee_id', 'employees.id')
                  ->selectRaw('count(*)');
            }, 'jobs_assigned_today');

        // Apply search logic - search across all relevant fields
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('users.first_name', 'like', "%{$search}%")
                  ->orWhere('users.last_name', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%")
                  ->orWhere('employees.employee_code', 'like', "%{$search}%")
                  ->orWhere('employees.contact_number', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply sorting with order
        if ($sort === 'name') {
            $query->orderBy('users.first_name', $sortOrder)
                  ->orderBy('users.last_name', $sortOrder);
        } else {
            // Default sort by employee_id
            $query->orderBy('employees.id', $sortOrder);
        }

        $employees = $query->paginate(15);

        return view('admin.employees', compact(
            'employees',
            'employeesAssignedToday',
            'completedJobsToday',
            'todayBookings',
            'search',
            'sort',
            'sortOrder'
        ));
    }

    /**
     * Show employee details for admin view/edit
     * Admin can edit employment details and work history, but only view personal info
     */
    public function show($userId)
    {
        // Get user with their employee record
        $user = User::with('employee')->findOrFail($userId);
        
        // Ensure this is an employee
        if ($user->role !== 'employee') {
            abort(404, 'User is not an employee');
        }

        // If no employee record exists, create a basic one
        if (!$user->employee) {
            $employee = new Employee();
            $employee->user_id = $user->id;
            $employee->save();
            $user->refresh();
        }

        // Update the jobs completed count to reflect actual completed jobs
        $user->employee->updateJobsCompletedCount();

        return view('admin.employee-details', compact('user'));
    }

    /**
     * Update employee employment details and work history (admin only)
     * Personal info can only be updated by the employee themselves
     */
    public function update(Request $request, $userId)
    {
        $user = User::with('employee')->findOrFail($userId);
        
        // Ensure this is an employee
        if ($user->role !== 'employee') {
            abort(404, 'User is not an employee');
        }

        // Validate only the fields that admin can edit
        $request->validate([
            'department' => 'nullable|string|max:255',
            'employment_type' => 'nullable|in:full-time,part-time,contract',
            'date_hired' => 'nullable|date',
            'employment_status' => 'nullable|in:active,inactive,terminated',
            'work_schedule' => 'nullable|string|max:255',
            'recent_job' => 'nullable|string|max:255',
            'attendance_summary' => 'nullable|string|max:255',
            'performance_rating' => 'nullable|string|max:255',
        ]);

        // Update only the employment and work history fields
        $employee = $user->employee;
        if (!$employee) {
            $employee = new Employee();
            $employee->user_id = $user->id;
        }

        $employee->department = $request->department;
        $employee->employment_type = $request->employment_type;
        $employee->date_hired = $request->date_hired;
        $employee->employment_status = $request->employment_status;
        $employee->work_schedule = $request->work_schedule;
        $employee->recent_job = $request->recent_job;
        $employee->attendance_summary = $request->attendance_summary;
        $employee->performance_rating = $request->performance_rating;
        
        $employee->save();

        return redirect()->route('admin.employee.show', $userId)
            ->with('status', 'Employee information updated successfully!');
    }

    /**
     * Increment jobs completed count for an employee
     * This should be called when a job is completed
     */
    public function incrementJobsCompleted($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->incrementJobsCompleted();
        
        return response()->json(['success' => true, 'jobs_completed' => $employee->jobs_completed]);
    }

    /**
     * Update all employees' job completion counts
     * This can be called periodically to sync the counts with actual completed jobs
     */
    public function updateAllJobCounts()
    {
        $employees = Employee::all();
        $updated = 0;
        
        foreach ($employees as $employee) {
            $oldCount = $employee->jobs_completed;
            $employee->updateJobsCompletedCount();
            if ($oldCount != $employee->jobs_completed) {
                $updated++;
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => "Updated job counts for {$updated} employees"
        ]);
    }
}


