<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\LaundryService;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // ============ DASHBOARD ============

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::whereIn('status', ['delivered', 'completed'])->sum('total'),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_riders' => User::where('role', 'rider')->count(),
            'total_services' => LaundryService::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'payment_pending'])->count(),
            'processing_orders' => Order::whereIn('status', ['confirmed', 'processing', 'at_laundry', 'washing', 'drying', 'ironing', 'folding', 'quality_check'])->count(),
            'delivered_orders' => Order::whereIn('status', ['delivered', 'completed'])->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                ->whereIn('status', ['delivered', 'completed'])
                ->sum('total'),
            'monthly_revenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['delivered', 'completed'])
                ->sum('total'),
        ];

        $chartData = $this->getChartData();
        
        $recentOrders = Order::with(['user', 'rider'])
            ->latest()
            ->limit(10)
            ->get();

        $recentUsers = User::latest()->limit(5)->get();
        
        $popularServices = LaundryService::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'chartData', 
            'recentOrders', 
            'recentUsers',
            'popularServices'
        ));
    }

    /**
     * Get chart data for last 30 days
     */
    private function getChartData(): array
    {
        $labels = [];
        $ordersData = [];
        $revenueData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $ordersData[] = Order::whereDate('created_at', $date)->count();
            $revenueData[] = Order::whereDate('created_at', $date)
                ->whereIn('status', ['delivered', 'completed'])
                ->sum('total');
        }

        return [
            'labels' => $labels,
            'orders' => $ordersData,
            'revenue' => $revenueData,
        ];
    }

    // ============ USER MANAGEMENT ============

    /**
     * List all users
     */
    public function usersIndex(Request $request)
    {
        $query = User::query();
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $users = $query->withCount('orders')
            ->latest()
            ->paginate(20)
            ->appends($request->query());
        
        $stats = [
            'total' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'supervisors' => User::where('role', 'supervisor')->count(),
            'riders' => User::where('role', 'rider')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show create user form
     */
    public function usersCreate()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:customer,admin,super_admin,supervisor,rider',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        $validated['email_verified_at'] = now(); // Auto-verify admin-created users
        
        User::create($validated);
        
        Log::info('User created by admin', [
            'admin_id' => auth()->id(),
            'new_user_email' => $validated['email'],
            'role' => $validated['role']
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function usersEdit($id)
    {
        $user = User::withCount('orders')
            ->withSum('orders', 'total')
            ->findOrFail($id);
            
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function usersUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:customer,admin,super_admin,supervisor,rider',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $user->update($validated);
        
        Log::info('User updated by admin', [
            'admin_id' => auth()->id(),
            'updated_user_id' => $user->id,
            'changes' => $validated
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function usersDestroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }
        
        // Check if user has orders
        if ($user->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete user with existing orders. Deactivate them instead.');
        }
        
        // Check if user is a rider with active orders
        if ($user->role === 'rider' && $user->riderOrders()->whereIn('status', ['confirmed', 'processing', 'out_for_delivery'])->count() > 0) {
            return back()->with('error', 'Cannot delete rider with active deliveries.');
        }
        
        $user->delete();
        
        Log::info('User deleted by admin', [
            'admin_id' => auth()->id(),
            'deleted_user_id' => $id
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user active status
     */
    public function usersToggleActive($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }
        
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "User {$status} successfully!");
    }

    // ============ ORDER MANAGEMENT ============

    /**
     * List all orders
     */
    public function ordersIndex(Request $request)
    {
        $query = Order::with(['user', 'rider']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'refunded']);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
        
        // Date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $orders = $query->latest()
            ->paginate(20)
            ->appends($request->query());
        
        $stats = [
            'total' => Order::count(),
            'pending' => Order::whereIn('status', ['pending', 'payment_pending'])->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'processing' => Order::whereIn('status', ['processing', 'at_laundry', 'washing', 'drying', 'ironing', 'folding', 'quality_check'])->count(),
            'ready_for_delivery' => Order::where('status', 'ready_for_delivery')->count(),
            'out_for_delivery' => Order::where('status', 'out_for_delivery')->count(),
            'delivered' => Order::whereIn('status', ['delivered', 'completed'])->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show order details
     */
    public function ordersShow($id)
    {
        $order = Order::with([
            'user',
            'items.service',
            'rider',
            'pickupAddress',
            'deliveryAddress',
            'statusHistory' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function ordersUpdateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,payment_pending,confirmed,processing,rider_assigned,rider_en_route_pickup,at_pickup_location,items_collected,in_transit_to_laundry,arrived_at_laundry,at_laundry,washing,drying,ironing,folding,quality_check,quality_check_failed,ready_for_delivery,out_for_delivery,delivered,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        // Use the Order model's updateStatus method if available
        if (method_exists($order, 'updateStatus')) {
            $order->updateStatus($newStatus, $request->notes);
        } else {
            // Fallback
            $updateData = ['status' => $newStatus];
            
            switch ($newStatus) {
                case 'confirmed':
                    $updateData['order_confirmed_at'] = $updateData['order_confirmed_at'] ?? now();
                    break;
                case 'processing':
                    $updateData['processing_started_at'] = $updateData['processing_started_at'] ?? now();
                    break;
                case 'ready_for_delivery':
                    $updateData['ready_for_delivery_at'] = now();
                    break;
                case 'out_for_delivery':
                    $updateData['out_for_delivery_at'] = now();
                    break;
                case 'delivered':
                case 'completed':
                    $updateData['delivered_at'] = now();
                    $updateData['completed_at'] = now();
                    break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    break;
            }
            
            $order->update($updateData);
        }
        
        Log::info('Order status updated by admin', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'admin_id' => auth()->id(),
            'notes' => $request->notes
        ]);
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order #{$order->order_number} status updated from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    // ============ RIDER ASSIGNMENT ============

    /**
     * Show assign rider form
     */
    public function showAssignRiderForm($id)
    {
        $order = Order::with(['user', 'pickupAddress', 'deliveryAddress'])->findOrFail($id);
        
        $availableRiders = User::where('role', 'rider')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get active order count for each rider
        foreach ($availableRiders as $rider) {
            $rider->active_orders_count = Order::where('rider_id', $rider->id)
                ->whereIn('status', ['confirmed', 'rider_assigned', 'rider_en_route_pickup', 'at_pickup_location', 
                    'items_collected', 'in_transit_to_laundry', 'processing', 'ready_for_delivery', 'out_for_delivery'])
                ->count();
            
            $rider->completed_today = Order::where('rider_id', $rider->id)
                ->whereDate('delivered_at', today())
                ->count();
        }
        
        $currentRider = $order->rider_id ? User::find($order->rider_id) : null;
        
        return view('admin.orders.assign-rider', compact('order', 'availableRiders', 'currentRider'));
    }

    /**
     * Assign rider to order
     */
    public function assignRiderToOrder(Request $request, $id)
    {
        $request->validate([
            'rider_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $order = Order::findOrFail($id);
        
        // Verify the user is actually a rider
        $rider = User::where('id', $request->rider_id)
            ->where('role', 'rider')
            ->where('is_active', true)
            ->first();
            
        if (!$rider) {
            return back()->with('error', 'Selected user is not an active rider.');
        }
        
        DB::beginTransaction();
        
        try {
            $updateData = [
                'rider_id' => $request->rider_id,
                'rider_assigned_at' => now(),
            ];
            
            // Auto-confirm order if it's pending
            if (in_array($order->status, ['pending', 'payment_pending'])) {
                $updateData['status'] = 'confirmed';
                $updateData['order_confirmed_at'] = now();
            }
            
            $order->update($updateData);
            
            Log::info('Rider assigned to order', [
                'order_id' => $order->id,
                'rider_id' => $rider->id,
                'rider_name' => $rider->name,
                'assigned_by' => auth()->id()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', "Rider {$rider->name} assigned to Order #{$order->order_number} successfully!");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign rider', [
                'order_id' => $order->id,
                'rider_id' => $request->rider_id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to assign rider. Please try again.');
        }
    }

    /**
     * Unassign rider from order
     */
    public function unassignRider($id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->rider_id) {
            return back()->with('error', 'No rider is assigned to this order.');
        }
        
        $riderName = $order->rider ? $order->rider->name : 'Unknown';
        
        $order->update([
            'rider_id' => null,
            'rider_assigned_at' => null,
        ]);
        
        Log::info('Rider unassigned from order', [
            'order_id' => $order->id,
            'rider_name' => $riderName,
            'unassigned_by' => auth()->id()
        ]);
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Rider {$riderName} has been unassigned from Order #{$order->order_number}.");
    }

    /**
     * Get available riders (AJAX)
     */
    public function getAvailableRiders()
    {
        $riders = User::where('role', 'rider')
            ->where('is_active', true)
            ->select('id', 'name', 'phone')
            ->get();
        
        return response()->json($riders);
    }

    // ============ RIDER MANAGEMENT ============

    /**
     * List all riders
     */
    public function ridersIndex(Request $request)
    {
        $query = User::where('role', 'rider');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $riders = $query->withCount(['riderOrders as total_orders' => function($q) {
                $q->whereIn('status', ['delivered', 'completed']);
            }])
            ->withCount(['riderOrders as active_orders' => function($q) {
                $q->whereIn('status', ['confirmed', 'rider_assigned', 'rider_en_route_pickup', 
                    'at_pickup_location', 'items_collected', 'in_transit_to_laundry', 
                    'processing', 'ready_for_delivery', 'out_for_delivery']);
            }])
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->query());
        
        return view('admin.riders.index', compact('riders'));
    }

    /**
     * Show rider details
     */
    public function ridersShow($id)
    {
        $rider = User::where('role', 'rider')->findOrFail($id);
        
        $stats = [
            'total_deliveries' => Order::where('rider_id', $rider->id)
                ->whereIn('status', ['delivered', 'completed'])
                ->count(),
            'active_orders' => Order::where('rider_id', $rider->id)
                ->whereIn('status', ['confirmed', 'processing', 'out_for_delivery'])
                ->count(),
            'today_deliveries' => Order::where('rider_id', $rider->id)
                ->whereDate('delivered_at', today())
                ->count(),
            'total_earnings' => Order::where('rider_id', $rider->id)
                ->whereIn('status', ['delivered', 'completed'])
                ->sum('delivery_fee'),
        ];
            
        $recentOrders = Order::where('rider_id', $rider->id)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.riders.show', compact('rider', 'stats', 'recentOrders'));
    }

    // ============ SERVICES MANAGEMENT ============

    /**
     * List all services
     */
    public function servicesIndex(Request $request)
    {
        $query = LaundryService::with('category');
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $services = $query->withCount('orderItems')
            ->latest()
            ->paginate(15)
            ->appends($request->query());
            
        $categories = Category::orderBy('name')->get();
        
        return view('admin.services.index', compact('services', 'categories'));
    }

    /**
     * Store new service
     */
    public function servicesStore(Request $request)
    {
        if (!Category::exists()) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Please create a category first before adding services!');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'unit' => 'required|string|in:kg,piece,item',
            'description' => 'nullable|string|max:1000',
            'short_description' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|integer|min:1',
            'min_estimated_hours' => 'nullable|integer|min:1',
            'max_estimated_hours' => 'nullable|integer|gt:min_estimated_hours',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'requires_pressing' => 'boolean',
            'requires_dry_cleaning' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['requires_pressing'] = $request->has('requires_pressing');
        $validated['requires_dry_cleaning'] = $request->has('requires_dry_cleaning');
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }
        
        LaundryService::create($validated);
        
        return redirect()->route('admin.services.index')
            ->with('success', "Service \"{$validated['name']}\" added successfully!");
    }

    /**
     * Show edit service form
     */
    public function servicesEdit($id)
    {
        $service = LaundryService::with('category')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update service
     */
    public function servicesUpdate(Request $request, $id)
    {
        $service = LaundryService::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'unit' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'short_description' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|integer|min:1',
            'min_estimated_hours' => 'nullable|integer|min:1',
            'max_estimated_hours' => 'nullable|integer|gt:min_estimated_hours',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'requires_pressing' => 'boolean',
            'requires_dry_cleaning' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['requires_pressing'] = $request->has('requires_pressing');
        $validated['requires_dry_cleaning'] = $request->has('requires_dry_cleaning');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }
        
        $service->update($validated);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Delete service
     */
    public function servicesDestroy($id)
    {
        $service = LaundryService::findOrFail($id);
        
        if ($service->orderItems()->count() > 0) {
            return back()->with('error', 'Cannot delete service with existing orders. Deactivate it instead.');
        }
        
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    // ============ CATEGORIES MANAGEMENT ============

    /**
     * List all categories
     */
    public function categoriesIndex()
    {
        $categories = Category::withCount('services')
            ->latest()
            ->paginate(15);
            
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store new category
     */
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
        ]);
        
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Update category
     */
    public function categoriesUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
        ]);
        
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category
     */
    public function categoriesDestroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->services()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing services. Remove or reassign services first.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // ============ PROMOTIONS MANAGEMENT ============

    /**
     * List all promotions
     */
    public function promotionsIndex()
    {
        $promotions = Promotion::withCount('orders')
            ->latest()
            ->paginate(15);
            
        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show create promotion form
     */
    public function promotionsCreate()
    {
        return view('admin.promotions.create');
    }

    /**
     * Store new promotion
     */
    public function promotionsStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promotions|max:50|alpha_dash',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed_amount,free_delivery',
            'value' => 'required_if:type,percentage,fixed_amount|nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);
        $validated['used_count'] = 0;
        
        Promotion::create($validated);
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion created successfully!');
    }

    /**
     * Show edit promotion form
     */
    public function promotionsEdit($id)
    {
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update promotion
     */
    public function promotionsUpdate(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|alpha_dash|unique:promotions,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed_amount,free_delivery',
            'value' => 'required_if:type,percentage,fixed_amount|nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);
        
        $promotion->update($validated);
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion updated successfully!');
    }

    /**
     * Delete promotion
     */
    public function promotionsDestroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion deleted successfully!');
    }

    // ============ REPORTS ============

    /**
     * Reports dashboard
     */
    public function reportsIndex(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $monthlyRevenue = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(CASE WHEN status IN ("delivered", "completed") THEN total ELSE 0 END) as revenue'),
            DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled'),
            DB::raw('AVG(CASE WHEN status IN ("delivered", "completed") THEN total ELSE NULL END) as avg_order_value')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();
        
        $topCustomers = User::where('role', 'customer')
            ->withCount(['orders as total_orders'])
            ->withSum(['orders as total_spent' => function($q) {
                $q->whereIn('status', ['delivered', 'completed']);
            }], 'total')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
        
        $popularServices = LaundryService::withCount('orderItems')
            ->withSum('orderItems', 'total')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();
            
        $riderPerformance = User::where('role', 'rider')
            ->withCount(['riderOrders as completed_deliveries' => function($q) {
                $q->whereIn('status', ['delivered', 'completed']);
            }])
            ->withCount(['riderOrders as active_deliveries' => function($q) {
                $q->whereIn('status', ['confirmed', 'processing', 'out_for_delivery']);
            }])
            ->orderBy('completed_deliveries', 'desc')
            ->limit(10)
            ->get();
            
        $dailyStats = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as orders'),
            DB::raw('SUM(total) as revenue')
        )
        ->whereDate('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        return view('admin.reports.index', compact(
            'monthlyRevenue', 
            'topCustomers', 
            'popularServices',
            'riderPerformance',
            'dailyStats',
            'period'
        ));
    }

    // ============ SETTINGS ============

    /**
     * Settings page
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'delivery_fee' => 'required|numeric|min:0',
            'express_delivery_fee' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_delivery_days' => 'nullable|integer|min:1|max:30',
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string|max:50',
            'business_hours_start' => 'nullable|date_format:H:i',
            'business_hours_end' => 'nullable|date_format:H:i',
        ]);
        
        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }
        
        Log::info('Settings updated by admin', [
            'admin_id' => auth()->id(),
            'settings' => $validated
        ]);
        
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }
}