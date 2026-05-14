<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'service_id',
        'item_name',
        'item_slug',
        'sku',
        'barcode',
        'quantity',
        'weight_per_unit',
        'total_weight',
        'size',
        'color',
        'fabric_type',
        'unit_price',
        'discount_per_unit',
        'total_discount',
        'subtotal',
        'total_price',
        'base_price',
        'express_premium',
        'special_care_premium',
        'selected_options',
        'addons',
        'addons_total',
        'status',
        'processing_started_at',
        'processing_completed_at',
        'quality_check_at',
        'quality_status',
        'quality_notes',
        'quality_checklist',
        'has_damage',
        'damage_type',
        'damage_description',
        'damage_photos',
        'special_care_instructions',
        'requires_dry_cleaning',
        'requires_hand_wash',
        'no_bleach',
        'no_tumble_dry',
        'low_heat_iron',
        'photo_before',
        'photo_after',
        'gallery_before',
        'gallery_after',
        'customer_notes',
        'rider_notes',
        'laundry_notes',
        'is_fragile',
        'is_valuable',
        'declared_value',
        'requires_express',
        'is_insured',
        'is_replacement',
        'replaces_item_id',
        'replacement_reason',
        'is_refunded',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'weight_per_unit' => 'decimal:3',
        'total_weight' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_per_unit' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'express_premium' => 'decimal:2',
        'special_care_premium' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'selected_options' => 'array',
        'addons' => 'array',
        'quality_checklist' => 'array',
        'damage_photos' => 'array',
        'gallery_before' => 'array',
        'gallery_after' => 'array',
        'metadata' => 'array',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'quality_check_at' => 'datetime',
        'refunded_at' => 'datetime',
        'has_damage' => 'boolean',
        'requires_dry_cleaning' => 'boolean',
        'requires_hand_wash' => 'boolean',
        'no_bleach' => 'boolean',
        'no_tumble_dry' => 'boolean',
        'low_heat_iron' => 'boolean',
        'is_fragile' => 'boolean',
        'is_valuable' => 'boolean',
        'requires_express' => 'boolean',
        'is_insured' => 'boolean',
        'is_replacement' => 'boolean',
        'is_refunded' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['status_label', 'quality_status_label', 'total_weight_formatted'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'service_id');
    }

    public function replacesItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'replaces_item_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderItemStatusHistory::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(OrderItemIssue::class);
    }

    public function activeIssue(): HasOne
    {
        return $this->hasOne(OrderItemIssue::class)->where('status', '!=', 'closed');
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'washed' => 'Washed',
            'dried' => 'Dried',
            'ironed' => 'Ironed',
            'folded' => 'Folded',
            'quality_check' => 'Quality Check',
            'completed' => 'Completed',
            'issue' => 'Issue Detected',
            'rejected' => 'Rejected',
            'refunded' => 'Refunded',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getQualityStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'passed' => 'Passed',
            'failed' => 'Failed',
            'needs_rework' => 'Needs Rework',
        ];

        return $labels[$this->quality_status] ?? ucfirst($this->quality_status);
    }

    public function getTotalWeightFormattedAttribute(): string
    {
        if ($this->total_weight) {
            return number_format($this->total_weight, 2) . ' kg';
        }
        return 'N/A';
    }

    public function calculateTotals(): void
    {
        // Calculate subtotal
        $this->subtotal = $this->unit_price * $this->quantity;
        
        // Calculate total discount
        $this->total_discount = $this->discount_per_unit * $this->quantity;
        
        // Calculate total price
        $this->total_price = $this->subtotal + $this->express_premium + $this->special_care_premium + $this->addons_total - $this->total_discount;
        
        // Calculate total weight if weight per unit is set
        if ($this->weight_per_unit) {
            $this->total_weight = $this->weight_per_unit * $this->quantity;
        }
        
        $this->saveQuietly();
    }

    public function updateStatus(string $status, ?string $notes = null, ?int $changedBy = null): void
    {
        // Create status history entry
        OrderItemStatusHistory::create([
            'order_item_id' => $this->id,
            'status' => $status,
            'previous_status' => $this->status,
            'notes' => $notes,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);

        // Update status with timestamps
        $this->status = $status;
        
        if ($status === 'processing' && !$this->processing_started_at) {
            $this->processing_started_at = now();
        }
        
        if ($status === 'completed' && !$this->processing_completed_at) {
            $this->processing_completed_at = now();
        }
        
        if ($status === 'quality_check') {
            $this->quality_check_at = now();
        }
        
        $this->save();
    }

    public function reportIssue(array $data): OrderItemIssue
    {
        $issue = $this->issues()->create([
            'reported_by' => $data['reported_by'],
            'issue_type' => $data['issue_type'],
            'description' => $data['description'],
            'photos' => $data['photos'] ?? null,
            'severity' => $data['severity'] ?? 'medium',
        ]);

        $this->updateStatus('issue', "Issue reported: {$data['issue_type']}", $data['reported_by']);
        
        return $issue;
    }

    public function addDamage(array $damageData): void
    {
        $this->has_damage = true;
        $this->damage_type = $damageData['type'];
        $this->damage_description = $damageData['description'];
        
        if (isset($damageData['photos'])) {
            $currentPhotos = $this->damage_photos ?? [];
            $this->damage_photos = array_merge($currentPhotos, $damageData['photos']);
        }
        
        $this->save();
    }

    public function processRefund(float $amount, string $reason, ?int $processedBy = null): void
    {
        $this->is_refunded = true;
        $this->refund_amount = $amount;
        $this->refund_reason = $reason;
        $this->refunded_at = now();
        $this->status = 'refunded';
        
        $this->save();
        
        // Add to status history
        $this->updateStatus('refunded', "Refunded: {$reason}", $processedBy);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->whereIn('status', ['confirmed', 'processing', 'washed', 'dried', 'ironed', 'folded', 'quality_check']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithIssues($query)
    {
        return $query->where('status', 'issue');
    }

    public function scopeByQualityStatus($query, $qualityStatus)
    {
        return $query->where('quality_status', $qualityStatus);
    }

    public function scopeValuableItems($query)
    {
        return $query->where('is_valuable', true)->orWhere('declared_value', '>', 100);
    }

    public function scopeFragileItems($query)
    {
        return $query->where('is_fragile', true);
    }
}