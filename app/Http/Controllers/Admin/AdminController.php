<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\LaundryService;
use App\Models\Category;
use App\Models\Promotion;
use App\Models\Appraisal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_riders' => User::where('role', 'rider')->count(),
            'total_services' => LaundryService::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
        ];

        // Chart Data (Last 30 Days)
        $chartData = $this->getChartData();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'chartData', 'recentOrders'));
    }

    private function getChartData()
    {
        $labels = [];
        $ordersData = [];
        $revenueData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $ordersData[] = Order::whereDate('created_at', $date)->count();
            $revenueData[] = Order::whereDate('created_at', $date)
                ->where('status', 'delivered')
                ->sum('total');
        }

        return [
            'labels' => $labels,
            'orders' => $ordersData,
            'revenue' => $revenueData,
        ];
    }

    // User Management
    public function usersIndex(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        $users = $query->latest()->paginate(20);
        
        $stats = [
            'total' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'supervisors' => User::where('role', 'supervisor')->count(),
            'riders' => User::where('role', 'rider')->count(),
            'active' => User::where('is_active', true)->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    public function usersCreate()
    {
        return view('admin.users.create');
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:customer,admin,supervisor,rider',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        
        $validated['password'] = bcrypt($validated['password']);
        
        User::create($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function usersEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function usersUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:customer,admin,supervisor,rider',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        
        $user->update($validated);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function usersDestroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    // Orders Management
    public function ordersIndex(Request $request)
    {
        $query = Order::with('user');
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }
        
        $orders = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function ordersShow($id)
    {
        $order = Order::with(['user', 'items.service'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function ordersUpdateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);
        
        $order->update([
            'status' => $request->status,
            'delivered_at' => $request->status === 'delivered' ? now() : $order->delivered_at,
        ]);
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated!');
    }

    // Services Management
    public function servicesIndex()
    {
        $services = LaundryService::with('category')->latest()->paginate(15);
        $categories = Category::all();
        
        return view('admin.services.index', compact('services', 'categories'));
    }

    public function servicesStore(Request $request)
{
    // First check if any categories exist
    $categoryExists = Category::exists();
    
    if (!$categoryExists) {
        return redirect()->route('admin.services.index')
            ->with('error', 'Please create a category first before adding services!')
            ->withInput();
    }
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'unit' => 'required|string|in:kg,piece,item',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ]);
    
    // Double check the category exists
    $category = Category::find($validated['category_id']);
    if (!$category) {
        return back()->with('error', 'Selected category does not exist. Please select a valid category.')
                     ->withInput();
    }
    
    // Set is_active flag
    $validated['is_active'] = $request->has('is_active');
    
    // Create slug from name
    $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
    
    LaundryService::create($validated);
    
    return redirect()->route('admin.services.index')
        ->with('success', 'Service "' . $validated['name'] . '" added successfully!');
}

    public function servicesUpdate(Request $request, $id)
    {
        $service = LaundryService::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $service->update($validated);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function servicesDestroy($id)
    {
        $service = LaundryService::findOrFail($id);
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }

    // Categories Management
    public function categoriesIndex()
    {
        $categories = Category::withCount('services')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function categoriesDestroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->services()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing services!');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // Reports
    public function reportsIndex()
    {
        $monthlyRevenue = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
        ->where('status', 'delivered')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();
        
        $topCustomers = User::withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('orders_sum_total', 'desc')
            ->limit(10)
            ->get();
        
        $popularServices = LaundryService::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.reports.index', compact('monthlyRevenue', 'topCustomers', 'popularServices'));
    }
}