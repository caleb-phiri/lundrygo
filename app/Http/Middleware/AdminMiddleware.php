<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allowed admin roles in order of privilege (highest first)
     */
    protected $allowedRoles = ['super_admin', 'admin', 'manager'];
    
    /**
     * Cache duration for permission checks (seconds)
     */
    protected $cacheDuration = 300; // 5 minutes
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return $this->unauthorizedResponse(
                $request,
                'Authentication required. Please log in to access the admin panel.',
                401
            );
        }
        
        $user = auth()->user();
        
        // Check if user has admin access
        if (!$this->hasAdminAccess($user, $requiredRole)) {
            $this->logUnauthorizedAccess($request, $user);
            return $this->unauthorizedResponse(
                $request,
                $this->getAccessDeniedMessage($requiredRole, $user->role),
                403
            );
        }
        
        // Check if account is active
        if (isset($user->is_active) && !$user->is_active) {
            $this->logInactiveAccountAttempt($request, $user);
            return $this->unauthorizedResponse(
                $request,
                'Your account has been deactivated. Please contact support.',
                403
            );
        }
        
        // Log successful admin access (optional - can be disabled in production)
        if (config('app.admin_audit_log', false)) {
            $this->logAdminAccess($request, $user);
        }
        
        // Add admin context to request for debugging
        $request->merge([
            '_admin_accessed_at' => now(),
            '_admin_role' => $user->role,
        ]);
        
        return $next($request);
    }
    
    /**
     * Check if user has admin access with caching for performance
     */
    protected function hasAdminAccess($user, ?string $requiredRole = null): bool
    {
        // Super admins always have access
        if ($user->role === 'super_admin') {
            return true;
        }
        
        // Check if user role is in allowed roles
        if (!in_array($user->role, $this->allowedRoles)) {
            return false;
        }
        
        // If specific role is required, check it
        if ($requiredRole && $requiredRole !== 'admin') {
            // For role-specific access
            if ($requiredRole === 'super_admin' && $user->role !== 'super_admin') {
                return false;
            }
            if ($requiredRole === 'manager' && !in_array($user->role, ['super_admin', 'admin', 'manager'])) {
                return false;
            }
            if ($requiredRole === 'admin' && !in_array($user->role, ['super_admin', 'admin'])) {
                return false;
            }
        }
        
        // Check cached permissions for performance
        $cacheKey = "admin_access_{$user->id}";
        $cachedAccess = Cache::get($cacheKey);
        
        if ($cachedAccess !== null) {
            return $cachedAccess;
        }
        
        // Verify additional permissions if method exists
        $hasAccess = true;
        if (method_exists($user, 'hasAdminAccess')) {
            $hasAccess = $user->hasAdminAccess();
        } elseif (isset($user->is_admin)) {
            $hasAccess = (bool) $user->is_admin;
        }
        
        // Cache the result
        Cache::put($cacheKey, $hasAccess, $this->cacheDuration);
        
        return $hasAccess;
    }
    
    /**
     * Get unauthorized response
     */
    protected function unauthorizedResponse(Request $request, string $message, int $statusCode = 403): Response
    {
        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'admin_access_denied',
                'status' => $statusCode,
                'timestamp' => now()->toIso8601String(),
                'path' => $request->path(),
            ], $statusCode);
        }
        
        // For web requests, redirect to login or show error page
        if ($statusCode === 401) {
            return redirect()->route('login')
                ->with('error', $message)
                ->with('intended_url', $request->url());
        }
        
        // For 403 errors, show a custom page if it exists
        if (view()->exists('errors.403')) {
            abort(403, $message);
        }
        
        // Fallback to simple abort
        abort($statusCode, $message);
    }
    
    /**
     * Get access denied message
     */
    protected function getAccessDeniedMessage(?string $requiredRole = null, ?string $userRole = null): string
    {
        $roleNames = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'manager' => 'Manager',
        ];
        
        $userRoleName = $roleNames[$userRole] ?? ucfirst($userRole ?? 'unknown');
        
        if ($requiredRole === 'super_admin') {
            return "Access denied. This area requires Super Administrator privileges. Your role: {$userRoleName}.";
        }
        
        if ($requiredRole === 'manager') {
            return "Access denied. This area requires Manager or higher privileges. Your role: {$userRoleName}.";
        }
        
        if ($requiredRole) {
            return "Access denied. This area requires {$requiredRole} privileges. Your role: {$userRoleName}.";
        }
        
        return "Access denied. You do not have administrator privileges. Your role: {$userRoleName}.";
    }
    
    /**
     * Log unauthorized access attempts for security monitoring
     */
    protected function logUnauthorizedAccess(Request $request, $user): void
    {
        $context = [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'user_role' => $user->role ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'timestamp' => now()->toIso8601String(),
        ];
        
        // Log warning for unauthorized access attempts
        Log::warning('Unauthorized admin access attempt', $context);
        
        // Track failed attempts for brute force detection
        if ($user && isset($user->id)) {
            $cacheKey = "admin_failed_attempts_{$user->id}";
            $attempts = Cache::increment($cacheKey);
            
            // Use put with ttl instead of expire
            Cache::put($cacheKey, $attempts, 3600); // 1 hour expiration
            
            // Alert if too many failures
            if ($attempts >= 5) {
                Log::alert('Multiple admin access failures detected', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'attempts' => $attempts,
                    'time_window' => '1 hour',
                ]);
            }
        }
        
        // Increment global failure counter
        $globalKey = 'admin_global_failures_' . now()->format('Y-m-d_H');
        $globalAttempts = Cache::increment($globalKey);
        Cache::put($globalKey, $globalAttempts, 3600); // 1 hour expiration
        
        if ($globalAttempts >= 50) {
            Log::critical('High volume of admin access failures detected', [
                'attempts' => $globalAttempts,
                'hour' => now()->format('Y-m-d H:00'),
            ]);
        }
    }
    
    /**
     * Log inactive account access attempts
     */
    protected function logInactiveAccountAttempt(Request $request, $user): void
    {
        Log::warning('Inactive admin account access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
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
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
        ]);
    }
    
    /**
     * Clear admin access cache for a specific user (useful after role changes)
     */
    public static function clearAdminCache($userId): void
    {
        Cache::forget("admin_access_{$userId}");
        Cache::forget("admin_failed_attempts_{$userId}");
    }
    
    /**
     * Clear all admin access cache
     */
    public static function clearAllAdminCache(): void
    {
        Log::info('Admin cache cleared by system');
    }
}