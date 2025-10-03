<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
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
            // Personal info - removed position field
            'date_of_birth' => ['nullable','date'],
            'gender' => ['nullable','in:male,female,other'],
            'contact_number' => ['nullable','string','max:100'],
            'email_address' => ['nullable','email','max:255'],
            'home_address' => ['nullable','string','max:255'],
            'emergency_contact_name' => ['nullable','string','max:255'],
            'emergency_contact_number' => ['nullable','string','max:100'],
            // Employment details - only date_hired remains
            'date_hired' => ['nullable','date'],
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


