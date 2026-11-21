<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'revenueToday' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('grand_total'),
            'pending' => Order::where('order_status', 'pending')->count(),
            'active' => Order::whereIn('order_status', ['confirmed', 'preparing', 'ready'])->count(),
            'reservations' => Reservation::where('status', 'pending')->count(),
            'products' => Product::where('is_active', true)->count(),
            'recent' => Order::with('cafeTable')->latest()->limit(8)->get(),
        ]);
    }
}
