<?php

namespace App\Http\Controllers;

use App\Models\LaundryService;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\Order;

class HomeController extends Controller
{
    public function index()
    {
        $featuredServices = LaundryService::where('is_featured', true)
            ->where('is_active', true)
            ->limit(6)
            ->get();
            
        $activePromotions = Promotion::where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->limit(3)
            ->get();
            
        $reviews = Review::where('is_published', true)
            ->where('is_verified', true)
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();
            
        $stats = [
            'orders_delivered' => Order::where('status', 'delivered')->count(),
            'happy_customers' => Order::distinct('user_id')->count('user_id'),
            'active_riders' => \App\Models\User::where('role', 'rider')->where('is_active', true)->count(),
            'years_experience' => 5,
        ];
        
        return view('home', compact('featuredServices', 'activePromotions', 'reviews', 'stats'));
    }
    
    public function services()
    {
        $categories = \App\Models\LaundryCategory::with(['services' => function($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();
        
        return view('services', compact('categories'));
    }
    
    public function pricing()
    {
        $services = LaundryService::where('is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();
            
        return view('pricing', compact('services'));
    }
    
    public function howItWorks()
    {
        $steps = [
            ['step' => 1, 'title' => 'Schedule Pickup', 'description' => 'Choose a convenient time for us to pick up your laundry'],
            ['step' => 2, 'title' => 'We Clean', 'description' => 'Our experts professionally clean your clothes'],
            ['step' => 3, 'title' => 'Quality Check', 'description' => 'Each item is inspected for quality'],
            ['step' => 4, 'title' => 'Delivery', 'description' => 'Get your fresh laundry delivered to your door'],
        ];
        
        return view('how-it-works', compact('steps'));
    }
    
    public function contact()
    {
        return view('contact');
    }
}