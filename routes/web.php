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
use App\Http\Controllers\Admin\TradeAccountController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\TradePortalController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\RoomVisualizationController;
use App\Http\Controllers\ImageController;

// Serve uploaded images directly from storage/app/public (bypasses broken symlinks on shared hosting)
Route::get('/media/{path}', [ImageController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// Lightweight health check — keeps PHP-FPM alive on shared hosting
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]);
})->name('health');

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

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
Route::post('/cart/options', [CartController::class, 'options'])->name('cart.options');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Product estimates, room visualization & sample requests
Route::post('/products/{product}/estimate/email', [EstimateController::class, 'email'])->name('estimate.email');
Route::post('/products/{product}/estimate/save', [EstimateController::class, 'save'])->name('estimate.save');
Route::post('/products/{product}/room-visualize', [RoomVisualizationController::class, 'store'])->name('room.visualize');
Route::get('/room-visualizations', [RoomVisualizationController::class, 'history'])->name('room.visualizations');
Route::post('/products/{product}/sample-request', [\App\Http\Controllers\SampleRequestController::class, 'createFromProduct'])->name('sample.request.product');
Route::post('/sample-requests', [\App\Http\Controllers\SampleRequestController::class, 'store'])->name('sample.request.store');

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
    Route::get('/export/csv', [AdminDashboardController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [AdminDashboardController::class, 'exportPdf'])->name('export.pdf');

    Route::get('/products/import', [ProductImportController::class, 'showForm'])->name('products.import');
    Route::post('/products/import', [ProductImportController::class, 'import'])->name('products.import.store');
    Route::get('/products/import/template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');
    // Enhanced Product Management (define BEFORE resource routes to avoid conflicts)
    Route::post('/products/bulk-edit', [AdminProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::put('/products/bulk-update', [AdminProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::delete('/products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
    Route::post('/products/bulk-toggle-status', [AdminProductController::class, 'bulkToggleStatus'])->name('products.bulk-toggle-status');
    Route::get('/products/export/csv', [AdminProductController::class, 'export'])->name('products.export');
    Route::post('/products/import-csv', [AdminProductController::class, 'import'])->name('products.import-csv');
    
    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/duplicate', [AdminProductController::class, 'duplicate'])->name('products.duplicate');
    Route::delete('/products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::get('/products/{product}/images/{image}', fn($product, $image) => redirect()->route('admin.products.edit', ['product' => $product])->with('error', 'Session expired. Please try again.'));
    Route::get('/products/{product}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');
    
    // Single product status toggle
    Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    
    // Filter Attributes Management
    Route::get('/product-filters', [AdminProductController::class, 'filterAttributes'])->name('product-filters.index');
    Route::post('/product-filters', [AdminProductController::class, 'storeFilterAttribute'])->name('product-filters.store');
    Route::post('/product-filters/{attribute}/values', [AdminProductController::class, 'storeFilterValue'])->name('product-filters.store-value');
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::resource('customers', AdminCustomerController::class)->only(['index', 'show']);
    Route::resource('coupons', AdminCouponController::class);
    Route::get('/filters', [AdminFilterController::class, 'index'])->name('filters.index');
    Route::put('/filters', [AdminFilterController::class, 'update'])->name('filters.update');

    // Trade Accounts
    Route::get('/trade-accounts', [TradeAccountController::class, 'index'])->name('trade-accounts.index');
    Route::put('/trade-accounts/{user}', [TradeAccountController::class, 'update'])->name('trade-accounts.update');
    Route::patch('/trade-accounts/{user}/toggle', [TradeAccountController::class, 'toggleTrade'])->name('trade-accounts.toggle');

    // Submissions
    Route::get('/submissions/estimates', [SubmissionController::class, 'estimates'])->name('submissions.estimates');
    Route::get('/submissions/visualizations', [SubmissionController::class, 'visualizations'])->name('submissions.visualizations');
    Route::get('/submissions/samples', [SubmissionController::class, 'samples'])->name('submissions.samples');
});

// Trade Portal routes — requires trade (or admin/team) account
Route::prefix('trade-portal')->name('trade.portal.')->middleware(['auth:sanctum', config('jetstream.auth_session'), 'trade'])->group(function () {
    Route::get('/',         [TradePortalController::class, 'dashboard'])->name('dashboard');

    // Projects
    Route::get('/projects',              [TradePortalController::class, 'projects'])->name('projects');
    Route::get('/projects/create',       [TradePortalController::class, 'createProject'])->name('projects.create');
    Route::post('/projects',             [TradePortalController::class, 'storeProject'])->name('projects.store');
    Route::get('/projects/{project}/edit',[TradePortalController::class, 'editProject'])->name('projects.edit');
    Route::put('/projects/{project}',    [TradePortalController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [TradePortalController::class, 'destroyProject'])->name('projects.destroy');

    // Quotes
    Route::get('/quotes',            [TradePortalController::class, 'quotes'])->name('quotes');
    Route::get('/quotes/create',     [TradePortalController::class, 'createQuote'])->name('quotes.create');
    Route::post('/quotes',           [TradePortalController::class, 'storeQuote'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit',[TradePortalController::class, 'editQuote'])->name('quotes.edit');
    Route::put('/quotes/{quote}',    [TradePortalController::class, 'updateQuote'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [TradePortalController::class, 'destroyQuote'])->name('quotes.destroy');
    Route::get('/quotes/{quote}/print',[TradePortalController::class, 'printQuote'])->name('quotes.print');

    Route::get('/samples',            [TradePortalController::class, 'samples'])->name('samples');
    Route::get('/samples/create',     [TradePortalController::class, 'createSample'])->name('samples.create');
    Route::get('/orders',   [TradePortalController::class, 'orders'])->name('orders');
    Route::get('/account',  [TradePortalController::class, 'account'])->name('account');
});
