<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(string $number)
    {
        $order = Order::with(['items', 'cafeTable'])->where('order_number', $number)->firstOrFail();
        return view('order.show', compact('order'));
    }

    public function table(string $token)
    {
        $table = CafeTable::where('qr_token', $token)->where('is_active', true)->first();
        if (!$table) {
            return response()->view('order.invalid', [], 404);
        }
        session(['table_token' => $table->qr_token]);
        return redirect()->route('menu')->with('success', 'Selamat datang! Anda memesan dari Meja ' . $table->table_number . '.');
    }

    public function scan()
    {
        return view('scan');
    }

    public function scanGo(Request $r)
    {
        $d = $r->validate(['table_number' => 'required|string|max:20']);
        $table = CafeTable::where('table_number', $d['table_number'])->where('is_active', true)->first();
        if (!$table) {
            return back()->withErrors(['table_number' => 'Nomor meja tidak ditemukan.'])->withInput();
        }
        return redirect()->route('table.order', $table->qr_token);
    }
}
