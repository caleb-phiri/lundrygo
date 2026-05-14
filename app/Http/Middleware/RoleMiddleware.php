<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in
        if (!auth()->check()) {
            // For registration and login routes, allow access
            if ($request->routeIs('register*') || $request->routeIs('login*')) {
                return $next($request);
            }
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // If user has no role assigned, set default as customer
        if (empty($user->role)) {
            $user->role = 'customer';
            $user->save();
        }
        
        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access. Required role: ' . implode(', ', $roles) . '. Your role: ' . $user->role);
        }
        
        return $next($request);
    }
}