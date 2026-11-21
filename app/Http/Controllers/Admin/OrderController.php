<?php

namespace App\\Http\\Controllers\\Admin;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Order;
use Illuminate\\Http\\Request;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $user = $r->user();
        $q = Order::with('cafeTable')->latest();
        if ($user->role === 'kitchen') {
            $q->where('payment_status', 'paid')->whereIn('order_status', ['pending', 'confirmed', 'preparing']);
        } else {
            if ($r->filled('status')) $q->where('order_status', $r->input('status'));
            if ($r->filled('payment')) $q->where('payment_status', $r->input('payment'));
            if ($r->filled('type')) $q->where('order_type', $r->input('type'));
            if ($r->filled('date')) $q->whereDate('created_at', $r->input('date'));
            if ($r->filled('q')) {
                $s = $r->input('q');
                $q->where(fn ($x) => $x->where('order_number', 'like', "%{$s}%")->orWhere('customer_name', 'like', "%{$s}%"));
            }
        }
        $orders = $q->paginate(30)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'cafeTable', 'histories.user']);
        $role = auth()->user()->role;
        $statusOptions = $role === 'kitchen'
            ? (in_array($order->order_status, ['pending', 'confirmed']) && $order->payment_status === 'paid'
                ? ['preparing' => 'Sedang dibuat']
                : ($order->order_status === 'preparing' ? ['ready' => 'Siap Disajikan'] : []))
            : (in_array($role, ['admin', 'cashier']) && !in_array($order->order_status, ['completed', 'cancelled'])
                ? ['cancelled' => 'Batalkan pesanan']
                : []);
        return view('admin.orders.show', compact('order', 'statusOptions'));
    }

    public function scan()
    {
        return view('admin.orders.scan');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate(['order_number' => ['required', 'string', 'max:40']]);
        $order = Order::where('order_number', $data['order_number'])->first();
        if (!$order) return response()->json(['message' => 'Nomor pesanan tidak ditemukan. Periksa QR atau coba pindai ulang.'], 404);
        return response()->json([
            'redirect_url' => route('admin.orders.show', $order),
            'order' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'order_status' => $order->statusLabel(),
                'payment_status' => $order->paymentLabel(),
                'grand_total' => $order->grand_total,
            ],
        ]);
    }

    public function updateStatus(Request $r, Order $order)
    {
        $r->validate(['order_status' => ['required', 'in:preparing,ready,cancelled']]);
        $role = $r->user()->role;
        $requested = $r->string('order_status')->toString();

        if ($role === 'kitchen') {
            abort_unless($order->payment_status === 'paid', 403, 'Pesanan harus lunas sebelum masuk dapur.');
            if ($requested === 'preparing') {
                abort_unless(in_array($order->order_status, ['pending', 'confirmed']), 422, 'Status pesanan tidak sesuai.');
                $next = 'preparing';
            } else {
                abort_unless($requested === 'ready' && $order->order_status === 'preparing', 422, 'Pesanan belum diproses.');
                $next = 'completed';
            }
        } else {
            abort_unless(in_array($role, ['admin', 'cashier']), 403);
            abort_unless($requested === 'cancelled' && !in_array($order->order_status, ['completed', 'cancelled']), 422, 'Admin/kasir hanya dapat membatalkan pesanan aktif.');
            $next = 'cancelled';
        }

        if ($next !== $order->order_status) {
            $order->histories()->create([
                'user_id' => $r->user()->id,
                'type' => 'status',
                'old_value' => $order->order_status,
                'new_value' => $next,
            ]);
            $order->update(['order_status' => $next]);
        }
        return back()->with('success', $next === 'completed' ? 'Pesanan ditandai siap disajikan.' : 'Status pesanan diperbarui.');
    }

    public function updatePayment(Request $r, Order $order)
    {
        abort_unless(in_array($r->user()->role, ['admin', 'cashier']), 403);
        $r->validate(['payment_status' => ['required', 'in:paid,unpaid']]);
        abort_unless(($order->payment_method ?? 'cash') === 'cash', 422, 'QRIS hanya dapat dikonfirmasi otomatis oleh payment gateway.');
        abort_unless($order->order_status !== 'cancelled', 422, 'Pesanan sudah dibatalkan.');
        $next = $r->input('payment_status');
        if ($next === 'unpaid' && $order->payment_status === 'paid') {
            abort(422, 'Pembayaran yang sudah dikonfirmasi tidak dapat dibatalkan dari halaman ini.');
        }
        if ($next !== $order->payment_status) {
            $order->histories()->create([
                'user_id' => $r->user()->id,
                'type' => 'payment',
                'old_value' => $order->payment_status,
                'new_value' => $next,
            ]);
            $order->update(['payment_status' => $next, 'paid_at' => $next === 'paid' ? now() : null, 'paid_by' => $next === 'paid' ? $r->user()->id : null]);
        }
        return back()->with('success', $next === 'paid' ? 'Pembayaran tunai dikonfirmasi. Pesanan masuk antrean dapur.' : 'Status pembayaran diperbarui.');
    }
}
