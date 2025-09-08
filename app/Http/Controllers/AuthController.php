<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Validate registration with unique username and email
        $data = $request->validate([
            'username' => ['required','string','alpha_dash','min:3','max:50','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required', Rule::in(['admin','employee','customer'])],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // Create the user using the new username field
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password_hash' => Hash::make($data['password']),
        ]);

        Auth::login($user);
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

        Auth::login($user, $request->boolean('remember'));
        return redirect()->route('dashboard.redirect');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Redirect to role dashboard
    public function redirectByRole()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => redirect()->route('preview.customer'),
        };
    }
}


