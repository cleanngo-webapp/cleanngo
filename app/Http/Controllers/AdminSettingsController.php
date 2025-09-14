<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminSettingsController extends Controller
{
    /**
     * Display the admin settings page
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Update the admin's password
     */
    public function updatePassword(Request $request)
    {
        // Validate the request data
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
            // Get the authenticated admin user
            $user = Auth::guard('admin')->user();
            
            // Update the password
            $user->update([
                'password_hash' => Hash::make($request->new_password)
            ]);

            // Return success response
            return redirect()->route('admin.settings')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('admin.settings')
                ->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }

    /**
     * Update the admin's profile information
     */
    public function updateProfile(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name must not exceed 255 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name must not exceed 255 characters.',
        ]);

        try {
            // Get the authenticated admin user
            $user = Auth::guard('admin')->user();
            
            // Update the profile information
            $user->update([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            // Return success response
            return redirect()->route('admin.settings')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('admin.settings')
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }
}
