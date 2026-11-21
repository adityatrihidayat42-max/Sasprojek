<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/menu/{product:slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.add');
Route::patch('/cart/items/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/items/{id}', [CartController::class, 'destroy'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:20,1')->name('checkout.store');

Route::get('/scan', [OrderController::class, 'scan'])->name('scan');
Route::post('/scan', [OrderController::class, 'scanGo'])->name('scan.go');
Route::get('/order/table/{token}', [OrderController::class, 'table'])->name('table.order');
Route::get('/order/{number}', [OrderController::class, 'show'])->middleware('throttle:60,1')->name('order.show');

Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions');
Route::get('/promotions/{promotion:slug}', [PromotionController::class, 'show'])->name('promotions.show');
Route::get('/reservasi', [ReservationController::class, 'create'])->name('reservation');
Route::post('/reservasi', [ReservationController::class, 'store'])->middleware('throttle:10,1')->name('reservation.store');

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,cashier,kitchen'])->group(function () {
    Route::get('dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders');

    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('orders/scan', [Admin\OrderController::class, 'scan'])->name('orders.scan');
        Route::post('orders/scan/lookup', [Admin\OrderController::class, 'lookup'])->middleware('throttle:30,1')->name('orders.scan.lookup');
        Route::patch('orders/{order}/payment', [Admin\OrderController::class, 'updatePayment'])->name('orders.payment');
        Route::get('reservations', [Admin\ReservationController::class, 'index'])->name('reservations');
        Route::patch('reservations/{reservation}', [Admin\ReservationController::class, 'update'])->name('reservations.update');
    });

    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

    Route::middleware('role:admin')->group(function () {
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('tables', Admin\TableController::class)->parameters(['tables' => 'cafeTable'])->only(['index', 'store', 'update', 'destroy']);
        Route::post('tables/{cafeTable}/regenerate', [Admin\TableController::class, 'regenerate'])->name('tables.regenerate');
        Route::get('tables/{cafeTable}/print', [Admin\TableController::class, 'print'])->name('tables.print');
        Route::resource('promotions', Admin\PromotionController::class)->except('show');
        Route::resource('staff', Admin\StaffController::class)->parameters(['staff' => 'user'])->only(['index', 'store', 'destroy']);
    });
});
