<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'name',
        'description',
        'price',
        'unit',
        'service_type',
        'billing_period',
        'basket_size',
        'baskets_per_week',
        'baskets_per_month',
        'ironing_included',
        'priority_service',
        'bedding_cleaning',
        'turnaround_hours',
        'is_popular',
        'is_best_value',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'basket_size' => 'integer',
        'baskets_per_week' => 'integer',
        'baskets_per_month' => 'integer',
        'ironing_included' => 'boolean',
        'priority_service' => 'boolean',
        'bedding_cleaning' => 'boolean',
        'turnaround_hours' => 'integer',
        'is_popular' => 'boolean',
        'is_best_value' => 'boolean',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStandard($query)
    {
        return $query->where('service_type', 'standard');
    }

    public function scopeWeekly($query)
    {
        return $query->where('service_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('service_type', 'monthly');
    }

    public function scopeExpress($query)
    {
        return $query->where('service_type', 'express');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getServiceTypeLabelAttribute()
    {
        $labels = [
            'standard' => 'Standard Service',
            'weekly' => 'Weekly Subscription',
            'monthly' => 'Monthly Subscription',
            'express' => 'Express Service',
        ];
        
        return $labels[$this->service_type] ?? 'Unknown';
    }

    public function getBillingPeriodLabelAttribute()
    {
        $labels = [
            'one-time' => 'One Time',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
        
        return $labels[$this->billing_period] ?? 'Unknown';
    }

    // Get basket size label
    public function getBasketSizeLabelAttribute()
    {
        if (!$this->basket_size) return 'N/A';
        
        $labels = [
            5 => 'Small (up to 5kg)',
            10 => 'Medium (up to 10kg)',
            15 => 'Large (up to 15kg)',
        ];
        
        return $labels[$this->basket_size] ?? "{$this->basket_size}kg";
    }

    // Check if service has ironing
    public function getHasIroningAttribute()
    {
        return $this->ironing_included || str_contains($this->name, 'Wash + Iron');
    }

    // Check if service is express
    public function getIsExpressAttribute()
    {
        return $this->service_type === 'express' || $this->priority_service;
    }

    // Get display badge
    public function getBadgeAttribute()
    {
        if ($this->is_popular && $this->is_best_value) {
            return '⭐ POPULAR & BEST VALUE';
        } elseif ($this->is_popular) {
            return '⭐ MOST POPULAR';
        } elseif ($this->is_best_value) {
            return '✨ BEST VALUE';
        }
        return null;
    }

    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'service_id');
    }
}