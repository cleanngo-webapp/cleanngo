<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        // Validate the request data (no current password required for employees)
        $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'new_password.required' => 'New password is required.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 8 characters.',
        ]);

        try {
            // Get the authenticated employee user
            $user = Auth::guard('employee')->user();
            
            // Update the password
            $user->update([
                'password_hash' => Hash::make($request->new_password)
            ]);

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
