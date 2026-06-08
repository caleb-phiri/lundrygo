<?php

namespace App\Traits;

trait HasAdminAccess
{
    /**
     * Check if user has admin access
     */
    public function hasAdminAccess(): bool
    {
        $adminRoles = ['super_admin', 'admin', 'manager'];
        
        return in_array($this->role, $adminRoles) && ($this->is_active ?? true);
    }
    
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }
    
    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return in_array($this->role, ['manager', 'admin', 'super_admin']);
    }
    
    /**
     * Check if user is rider
     */
    public function isRider(): bool
    {
        return $this->role === 'rider';
    }
    
    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
    
    /**
     * Get user's permission level (higher number = higher access)
     */
    public function getAdminLevel(): int
    {
        $levels = [
            'super_admin' => 100,
            'admin' => 80,
            'manager' => 60,
            'supervisor' => 40,
            'rider' => 20,
            'customer' => 0,
        ];
        
        return $levels[$this->role] ?? 0;
    }
    
    /**
     * Check if user has permission to perform action
     */
    public function canPerformAdminAction(string $action): bool
    {
        $permissions = [
            'view_orders' => ['customer', 'rider', 'supervisor', 'manager', 'admin', 'super_admin'],
            'manage_orders' => ['supervisor', 'manager', 'admin', 'super_admin'],
            'manage_users' => ['admin', 'super_admin'],
            'manage_riders' => ['manager', 'admin', 'super_admin'],
            'manage_services' => ['admin', 'super_admin'],
            'view_reports' => ['manager', 'admin', 'super_admin'],
            'system_settings' => ['super_admin'],
        ];
        
        $allowedRoles = $permissions[$action] ?? ['super_admin'];
        
        return in_array($this->role, $allowedRoles);
    }
}