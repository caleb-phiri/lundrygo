<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\LaundryService;
use App\Models\UserAddress;
use App\Models\Promotion;
use App\Services\MapService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $mapService;
    protected $paymentService;
    protected $notificationService;
    
    public function __construct(
        MapService $mapService,
        PaymentService $paymentService,
        NotificationService $notificationService
    ) {
        $this->mapService = $mapService;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }
    
    public function dashboard()
    {
        $user = auth()->user();
        
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['pickupLocation', 'deliveryLocation'])
            ->latest()
            ->limit(5)
            ->get();
        
        $activeOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'confirmed', 'ready_for_pickup', 'out_for_delivery'])
            ->count();
        
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->count();
        
        $savings = Order::where('user_id', $user->id)->sum('discount') ?? 0;
        
        $addresses = UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->sum('total') ?? 0;
        
        $totalOrders = Order::where('user_id', $user->id)->count();
        
        $startDate = now()->subMonths(12)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        $monthlyOrders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function($group) {
                return $group->count();
            });
        
        $chartLabels = [];
        $chartData = [];
        $date = now()->subMonths(11)->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $date->copy()->addMonths($i);
            $monthKey = $currentMonth->format('Y-m');
            $chartLabels[] = $currentMonth->format('M Y');
            $chartData[] = $monthlyOrders[$monthKey] ?? 0;
        }
        
        $currentMonthOrders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        
        $previousMonthOrders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();
        
        $orderGrowth = $previousMonthOrders > 0 
            ? round((($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders) * 100, 1)
            : ($currentMonthOrders > 0 ? 100 : 0);
        
        $firstOrder = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();
        
        $memberSince = $firstOrder ? $firstOrder->created_at->format('M Y') : now()->format('M Y');
        
        $pendingOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'confirmed'])
            ->count();
        
        return view('customer.dashboard', compact(
            'recentOrders',
            'activeOrders',
            'completedOrders',
            'savings',
            'addresses',
            'totalSpent',
            'totalOrders',
            'chartLabels',
            'chartData',
            'orderGrowth',
            'memberSince',
            'pendingOrders'
        ));
    }
    
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['pickupLocation', 'deliveryLocation', 'rider'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('customer.orders.index', compact('orders'));
    }
    
    public function create()
    {
        $user = auth()->user();
        
        $services = LaundryService::where('is_active', true)
            ->with('category')
            ->get();
            
        $addresses = UserAddress::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
            
        $promotions = Promotion::where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->get();
        
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
            
        $activeOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'confirmed'])
            ->count();
            
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->count();
            
        $savings = Order::where('user_id', $user->id)->sum('discount') ?? 0;
        
        return view('customer.orders.create', compact(
            'services', 
            'addresses', 
            'promotions',
            'recentOrders',
            'activeOrders',
            'completedOrders',
            'savings'
        ));
    }
    
    public function store(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Order submission received:', $request->all());
        
        $validated = $request->validate([
            'pickup_address_id' => 'required|exists:user_addresses,id',
            'delivery_address_id' => 'required|exists:user_addresses,id',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:laundry_services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'pickup_date' => 'required|date|after:now',
            'delivery_date' => 'required|date|after:pickup_date',
            'special_instructions' => 'nullable|string',
            'promotion_code' => 'nullable|string',
            'payment_method' => 'required|in:cash,card,wallet',
            'pickup_latitude' => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
        ]);
        
        DB::beginTransaction();
        
        try {
            $pickupAddress = UserAddress::find($validated['pickup_address_id']);
            $deliveryAddress = UserAddress::find($validated['delivery_address_id']);
            
            if (!$pickupAddress || !$deliveryAddress) {
                throw new \Exception('Address not found');
            }
            
            // Get coordinates from request or address with fallback defaults
            $pickupLat = $request->filled('pickup_latitude') ? (float)$request->pickup_latitude : ($pickupAddress->latitude ?? -15.3875);
            $pickupLng = $request->filled('pickup_longitude') ? (float)$request->pickup_longitude : ($pickupAddress->longitude ?? 28.3228);
            $deliveryLat = $request->filled('delivery_latitude') ? (float)$request->delivery_latitude : ($deliveryAddress->latitude ?? -15.3875);
            $deliveryLng = $request->filled('delivery_longitude') ? (float)$request->delivery_longitude : ($deliveryAddress->longitude ?? 28.3228);
            
            // Calculate delivery fee
            $deliveryFee = 5.00;
            
            try {
                if (is_numeric($pickupLat) && is_numeric($pickupLng) && 
                    is_numeric($deliveryLat) && is_numeric($deliveryLng)) {
                    
                    $distance = $this->mapService->calculateDistance(
                        $pickupLat, $pickupLng,
                        $deliveryLat, $deliveryLng
                    );
                    
                    $feeResult = $this->mapService->calculateDeliveryFee(
                        $pickupLat, $pickupLng,
                        $deliveryLat, $deliveryLng
                    );
                    $deliveryFee = $feeResult['standard_fee'] ?? 5.00;
                }
            } catch (\Exception $e) {
                Log::warning('Distance calculation failed: ' . $e->getMessage());
            }
            
            // Calculate subtotal
            $subtotal = 0;
            $itemsArray = [];
            
            foreach ($validated['items'] as $item) {
                $service = LaundryService::find($item['service_id']);
                if (!$service) {
                    throw new \Exception('Service not found: ' . $item['service_id']);
                }
                $itemTotal = $service->price * $item['quantity'];
                $subtotal += $itemTotal;
                $itemsArray[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $service->price,
                    'total' => $itemTotal,
                ];
            }
            
            // Apply promotion if exists
            $discount = 0;
            if ($request->filled('promotion_code')) {
                $promotion = Promotion::where('code', $request->promotion_code)->first();
                if ($promotion && $this->validatePromotion($promotion, $subtotal)) {
                    $discount = $this->calculateDiscount($promotion, $subtotal);
                }
            }
            
            $total = $subtotal + $deliveryFee - $discount;
            
            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'pickup_location_id' => $pickupAddress->id,
                'delivery_location_id' => $deliveryAddress->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'pickup_scheduled_at' => $validated['pickup_date'],
                'delivery_scheduled_at' => $validated['delivery_date'],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'is_express' => $request->is_express ?? false,
            ]);
            
            // Create order items
            foreach ($itemsArray as $item) {
                $order->items()->create($item);
            }
            
            DB::commit();
            
            Log::info('Order created successfully', ['order_id' => $order->id, 'order_number' => $order->order_number]);
            
            return redirect()->route('checkout', ['order' => $order->id])
                ->with('success', 'Order created successfully! Proceed to checkout.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['items.service', 'pickupLocation', 'deliveryLocation', 'rider']);
        
        return view('customer.orders.show', compact('order'));
    }
    
    public function cancel(Order $order)
    {
        $this->authorize('cancel', $order);
        
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }
        
        DB::beginTransaction();
        
        try {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => request('reason', 'Cancelled by customer'),
            ]);
            
            DB::commit();
            
            return redirect()->route('customer.orders.show', $order)
                ->with('success', 'Order cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order.');
        }
    }
    
    public function reorder(Order $order)
    {
        $this->authorize('view', $order);
        
        return redirect()->route('customer.orders.create')
            ->with('reorder_data', $order->toArray());
    }
    
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
    
    private function validatePromotion($promotion, $subtotal)
    {
        if (!$promotion || $promotion->status !== 'active') {
            return false;
        }
        
        if ($promotion->min_order_amount > 0 && $subtotal < $promotion->min_order_amount) {
            return false;
        }
        
        if ($promotion->expires_at && now()->gt($promotion->expires_at)) {
            return false;
        }
        
        if ($promotion->max_uses && $promotion->used_count >= $promotion->max_uses) {
            return false;
        }
        
        return true;
    }
    
    private function calculateDiscount($promotion, $subtotal)
    {
        if ($promotion->type === 'percentage') {
            $discount = ($subtotal * $promotion->value) / 100;
            if ($promotion->max_discount) {
                $discount = min($discount, $promotion->max_discount);
            }
            return $discount;
        } elseif ($promotion->type === 'fixed_amount') {
            return min($promotion->value, $subtotal);
        }
        
        return 0;
    }
}