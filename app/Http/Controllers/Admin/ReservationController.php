<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderByDesc('reserved_at')->paginate(20);
        return view('admin.reservations', compact('reservations'));
    }

    public function update(Request $r, Reservation $reservation)
    {
        $d = $r->validate(['status' => ['required', Rule::in(array_keys(Reservation::STATUSES))]]);
        $reservation->update($d);
        return back()->with('success', 'Status reservasi diperbarui.');
    }
}
