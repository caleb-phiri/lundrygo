<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\UserAddress;
use App\Models\Promotion;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Customer Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Recent Orders
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['pickupAddress', 'deliveryAddress', 'items'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Active Orders Count
        $activeOrders = Order::where('user_id', $user->id)
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded'])
            ->count();
        
        // Completed Orders Count
        $completedOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'completed'])
            ->count();
        
        // Total Spent
        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total');
        
        // Total Savings from discounts
        $savings = Order::where('user_id', $user->id)
            ->sum(DB::raw('COALESCE(discount, 0) + COALESCE(coupon_discount, 0) + COALESCE(wallet_discount, 0)'));
        
        // Saved Addresses
        $addresses = UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        // Total Orders (lifetime)
        $totalOrders = Order::where('user_id', $user->id)->count();
        
        // Cancelled Orders
        $cancelledOrders = Order::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->count();
        
        // Pending Orders (needs attention)
        $pendingOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'payment_pending'])
            ->count();
        
        // Member Since
        $firstOrder = Order::where('user_id', $user->id)
            ->oldest()
            ->first();
        $memberSince = $firstOrder ? $firstOrder->created_at->format('M Y') : now()->format('M Y');
        
        // Monthly Orders Chart Data (last 12 months)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels[] = $date->format('M Y');
            $chartData[] = Order::where('user_id', $user->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        
        // Order Growth (current month vs previous month)
        $currentMonthOrders = Order::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $previousMonthOrders = Order::where('user_id', $user->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $orderGrowth = $previousMonthOrders > 0 
            ? round((($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100, 1)
            : ($currentMonthOrders > 0 ? 100 : 0);
        
        return view('customer.dashboard', compact(
            'recentOrders',
            'activeOrders',
            'completedOrders',
            'cancelledOrders',
            'totalSpent',
            'savings',
            'addresses',
            'totalOrders',
            'pendingOrders',
            'memberSince',
            'chartLabels',
            'chartData',
            'orderGrowth'
        ));
    }

    /**
     * List all orders for the authenticated customer
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['pickupAddress', 'deliveryAddress', 'rider', 'items']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded']);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Search by order number
        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        
        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Order statistics
        $stats = [
            'all' => Order::where('user_id', auth()->id())->count(),
            'active' => Order::where('user_id', auth()->id())
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded'])
                ->count(),
            'completed' => Order::where('user_id', auth()->id())
                ->whereIn('status', ['delivered', 'completed'])
                ->count(),
            'cancelled' => Order::where('user_id', auth()->id())
                ->where('status', 'cancelled')
                ->count(),
        ];
        
        return view('customer.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        // Fetch services grouped by type from your new Service model
        $standardServices = Service::where('service_type', 'standard')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        $weeklySubscriptions = Service::where('service_type', 'weekly')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        $monthlySubscriptions = Service::where('service_type', 'monthly')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        $expressServices = Service::where('service_type', 'express')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        // Get user's saved addresses
        $addresses = UserAddress::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();
        
        // Check if user has any addresses
        if ($addresses->isEmpty()) {
            return redirect()->route('customer.addresses')
                ->with('warning', 'Please add at least one address before placing an order.');
        }
        
        // Get active promotions
        $promotions = Promotion::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
            })
            ->get();
        
        $activeOrders = Order::where('user_id', auth()->id())
            ->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded'])
            ->count();
        
        $completedOrders = Order::where('user_id', auth()->id())
            ->whereIn('status', ['delivered', 'completed'])
            ->count();
        
        return view('customer.orders.create', compact(
            'standardServices',
            'weeklySubscriptions',
            'monthlySubscriptions',
            'expressServices',
            'addresses',
            'promotions',
            'activeOrders',
            'completedOrders'
        ));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_address_id' => 'required|exists:user_addresses,id',
            'delivery_address_id' => 'required|exists:user_addresses,id',
            'selected_services' => 'required|json',
            'pickup_date' => 'required|date|after_or_equal:today',
            'delivery_date' => 'required|date|after:pickup_date',
            'special_instructions' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:card,cash,wallet',
            'coupon_code' => 'nullable|string|max:50',
        ]);
        
        // Verify addresses belong to user
        $pickupAddress = UserAddress::where('id', $request->pickup_address_id)
            ->where('user_id', auth()->id())
            ->first();
            
        $deliveryAddress = UserAddress::where('id', $request->delivery_address_id)
            ->where('user_id', auth()->id())
            ->first();
            
        if (!$pickupAddress || !$deliveryAddress) {
            return back()->with('error', 'Invalid address selected.')->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Decode selected services
            $selectedServices = json_decode($request->selected_services, true);
            
            if (empty($selectedServices)) {
                throw new \Exception('No services selected.');
            }
            
            // Calculate order totals
            $subtotal = 0;
            $orderItemsData = [];
            $hasExpressService = false;
            
            foreach ($selectedServices as $service) {
                $serviceData = Service::findOrFail($service['id']);
                
                if (!$serviceData->is_active) {
                    throw new \Exception("Service '{$serviceData->name}' is no longer available.");
                }
                
                $quantity = (int) $service['quantity'];
                $price = (float) $service['price'];
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;
                
                if ($serviceData->service_type === 'express') {
                    $hasExpressService = true;
                }
                
                $orderItemsData[] = [
                    'service_id' => $serviceData->id,
                    'service_name' => $serviceData->name,
                    'service_type' => $serviceData->service_type,
                    'billing_period' => $serviceData->billing_period,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total' => $itemTotal,
                ];
            }
            
            // Calculate fees
            $deliveryFee = $hasExpressService ? 10.00 : 5.00;
            $expressFee = $hasExpressService ? 5.00 : 0;
            $serviceFee = 0;
            $taxRate = 0;
            $taxAmount = $subtotal * $taxRate;
            
            // Apply coupon if provided
            $couponDiscount = 0;
            $couponCode = null;
            
            if ($request->coupon_code) {
                $promotion = Promotion::where('code', $request->coupon_code)
                    ->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>=', now());
                    })
                    ->first();
                    
                if ($promotion) {
                    if ($promotion->type === 'percentage') {
                        $couponDiscount = $subtotal * ($promotion->value / 100);
                    } elseif ($promotion->type === 'fixed_amount') {
                        $couponDiscount = $promotion->value;
                    } elseif ($promotion->type === 'free_delivery') {
                        $couponDiscount = $deliveryFee;
                    }
                    $couponCode = $promotion->code;
                }
            }
            
            // Calculate total
            $total = $subtotal + $deliveryFee + $serviceFee + $taxAmount + $expressFee - $couponDiscount;
            $total = max(0, $total);
            
            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();
            
            // Create the order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'pickup_address_id' => $request->pickup_address_id,
                'delivery_address_id' => $request->delivery_address_id,
                'status' => 'pending',
                'order_type' => 'regular',
                'priority' => $hasExpressService ? 'high' : 'normal',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'service_fee' => $serviceFee,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount' => 0,
                'coupon_discount' => $couponDiscount,
                'coupon_code' => $couponCode,
                'wallet_discount' => 0,
                'total' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'total_items' => count($orderItemsData),
                'pickup_scheduled_at' => $request->pickup_date,
                'delivery_scheduled_at' => $request->delivery_date,
                'special_instructions' => $request->special_instructions,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'is_express' => $hasExpressService,
                'express_fee' => $expressFee,
                'is_scheduled' => true,
                'requires_special_handling' => false,
                'is_insured' => false,
                'is_priority' => $hasExpressService,
                'is_rush_order' => false,
            ]);
            
            // Create order items
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }
            
            DB::commit();
            
            // Handle payment if not cash
            if ($request->payment_method !== 'cash') {
                return redirect()->route('checkout', ['order' => $order->id]);
            }
            
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Order #' . $order->order_number . ' placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        // Load all necessary relationships
        $order->load([
            'items',
            'pickupAddress',
            'deliveryAddress',
            'rider',
            'statusHistory' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        // Get progress percentage
        $progress = $order->progress_percentage ?? 0;
        
        // Get tracking timeline
        $timeline = $order->tracking_timeline ?? [];
        
        return view('customer.orders.show', compact('order', 'progress', 'timeline'));
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'payment_pending', 'confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled. Current status: ' . $order->status);
        }
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason,
        ]);
        
        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Order #' . $order->order_number . ' has been cancelled.');
    }

    /**
     * Reorder from a previous order
     */
    public function reorder(Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Prepare reorder data
        $reorderData = [
            'pickup_address_id' => $order->pickup_address_id,
            'delivery_address_id' => $order->delivery_address_id,
            'special_instructions' => $order->special_instructions,
            'items' => $order->items->map(function($item) {
                return [
                    'service_id' => $item->service_id,
                    'service_name' => $item->service_name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                ];
            })->toArray(),
        ];
        
        return redirect()->route('customer.orders.create')
            ->with('reorder_data', $reorderData)
            ->with('success', 'Previous order items have been loaded. Review and place your new order.');
    }

    /**
     * Track order status
     */
    public function track(Order $order)
    {
        // Authorization check
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin() && 
            !(auth()->user()->isRider() && $order->rider_id === auth()->id())) {
            abort(403, 'Unauthorized access.');
        }
        
        $order->load(['rider', 'items']);
        
        $progress = $order->progress_percentage ?? 0;
        $timeline = $order->tracking_timeline ?? [];
        
        return view('customer.orders.tracking', compact('order', 'progress', 'timeline'));
    }

    /**
     * Generate a unique order number
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $unique = strtoupper(substr(uniqid(), -6));
        
        $orderNumber = "{$prefix}-{$date}-{$unique}";
        
        while (Order::where('order_number', $orderNumber)->exists()) {
            $unique = strtoupper(substr(uniqid(), -6));
            $orderNumber = "{$prefix}-{$date}-{$unique}";
        }
        
        return $orderNumber;
    }
}