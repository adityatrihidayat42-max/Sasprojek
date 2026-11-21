<?php

namespace App\Models;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Menunggu konfirmasi',
        'confirmed' => 'Dikonfirmasi',
        'preparing' => 'Sedang disiapkan',
        'ready' => 'Siap diambil/disajikan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const PAYMENTS = [
        'unpaid' => 'Belum dibayar',
        'pending' => 'Menunggu pembayaran',
        'paid' => 'Lunas',
        'refunded' => 'Dikembalikan',
    ];

    protected $guarded = [];
    protected $casts = [
        'pickup_at' => 'datetime',
        'subtotal' => 'float',
        'discount_total' => 'float',
        'grand_total' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function cafeTable()
    {
        return $this->belongsTo(CafeTable::class);
    }

    public function qrSvg(int $size = 280): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString(route('order.show', $this->order_number));
        return preg_replace('/^<\?xml.*?\?>\s*/s', '', $svg);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->order_status] ?? $this->order_status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENTS[$this->payment_status] ?? $this->payment_status;
    }

    public function typeLabel(): string
    {
        return $this->order_type === 'dine_in'
            ? 'Meja ' . optional($this->cafeTable)->table_number
            : 'Pre-order';
    }
}
