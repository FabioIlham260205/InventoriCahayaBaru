<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShopAuthController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/shop/login');

Route::get('/shop/login', [ShopAuthController::class, 'showLogin'])->name('shop.login');
Route::post('/shop/login', [ShopAuthController::class, 'redirectToGoogle'])->name('shop.login.store');

Route::middleware('shop.auth')->group(function (): void {
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::get('/shop/checkout', [ShopController::class, 'checkoutPage'])->name('shop.checkout.page');
    Route::post('/shop/cart/{fruit}', [ShopController::class, 'addToCart'])->name('shop.cart.add');
    Route::patch('/shop/cart', [ShopController::class, 'updateCart'])->name('shop.cart.update');
    Route::post('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
    Route::get('/shop/payment/{order}', [ShopController::class, 'showPayment'])->name('shop.payment.show');
    Route::get('/shop/payment/{order}/finish', [ShopController::class, 'finishPayment'])->name('shop.payment.finish');
    Route::post('/shop/logout', [ShopAuthController::class, 'logout'])->name('shop.logout');
});
Route::post('/payment/midtrans/notification', [ShopController::class, 'paymentNotification'])->name('payment.midtrans.notification');
Route::post('/payment/doku/notification', [ShopController::class, 'paymentNotificationDoku'])->name('payment.doku.notification');

Route::get('/inventory/login', [AuthController::class, 'showLogin'])->name('inventory.login');
Route::post('/inventory/login', [AuthController::class, 'redirectToGoogle'])->name('inventory.login.store');
Route::get('/oauth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('inventory.oauth.google.callback');
Route::post('/inventory/logout', [AuthController::class, 'logout'])->name('inventory.logout');

Route::middleware('inventory.auth')->group(function (): void {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/fruits', [InventoryController::class, 'storeFruit'])->name('fruits.store');
    Route::post('/inventory/stock-movements', [InventoryController::class, 'storeMovement'])->name('stock-movements.store');
    Route::patch('/inventory/alerts/{alert}/read', [InventoryController::class, 'markAlertRead'])->name('alerts.read');
    Route::get('/inventory/detail', [InventoryController::class, 'detail'])->name('dashboard.detail');
    Route::get('/inventory/dashboard', [InventoryController::class, 'detail']);

    Route::get('/inventory/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/inventory/reports/print', [ReportController::class, 'print'])->name('reports.print');

    Route::get('/inventory/communication', [CommunicationController::class, 'index'])->name('communication.index');
    Route::post('/inventory/communication', [CommunicationController::class, 'store'])->name('communication.store');

    Route::get('/inventory/orders', [ShopController::class, 'orders'])->name('orders.index');
    Route::patch('/inventory/orders/{order}/status', [ShopController::class, 'updateOrderStatus'])->name('orders.status');
});
