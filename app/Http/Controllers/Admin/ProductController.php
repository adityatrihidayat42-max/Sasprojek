<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::with('category')->orderBy('name');
        if ($r->filled('q')) {
            $q->where('name', 'like', '%' . $r->input('q') . '%');
        }
        $products = $q->paginate(15)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $product = new Product(['is_available' => true, 'is_active' => true]);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function store(Request $r)
    {
        Product::create($this->data($r));
        return redirect()->route('admin.products.index')->with('success', 'Produk ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $r, Product $product)
    {
        $product->update($this->data($r, $product));
        return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            @unlink(public_path($product->image));
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus.');
    }

    private function data(Request $r, ?Product $product = null): array
    {
        $d = $r->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999999',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $d['is_available'] = $r->boolean('is_available');
        $d['is_active'] = $r->boolean('is_active');
        $d['is_best_seller'] = $r->boolean('is_best_seller');
        $d['slug'] = Slug::make(Product::class, $d['name'], $product?->id);

        if ($r->hasFile('image')) {
            $file = $r->file('image');
            $name = Str::lower(Str::random(20)) . '.' . $file->extension();
            $file->move(public_path('uploads'), $name);
            $d['image'] = 'uploads/' . $name;
            if ($product?->image) {
                @unlink(public_path($product->image));
            }
        } else {
            unset($d['image']);
        }
        return $d;
    }
}
