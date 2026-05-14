<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method_id',
        'payment_method',
        'payment_method_type',
        'transaction_id',
        'invoice_id',
        'receipt_number',
        'amount',
        'tax_amount',
        'fee_amount',
        'currency',
        'status',
        'status_reason',
        'failure_reason',
        'gateway',
        'gateway_payment_id',
        'gateway_customer_id',
        'gateway_charge_id',
        'gateway_response',
        'gateway_webhook_data',
        'card_last_four',
        'card_brand',
        'card_expiry_month',
        'card_expiry_year',
        'card_holder_name',
        'payment_token',
        'bank_name',
        'bank_account_last_four',
        'reference_number',
        'refunded_amount',
        'refund_reason',
        'refund_notes',
        'refunded_at',
        'refund_transaction_id',
        'refund_details',
        'is_partial',
        'installment_number',
        'total_installments',
        'is_split_payment',
        'split_details',
        'authorized_at',
        'captured_at',
        'paid_at',
        'failed_at',
        'voided_at',
        'disputed_at',
        'ip_address',
        'user_agent',
        'metadata',
        'notes',
        'receipt_url',
        'invoice_url',
        'attachments',
        'webhook_processed',
        'webhook_received_at',
        'webhook_signature',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'is_partial' => 'boolean',
        'installment_number' => 'integer',
        'total_installments' => 'integer',
        'is_split_payment' => 'boolean',
        'split_details' => 'array',
        'gateway_response' => 'array',
        'gateway_webhook_data' => 'array',
        'refund_details' => 'array',
        'metadata' => 'array',
        'attachments' => 'array',
        'authorized_at' => 'datetime',
        'captured_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'voided_at' => 'datetime',
        'disputed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'webhook_received_at' => 'datetime',
        'webhook_processed' => 'boolean',
    ];

    protected $appends = ['status_label', 'can_be_refunded'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(PaymentDispute::class);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'authorized' => 'Authorized',
            'captured' => 'Captured',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'partially_refunded' => 'Partially Refunded',
            'disputed' => 'Disputed',
            'chargeback' => 'Chargeback',
            'voided' => 'Voided',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getCanBeRefundedAttribute(): bool
    {
        return in_array($this->status, ['completed', 'captured']) && 
               $this->refunded_amount < $this->amount;
    }

    public function markAsAuthorized(string $gatewayPaymentId = null): void
    {
        $this->status = 'authorized';
        $this->authorized_at = now();
        
        if ($gatewayPaymentId) {
            $this->gateway_payment_id = $gatewayPaymentId;
        }
        
        $this->save();
        $this->logAction('authorized', 'Payment authorized successfully');
    }

    public function markAsCaptured(): void
    {
        $this->status = 'captured';
        $this->captured_at = now();
        $this->save();
        $this->logAction('captured', 'Payment captured successfully');
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->paid_at = now();
        $this->save();
        $this->logAction('completed', 'Payment completed successfully');
        
        // Update order payment status
        $this->order->update([
            'payment_status' => 'paid',
            'paid_amount' => $this->order->paid_amount + $this->amount,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->status = 'failed';
        $this->failure_reason = $reason;
        $this->failed_at = now();
        $this->save();
        $this->logAction('failed', "Payment failed: {$reason}");
    }

    public function processRefund(float $amount, string $reason, string $notes = null): Refund
    {
        if (!$this->can_be_refunded) {
            throw new \Exception('Payment cannot be refunded');
        }

        if ($amount > ($this->amount - $this->refunded_amount)) {
            throw new \Exception('Refund amount exceeds available balance');
        }

        $refund = Refund::create([
            'payment_id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'refund_transaction_id' => $this->generateRefundTransactionId(),
            'amount' => $amount,
            'reason' => $reason,
            'notes' => $notes,
            'status' => 'pending',
        ]);

        $this->refunded_amount += $amount;
        
        if ($this->refunded_amount >= $this->amount) {
            $this->status = 'refunded';
        } else {
            $this->status = 'partially_refunded';
        }
        
        $this->refund_reason = $reason;
        $this->refund_notes = $notes;
        $this->refunded_at = now();
        $this->save();
        
        $this->logAction('refund_initiated', "Refund of {$amount} initiated: {$reason}");
        
        return $refund;
    }

    public function addLog(string $action, string $status, string $message = null, array $data = null): void
    {
        PaymentLog::create([
            'payment_id' => $this->id,
            'action' => $action,
            'status' => $status,
            'message' => $message,
            'request_data' => $data['request'] ?? null,
            'response_data' => $data['response'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function logAction(string $action, string $message = null): void
    {
        $this->addLog($action, $this->status, $message);
    }

    protected function generateRefundTransactionId(): string
    {
        return 'REF_' . strtoupper(uniqid()) . '_' . time();
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'captured']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}