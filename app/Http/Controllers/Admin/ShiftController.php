<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $openShift = CashierShift::where('cashier_id', $user->id)->where('status', 'open')->latest()->first();
        $shifts = CashierShift::with('cashier')
            ->when($user->role !== 'admin', fn ($q) => $q->where('cashier_id', $user->id))
            ->latest('opened_at')->paginate(20);
        return view('admin.shifts.index', compact('openShift', 'shifts'));
    }

    public function open(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'cashier']), 403);
        $data = $request->validate(['opening_cash' => ['required', 'numeric', 'min:0', 'max:999999999']]);
        if (CashierShift::where('cashier_id', $request->user()->id)->where('status', 'open')->exists()) {
            return back()->withErrors(['shift' => 'Masih ada shift yang belum ditutup.']);
        }
        CashierShift::create([
            'cashier_id' => $request->user()->id,
            'opening_cash' => $data['opening_cash'],
            'status' => 'open',
            'opened_at' => now(),
        ]);
        return back()->with('success', 'Shift berhasil dibuka.');
    }

    public function close(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'cashier']), 403);
        $data = $request->validate(['closing_cash' => ['required', 'numeric', 'min:0', 'max:999999999']]);
        DB::transaction(function () use ($data, $request) {
            $shift = CashierShift::where('cashier_id', $request->user()->id)->where('status', 'open')->lockForUpdate()->first();
            if (!$shift) abort(422, 'Tidak ada shift aktif.');
            $orders = Order::where('payment_status', 'paid')
                ->where('paid_by', $request->user()->id)
                ->whereBetween('paid_at', [$shift->opened_at, now()])
                ->get();
            $cash = $orders->where('payment_method', 'cash')->sum('grand_total');
            $nonCash = $orders->where('payment_method', '!=', 'cash')->sum('grand_total');
            $expected = $shift->opening_cash + $cash;
            $shift->update([
                'cash_sales' => $cash,
                'non_cash_sales' => $nonCash,
                'closing_cash' => $data['closing_cash'],
                'expected_cash' => $expected,
                'cash_difference' => $data['closing_cash'] - $expected,
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        });
        return back()->with('success', 'Shift ditutup. Selisih kas telah dihitung.');
    }
}
