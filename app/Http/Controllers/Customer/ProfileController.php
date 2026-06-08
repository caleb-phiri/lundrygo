<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.addresses', compact('addresses'));
    }
    
  public function storeAddress(Request $request)
{
    $validated = $request->validate([
        'label' => 'required|string|max:255',
        'full_address' => 'required|string|min:5',
        'contact_phone' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'is_default' => 'boolean',
    ]);
    
    $validated['user_id'] = Auth::id();
    $validated['is_active'] = true;
    
    // Handle default address
    if ($request->has('is_default') && $request->is_default == 1) {
        // Remove default from all existing addresses
        UserAddress::where('user_id', Auth::id())->update(['is_default' => 0]);
        $validated['is_default'] = 1;
    } else {
        // Check if user has any default address
        $hasDefault = UserAddress::where('user_id', Auth::id())->where('is_default', 1)->exists();
        $validated['is_default'] = $hasDefault ? 0 : 1;
    }
    
    UserAddress::create($validated);
    
    return redirect()->route('customer.addresses')->with('success', 'Address added successfully!');
}
    
    public function updateAddress(Request $request, $id)
    {
        $address = UserAddress::findOrFail($id);
        
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'full_address' => 'required|string|min:5',
            'contact_phone' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);
        
        $isDefaultChecked = $request->has('is_default') && $request->is_default == 1;
        
        if ($isDefaultChecked) {
            // Remove default from all other addresses
            UserAddress::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        } else {
            $validated['is_default'] = false;
            
            // If this was the only default address, set another address as default
            $defaultCount = UserAddress::where('user_id', Auth::id())->where('is_default', true)->count();
            if ($defaultCount == 0 && $address->is_default) {
                $anotherAddress = UserAddress::where('user_id', Auth::id())->where('id', '!=', $address->id)->first();
                if ($anotherAddress) {
                    $anotherAddress->update(['is_default' => true]);
                }
            }
        }
        
        $address->update($validated);
        
        return redirect()->route('customer.addresses')->with('success', 'Address updated successfully!');
    }
    
    public function deleteAddress($id)
    {
        $address = UserAddress::findOrFail($id);
        
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        
        $wasDefault = $address->is_default;
        $address->delete();
        
        // If the deleted address was default, set another address as default
        if ($wasDefault) {
            $anotherAddress = UserAddress::where('user_id', Auth::id())->first();
            if ($anotherAddress) {
                $anotherAddress->update(['is_default' => true]);
            }
        }
        
        return redirect()->route('customer.addresses')->with('success', 'Address deleted successfully!');
    }
    
    public function setDefaultAddress($id)
    {
        $address = UserAddress::findOrFail($id);
        
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Remove default from all addresses of this user
        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        
        // Set this as default
        $address->is_default = true;
        $address->save();
        
        return redirect()->route('customer.addresses')->with('success', 'Default address updated successfully!');
    }
    
    public function promotions()
    {
        return view('customer.promotions');
    }
}