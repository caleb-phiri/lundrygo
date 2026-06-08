<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Allow riders and higher roles
        if (!in_array($user->role, ['rider', 'supervisor', 'manager', 'admin', 'super_admin'])) {
            abort(403, 'Rider access required.');
        }
        
        return $next($request);
    }
}