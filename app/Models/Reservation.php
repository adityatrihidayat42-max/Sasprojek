<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public const STATUSES = [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'done' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected $guarded = [];
    protected $casts = ['reserved_at' => 'datetime'];
}
