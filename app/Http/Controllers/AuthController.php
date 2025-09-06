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
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required', Rule::in(['admin','staff','cleaner','customer'])],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password_hash' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        return redirect()->route('dashboard.redirect');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        // Manually attempt using custom password field
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
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
            'admin' => redirect()->route('preview.admin'),
            'staff' => redirect()->route('preview.staff'),
            'cleaner' => redirect()->route('preview.cleaner'),
            default => redirect()->route('preview.customer'),
        };
    }
}


