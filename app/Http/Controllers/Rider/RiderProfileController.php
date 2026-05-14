<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RiderProfileController extends Controller
{
    public function index()
    {
        $rider = auth()->user();
        return view('rider.profile', compact('rider'));
    }
    
    public function update(Request $request)
    {
        $rider = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_number' => 'nullable|string|max:50',
        ]);
        
        $rider->update($request->only(['name', 'phone', 'vehicle_number']));
        
        return redirect()->route('rider.profile')->with('success', 'Profile updated!');
    }
    
    public function earnings()
    {
        $earnings = Order::where('rider_id', auth()->id())
            ->where('status', 'delivered')
            ->selectRaw('DATE(created_at) as date, SUM(delivery_fee) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        $totalEarnings = Order::where('rider_id', auth()->id())
            ->where('status', 'delivered')
            ->sum('delivery_fee');
            
        return view('rider.earnings', compact('earnings', 'totalEarnings'));
    }
}