<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\RiderLocation;
use App\Models\Order;
use Illuminate\Http\Request;

class RiderTrackingController extends Controller
{
    public function index()
    {
        $location = RiderLocation::where('rider_id', auth()->id())->first();
        return view('rider.tracking', compact('location'));
    }
    
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $location = RiderLocation::updateOrCreate(
            ['rider_id' => auth()->id()],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'last_updated_at' => now(),
            ]
        );
        
        return response()->json(['success' => true]);
    }
    
    public function goOnline()
    {
        RiderLocation::updateOrCreate(
            ['rider_id' => auth()->id()],
            ['is_online' => true, 'is_available' => true, 'last_updated_at' => now()]
        );
        
        return redirect()->back()->with('success', 'You are now online');
    }
    
    public function goOffline()
    {
        RiderLocation::where('rider_id', auth()->id())->update([
            'is_online' => false,
            'is_available' => false,
        ]);
        
        return redirect()->back()->with('success', 'You are now offline');
    }
}