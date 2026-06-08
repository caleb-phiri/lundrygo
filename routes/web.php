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
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
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
Route::prefix('customer')->name('customer.')->middleware(['auth'])->group(function () {
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
Route::prefix('rider')->name('rider.')->middleware(['auth'])->group(function () {
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

// ============ ADMIN ROUTES - NO MIDDLEWARE RESTRICTIONS ============
Route::prefix('admin')->name('admin.')->group(function () {
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
// ============ ADMIN ROUTES ============
Route::prefix('admin')->name('admin.')->group(function () {
    // ... existing routes
    
    // Rider Assignment Routes
    Route::get('/orders/{id}/assign-rider', [AdminController::class, 'assignRiderForm'])->name('orders.assign-rider');
    Route::post('/orders/{id}/assign-rider', [AdminController::class, 'assignRider'])->name('orders.assign-rider');
    Route::delete('/orders/{id}/unassign-rider', [AdminController::class, 'unassignRider'])->name('orders.unassign-rider');
    Route::get('/riders/available', [AdminController::class, 'getAvailableRiders'])->name('riders.available');
});

// Make sure these routes exist in your admin section
Route::prefix('admin')->name('admin.')->group(function () {
    // ... other routes
    
    // Rider Assignment Routes - Make sure these are inside the admin group
    Route::get('/orders/{id}/assign-rider', [AdminController::class, 'assignRiderForm'])->name('orders.assign-rider');
    Route::post('/orders/{id}/assign-rider', [AdminController::class, 'assignRider'])->name('orders.assign-rider');
    Route::delete('/orders/{id}/unassign-rider', [AdminController::class, 'unassignRider'])->name('orders.unassign-rider');
});

// Change this line in your admin routes
Route::get('/orders/{id}/assign-rider', [AdminController::class, 'showAssignRiderForm'])->name('orders.assign-rider');

Route::post('/orders/{id}/assign-rider', [AdminController::class, 'assignRiderToOrder'])->name('orders.assign-rider-to-order');

// ============ ADMIN ROUTES ============
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // ... your existing routes ...
    
    // Rider Assignment Routes - Using the new controller
    Route::get('/orders/{id}/assign-rider', [\App\Http\Controllers\RiderAssignmentController::class, 'showForm'])->name('orders.assign-rider');
    Route::post('/orders/{id}/assign-rider', [\App\Http\Controllers\RiderAssignmentController::class, 'assign'])->name('orders.assign-rider');
    Route::delete('/orders/{id}/unassign-rider', [\App\Http\Controllers\RiderAssignmentController::class, 'unassign'])->name('orders.unassign-rider');
});
// ============ RIDER ROUTES ============
Route::prefix('rider')->name('rider.')->middleware(['auth', 'role:rider'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Rider\RiderController::class, 'dashboard'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Rider\RiderOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Rider\RiderOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/accept', [App\Http\Controllers\Rider\RiderController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{id}/pickup', [App\Http\Controllers\Rider\RiderController::class, 'markAsPickedUp'])->name('orders.pickup');
    Route::post('/orders/{id}/deliver', [App\Http\Controllers\Rider\RiderController::class, 'markAsDelivered'])->name('orders.deliver');
    
    // Tracking
    Route::post('/tracking/update-location', [App\Http\Controllers\Rider\RiderController::class, 'updateLocation'])->name('tracking.update-location');
});

// ============ RIDER ROUTES ============
Route::prefix('rider')->name('rider.')->middleware(['auth', 'role:rider'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Rider\RiderController::class, 'dashboard'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Rider\RiderOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\Rider\RiderOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [App\Http\Controllers\Rider\RiderOrderController::class, 'accept'])->name('orders.accept');
    
    // Order Actions
    Route::post('/orders/{order}/pickup', [App\Http\Controllers\Rider\RiderController::class, 'markAsPickedUp'])->name('orders.pickup');
    Route::post('/orders/{order}/deliver', [App\Http\Controllers\Rider\RiderController::class, 'markAsDelivered'])->name('orders.deliver');
    
    // Tracking
    Route::post('/tracking/update-location', [App\Http\Controllers\Rider\RiderController::class, 'updateLocation'])->name('tracking.update-location');
    Route::get('/tracking', [App\Http\Controllers\Rider\RiderController::class, 'tracking'])->name('tracking');
    
    // Earnings & Profile
    Route::get('/earnings', [App\Http\Controllers\Rider\RiderController::class, 'earnings'])->name('earnings');
    Route::get('/profile', [App\Http\Controllers\Rider\RiderController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Rider\RiderController::class, 'updateProfile'])->name('profile.update');
});

Route::post('/tracking/update-location', [RiderController::class, 'updateLocation'])->name('tracking.update-location');

Route::put('/addresses/{address}', [CustomerProfileController::class, 'updateAddress'])->name('addresses.update');

Route::prefix('customer')->name('customer.')->middleware(['auth'])->group(function () {
    // Addresses
    Route::get('/addresses', [CustomerProfileController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [CustomerProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{id}', [CustomerProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{id}', [CustomerProfileController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{id}/default', [CustomerProfileController::class, 'setDefaultAddress'])->name('addresses.default');
});




Route::middleware(['auth'])->group(function () {
    // Student Resource Routes
    Route::resource('students', StudentController::class);
    
    // Additional Student Routes
    Route::get('students/export/csv', [StudentController::class, 'export'])->name('students.export');
    Route::post('students/bulk-upload', [StudentController::class, 'bulkUpload'])->name('students.bulk-upload');
    Route::get('students/report/academic', [StudentController::class, 'academicReport'])->name('students.report');
});

// API Routes for AJAX
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('students/search', [StudentController::class, 'searchAjax'])->name('api.students.search');
    Route::get('students/stats', [StudentController::class, 'getStats'])->name('api.students.stats');
});