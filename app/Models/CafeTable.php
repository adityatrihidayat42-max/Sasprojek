<?php

namespace App\Models;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Model;

class CafeTable extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderUrl(): string
    {
        return route('table.order', $this->qr_token);
    }

    public function qrSvg(int $size = 280): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($this->orderUrl());
        return preg_replace('/^<\?xml.*?\?>\s*/s', '', $svg);
    }
}
