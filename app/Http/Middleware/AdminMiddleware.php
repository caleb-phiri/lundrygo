<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminMiddleware
{
    /**
     * Admin roles that are allowed
     */
    protected $allowedRoles = ['admin', 'super_admin', 'manager'];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->unauthorizedResponse(
                $request,
                'Authentication required. Please log in.',
                401
            );
        }
        
        $user = auth()->user();
        
        // Check if user has admin role
        $hasAccess = $this->checkAdminAccess($user, $role);
        
        if (!$hasAccess) {
            $this->logUnauthorizedAccess($request, $user);
            return $this->unauthorizedResponse(
                $request,
                $this->getUnauthorizedMessage($role),
                403
            );
        }
        
        // Log admin access for audit trail (optional)
        if (config('app.admin_audit_log', false)) {
            $this->logAdminAccess($request, $user);
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has admin access
     */
    protected function checkAdminAccess($user, ?string $requiredRole = null): bool
    {
        // Check if user role is in allowed roles
        $isAdmin = in_array($user->role, $this->allowedRoles);
        
        // If specific role required, check that too
        if ($requiredRole && $isAdmin) {
            return $user->role === $requiredRole;
        }
        
        // Check for super admin specific permissions
        if ($requiredRole === 'super_admin') {
            return $user->role === 'super_admin' || $user->is_super_admin === true;
        }
        
        // Check if admin is active
        if ($isAdmin && isset($user->is_active) && !$user->is_active) {
            return false;
        }
        
        // Check admin permissions cache (for performance)
        if ($isAdmin && $this->hasAdminPermissions($user)) {
            return true;
        }
        
        return $isAdmin;
    }
    
    /**
     * Check if user has admin permissions (with caching)
     */
    protected function hasAdminPermissions($user): bool
    {
        $cacheKey = "admin_permissions_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            // Check if user has specific admin permissions
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission('admin.access');
            }
            
            // Check for admin flag
            if (isset($user->is_admin)) {
                return $user->is_admin === true;
            }
            
            return false;
        });
    }
    
    /**
     * Get unauthorized response
     */
    protected function unauthorizedResponse(Request $request, string $message, int $statusCode = 403)
    {
        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'admin_access_denied',
                'status' => $statusCode,
                'timestamp' => now()->toIso8601String(),
            ], $statusCode);
        }
        
        // For web requests, redirect to login or admin denied page
        if ($statusCode === 401) {
            return redirect()->route('login')->with('error', $message);
        }
        
        abort($statusCode, $message);
    }
    
    /**
     * Get unauthorized message based on required role
     */
    protected function getUnauthorizedMessage(?string $requiredRole = null): string
    {
        if ($requiredRole === 'super_admin') {
            return 'Unauthorized. Super Admin access required.';
        }
        
        if ($requiredRole) {
            return "Unauthorized. {$requiredRole} access required.";
        }
        
        return 'Unauthorized. Admin access required.';
    }
    
    /**
     * Log unauthorized access attempts
     */
    protected function logUnauthorizedAccess(Request $request, $user): void
    {
        Log::warning('Unauthorized admin access attempt', [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'user_role' => $user->role ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toIso8601String(),
        ]);
        
        // Increment failed attempts counter
        if ($user && isset($user->id)) {
            $cacheKey = "admin_failed_attempts_{$user->id}";
            $attempts = Cache::increment($cacheKey);
            
            if ($attempts >= 5) {
                Log::alert('Multiple admin access failures', [
                    'user_id' => $user->id,
                    'attempts' => $attempts,
                ]);
            }
        }
    }
    
    /**
     * Log successful admin access for audit trail
     */
    protected function logAdminAccess(Request $request, $user): void
    {
        Log::info('Admin access granted', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
    }
}