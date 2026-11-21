<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $lines = Cart::lines();
        $totals = Cart::totals($lines);
        return view('cart', compact('lines', 'totals'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1|max:99',
            'note' => 'nullable|string|max:150',
        ]);

        $p = Product::find($d['product_id']);
        if (!$p->is_active || !$p->is_available) {
            return back()->withErrors(['cart' => $p->name . ' sedang tidak tersedia.']);
        }

        Cart::add($p->id, $d['qty'] ?? 1, $d['note'] ?? null);
        return back()->with('success', $p->name . ' ditambahkan ke keranjang.');
    }

    public function update(Request $r, int $id)
    {
        $d = $r->validate(['qty' => 'required|integer|min:0|max:99', 'note' => 'nullable|string|max:150']);
        Cart::set($id, $d['qty'], $d['note'] ?? null);
        return redirect()->route('cart');
    }

    public function destroy(int $id)
    {
        Cart::remove($id);
        return redirect()->route('cart')->with('success', 'Item dihapus dari keranjang.');
    }
}
