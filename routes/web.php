<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\FilterController as AdminFilterController;
use App\Http\Controllers\TradePortalController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/weave', [PageController::class, 'weave'])->name('weave');
Route::post('/weave', [PageController::class, 'weaveSubmit'])->name('weave.submit');
Route::get('/trade', [PageController::class, 'trade'])->name('trade');
Route::get('/services', [PageController::class, 'services'])->name('services');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Auth-required routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Client Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/orders', [OrderController::class, 'index'])->name('dashboard.orders');
    Route::get('/dashboard/orders/{order}', [OrderController::class, 'show'])->name('dashboard.orders.show');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Order confirmation
    Route::get('/orders/{order}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::resource('customers', AdminCustomerController::class)->only(['index', 'show']);
    Route::resource('coupons', AdminCouponController::class);
    Route::get('/filters', [AdminFilterController::class, 'index'])->name('filters.index');
    Route::put('/filters', [AdminFilterController::class, 'update'])->name('filters.update');
});

// Trade Portal routes — requires trade (or admin/team) account
Route::prefix('trade-portal')->name('trade.portal.')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'trade'])->group(function () {
    Route::get('/',         [TradePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [TradePortalController::class, 'projects'])->name('projects');
    Route::get('/quotes',   [TradePortalController::class, 'quotes'])->name('quotes');
    Route::get('/samples',  [TradePortalController::class, 'samples'])->name('samples');
    Route::get('/orders',   [TradePortalController::class, 'orders'])->name('orders');
    Route::get('/account',  [TradePortalController::class, 'account'])->name('account');
});
