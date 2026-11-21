<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index()
    {
        $tables = CafeTable::withCount('orders')->orderByRaw('CAST(table_number AS INTEGER), table_number')->get();
        return view('admin.tables', compact('tables'));
    }

    public function store(Request $r)
    {
        $d = $r->validate(['table_number' => 'required|string|max:20|unique:cafe_tables,table_number']);
        CafeTable::create($d + ['qr_token' => Str::random(40), 'is_active' => true]);
        return back()->with('success', 'Meja ditambahkan.');
    }

    public function update(Request $r, CafeTable $cafeTable)
    {
        $d = $r->validate(['table_number' => 'required|string|max:20|unique:cafe_tables,table_number,' . $cafeTable->id]);
        $cafeTable->update($d + ['is_active' => $r->boolean('is_active')]);
        return back()->with('success', 'Meja diperbarui.');
    }

    public function destroy(CafeTable $cafeTable)
    {
        if ($cafeTable->orders()->exists()) {
            return back()->withErrors(['table' => 'Meja sudah punya riwayat pesanan. Nonaktifkan saja.']);
        }
        $cafeTable->delete();
        return back()->with('success', 'Meja dihapus.');
    }

    public function regenerate(CafeTable $cafeTable)
    {
        $cafeTable->update(['qr_token' => Str::random(40)]);
        return back()->with('success', 'QR Meja ' . $cafeTable->table_number . ' dibuat ulang. QR lama tidak berlaku lagi.');
    }

    public function print(CafeTable $cafeTable)
    {
        return view('admin.tables-print', [
            'cafeTable' => $cafeTable,
            'svg' => $cafeTable->qrSvg(300),
            'url' => $cafeTable->orderUrl(),
        ]);
    }
}
