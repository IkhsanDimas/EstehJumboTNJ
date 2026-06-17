<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'menu'])->name('menu');
Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/menu/{product}/data', [MenuController::class, 'productData'])->name('menu.product-data');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'data'])->name('cart.data');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])
    ->name('checkout.success');
Route::get('/lacak/{order_number?}', [CheckoutController::class, 'track'])
    ->name('order.track');

// Admin Auth
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Protected Admin Panel Group
Route::middleware(['auth', \App\Http\Middleware\EnsureIsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/settings/visuals', [AdminController::class, 'updateVisualSettings'])->name('admin.settings.visuals');
    Route::post('/change-password', [AdminController::class, 'changePassword'])->name('admin.change-password');
    
    // Product CRUD
    Route::resource('/products', AdminProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);

    // Order Management
    Route::post('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
    Route::get('/orders/latest-id', [AdminController::class, 'getLatestOrderId'])->name('admin.orders.latest-id');
    
    // Global settings toggles
    Route::post('/settings/toggle', [AdminController::class, 'toggleStoreSetting'])->name('admin.settings.toggle');
    
    // Ingredients & Inventory Management
    Route::post('/ingredients/{ingredient}/replenish', [AdminController::class, 'replenishIngredient'])->name('admin.ingredients.replenish');
    Route::post('/ingredients/{ingredient}/update', [AdminController::class, 'updateIngredient'])->name('admin.ingredients.update');
});