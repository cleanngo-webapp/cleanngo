<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class CustomerSettingsController extends Controller
{
    /**
     * Display the customer settings page
     */
    public function index()
    {
        return view('customer.settings');
    }

    /**
     * Update the customer's password
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
            // Get the authenticated customer user
            $user = Auth::guard('customer')->user();
            
            // Update the password using DB facade for more explicit control
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password_hash' => Hash::make($request->new_password)]);

            // Return success response
            return redirect()->route('customer.settings')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('customer.settings')
                ->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }

    /**
     * Update the customer's profile information
     */
    public function updateProfile(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
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
            'phone.required' => 'Phone number is required.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
        ]);

        try {
            // Get the authenticated customer user
            $user = Auth::guard('customer')->user();
            
            // Update the profile information using DB facade for more explicit control
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                ]);

            // Return success response
            return redirect()->route('customer.settings')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('customer.settings')
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    /**
     * Update the customer's avatar
     */
    public function updateAvatar(Request $request)
    {
        // Validate the request data
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // Max 10MB
        ], [
            'avatar.required' => 'Please select an image to upload.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'avatar.max' => 'The image may not be greater than 10MB.',
        ]);

        try {
            // Get the authenticated customer user
            $user = Auth::guard('customer')->user();
            
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store the new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            
            // Update the avatar path in the database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['avatar' => $avatarPath]);

            // Return success response
            return redirect()->route('customer.settings')
                ->with('success', 'Avatar updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('customer.settings')
                ->with('error', 'An error occurred while updating your avatar. Please try again.');
        }
    }

    /**
     * Remove the customer's avatar
     */
    public function removeAvatar()
    {
        try {
            // Get the authenticated customer user
            $user = Auth::guard('customer')->user();
            
            // Delete the avatar file if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Remove the avatar path from the database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['avatar' => null]);

            // Return success response
            return redirect()->route('customer.settings')
                ->with('success', 'Avatar removed successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('customer.settings')
                ->with('error', 'An error occurred while removing your avatar. Please try again.');
        }
    }
}
