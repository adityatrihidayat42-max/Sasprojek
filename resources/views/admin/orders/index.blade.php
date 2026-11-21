@extends('layouts.admin')
@section('title', auth()->user()->role === 'kitchen' ? 'Kitchen Display' : 'Pesanan')
@push('head')
<meta http-equiv="refresh" content="15">
<style>
.kds-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}
.kds-card{background:#fff;border:1px solid #eadfd2;border-radius:16px;padding:18px;box-shadow:0 5px 18px #2b1d140a}
.kds-number{font-size:1.25rem;font-weight:800}.kds-items{padding-left:20px;margin:12px 0}.kds-note{background:#fff6df;padding:10px;border-radius:10px;margin:10px 0}
</style>
@endpush
@section('content')
@if(auth()->user()->role === 'kitchen')
  <h1 class="serif">Kitchen Display System</h1>
  <p class="muted">Pesanan lunas yang perlu disiapkan. Halaman diperbarui otomatis setiap 15 detik.</p>
  <div class="kds-grid">
  @forelse($orders as $o)
    <article class="kds-card">
      <div class="row" style="justify-content:space-between;align-items:center">
        <span class="kds-number">{{ $o->order_number }}</span>
        <span class="pill {{ $o->order_status }}">{{ $o->order_status === 'preparing' ? 'Sedang dibuat' : 'Pesanan masuk' }}</span>
      </div>
      <p class="muted small">{{ $o->created_at->format('d/m/Y H:i') }} · {{ $o->typeLabel() }}</p>
      <ul class="kds-items">
        @foreach($o->items as $item)
          <li><b>{{ $item->quantity }}×</b> {{ $item->product_name }} @if($item->note)<span class="muted">— {{ $item->note }}</span>@endif</li>
        @endforeach
      </ul>
      @if($o->customer_note)<div class="kds-note">Catatan: {{ $o->customer_note }}</div>@endif
      <a class="btn sm" href="{{ route('admin.orders.show', $o) }}">{{ $o->order_status === 'preparing' ? 'Buka untuk Siap Disajikan' : 'Mulai dibuat' }}</a>
    </article>
  @empty
    <div class="panel muted">Belum ada pesanan lunas di antrean dapur.</div>
  @endforelse
  </div>
@else
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
@endif
@endsection
