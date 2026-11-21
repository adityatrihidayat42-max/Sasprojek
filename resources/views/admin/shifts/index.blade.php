@extends('layouts.admin')
@section('title', 'Shift Kasir')
@section('content')
<h1 class="serif">Shift Kasir</h1>
@if($openShift)
  <div class="panel">
    <h3 class="serif">Shift sedang berjalan</h3>
    <p class="muted small">Dibuka {{ $openShift->opened_at->format('d/m/Y H:i') }}</p>
    <p>Modal awal: <b>{{ rp($openShift->opening_cash) }}</b></p>
    <form method="POST" action="{{ route('admin.shifts.close') }}" class="row">
      @csrf
      <label for="closing_cash">Uang fisik saat tutup shift</label>
      <input id="closing_cash" name="closing_cash" type="number" min="0" step="1000" required placeholder="Masukkan jumlah uang fisik">
      <button class="btn">Tutup Shift</button>
    </form>
  </div>
@else
  <form method="POST" action="{{ route('admin.shifts.open') }}" class="panel">
    @csrf
    <h3 class="serif">Buka Shift</h3>
    <label for="opening_cash">Modal awal kas</label>
    <input id="opening_cash" name="opening_cash" type="number" min="0" step="1000" required placeholder="Contoh: 200000">
    <button class="btn" style="margin-top:12px">Buka Shift</button>
  </form>
@endif
<div class="panel">
  <h3 class="serif">Riwayat Shift</h3>
  <div class="table-wrap"><table>
    <tr><th>Kasir</th><th>Dibuka</th><th>Ditutup</th><th>Modal</th><th>Penjualan tunai</th><th>Non-tunai</th><th>Uang fisik</th><th>Selisih</th><th>Status</th></tr>
    @forelse($shifts as $s)
      <tr><td>{{ $s->cashier->name ?? 'Kasir' }}</td><td>{{ $s->opened_at->format('d/m H:i') }}</td>
      <td>{{ $s->closed_at?->format('d/m H:i') ?? '—' }}</td><td>{{ rp($s->opening_cash) }}</td>
      <td>{{ rp($s->cash_sales) }}</td><td>{{ rp($s->non_cash_sales) }}</td>
      <td>{{ $s->closing_cash === null ? '—' : rp($s->closing_cash) }}</td>
      <td>{{ $s->cash_difference === null ? '—' : rp($s->cash_difference) }}</td>
      <td>{{ $s->status === 'open' ? 'Berjalan' : 'Ditutup' }}</td></tr>
    @empty
      <tr><td colspan="9" class="muted">Belum ada riwayat shift.</td></tr>
    @endforelse
  </table></div>
  {{ $shifts->links() }}
</div>
@endsection
