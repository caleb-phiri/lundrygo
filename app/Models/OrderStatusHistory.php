<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';
    
    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
        'changed_at',
    ];
    
    protected $casts = [
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the order that owns the status history
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the user who changed the status
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}