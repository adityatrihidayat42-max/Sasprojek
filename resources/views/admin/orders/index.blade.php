@extends('layouts.admin')
@section('title', 'Pesanan')
@push('head')<meta http-equiv="refresh" content="60">@endpush
@section('content')
<h1 class="serif">Pesanan</h1>
<form method="GET" class="panel row">
  <input name="q" value="{{ request('q') }}" placeholder="No / nama" style="max-width:180px">
  <select name="status" style="max-width:190px"><option value="">Semua status</option>
    @foreach(\App\Models\Order::STATUSES as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach</select>
  <select name="payment" style="max-width:170px"><option value="">Semua pembayaran</option>
    @foreach(\App\Models\Order::PAYMENTS as $k => $v)<option value="{{ $k }}" @selected(request('payment') === $k)>{{ $v }}</option>@endforeach</select>
  <select name="type" style="max-width:140px"><option value="">Semua tipe</option>
    <option value="preorder" @selected(request('type') === 'preorder')>Pre-order</option>
    <option value="dine_in" @selected(request('type') === 'dine_in')>Dari meja</option></select>
  <input type="date" name="date" value="{{ request('date') }}" style="max-width:160px">
  <button class="btn sm">Filter</button><a class="btn sm light" href="{{ route('admin.orders') }}">Reset</a>
</form>
<div class="table-wrap"><table>
  <tr><th>No</th><th>Waktu</th><th>Tipe</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Bayar</th><th></th></tr>
  @forelse($orders as $o)
    <tr><td>{{ $o->order_number }}</td><td>{{ $o->created_at->format('d/m H:i') }}</td><td>{{ $o->typeLabel() }}</td>
      <td>{{ $o->customer_name }}</td><td>{{ rp($o->grand_total) }}</td>
      <td><span class="pill {{ $o->order_status }}">{{ $o->statusLabel() }}</span></td>
      <td><span class="pill {{ $o->payment_status }}">{{ $o->paymentLabel() }}</span></td>
      <td><a class="btn sm light" href="{{ route('admin.orders.show', $o) }}">Detail</a></td></tr>
  @empty
    <tr><td colspan="8" class="muted">Tidak ada pesanan.</td></tr>
  @endforelse
</table></div>
{{ $orders->links() }}
@endsection
