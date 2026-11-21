<?php

namespace App\Support;

use App\Models\CafeTable;
use App\Models\Product;
use App\Models\Promotion;

class Cart
{
    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function add(int $id, int $qty = 1, ?string $note = null): void
    {
        $c = self::raw();
        $c[$id] = [
            'qty' => min(99, ($c[$id]['qty'] ?? 0) + $qty),
            'note' => $note ?: ($c[$id]['note'] ?? null),
        ];
        session(['cart' => $c]);
    }

    public static function set(int $id, int $qty, ?string $note = null): void
    {
        $c = self::raw();
        if (!isset($c[$id])) {
            return;
        }
        if ($qty <= 0) {
            unset($c[$id]);
        } else {
            $c[$id] = ['qty' => min(99, $qty), 'note' => $note];
        }
        session(['cart' => $c]);
    }

    public static function remove(int $id): void
    {
        $c = self::raw();
        unset($c[$id]);
        session(['cart' => $c]);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }

    public static function count(): int
    {
        return (int) array_sum(array_column(self::raw(), 'qty'));
    }

    public static function lines(): array
    {
        $raw = self::raw();
        if (!$raw) {
            return [];
        }
        $products = Product::with('category')->whereIn('id', array_keys($raw))->where('is_active', true)->get()->keyBy('id');
        $lines = [];
        foreach ($raw as $id => $r) {
            $p = $products[$id] ?? null;
            if (!$p) {
                continue;
            }
            $lines[] = [
                'product' => $p,
                'qty' => $r['qty'],
                'note' => $r['note'] ?? null,
                'total' => (float) $p->price * $r['qty'],
            ];
        }
        return $lines;
    }

    /** Hitung subtotal + promo terbaik (satu promo per pesanan). */
    public static function totals(array $lines): array
    {
        $subtotal = (float) collect($lines)->sum('total');
        $best = null;
        $bestDisc = 0;

        foreach (Promotion::active()->get() as $promo) {
            if ($promo->product_id) {
                $eligible = (float) collect($lines)
                    ->filter(fn ($l) => $l['product']->id === $promo->product_id)
                    ->sum('total');
            } else {
                $eligible = $subtotal;
            }
            if ($eligible <= 0) {
                continue;
            }
            $d = $promo->discount_type === 'percentage'
                ? $eligible * (float) $promo->discount_value / 100
                : min((float) $promo->discount_value, $eligible);
            if ($d > $bestDisc) {
                $bestDisc = $d;
                $best = $promo;
            }
        }

        $discount = round($bestDisc);
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'promo' => $best,
        ];
    }

    public static function table(): ?CafeTable
    {
        $token = session('table_token');
        return $token ? CafeTable::where('qr_token', $token)->where('is_active', true)->first() : null;
    }
}
