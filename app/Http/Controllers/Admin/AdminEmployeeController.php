<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminEmployeeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
     * Store a newly created employee
     * Admin can create new employees with all required information
     */
    public function store(Request $request)
    {
        // Validate employee registration data
        $data = $request->validate([
            'username' => ['required','string','alpha_dash','min:3','max:50','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'contact' => ['nullable','string','max:50'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // Create the user with employee role
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['contact'] ?? null,
            'role' => 'employee', // Always set as employee for admin-created accounts
            'password_hash' => Hash::make($data['password']),
        ]);

        // Create the employee profile record
        Employee::create([
            'user_id' => $user->id,
            'is_cleaner' => true,
            'is_active' => true,
            'contact_number' => $user->phone,
            'email_address' => $user->email,
            'date_hired' => now()->toDateString(),
            'employee_code' => 'E' . now()->format('Y') . str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT),
        ]);

        // Return JSON response for AJAX requests (modal submissions)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!'
            ]);
        }
        
        // Return redirect for regular form submissions
        return redirect()->route('admin.employees')
            ->with('status', 'Employee created successfully!');
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
        // Removed employment fields: department, employment_type, employment_status, work_schedule
        $request->validate([
            'date_hired' => 'nullable|date',
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

        // Only update fields that still exist in the database
        $employee->date_hired = $request->date_hired;
        $employee->recent_job = $request->recent_job;
        $employee->attendance_summary = $request->attendance_summary;
        $employee->performance_rating = $request->performance_rating;
        
        $employee->save();

        return redirect()->route('admin.employee.show', $userId)
            ->with('status', 'Employee information updated successfully!');
    }

    /**
     * Delete an employee (remove user from users table)
     * This will permanently delete the employee and all their data
     */
    public function destroy($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Ensure this is an employee
            if ($user->role !== 'employee') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an employee'
                ], 400);
            }

            // Store employee details for notification before deletion
            $employeeName = $user->first_name . ' ' . $user->last_name;
            $employeeCode = $user->employee->employee_code ?? 'N/A';
            $username = $user->username;

            // Check if employee has any active bookings
            $activeBookings = DB::table('booking_staff_assignments')
                ->join('bookings', 'booking_staff_assignments.booking_id', '=', 'bookings.id')
                ->where('booking_staff_assignments.employee_id', $user->employee->id ?? null)
                ->whereIn('bookings.status', ['pending', 'confirmed', 'in_progress'])
                ->count();

            if ($activeBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete employee with active bookings. Please reassign or complete their bookings first.'
                ], 400);
            }

            // Delete related records first (cascade delete)
            if ($user->employee) {
                // Delete booking staff assignments
                DB::table('booking_staff_assignments')
                    ->where('employee_id', $user->employee->id)
                    ->delete();
                
                // Delete employee record
                $user->employee->delete();
            }

            // Delete the user
            $user->delete();

            // Trigger notification for employee deletion
            $this->notificationService->notifyEmployeeDeleted($employeeName, $employeeCode, $username);
            
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
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


