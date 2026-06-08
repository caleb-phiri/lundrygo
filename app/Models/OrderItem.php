<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // Uncomment if you want soft deletes for order items
    // use SoftDeletes;
    
    protected $table = 'order_items';
    
    protected $fillable = [
        'order_id',
        'service_id',
        'service_name',
        'quantity',
        'unit_price',
        'total',
        'options',              // JSON field for service options
        'special_instructions', // Any special instructions
        'discount_amount',      // Item-level discount
        'basket_size_id',       // If using basket sizes
        'estimated_completion', // Estimated completion time
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'options' => 'array',
        'estimated_completion' => 'datetime',
    ];
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatically calculate total when creating/updating
        static::saving(function ($item) {
            if ($item->isDirty(['quantity', 'unit_price', 'discount_amount'])) {
                $item->calculateTotal();
            }
            
            // Auto-populate service name if not set
            if (empty($item->service_name) && $item->service_id) {
                $service = LaundryService::find($item->service_id);
                if ($service) {
                    $item->service_name = $service->name;
                }
            }
        });
    }
    
    /**
     * Get the order that owns this item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the service for this item
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }
    
    /**
     * Get the basket size if applicable
     */
    public function basketSize(): BelongsTo
    {
        return $this->belongsTo(BasketSize::class, 'basket_size_id');
    }
    
    /**
     * Calculate the total price
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        $discount = $this->discount_amount ?? 0;
        $this->total = max(0, $subtotal - $discount);
        
        return $this->total;
    }
    
    /**
     * Get the subtotal (before discount)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }
    
    /**
     * Get the discount amount if any
     */
    public function getDiscountAmountAttribute($value): float
    {
        return $value ?? 0;
    }
    
    /**
     * Check if this item has a discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }
    
    /**
     * Apply a discount to this item
     */
    public function applyDiscount(float $amount): void
    {
        $this->discount_amount = min($amount, $this->subtotal);
        $this->calculateTotal();
        $this->save();
    }
    
    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }
    
    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }
    
    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }
    
    /**
     * Update the quantity and recalculate
     */
    public function updateQuantity(int $quantity): void
    {
        $this->quantity = max(1, $quantity);
        $this->calculateTotal();
        $this->save();
    }
    
    /**
     * Set service options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
        
        // Recalculate price if options affect pricing
        if ($this->service_id && method_exists($this->service, 'getPriceForQuantity')) {
            $this->unit_price = $this->service->getPriceForQuantity($this->quantity);
            $this->calculateTotal();
        }
        
        $this->save();
    }
    
    /**
     * Scope for items by service
     */
    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }
    
    /**
     * Scope for items with discounts
     */
    public function scopeDiscounted($query)
    {
        return $query->where('discount_amount', '>', 0);
    }
    
    /**
     * Scope for items with quantity greater than
     */
    public function scopeMinQuantity($query, int $quantity)
    {
        return $query->where('quantity', '>=', $quantity);
    }
}