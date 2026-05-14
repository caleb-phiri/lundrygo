<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Tracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public Tracking $tracking;

    public function __construct(Order $order, Tracking $tracking)
    {
        $this->order = $order;
        $this->tracking = $tracking;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('orders.' . $this->order->id),
            new Channel('user.' . $this->order->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'tracking' => [
                'id' => $this->tracking->id,
                'title' => $this->tracking->title,
                'description' => $this->tracking->description,
                'time' => $this->tracking->tracked_at->toIso8601String(),
            ],
            'progress' => app(TrackingService::class)->getOrderProgress($this->order),
        ];
    }
}