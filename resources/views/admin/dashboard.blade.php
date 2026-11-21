@extends('layouts.admin')
@section('content')
<h1 class="serif">Dashboard</h1>
<div class="stats">
  <div class="stat"><span class="muted small">Pesanan hari ini</span><b>{{ $ordersToday }}</b></div>
  <div class="stat"><span class="muted small">Pendapatan hari ini (lunas)</span><b>{{ rp($revenueToday) }}</b></div>
  <div class="stat"><span class="muted small">Menunggu konfirmasi</span><b>{{ $pending }}</b></div>
  <div class="stat"><span class="muted small">Sedang diproses</span><b>{{ $active }}</b></div>
  <div class="stat"><span class="muted small">Reservasi baru</span><b>{{ $reservations }}</b></div>
  <div class="stat"><span class="muted small">Produk aktif</span><b>{{ $products }}</b></div>
</div>
<div class="panel">
  <h3 class="serif" style="margin-bottom:10px">Pesanan Terbaru</h3>
  <div class="table-wrap"><table>
    <tr><th>No</th><th>Waktu</th><th>Tipe</th><th>Pelanggan</th><th>Total</th><th>Status</th><th></th></tr>
    @forelse($recent as $o)
      <tr><td>{{ $o->order_number }}</td><td>{{ $o->created_at->format('d/m H:i') }}</td><td>{{ $o->typeLabel() }}</td>
        <td>{{ $o->customer_name }}</td><td>{{ rp($o->grand_total) }}</td>
        <td><span class="pill {{ $o->order_status }}">{{ $o->statusLabel() }}</span></td>
        <td><a class="btn sm light" href="{{ route('admin.orders.show', $o) }}">Detail</a></td></tr>
    @empty
      <tr><td colspan="7" class="muted">Belum ada pesanan.</td></tr>
    @endforelse
  </table></div>
</div>
@endsection
