<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BasketSize extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'base_price',
        'price_multiplier', // Add this
        'max_weight_kg',
        'estimated_pieces',
        'description',
        'is_active',
        'sort_order'
    ];
    
    protected $casts = [
        'base_price' => 'decimal:2',
        'price_multiplier' => 'decimal:2',
        'max_weight_kg' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($basketSize) {
            if (empty($basketSize->slug)) {
                $basketSize->slug = Str::slug($basketSize->name);
            }
            if (empty($basketSize->price_multiplier)) {
                $basketSize->price_multiplier = 1;
            }
        });
    }
    
    public function services()
    {
        return $this->hasMany(LaundryService::class);
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}