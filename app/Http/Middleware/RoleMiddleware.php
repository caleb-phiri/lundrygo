<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // If user has no role, set default
        if (empty($user->role)) {
            $user->role = 'customer';
            $user->save();
        }
        
        // Super admin has access to everything
        if ($user->role === 'super_admin') {
            return $next($request);
        }
        
        // Check if user's role is in the allowed roles list
        if (!in_array($user->role, $roles)) {
            $rolesList = implode(', ', $roles);
            abort(403, "Access denied. Required role(s): {$rolesList}. Your role: {$user->role}");
        }
        
        // Check if account is active
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }
        
        return $next($request);
    }
}