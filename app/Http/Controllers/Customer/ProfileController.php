<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('customer.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update($request->only(['name', 'email', 'phone']));
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }
        
        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }
    
    public function addresses()
    {
        $addresses = UserAddress::where('user_id', auth()->id())->get();
        return view('customer.addresses', compact('addresses'));
    }
    
    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'full_address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        $address = UserAddress::create([
            'user_id' => auth()->id(),
            'label' => $request->label,
            'full_address' => $request->full_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'US',
        ]);
        
        if ($request->is_default) {
            $address->setAsDefault();
        }
        
        return redirect()->route('customer.addresses')->with('success', 'Address added successfully!');
    }
    
    public function updateAddress(Request $request, UserAddress $address)
    {
        $this->authorize('update', $address);
        
        $request->validate([
            'label' => 'required|string',
            'full_address' => 'required|string',
        ]);
        
        $address->update($request->only(['label', 'full_address', 'city', 'state', 'postal_code', 'country']));
        
        if ($request->is_default) {
            $address->setAsDefault();
        }
        
        return redirect()->route('customer.addresses')->with('success', 'Address updated successfully!');
    }
    
    public function deleteAddress(UserAddress $address)
    {
        $this->authorize('delete', $address);
        $address->delete();
        
        return redirect()->route('customer.addresses')->with('success', 'Address deleted successfully!');
    }
    
    public function setDefaultAddress(UserAddress $address)
    {
        $this->authorize('update', $address);
        $address->setAsDefault();
        
        return redirect()->route('customer.addresses')->with('success', 'Default address updated!');
    }
    
    public function promotions()
    {
        return view('customer.promotions');
    }
}