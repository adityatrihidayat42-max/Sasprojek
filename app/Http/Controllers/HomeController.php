<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;

class HomeController extends Controller
{
    public function index()
    {
        $favorites = Product::with('category')
            ->where('is_active', true)
            ->orderByDesc('is_best_seller')
            ->orderBy('name')
            ->limit(4)->get();

        $promo = Promotion::active()->with('product')->latest()->first();

        return view('home', compact('favorites', 'promo'));
    }
}
