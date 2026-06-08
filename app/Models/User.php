<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\HasAdminAccess;

class User extends Authenticatable
{
    use HasFactory, Notifiable,  HasAdminAccess;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'is_active',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    
    /**
     * Get the reviews by the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * Get the orders assigned to this rider.
     */
    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'rider_id');
    }
    
    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for customers
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }
    
    /**
     * Scope for riders
     */
    public function scopeRiders($query)
    {
        return $query->where('role', 'rider');
    }
    
    /**
     * Scope for admins
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'super_admin']);
    }
    
    /**
     * Get role badge color
     */
    public function getRoleBadgeColorAttribute(): string
    {
        $colors = [
            'super_admin' => 'danger',
            'admin' => 'danger',
            'manager' => 'warning',
            'supervisor' => 'info',
            'rider' => 'success',
            'customer' => 'primary',
        ];
        
        return $colors[$this->role] ?? 'secondary';
    }
    
    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'super_admin' => 'Super Admin',
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'supervisor' => 'Supervisor',
            'rider' => 'Rider',
            'customer' => 'Customer',
        ];
        
        return $labels[$this->role] ?? ucfirst($this->role);
    }
    
    /**
     * Get full name with role
     */
    public function getNameWithRoleAttribute(): string
    {
        return "{$this->name} ({$this->role_label})";
    }
    
    /**
     * Check if user can access admin panel
     */
    public function getCanAccessAdminAttribute(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'manager', 'supervisor']);
    }
}