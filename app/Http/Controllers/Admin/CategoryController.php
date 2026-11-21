<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories', compact('categories'));
    }

    public function store(Request $r)
    {
        Category::create($this->data($r));
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $r, Category $category)
    {
        $category->update($this->data($r));
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih memiliki produk. Nonaktifkan saja atau pindahkan produknya.']);
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    private function data(Request $r): array
    {
        $d = $r->validate(['name' => 'required|string|max:60', 'description' => 'nullable|string|max:300']);
        $d['is_active'] = $r->boolean('is_active');
        return $d;
    }
}
