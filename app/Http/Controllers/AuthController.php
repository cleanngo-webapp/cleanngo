<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate registration with unique username and email, plus profile fields
        $data = $request->validate([
            'username' => ['required','string','alpha_dash','min:3','max:50','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'contact' => ['nullable','string','max:50'],
            'role' => ['required', Rule::in(['admin','employee','customer'])],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // Create the user using the new username field
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['contact'] ?? null,
            'role' => $data['role'],
            'password_hash' => Hash::make($data['password']),
        ]);

        // If the registered user is an employee, create the employee profile row immediately
        if ($user->role === 'employee') {
            Employee::create([
                'user_id' => $user->id,
                'is_cleaner' => true,
                'is_active' => true,
                'contact_number' => $user->phone,
                'email_address' => $user->email,
                'employment_status' => 'active',
                'date_hired' => now()->toDateString(),
                'employee_code' => 'E' . now()->format('Y') . str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT),
            ]);
        }
        // If the registered user is a customer, create the customer profile row with a unique code
        elseif ($user->role === 'customer') {
            // Generate a unique CYYYYXXX code
            $year = now()->format('Y');
            for ($i = 0; $i < 1000; $i++) {
                $suffix = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
                $code = 'C' . $year . $suffix;
                $exists = DB::table('customers')->where('customer_code', $code)->exists();
                if (!$exists) {
                    Customer::create([
                        'user_id' => $user->id,
                        'customer_code' => $code,
                    ]);
                    break;
                }
            }
        }

        // Log in to the appropriate role guard to avoid logging out other roles
        $guard = $user->role;
        Auth::guard($guard)->login($user);
        return redirect()->route('dashboard.redirect');
    }

    public function login(Request $request)
    {
        // Accept either email or username in a single field
        $data = $request->validate([
            'login' => ['required','string','max:255'],
            'password' => ['required','string'],
        ]);

        $login = $data['login'];
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        // Find by email or username depending on the input
        $userQuery = User::query();
        $user = $isEmail
            ? $userQuery->where('email', $login)->first()
            : $userQuery->where('username', $login)->first();

        if (!$user) {
            // Provide specific feedback based on the identifier type
            $message = $isEmail ? 'Email not found' : 'Username not found';
            return back()->withErrors(['login' => $message])->withInput();
        }

        if (!Hash::check($data['password'], $user->password_hash)) {
            return back()->withErrors(['password' => 'Incorrect password'])->withInput([
                'login' => $login,
            ]);
        }

        // Use role-specific guard
        $guard = $user->role;
        Auth::guard($guard)->login($user, $request->boolean('remember'));
        return redirect()->route('dashboard.redirect');
    }

    public function logout(Request $request)
    {
        // Logout only from the current guard if present
        $guard = Auth::getDefaultDriver();
        try { Auth::guard($guard)->logout(); } catch (\Throwable $e) { Auth::logout(); }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    // Redirect to role dashboard
    public function redirectByRole()
    {
        // Detect logged-in user from any role-specific guard
        foreach (['admin', 'employee', 'customer'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                $role = $guard;
                return match ($role) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'employee' => redirect()->route('employee.dashboard'),
                    'customer' => redirect()->route('customer.dashboard'),
                    default => redirect()->route('login'),
                };
            }
        }
        return redirect()->route('landing');
    }
}


