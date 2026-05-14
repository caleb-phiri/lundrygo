<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiderLocation;
use Illuminate\Http\Request;

class RiderOrderController extends Controller
{
    public function dashboard()
    {
        $rider = auth()->user();
        $assignedOrders = Order::where('rider_id', $rider->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get();
        $completedOrders = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->count();
        $totalEarnings = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->sum('delivery_fee');
            
        return view('rider.dashboard', compact('assignedOrders', 'completedOrders', 'totalEarnings'));
    }
    
    public function index()
    {
        $orders = Order::where('rider_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('rider.orders', compact('orders'));
    }
    
    public function show(Order $order)
    {
        return view('rider.order-details', compact('order'));
    }
    
    public function accept(Order $order)
    {
        $order->update([
            'rider_id' => auth()->id(),
            'status' => 'rider_assigned',
            'rider_assigned_at' => now(),
        ]);
        
        return redirect()->route('rider.orders.show', $order)
            ->with('success', 'Order accepted successfully!');
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:rider_en_route_pickup,items_collected,in_transit_to_laundry,out_for_delivery',
        ]);
        
        $order->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }
    
    public function complete(Order $order)
    {
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        
        return redirect()->route('rider.orders.show', $order)
            ->with('success', 'Order completed successfully!');
    }
}