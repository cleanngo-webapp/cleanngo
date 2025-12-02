<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            // Optional profile picture
            'avatar' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:10240'],
        ]);

        // Remove avatar from employee-specific data so it doesn't try to fill() on the employee model
        $avatarFile = $request->file('avatar');
        unset($data['avatar']);

        $employee->fill($data);
        $employee->save();

        // Handle optional profile picture upload on the user record (same pattern as customer)
        if ($avatarFile) {
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store the new avatar
            $avatarPath = $avatarFile->store('avatars', 'public');

            // Update the avatar path in the database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['avatar' => $avatarPath]);
        }

        return redirect()->route('employee.profile.show')->with('status','Profile updated');
    }
}


