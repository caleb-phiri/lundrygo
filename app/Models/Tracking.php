<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tracking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'rider_id',
        'status',
        'previous_status',
        'status_code',
        'status_type',
        'title',
        'description',
        'short_description',
        'details',
        'location_name',
        'address',
        'latitude',
        'longitude',
        'coordinates',
        'place_id',
        'tracked_at',
        'expected_at',
        'duration_minutes',
        'remaining_minutes',
        'trackable_type',
        'trackable_id',
        'is_read',
        'read_at',
        'is_notification_sent',
        'notification_sent_at',
        'notification_channel',
        'attachments',
        'thumbnail',
        'metrics',
        'accuracy',
        'is_automatic',
        'is_critical',
        'requires_action',
        'action_url',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'expected_at' => 'datetime',
        'read_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'details' => 'array',
        'attachments' => 'array',
        'metrics' => 'array',
        'metadata' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
        'duration_minutes' => 'integer',
        'remaining_minutes' => 'integer',
        'is_read' => 'boolean',
        'is_notification_sent' => 'boolean',
        'is_automatic' => 'boolean',
        'is_critical' => 'boolean',
        'requires_action' => 'boolean',
    ];

    protected $appends = ['formatted_time', 'is_overdue'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function alerts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TrackingAlert::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->tracked_at->diffForHumans();
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->expected_at && !$this->is_read) {
            return now()->gt($this->expected_at);
        }
        return false;
    }

    public function markAsRead(?int $userId = null): void
    {
        $this->is_read = true;
        $this->read_at = now();
        
        if ($userId && !$this->user_id) {
            $this->user_id = $userId;
        }
        
        $this->save();
    }

    public function scopeUnread($query, ?int $userId = null)
    {
        $query->where('is_read', false);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query;
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStatusType($query, string $statusType)
    {
        return $query->where('status_type', $statusType);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeRequiresAction($query)
    {
        return $query->where('requires_action', true)->where('is_read', false);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tracked_at', [$startDate, $endDate]);
    }

    public function scopeNearby($query, float $latitude, float $longitude, int $radius = 1)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(coordinates, POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }
}