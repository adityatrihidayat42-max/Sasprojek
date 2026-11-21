<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Support\Slug;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promos = Promotion::with('product')->latest()->get();
        return view('admin.promotions.index', compact('promos'));
    }

    public function create()
    {
        $promo = new Promotion([
            'is_active' => true, 'discount_type' => 'percentage',
            'starts_at' => now(), 'ends_at' => now()->addDays(30),
        ]);
        $products = Product::orderBy('name')->get();
        return view('admin.promotions.form', compact('promo', 'products'));
    }

    public function store(Request $r)
    {
        Promotion::create($this->data($r));
        return redirect()->route('admin.promotions.index')->with('success', 'Promo ditambahkan.');
    }

    public function edit(Promotion $promotion)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.promotions.form', ['promo' => $promotion, 'products' => $products]);
    }

    public function update(Request $r, Promotion $promotion)
    {
        $promotion->update($this->data($r, $promotion));
        return redirect()->route('admin.promotions.index')->with('success', 'Promo diperbarui.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'Promo dihapus.');
    }

    private function data(Request $r, ?Promotion $promo = null): array
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'product_id' => 'nullable|exists:products,id',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0|max:99999999',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'terms' => 'nullable|string|max:1000',
        ]);
        if ($d['discount_type'] === 'percentage' && $d['discount_value'] > 100) {
            $d['discount_value'] = 100;
        }
        $d['is_active'] = $r->boolean('is_active');
        $d['slug'] = Slug::make(Promotion::class, $d['name'], $promo?->id);
        return $d;
    }
}
