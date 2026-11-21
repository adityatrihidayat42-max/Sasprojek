<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::with('category')
            ->where('is_active', true)
            ->whereHas('category', fn ($c) => $c->where('is_active', true));

        if ($r->filled('category')) {
            $q->where('category_id', $r->input('category'));
        }
        if ($r->filled('q')) {
            $q->where('name', 'like', '%' . $r->input('q') . '%');
        }

        $products = $q->orderByDesc('is_best_seller')->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('menu.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('category');
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)->where('is_active', true)->limit(4)->get();

        return view('menu.show', compact('product', 'related'));
    }
}
