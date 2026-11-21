<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slug
{
    public static function make(string $model, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::lower(Str::random(6));
        $slug = $base;
        $i = 2;
        while ($model::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
