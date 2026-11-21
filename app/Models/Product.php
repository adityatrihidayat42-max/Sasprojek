<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    protected $casts = [
        'price' => 'float',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function emoji(): string
    {
        $n = strtolower($this->name . ' ' . optional($this->category)->name);
        return match (true) {
            str_contains($n, 'cake') || str_contains($n, 'dessert') || str_contains($n, 'roti') || str_contains($n, 'croissant') => '🍰',
            str_contains($n, 'matcha') || str_contains($n, 'tea') || str_contains($n, 'teh') => '🍵',
            str_contains($n, 'iced') || str_contains($n, 'es ') => '🧋',
            str_contains($n, 'fries') || str_contains($n, 'snack') || str_contains($n, 'makanan') => '🍟',
            default => '☕',
        };
    }
}
