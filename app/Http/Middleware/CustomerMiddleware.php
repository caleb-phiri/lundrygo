<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Allow customers and higher roles to access customer routes
        if (!in_array($user->role, ['customer', 'rider', 'supervisor', 'manager', 'admin', 'super_admin'])) {
            abort(403, 'Customer access required.');
        }
        
        return $next($request);
    }
}