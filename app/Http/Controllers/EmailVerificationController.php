<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use App\Models\Employee;
use App\Models\Customer;
use App\Mail\EmailVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification form
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showVerificationForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('register')->with('error', 'Email address is required for verification.');
        }

        return view('auth.email-verification', compact('email'));
    }

    /**
     * Send OTP to email address
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;

        // Check if email already exists in users table
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'This email address is already registered.']);
        }

        try {
            // Create OTP
            $otpRecord = EmailVerification::createOTP($email, 'registration', 15);
            
            // Get user name from session if available
            $registrationData = $request->session()->get('pending_registration');
            $userName = '';
            if ($registrationData && isset($registrationData['first_name'])) {
                $userName = $registrationData['first_name'] . ' ' . $registrationData['last_name'];
            }
            
            // Send email
            Mail::to($email)->send(new EmailVerificationMail(
                $otpRecord->otp_code,
                $userName,
                15
            ));

            // Set session flag to show OTP form
            $request->session()->put('otp_sent', true);

            return back()->with('success', 'Verification code sent to your email address. Please check your inbox.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }

    /**
     * Verify OTP and complete registration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'otp_code' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $otpCode = $request->otp_code;

        // Get registration data from session
        $registrationData = $request->session()->get('pending_registration');
        
        if (!$registrationData) {
            return redirect()->route('register')->with('error', 'Registration session expired. Please register again.');
        }

        // Validate OTP without marking it as used yet
        if (!EmailVerification::validateOTP($email, $otpCode, 'registration')) {
            return back()->withErrors(['otp_code' => 'Invalid or expired verification code.']);
        }

        try {
            DB::beginTransaction();

            // Log registration data for debugging
            Log::info('Starting user registration', [
                'email' => $email,
                'username' => $registrationData['username'] ?? 'not_set',
                'first_name' => $registrationData['first_name'] ?? 'not_set',
                'last_name' => $registrationData['last_name'] ?? 'not_set',
                'contact' => $registrationData['contact'] ?? 'not_set'
            ]);

            // Create the user using session data
            $user = User::create([
                'username' => $registrationData['username'],
                'email' => $email,
                'first_name' => $registrationData['first_name'],
                'last_name' => $registrationData['last_name'],
                'phone' => $registrationData['contact'] ?? null,
                'role' => 'customer', // Default to customer for registration
                'password_hash' => Hash::make($registrationData['password']),
                'email_verified_at' => now(), // Mark email as verified
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Create customer profile
            Log::info('Creating customer profile', ['user_id' => $user->id]);
            $year = now()->format('Y');
            for ($i = 0; $i < 1000; $i++) {
                $suffix = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
                $code = 'C' . $year . $suffix;
                $exists = DB::table('customers')->where('customer_code', $code)->exists();
                if (!$exists) {
                    $customer = Customer::create([
                        'user_id' => $user->id,
                        'customer_code' => $code,
                    ]);
                    Log::info('Customer profile created successfully', [
                        'customer_id' => $customer->id,
                        'customer_code' => $code
                    ]);
                    break;
                }
            }

            // Mark OTP as used only after successful registration
            EmailVerification::markOTPAsUsed($email, $otpCode, 'registration');

            // Clear session data
            $request->session()->forget('pending_registration');

            DB::commit();

            return redirect()->route('login')->with('success', 'Registration successful! Your email has been verified. Please sign in with your credentials.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the specific error for debugging
            Log::error('Registration failed during OTP verification', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['email' => 'Registration failed: ' . $e->getMessage() . '. Please try again.']);
        }
    }

    /**
     * Resend OTP code
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = $request->email;

        // Check if email already exists in users table
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'This email address is already registered.']);
        }

        try {
            // Create new OTP (this will invalidate any existing ones)
            $otpRecord = EmailVerification::createOTP($email, 'registration', 15);
            
            // Get user name from session if available
            $registrationData = $request->session()->get('pending_registration');
            $userName = '';
            if ($registrationData && isset($registrationData['first_name'])) {
                $userName = $registrationData['first_name'] . ' ' . $registrationData['last_name'];
            }
            
            // Send email
            Mail::to($email)->send(new EmailVerificationMail(
                $otpRecord->otp_code,
                $userName,
                15
            ));

            return back()->with('success', 'New verification code sent to your email address.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }
}
