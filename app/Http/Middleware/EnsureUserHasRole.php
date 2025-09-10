<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Authenticate using guard per role so sessions are isolated
        $guard = $role; // guard names mirror role names (admin, employee, customer)
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login');
        }
        // Make the role-specific guard the default for the request lifecycle
        Auth::shouldUse($guard);
        return $next($request);
    }
}


