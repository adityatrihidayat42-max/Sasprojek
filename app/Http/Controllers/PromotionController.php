<?php

namespace App\Http\Controllers;

use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promos = Promotion::active()->with('product')->orderBy('ends_at')->get();
        return view('promotions.index', compact('promos'));
    }

    public function show(Promotion $promotion)
    {
        abort_unless($promotion->is_active, 404);
        $promotion->load('product');

        $others = Promotion::active()
            ->with('product')
            ->whereKeyNot($promotion->id)
            ->orderBy('ends_at')
            ->take(3)
            ->get();

        return view('promotions.show', ['promo' => $promotion, 'others' => $others]);
    }
}
