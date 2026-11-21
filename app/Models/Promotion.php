<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $guarded = [];
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'discount_value' => 'float',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function isRunning(): bool
    {
        return $this->is_active && $this->starts_at <= now() && $this->ends_at >= now();
    }

    public function label(): string
    {
        $v = $this->discount_value;
        $v = floor($v) == $v ? (int) $v : $v;
        return $this->discount_type === 'percentage' ? "Diskon {$v}%" : 'Potongan ' . rp($v);
    }

    /**
     * Foto promo: pakai foto produk jika ada, jika tidak foto kopi default.
     */
    public function photoUrl(): string
    {
        return $this->product?->imageUrl() ?: self::defaultPhoto();
    }

    public static function defaultPhoto(): string
    {
        return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1400&q=85';
    }
}
