<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryService extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'laundry_services';
    
    protected $fillable = [
        'category_id',
        'basket_size_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'price_type',
        'min_quantity',
        'max_quantity',
        'unit',
        'estimated_hours',
        'min_estimated_hours',
        'max_estimated_hours',
        'is_active',
        'is_featured',
        'requires_pressing',
        'requires_dry_cleaning',
        'image',
        'gallery',
        'tags',
        'metadata',
        'sort_order',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'max_quantity' => 'decimal:2',
        'estimated_hours' => 'integer',
        'min_estimated_hours' => 'integer',
        'max_estimated_hours' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_pressing' => 'boolean',
        'requires_dry_cleaning' => 'boolean',
        'gallery' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];
    
    protected $appends = ['final_price', 'estimated_hours_range'];
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
        
        static::updating(function ($service) {
            if ($service->isDirty('name') && !$service->isDirty('slug')) {
                $service->slug = Str::slug($service->name);
            }
        });
    }
    
    /**
     * Get the category that owns the service
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    /**
     * Get the basket size for this service
     */
    public function basketSize(): BelongsTo
    {
        return $this->belongsTo(BasketSize::class, 'basket_size_id');
    }
    
    /**
     * Get the options for this service
     */
    public function options(): HasMany
    {
        return $this->hasMany(LaundryServiceOption::class, 'service_id');
    }
    
    /**
     * Get the pricing tiers for this service
     */
    public function tiers(): HasMany
    {
        return $this->hasMany(LaundryServiceTier::class, 'service_id');
    }
    
    /**
     * Get the order items for this service
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'laundry_service_id');
    }
    
    /**
     * Get the final price (discounted if available)
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }
    
    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->final_price, 2);
    }
    
    /**
     * Get estimated hours range as string
     */
    public function getEstimatedHoursRangeAttribute(): ?string
    {
        if ($this->min_estimated_hours && $this->max_estimated_hours) {
            return "{$this->min_estimated_hours}-{$this->max_estimated_hours} hours";
        }
        return $this->estimated_hours ? "{$this->estimated_hours} hours" : null;
    }
    
    /**
     * Calculate price based on basket size and quantity
     */
    public function getPriceForBasketSize($basketSizeId, float $quantity = 1): float
    {
        $basketSize = BasketSize::find($basketSizeId);
        
        if ($basketSize) {
            $basePrice = $this->final_price;
            $multiplier = $basketSize->price_multiplier ?? 1;
            
            return round(($basePrice * $multiplier) * $quantity, 2);
        }
        
        return $this->getPriceForQuantity($quantity);
    }
    
    /**
     * Calculate price based on quantity with tiered pricing
     */
    public function getPriceForQuantity(float $quantity): float
    {
        // Check volume pricing tiers first
        $tier = $this->tiers()
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($tier) {
            return round($tier->price * $quantity, 2);
        }

        // Use regular pricing
        return round($this->final_price * $quantity, 2);
    }
    
    /**
     * Check if service has discount
     */
    public function hasDiscount(): bool
    {
        return !is_null($this->discount_price) && $this->discount_price < $this->price;
    }
    
    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->hasDiscount()) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return null;
    }
    
    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for featured services
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope for services by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    /**
     * Scope for services by basket size
     */
    public function scopeByBasketSize($query, $basketSizeId)
    {
        return $query->where('basket_size_id', $basketSizeId);
    }
    
    /**
     * Scope for price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }
    
    /**
     * Scope for express services (faster turnaround)
     */
    public function scopeExpress($query, $hours = 24)
    {
        return $query->where(function($q) use ($hours) {
            $q->where('estimated_hours', '<=', $hours)
              ->orWhere('max_estimated_hours', '<=', $hours);
        });
    }
    
    /**
     * Scope for discounted services
     */
    public function scopeDiscounted($query)
    {
        return $query->whereNotNull('discount_price')
                     ->whereColumn('discount_price', '<', 'price');
    }
    
    /**
     * Scope for services requiring specific treatments
     */
    public function scopeRequiresPressing($query)
    {
        return $query->where('requires_pressing', true);
    }
    
    public function scopeRequiresDryCleaning($query)
    {
        return $query->where('requires_dry_cleaning', true);
    }
}