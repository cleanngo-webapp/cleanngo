<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Models\PaymentSettings;

class AdminSettingsController extends Controller
{
    /**
     * Display the admin settings page
     */
    public function index()
    {
        // Get current payment settings
        $paymentSettings = PaymentSettings::getActive();
        
        return view('admin.settings', compact('paymentSettings'));
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
            
            // Update the password using DB facade for more explicit control
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password_hash' => Hash::make($request->new_password)]);

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
            
            // Update the profile information using DB facade for more explicit control
            DB::table('users')
                ->where('id', $user->id)
                ->update([
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

    /**
     * Update payment settings (GCash QR code and details)
     */
    public function updatePaymentSettings(Request $request)
    {
        // Validate the request data
        $request->validate([
            'gcash_name' => ['required', 'string', 'max:255'],
            'gcash_number' => ['required', 'string', 'max:20'],
            'qr_code' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'], // Max 10MB
        ], [
            'gcash_name.required' => 'GCash name is required.',
            'gcash_name.string' => 'GCash name must be a string.',
            'gcash_name.max' => 'GCash name must not exceed 255 characters.',
            'gcash_number.required' => 'GCash number is required.',
            'gcash_number.string' => 'GCash number must be a string.',
            'gcash_number.max' => 'GCash number must not exceed 20 characters.',
            'qr_code.image' => 'QR code must be an image file.',
            'qr_code.mimes' => 'QR code must be a JPEG, PNG, JPG, GIF, or WebP file.',
            'qr_code.max' => 'QR code file size must not exceed 10MB.',
        ]);

        try {
            // Get current active payment settings
            $currentSettings = PaymentSettings::getActive();
            
            $data = [
                'gcash_name' => $request->gcash_name,
                'gcash_number' => $request->gcash_number,
                'is_active' => true,
            ];

            // Handle QR code upload
            if ($request->hasFile('qr_code')) {
                // Delete old QR code if exists
                if ($currentSettings && $currentSettings->qr_code_path) {
                    Storage::disk('public')->delete($currentSettings->qr_code_path);
                }
                
                // Store new QR code
                $qrCodePath = $request->file('qr_code')->store('payment-qr-codes', 'public');
                $data['qr_code_path'] = $qrCodePath;
            } elseif ($currentSettings) {
                // Keep existing QR code if no new one uploaded
                $data['qr_code_path'] = $currentSettings->qr_code_path;
            }

            if ($currentSettings) {
                // Update existing payment settings instead of creating new ones
                $currentSettings->update($data);
            } else {
                // Only create new record if no existing settings found
                PaymentSettings::create($data);
            }

            // Clean up old inactive payment settings records (keep only the last 3 inactive ones)
            $this->cleanupOldPaymentSettings();

            // Return success response
            return redirect()->route('admin.settings')
                ->with('success', 'Payment settings updated successfully!');

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return redirect()->route('admin.settings')
                ->with('error', 'An error occurred while updating payment settings. Please try again.');
        }
    }

    /**
     * Clean up old inactive payment settings records
     * Keeps only the most recent 3 inactive records for audit purposes
     */
    private function cleanupOldPaymentSettings()
    {
        try {
            // Get all inactive payment settings ordered by created_at desc
            $inactiveSettings = PaymentSettings::where('is_active', false)
                ->orderBy('created_at', 'desc')
                ->get();

            // If we have more than 3 inactive records, delete the older ones
            if ($inactiveSettings->count() > 3) {
                $settingsToDelete = $inactiveSettings->skip(3);
                
                foreach ($settingsToDelete as $setting) {
                    // Delete associated QR code file if it exists
                    if ($setting->qr_code_path) {
                        Storage::disk('public')->delete($setting->qr_code_path);
                    }
                    // Delete the record
                    $setting->delete();
                }
            }
        } catch (\Exception $e) {
            // Log error but don't throw it to avoid breaking the main update process
            Log::error('Error cleaning up old payment settings: ' . $e->getMessage());
        }
    }
}
