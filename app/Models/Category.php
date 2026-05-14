<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Auto-generate slug from name
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }
    
    /**
     * Get the services for this category
     * Changed from Service::class to LaundryService::class
     */
    public function services()
    {
        return $this->hasMany(LaundryService::class, 'category_id');
    }
    
    /**
     * Get the active services count
     */
    public function activeServices()
    {
        return $this->hasMany(LaundryService::class, 'category_id')->where('is_active', true);
    }
    
    /**
     * Get route key name for implicit binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}