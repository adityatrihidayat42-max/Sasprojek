<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function create()
    {
        return view('reservation');
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:80',
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'reserved_at' => 'required|date|after:now',
            'guests' => 'required|integer|min:1|max:30',
            'note' => 'nullable|string|max:300',
        ]);
        Reservation::create($d);
        return redirect()->route('reservation')->with('success', 'Reservasi terkirim! Kami akan menghubungi Anda untuk konfirmasi.');
    }
}
