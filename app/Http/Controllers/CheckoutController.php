<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $orderId = $request->get('order');
        $order = Order::findOrFail($orderId);
        
        return view('checkout', compact('order'));
    }
    
    public function process(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        // Process payment logic here
        $order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        
        return redirect()->route('checkout.success', $order);
    }
    
    public function success(Order $order)
    {
        return view('checkout-success', compact('order'));
    }
    
    public function cancel(Order $order)
    {
        return view('checkout-cancel', compact('order'));
    }
}