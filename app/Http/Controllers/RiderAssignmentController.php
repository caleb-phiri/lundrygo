<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class RiderAssignmentController extends Controller
{
    public function showForm($id)
    {
        $order = Order::findOrFail($id);
        $availableRiders = User::where('role', 'rider')->where('is_active', true)->get();
        $currentRider = $order->rider_id ? User::find($order->rider_id) : null;
        
        return view('admin.orders.assign-rider', compact('order', 'availableRiders', 'currentRider'));
    }
    
    public function assign(Request $request, $id)
    {
        $request->validate(['rider_id' => 'required|exists:users,id']);
        
        $order = Order::findOrFail($id);
        $order->update([
            'rider_id' => $request->rider_id,
            'rider_assigned_at' => now(),
        ]);
        
        return redirect()->route('admin.orders.show', $order)->with('success', 'Rider assigned successfully!');
    }
    
    public function unassign($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['rider_id' => null, 'rider_assigned_at' => null]);
        
        return redirect()->route('admin.orders.show', $order)->with('success', 'Rider unassigned successfully!');
    }
}