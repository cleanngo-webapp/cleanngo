<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class EmployeeSettingsController extends Controller
{
    /**
     * Display the employee settings page
     */
    public function index()
    {
        return view('employee.settings');
    }

    /**
     * Update the employee's password
     */
    public function updatePassword(Request $request)
    {
        // Validate the request data with current password verification
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Current password is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.required' => 'New password is required.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 8 characters.',
        ]);

        try {
            // Get the authenticated employee user
            $user = Auth::guard('employee')->user();
            
            // Update the password using DB facade for more explicit control
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password_hash' => Hash::make($request->new_password)]);

            // Return success response
            return redirect()->route('employee.settings')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('employee.settings')
                ->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }
}
