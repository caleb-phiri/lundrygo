<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Tracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RiderLocationUpdated implements ShouldBroadcast
{
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
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'rider_id' => $this->order->rider_id,
            'location' => [
                'lat' => $this->tracking->latitude,
                'lng' => $this->tracking->longitude,
            ],
            'speed' => $this->tracking->speed,
            'bearing' => $this->tracking->bearing,
            'updated_at' => $this->tracking->tracked_at->toIso8601String(),
        ];
    }
}