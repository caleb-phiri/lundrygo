<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'orders';
    
    protected $fillable = [
        'order_number',
        'user_id',
        'rider_id',
        'pickup_address_id',
        'delivery_address_id',
        'status',
        'order_type',
        'priority',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'tax_rate',
        'tax_amount',
        'discount',
        'coupon_discount',
        'coupon_code',
        'wallet_discount',
        'total',
        'paid_amount',
        'due_amount',
        'estimated_weight',
        'actual_weight',
        'total_items',
        'pickup_scheduled_at',
        'delivery_scheduled_at',
        'payment_confirmed_at',
        'order_confirmed_at',
        'rider_assigned_at',
        'picked_up_at',
        'arrived_at_laundry_at',
        'processing_started_at',
        'processing_completed_at',
        'ready_for_delivery_at',
        'out_for_delivery_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'estimated_pickup_minutes',
        'estimated_delivery_minutes',
        'estimated_processing_hours',
        'estimated_completion_at',
        'special_instructions',
        'cancellation_reason',
        'payment_status',
        'payment_method',
        'transaction_id',
        'payment_intent_id',
        'payment_metadata',
        'is_express',
        'express_fee',
        'rider_rating',
        'laundry_rating',
        'overall_rating',
        'rider_review',
        'laundry_review',
        'overall_review',
        'tracking_updates',
        'is_scheduled',
        'requires_special_handling',
        'is_insured',
        'insurance_amount',
        'is_priority',
        'is_rush_order',
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'wallet_discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'express_fee' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'estimated_weight' => 'decimal:2',
        'actual_weight' => 'decimal:2',
        'rider_rating' => 'decimal:1',
        'laundry_rating' => 'decimal:1',
        'overall_rating' => 'decimal:1',
        'pickup_scheduled_at' => 'datetime',
        'delivery_scheduled_at' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'order_confirmed_at' => 'datetime',
        'rider_assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'arrived_at_laundry_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'ready_for_delivery_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_completion_at' => 'datetime',
        'tracking_updates' => 'array',
        'payment_metadata' => 'array',
        'is_express' => 'boolean',
        'is_scheduled' => 'boolean',
        'requires_special_handling' => 'boolean',
        'is_insured' => 'boolean',
        'is_priority' => 'boolean',
        'is_rush_order' => 'boolean',
    ];
    
    protected $appends = [
        'status_label',
        'status_badge',
        'payment_status_label',
        'progress_percentage',
        'tracking_timeline',
        'formatted_total',
        'formatted_subtotal',
    ];
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
            
            // Set default values
            $order->status = $order->status ?? 'pending';
            $order->payment_status = $order->payment_status ?? 'pending';
            $order->order_type = $order->order_type ?? 'regular';
            $order->priority = $order->priority ?? 'normal';
        });
        
        static::saving(function ($order) {
            // Ensure due amount is calculated
            $order->due_amount = $order->total - ($order->paid_amount ?? 0);
        });
    }
    
    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        $sequential = str_pad((static::withTrashed()->count() + 1), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$timestamp}-{$random}-{$sequential}";
    }
    
    // ============ RELATIONSHIPS ============
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }
    
    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'pickup_address_id');
    }
    
    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'delivery_address_id');
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    
    public function tracking(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }
    
    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(OrderDeliveryProof::class);
    }
    
    public function disputes(): HasMany
    {
        return $this->hasMany(OrderDispute::class);
    }
    
    // Alias for rider relationship used in scopes
    public function riderOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'rider_id');
    }
    
    // ============ ACCESSORS ============
    
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'payment_pending' => 'Awaiting Payment',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'rider_assigned' => 'Rider Assigned',
            'rider_en_route_pickup' => 'Rider En Route',
            'at_pickup_location' => 'At Pickup Location',
            'items_collected' => 'Items Collected',
            'in_transit_to_laundry' => 'In Transit to Laundry',
            'arrived_at_laundry' => 'At Laundry',
            'washing' => 'Washing',
            'drying' => 'Drying',
            'ironing' => 'Ironing',
            'folding' => 'Folding',
            'quality_check' => 'Quality Check',
            'quality_check_failed' => 'Quality Check Failed',
            'ready_for_delivery' => 'Ready for Delivery',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'disputed' => 'Disputed',
        ];
        
        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
    
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'payment_pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'rider_assigned' => 'info',
            'rider_en_route_pickup' => 'info',
            'at_pickup_location' => 'info',
            'items_collected' => 'primary',
            'in_transit_to_laundry' => 'primary',
            'arrived_at_laundry' => 'primary',
            'washing' => 'primary',
            'drying' => 'primary',
            'ironing' => 'primary',
            'folding' => 'primary',
            'quality_check' => 'info',
            'quality_check_failed' => 'danger',
            'ready_for_delivery' => 'secondary',
            'out_for_delivery' => 'info',
            'delivered' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'dark',
            'disputed' => 'danger',
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }
    
    public function getPaymentStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'chargeback' => 'Chargeback',
        ];
        
        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }
    
    public function getProgressPercentageAttribute(): int
    {
        $progressMap = [
            'pending' => 5,
            'payment_pending' => 10,
            'confirmed' => 15,
            'rider_assigned' => 25,
            'rider_en_route_pickup' => 30,
            'at_pickup_location' => 35,
            'items_collected' => 40,
            'in_transit_to_laundry' => 45,
            'arrived_at_laundry' => 50,
            'washing' => 55,
            'drying' => 60,
            'ironing' => 65,
            'folding' => 70,
            'quality_check' => 75,
            'ready_for_delivery' => 80,
            'out_for_delivery' => 85,
            'delivered' => 95,
            'completed' => 100,
            'cancelled' => 100,
            'refunded' => 100,
        ];
        
        return $progressMap[$this->status] ?? 0;
    }
    
    public function getTrackingTimelineAttribute(): array
    {
        $timeline = [];
        
        // Order Placed
        $timeline[] = [
            'status' => 'Order Placed',
            'timestamp' => $this->created_at,
            'completed' => true,
            'icon' => 'fa-shopping-cart'
        ];
        
        // Order Confirmed
        $timeline[] = [
            'status' => 'Order Confirmed',
            'timestamp' => $this->order_confirmed_at,
            'completed' => !is_null($this->order_confirmed_at) || !in_array($this->status, ['pending', 'payment_pending', 'cancelled']),
            'icon' => 'fa-check-circle'
        ];
        
        // Rider Assigned
        if ($this->rider_id || $this->rider_assigned_at) {
            $timeline[] = [
                'status' => 'Rider Assigned',
                'timestamp' => $this->rider_assigned_at,
                'completed' => !is_null($this->rider_assigned_at),
                'icon' => 'fa-user-plus'
            ];
        }
        
        // Picked Up
        if ($this->status !== 'cancelled') {
            $timeline[] = [
                'status' => 'Picked Up',
                'timestamp' => $this->picked_up_at,
                'completed' => !is_null($this->picked_up_at),
                'icon' => 'fa-box'
            ];
        }
        
        // Processing
        if (!in_array($this->status, ['pending', 'payment_pending', 'confirmed', 'cancelled'])) {
            $timeline[] = [
                'status' => 'Processing',
                'timestamp' => $this->processing_started_at,
                'completed' => !is_null($this->processing_started_at) || in_array($this->status, ['ready_for_delivery', 'out_for_delivery', 'delivered', 'completed']),
                'icon' => 'fa-cog'
            ];
        }
        
        // Out for Delivery
        if (!in_array($this->status, ['pending', 'payment_pending', 'confirmed', 'processing', 'cancelled'])) {
            $timeline[] = [
                'status' => 'Out for Delivery',
                'timestamp' => $this->out_for_delivery_at,
                'completed' => !is_null($this->out_for_delivery_at) || in_array($this->status, ['delivered', 'completed']),
                'icon' => 'fa-truck'
            ];
        }
        
        // Delivered
        if (!in_array($this->status, ['cancelled'])) {
            $timeline[] = [
                'status' => 'Delivered',
                'timestamp' => $this->delivered_at,
                'completed' => !is_null($this->delivered_at) || $this->status === 'completed',
                'icon' => 'fa-check-double'
            ];
        }
        
        return $timeline;
    }
    
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }
    
    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }
    
    // ============ HELPER METHODS ============
    
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
    
    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'completed']);
    }
    
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'payment_pending', 'confirmed', 'processing']);
    }
    
    public function needsAttention(): bool
    {
        return in_array($this->status, ['quality_check_failed', 'disputed']);
    }
    
    public function hasRider(): bool
    {
        return !is_null($this->rider_id);
    }
    
    public function isOverdue(): bool
    {
        if ($this->estimated_completion_at && !$this->isDelivered()) {
            return now()->gt($this->estimated_completion_at);
        }
        return false;
    }
    
    /**
     * Update order status with proper timestamp handling
     */
    public function updateStatus(string $newStatus, ?string $notes = null): self
    {
        $oldStatus = $this->status;
        
        // Set status
        $this->status = $newStatus;
        
        // Set timestamps based on status
        switch ($newStatus) {
            case 'confirmed':
                $this->order_confirmed_at = $this->order_confirmed_at ?? now();
                break;
            case 'processing':
                $this->processing_started_at = $this->processing_started_at ?? now();
                break;
            case 'picked_up':
                $this->picked_up_at = $this->picked_up_at ?? now();
                break;
            case 'arrived_at_laundry':
                $this->arrived_at_laundry_at = now();
                break;
            case 'ready_for_delivery':
                $this->ready_for_delivery_at = now();
                $this->processing_completed_at = now();
                break;
            case 'out_for_delivery':
                $this->out_for_delivery_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                $this->completed_at = now();
                break;
            case 'completed':
                $this->completed_at = $this->completed_at ?? now();
                break;
            case 'cancelled':
                $this->cancelled_at = now();
                $this->cancellation_reason = $notes ?? $this->cancellation_reason;
                break;
        }
        
        $this->save();
        
        // Log status history
        if (class_exists(OrderStatusHistory::class)) {
            OrderStatusHistory::create([
                'order_id' => $this->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $notes,
                'changed_by' => auth()->check() ? auth()->id() : null,
                'changed_at' => now(),
            ]);
        }
        
        return $this;
    }
    
    /**
     * Update order totals
     */
    public function updateTotal(): void
    {
        $this->total = 
            ($this->subtotal ?? 0) + 
            ($this->delivery_fee ?? 0) + 
            ($this->service_fee ?? 0) + 
            ($this->tax_amount ?? 0) + 
            ($this->express_fee ?? 0) + 
            ($this->insurance_amount ?? 0) - 
            ($this->discount ?? 0) - 
            ($this->coupon_discount ?? 0) - 
            ($this->wallet_discount ?? 0);
        
        $this->total = max(0, $this->total);
        $this->due_amount = $this->total - ($this->paid_amount ?? 0);
        
        $this->saveQuietly();
    }
    
    /**
     * Sync order totals from items
     */
    public function syncOrderTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
        });
        
        $this->subtotal = $subtotal;
        $this->updateTotal();
    }
    
    /**
     * Apply coupon discount
     */
    public function applyCoupon(string $code, float $amount): void
    {
        $this->coupon_code = $code;
        $this->coupon_discount = $amount;
        $this->updateTotal();
    }
    
    /**
     * Mark as paid
     */
    public function markAsPaid(string $paymentMethod = 'card', ?string $transactionId = null): void
    {
        $this->payment_status = 'paid';
        $this->payment_method = $paymentMethod;
        $this->paid_amount = $this->total;
        $this->due_amount = 0;
        $this->payment_confirmed_at = now();
        
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        
        $this->save();
    }
    
    /**
     * Get delivery time in human-readable format
     */
    public function getDeliveryTimeAttribute(): ?string
    {
        if ($this->picked_up_at && $this->delivered_at) {
            return $this->picked_up_at->diffForHumans($this->delivered_at, true);
        }
        return null;
    }
    
    // ============ SCOPES ============
    
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'payment_pending']);
    }
    
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded']);
    }
    
    public function scopeProcessing($query)
    {
        return $query->whereIn('status', ['confirmed', 'processing', 'washing', 'drying', 'ironing', 'folding', 'quality_check']);
    }
    
    public function scopeDelivered($query)
    {
        return $query->whereIn('status', ['delivered', 'completed']);
    }
    
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
    
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', ['quality_check_failed', 'disputed']);
    }
    
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopeByRider($query, $riderId)
    {
        return $query->where('rider_id', $riderId);
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }
    
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
    
    public function scopeExpress($query)
    {
        return $query->where('is_express', true);
    }
    
    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }
    
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('estimated_completion_at')
                     ->where('estimated_completion_at', '<', now())
                     ->whereNotIn('status', ['delivered', 'completed', 'cancelled']);
    }
    
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }
    
}