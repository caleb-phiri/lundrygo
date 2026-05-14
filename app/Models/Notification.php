<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_type',
        'notification_uuid',
        'type',
        'subtype',
        'title',
        'message',
        'short_message',
        'data',
        'action_text',
        'action_url',
        'action_type',
        'send_email',
        'send_sms',
        'send_push',
        'send_in_app',
        'send_webhook',
        'email_status',
        'sms_status',
        'push_status',
        'in_app_status',
        'email_sent_at',
        'sms_sent_at',
        'push_sent_at',
        'in_app_sent_at',
        'email_message_id',
        'sms_message_id',
        'push_message_id',
        'is_read',
        'read_at',
        'read_device',
        'priority',
        'is_critical',
        'is_scheduled',
        'scheduled_for',
        'sent_at',
        'expires_at',
        'is_expired',
        'group_id',
        'group_count',
        'is_clicked',
        'clicked_at',
        'clicked_action',
        'is_dismissed',
        'dismissed_at',
        'template_id',
        'template_variables',
        'source',
        'triggered_by',
        'variant',
        'ab_test_data',
        'analytics',
        'metadata',
        'delivery_logs',
    ];

    protected $casts = [
        'data' => 'array',
        'template_variables' => 'array',
        'ab_test_data' => 'array',
        'analytics' => 'array',
        'metadata' => 'array',
        'delivery_logs' => 'array',
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'send_push' => 'boolean',
        'send_in_app' => 'boolean',
        'send_webhook' => 'boolean',
        'is_read' => 'boolean',
        'is_critical' => 'boolean',
        'is_scheduled' => 'boolean',
        'is_expired' => 'boolean',
        'is_clicked' => 'boolean',
        'is_dismissed' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'sms_sent_at' => 'datetime',
        'push_sent_at' => 'datetime',
        'in_app_sent_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'clicked_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'group_count' => 'integer',
    ];

    protected $appends = ['is_expired_status', 'read_time_ago'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function triggerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function getIsExpiredStatusAttribute(): bool
    {
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return true;
        }
        return $this->is_expired;
    }

    public function getReadTimeAgoAttribute(): ?string
    {
        if ($this->read_at) {
            return $this->read_at->diffForHumans();
        }
        return null;
    }

    public function markAsRead(string $device = null): void
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->in_app_status = 'read';
        
        if ($device) {
            $this->read_device = $device;
        }
        
        $this->save();
    }

    public function markAsClicked(string $action = null): void
    {
        $this->is_clicked = true;
        $this->clicked_at = now();
        
        if ($action) {
            $this->clicked_action = $action;
        }
        
        $this->save();
    }

    public function markAsDismissed(): void
    {
        $this->is_dismissed = true;
        $this->dismissed_at = now();
        $this->save();
    }

    public function updateChannelStatus(string $channel, string $status, string $messageId = null): void
    {
        $field = "{$channel}_status";
        $sentField = "{$channel}_sent_at";
        $idField = "{$channel}_message_id";
        
        if (property_exists($this, $field)) {
            $this->$field = $status;
            
            if ($status === 'sent' || $status === 'delivered') {
                $this->$sentField = now();
            }
            
            if ($messageId) {
                $this->$idField = $messageId;
            }
            
            $this->save();
        }
    }

    public function addDeliveryLog(string $channel, string $status, array $response = null, string $error = null): void
    {
        $logs = $this->delivery_logs ?? [];
        $logs[] = [
            'channel' => $channel,
            'status' => $status,
            'response' => $response,
            'error' => $error,
            'timestamp' => now()->toISOString(),
        ];
        
        $this->delivery_logs = $logs;
        $this->save();
        
        // Also log to notification_logs table
        NotificationLog::create([
            'notification_id' => $this->id,
            'channel' => $channel,
            'status' => $status,
            'response' => $response ? json_encode($response) : null,
            'error_message' => $error,
            'logged_at' => now(),
        ]);
    }

    public function scopeUnread($query, ?int $userId = null)
    {
        $query->where('is_read', false)
            ->where('in_app_status', 'sent');
            
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query;
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeUnsent($query)
    {
        return $query->where('is_scheduled', false)
            ->whereNull('sent_at')
            ->where('status', 'pending');
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true)
            ->where('scheduled_for', '>', now())
            ->whereNull('sent_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->orWhere('is_expired', true);
    }

    public function scopeByGroup($query, string $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}