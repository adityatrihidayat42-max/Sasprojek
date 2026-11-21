<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $q = Order::with('cafeTable')->latest();
        if ($r->filled('status')) {
            $q->where('order_status', $r->input('status'));
        }
        if ($r->filled('payment')) {
            $q->where('payment_status', $r->input('payment'));
        }
        if ($r->filled('type')) {
            $q->where('order_type', $r->input('type'));
        }
        if ($r->filled('date')) {
            $q->whereDate('created_at', $r->input('date'));
        }
        if ($r->filled('q')) {
            $s = $r->input('q');
            $q->where(fn ($x) => $x->where('order_number', 'like', "%{$s}%")->orWhere('customer_name', 'like', "%{$s}%"));
        }
        $orders = $q->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'cafeTable', 'histories.user']);
        $statusOptions = auth()->user()->role === 'kitchen'
            ? array_intersect_key(Order::STATUSES, array_flip(['preparing', 'ready']))
            : Order::STATUSES;
        return view('admin.orders.show', compact('order', 'statusOptions'));
    }

    public function updateStatus(Request $r, Order $order)
    {
        $d = $r->validate(['order_status' => ['required', Rule::in(array_keys(Order::STATUSES))]]);
        if (auth()->user()->role === 'kitchen' && !in_array($d['order_status'], ['preparing', 'ready'], true)) {
            abort(403);
        }
        if ($d['order_status'] !== $order->order_status) {
            $order->histories()->create([
                'user_id' => auth()->id(), 'type' => 'status',
                'old_value' => $order->order_status, 'new_value' => $d['order_status'],
            ]);
            $order->update(['order_status' => $d['order_status']]);
        }
        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function updatePayment(Request $r, Order $order)
    {
        $d = $r->validate(['payment_status' => ['required', Rule::in(array_keys(Order::PAYMENTS))]]);
        if ($d['payment_status'] !== $order->payment_status) {
            $order->histories()->create([
                'user_id' => auth()->id(), 'type' => 'payment',
                'old_value' => $order->payment_status, 'new_value' => $d['payment_status'],
            ]);
            $order->update(['payment_status' => $d['payment_status']]);
        }
        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
