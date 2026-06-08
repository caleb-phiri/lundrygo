<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function track(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'super_admin', 'rider'])) {
            abort(404, 'Order not found');
        }
        
        // Get tracking information
        $trackingData = [
            'order' => $order,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'progress' => $order->delivery_progress_percentage,
            'pickup_scheduled' => $order->pickup_scheduled_at,
            'delivery_scheduled' => $order->delivery_scheduled_at,
            'picked_up_at' => $order->picked_up_at,
            'delivered_at' => $order->delivered_at,
        ];
        
        return view('customer.orders.tracking', $trackingData);
    }
}