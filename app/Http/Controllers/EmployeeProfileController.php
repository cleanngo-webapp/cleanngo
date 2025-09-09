<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $employee = $user?->employee;
        return view('employee.profile', compact('user','employee'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = $user?->employee;
        if (!$employee) {
            abort(403);
        }

        $data = $request->validate([
            // Personal info
            'position' => ['nullable','string','max:255'],
            'date_of_birth' => ['nullable','date'],
            'gender' => ['nullable','in:male,female,other'],
            'contact_number' => ['nullable','string','max:100'],
            'email_address' => ['nullable','email','max:255'],
            'home_address' => ['nullable','string','max:255'],
            'emergency_contact_name' => ['nullable','string','max:255'],
            'emergency_contact_number' => ['nullable','string','max:100'],
            // Employment details
            'department' => ['nullable','string','max:255'],
            'employment_type' => ['nullable','in:full-time,part-time,contract'],
            'date_hired' => ['nullable','date'],
            'employment_status' => ['nullable','in:active,inactive,terminated'],
            'work_schedule' => ['nullable','string','max:255'],
            // Work history records
            'jobs_completed' => ['nullable','integer','min:0'],
            'recent_job' => ['nullable','string','max:255'],
            'attendance_summary' => ['nullable','string','max:255'],
            'performance_rating' => ['nullable','string','max:255'],
        ]);

        $employee->fill($data);
        $employee->save();

        return redirect()->route('employee.profile.show')->with('status','Profile updated');
    }
}


