<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::where('user_id', auth()->id())
            ->with('order')
            ->latest()
            ->paginate(10);
            
        return view('customer.reviews', compact('reviews'));
    }
    
    public function store(Request $request, Order $order)
    {
        $this->authorize('review', $order);
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);
        
        $review = Review::updateOrCreate(
            ['order_id' => $order->id, 'user_id' => auth()->id()],
            [
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_verified_purchase' => true,
                'is_published' => true,
            ]
        );
        
        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Thank you for your review!');
    }
}