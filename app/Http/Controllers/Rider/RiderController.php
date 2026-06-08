<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:rider');
    }

    public function dashboard()
    {
        $rider = auth()->user();
        
        // Get statistics
        $stats = [
            'total_deliveries' => Order::where('rider_id', $rider->id)->count(),
            'completed_deliveries' => Order::where('rider_id', $rider->id)->where('status', 'delivered')->count(),
            'pending_deliveries' => Order::where('rider_id', $rider->id)->whereIn('status', ['pending', 'confirmed', 'processing', 'ready_for_delivery'])->count(),
            'out_for_delivery' => Order::where('rider_id', $rider->id)->where('status', 'out_for_delivery')->count(),
            'total_earnings' => Order::where('rider_id', $rider->id)->where('status', 'delivered')->sum('delivery_fee'),
            'rating' => 4.8,
        ];
        
        // Get available orders for pickup
        $availableOrders = Order::whereNull('rider_id')
            ->whereIn('status', ['pending', 'confirmed', 'processing', 'ready_for_delivery'])
            ->with(['user', 'pickupAddress', 'deliveryAddress'])
            ->latest()
            ->get();
        
        // Get current active orders
        $activeOrders = Order::where('rider_id', $rider->id)
            ->whereIn('status', ['out_for_delivery', 'ready_for_delivery', 'processing', 'confirmed'])
            ->with(['user', 'pickupAddress', 'deliveryAddress'])
            ->latest()
            ->get();
        
        // Get recent completed orders
        $recentOrders = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->with(['user', 'pickupAddress', 'deliveryAddress'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('rider.dashboard', compact('stats', 'availableOrders', 'activeOrders', 'recentOrders'));
    }

    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->rider_id) {
            return back()->with('error', 'This order already has a rider assigned.');
        }
        
        $order->update([
            'rider_id' => auth()->id(),
            'rider_assigned_at' => now(),
            'status' => 'confirmed'
        ]);
        
        return redirect()->route('rider.orders.show', $order)
            ->with('success', 'Order accepted successfully!');
    }

    public function markAsPickedUp($id)
    {
        $order = Order::findOrFail($id);
        
        $order->update([
            'status' => 'out_for_delivery',
            'picked_up_at' => now(),
        ]);
        
        return redirect()->route('rider.orders.show', $order)
            ->with('success', 'Order marked as picked up! You are now on your way to delivery.');
    }

    public function markAsDelivered($id)
    {
        $order = Order::findOrFail($id);
        
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'completed_at' => now(),
        ]);
        
        return redirect()->route('rider.dashboard')
            ->with('success', 'Order delivered successfully! Great job!');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $rider = auth()->user();
        
        // Update rider's current location
        $rider->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'last_location_update' => now(),
        ]);
        
        // Update order tracking
        $order = Order::findOrFail($request->order_id);
        $order->update([
            'rider_current_latitude' => $request->latitude,
            'rider_current_longitude' => $request->longitude,
        ]);
        
        return response()->json(['success' => true]);
    }

    public function tracking()
    {
        $activeOrders = Order::where('rider_id', auth()->id())
            ->whereIn('status', ['out_for_delivery', 'confirmed', 'processing', 'ready_for_delivery'])
            ->with(['user', 'pickupAddress', 'deliveryAddress'])
            ->get();
        
        return view('rider.tracking', compact('activeOrders'));
    }

    public function earnings()
    {
        $earnings = Order::where('rider_id', auth()->id())
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(delivery_fee) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        $totalEarnings = Order::where('rider_id', auth()->id())
            ->where('status', 'delivered')
            ->sum('delivery_fee');
        
        $monthlyEarnings = Order::where('rider_id', auth()->id())
            ->where('status', 'delivered')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(delivery_fee) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
        
        return view('rider.earnings', compact('earnings', 'totalEarnings', 'monthlyEarnings'));
    }

    public function profile()
    {
        $rider = auth()->user();
        return view('rider.profile', compact('rider'));
    }

    public function updateProfile(Request $request)
    {
        $rider = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
        ]);
        
        $rider->update($validated);
        
        return redirect()->route('rider.profile')->with('success', 'Profile updated successfully!');
    }
}