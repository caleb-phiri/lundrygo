<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\TrackingController as CustomerTrackingController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Rider\RiderOrderController;
use App\Http\Controllers\Rider\RiderTrackingController;
use App\Http\Controllers\Rider\RiderProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============ PUBLIC ROUTES ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/how-it-works', [HomeController::class, 'howItWorks'])->name('how-it-works');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// ============ GUEST ROUTES (Authentication) ============
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'reset'])->name('password.update');
});

// ============ AUTHENTICATED ROUTES (Base) ============
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// ============ CUSTOMER ROUTES ============
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerOrderController::class, 'dashboard'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [CustomerTrackingController::class, 'track'])->name('orders.tracking');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/reorder', [CustomerOrderController::class, 'reorder'])->name('orders.reorder');
    
    // New Order
    Route::get('/new-order', [CustomerOrderController::class, 'create'])->name('orders.create');
    Route::post('/new-order', [CustomerOrderController::class, 'store'])->name('orders.store');
    
    // Profile & Addresses
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/addresses', [CustomerProfileController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [CustomerProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [CustomerProfileController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{address}/default', [CustomerProfileController::class, 'setDefaultAddress'])->name('addresses.default');
    
    // Reviews
    Route::get('/reviews', [CustomerReviewController::class, 'index'])->name('reviews');
    Route::post('/orders/{order}/review', [CustomerReviewController::class, 'store'])->name('reviews.store');
    
    // Promotions
    Route::get('/promotions', [CustomerProfileController::class, 'promotions'])->name('promotions');
});

// ============ RIDER ROUTES ============
Route::prefix('rider')->name('rider.')->middleware(['auth', 'role:rider'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [RiderOrderController::class, 'dashboard'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [RiderOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [RiderOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [RiderOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/status', [RiderOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/complete', [RiderOrderController::class, 'complete'])->name('orders.complete');
    
    // Tracking
    Route::get('/tracking', [RiderTrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/location', [RiderTrackingController::class, 'updateLocation'])->name('tracking.location');
    Route::post('/tracking/online', [RiderTrackingController::class, 'goOnline'])->name('tracking.online');
    Route::post('/tracking/offline', [RiderTrackingController::class, 'goOffline'])->name('tracking.offline');
    
    // Earnings & Profile
    Route::get('/earnings', [RiderProfileController::class, 'earnings'])->name('earnings');
    Route::get('/profile', [RiderProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [RiderProfileController::class, 'update'])->name('profile.update');
});

// ============ ADMIN ROUTES ============
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super_admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [AdminController::class, 'dashboard'])->name('index');
    
    // User Management
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    
    // Orders Management
    Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/{id}', [AdminController::class, 'ordersShow'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminController::class, 'ordersUpdateStatus'])->name('orders.update-status');
    
    // Services Management
    Route::get('/services', [AdminController::class, 'servicesIndex'])->name('services.index');
    Route::post('/services', [AdminController::class, 'servicesStore'])->name('services.store');
    Route::put('/services/{id}', [AdminController::class, 'servicesUpdate'])->name('services.update');
    Route::delete('/services/{id}', [AdminController::class, 'servicesDestroy'])->name('services.destroy');
    
    // Categories Management
    Route::get('/categories', [AdminController::class, 'categoriesIndex'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'categoriesStore'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'categoriesDestroy'])->name('categories.destroy');
    
    // Promotions Management
    Route::get('/promotions', [AdminController::class, 'promotionsIndex'])->name('promotions.index');
    Route::get('/promotions/create', [AdminController::class, 'promotionsCreate'])->name('promotions.create');
    Route::post('/promotions', [AdminController::class, 'promotionsStore'])->name('promotions.store');
    Route::get('/promotions/{id}/edit', [AdminController::class, 'promotionsEdit'])->name('promotions.edit');
    Route::put('/promotions/{id}', [AdminController::class, 'promotionsUpdate'])->name('promotions.update');
    Route::delete('/promotions/{id}', [AdminController::class, 'promotionsDestroy'])->name('promotions.destroy');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reportsIndex'])->name('reports.index');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// ============ CHECKOUT & PAYMENT ROUTES ============
Route::middleware('auth')->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{order}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    
    // Payment
    Route::post('/payment/intent', [PaymentController::class, 'createIntent'])->name('payment.intent');
    Route::post('/payment/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
});