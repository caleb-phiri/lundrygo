<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LaundryService extends Model
{
    use HasFactory;
    
    protected $table = 'laundry_services';
    
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'price',
        'unit',
        'description',
        'is_active',
        'estimated_time',
        'image'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    // Auto-generate slug when creating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
        
        static::updating(function ($service) {
            if ($service->isDirty('name')) {
                $service->slug = Str::slug($service->name);
            }
        });
    }
    
    // Relationship with category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'service_id');
    }
    
    // Scope for active services
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Scope for services by category
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}