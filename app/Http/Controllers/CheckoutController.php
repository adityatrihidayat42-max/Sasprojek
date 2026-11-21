<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show()
    {
        $lines = Cart::lines();
        if (!$lines) {
            return redirect()->route('cart');
        }
        $totals = Cart::totals($lines);
        $table = Cart::table();
        $token = (string) Str::uuid();
        session(['checkout_token' => $token]);

        return view('checkout', compact('lines', 'totals', 'table', 'token'));
    }

    public function store(Request $r)
    {
        $lines = Cart::lines();
        if (!$lines) {
            return redirect()->route('cart');
        }
        $table = Cart::table();

        $data = $r->validate([
            'customer_name' => 'required|string|max:80',
            'customer_phone' => [$table ? 'nullable' : 'required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'pickup_at' => 'nullable|date|after:now',
            'customer_note' => 'nullable|string|max:500',
            'token' => 'required|string',
        ]);

        // cegah pesanan ganda (double submit)
        if ($data['token'] !== session()->pull('checkout_token')) {
            return redirect()->route('cart')->withErrors(['cart' => 'Formulir sudah dikirim atau kedaluwarsa. Periksa kembali pesanan Anda.']);
        }

        foreach ($lines as $l) {
            if (!$l['product']->is_available) {
                return redirect()->route('cart')->withErrors(['cart' => $l['product']->name . ' sedang tidak tersedia. Hapus dari keranjang.']);
            }
        }

        $order = DB::transaction(function () use ($data, $lines, $table) {
            $t = Cart::totals($lines);
            $order = Order::create([
                'order_number' => $this->number(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'order_type' => $table ? 'dine_in' : 'preorder',
                'cafe_table_id' => $table?->id,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'subtotal' => $t['subtotal'],
                'discount_total' => $t['discount'],
                'grand_total' => $t['total'],
                'promotion_id' => $t['promo']?->id,
                'promotion_name' => $t['promo']?->name,
                'customer_note' => $data['customer_note'] ?? null,
                'pickup_at' => $table ? null : ($data['pickup_at'] ?? null),
            ]);

            foreach ($lines as $l) {
                $order->items()->create([
                    'product_id' => $l['product']->id,
                    'product_name' => $l['product']->name,
                    'unit_price' => $l['product']->price,
                    'quantity' => $l['qty'],
                    'line_total' => $l['total'],
                    'note' => $l['note'],
                ]);
            }

            $order->histories()->create(['type' => 'status', 'old_value' => null, 'new_value' => 'pending']);
            return $order;
        });

        Cart::clear();
        return redirect()->route('order.show', $order->order_number)->with('success', 'Pesanan berhasil dikirim!');
    }

    private function number(): string
    {
        do {
            $no = 'BN' . now()->format('ymd') . strtoupper(Str::random(4));
        } while (Order::where('order_number', $no)->exists());
        return $no;
    }
}
