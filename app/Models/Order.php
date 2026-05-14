<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'rider_id',
        'pickup_location_id',
        'delivery_location_id',
        'assigned_laundry_id',
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

    protected $appends = ['status_label', 'payment_status_label', 'delivery_progress_percentage'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function deliveryLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'delivery_location_id');
    }

    public function assignedLaundry(): BelongsTo
    {
        return $this->belongsTo(Laundry::class, 'assigned_laundry_id');
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

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'payment_pending' => 'Awaiting Payment',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'rider_assigned' => 'Rider Assigned',
            'rider_en_route_pickup' => 'Rider En Route for Pickup',
            'at_pickup_location' => 'Arrived at Pickup Location',
            'items_collected' => 'Items Collected',
            'in_transit_to_laundry' => 'In Transit to Laundry',
            'arrived_at_laundry' => 'Arrived at Laundry',
            'at_laundry' => 'At Laundry Facility',
            'washing' => 'Washing in Progress',
            'drying' => 'Drying in Progress',
            'ironing' => 'Ironing in Progress',
            'folding' => 'Folding in Progress',
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

        return $labels[$this->status] ?? ucfirst($this->status);
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

    public function getDeliveryProgressPercentageAttribute(): int
    {
        $progressMap = [
            'pending' => 0,
            'payment_pending' => 5,
            'confirmed' => 10,
            'processing' => 15,
            'rider_assigned' => 20,
            'rider_en_route_pickup' => 25,
            'at_pickup_location' => 30,
            'items_collected' => 35,
            'in_transit_to_laundry' => 40,
            'arrived_at_laundry' => 45,
            'at_laundry' => 50,
            'washing' => 55,
            'drying' => 60,
            'ironing' => 65,
            'folding' => 70,
            'quality_check' => 75,
            'ready_for_delivery' => 80,
            'out_for_delivery' => 85,
            'delivered' => 95,
            'completed' => 100,
        ];

        return $progressMap[$this->status] ?? 0;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered' || $this->status === 'completed';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'payment_pending', 'confirmed', 'processing']);
    }

    public function updateTotal()
    {
        $this->total = $this->subtotal + $this->delivery_fee + $this->service_fee + $this->tax_amount + $this->express_fee - $this->discount - $this->coupon_discount - $this->wallet_discount;
        $this->due_amount = $this->total - $this->paid_amount;
        $this->save();
    }

    public function addStatusHistory(string $status, ?string $notes = null, ?int $changedBy = null)
    {
        OrderStatusHistory::create([
            'order_id' => $this->id,
            'status' => $status,
            'previous_status' => $this->status,
            'notes' => $notes,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);

        $this->status = $status;
        $this->save();
    }
}