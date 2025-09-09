<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminEmployeeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // List ALL users with role=employee (even if employees row not created yet)
        $employees = DB::table('users')
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
            ->selectSub(function ($q) use ($today) {
                $q->from('booking_staff_assignments as bsa')
                  ->join('bookings as b', 'b.id', '=', 'bsa.booking_id')
                  ->whereDate('b.scheduled_start', $today)
                  ->whereColumn('bsa.employee_id', 'employees.id')
                  ->selectRaw('count(*)');
            }, 'jobs_assigned_today')
            ->orderBy('employees.id', 'asc')
            ->paginate(15);

        return view('admin.employees', compact('employees'));
    }
}


