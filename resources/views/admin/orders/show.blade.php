@extends('layouts.admin')
@section('title', 'Pesanan ' . $order->order_number)
@section('content')
<h1 class="serif">Pesanan {{ $order->order_number }}</h1>
<div class="cols">
  <div>
    <div class="panel">
      <div class="row"><span class="pill {{ $order->order_status }}">{{ $order->statusLabel() }}</span>
        <span class="pill {{ $order->payment_status }}">{{ $order->paymentLabel() }}</span><span class="pill">{{ $order->typeLabel() }}</span></div>
      <p style="margin-top:10px"><b>{{ $order->customer_name }}</b> @if($order->customer_phone) · {{ $order->customer_phone }} @endif</p>
      <p class="muted small">Dibuat {{ $order->created_at->translatedFormat('d M Y H:i') }}
        @if($order->pickup_at) · Ambil {{ $order->pickup_at->translatedFormat('d M Y H:i') }} @endif</p>
      @if($order->customer_note)<p style="margin-top:8px">📝 {{ $order->customer_note }}</p>@endif
    </div>
    <div class="panel">
      <h3 class="serif" style="margin-bottom:8px">Item Pesanan</h3>
      @foreach($order->items as $it)
        <div class="sum-line"><span><b>{{ $it->quantity }}×</b> {{ $it->product_name }}@if($it->note) <i class="muted small">({{ $it->note }})</i>@endif</span><span>{{ rp($it->line_total) }}</span></div>
      @endforeach
      <div class="sum-line"><span>Subtotal</span><span>{{ rp($order->subtotal) }}</span></div>
      @if($order->discount_total > 0)<div class="sum-line ok"><span>{{ $order->promotion_name }}</span><span>- {{ rp($order->discount_total) }}</span></div>@endif
      <div class="sum-line total"><span>Total</span><span>{{ rp($order->grand_total) }}</span></div>
    </div>
  </div>
  <div>
    @if(count($statusOptions))
    <form class="panel" method="POST" action="{{ route('admin.orders.status', $order) }}">
      @csrf @method('PATCH')
      <label>{{ auth()->user()->role === 'kitchen' ? 'Proses dapur' : 'Tindakan pesanan' }}</label>
      <div class="row">
        <select name="order_status" style="flex:1" required>
          @foreach($statusOptions as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
        </select>
        <button class="btn sm">{{ auth()->user()->role === 'kitchen' ? 'Perbarui' : 'Simpan' }}</button>
      </div>
      @if(auth()->user()->role === 'kitchen')
        <p class="muted small" style="margin-top:8px">Saat tombol Siap Disajikan ditekan, pesanan ditandai selesai. Staf kemudian mengantarkan pesanan ke meja atau menyerahkannya kepada pelanggan takeaway.</p>
      @endif
    </form>
    @endif
    @if(in_array(auth()->user()->role, ['admin','cashier']) && ($order->payment_method ?? 'cash') === 'cash' && $order->payment_status !== 'paid' && $order->order_status !== 'cancelled')
    <form class="panel" method="POST" action="{{ route('admin.orders.payment', $order) }}">
      @csrf @method('PATCH')
      <h3 class="serif">Konfirmasi pembayaran tunai</h3>
      <p class="muted small">Pastikan uang sudah diterima. Setelah dikonfirmasi, pesanan otomatis masuk antrean dapur.</p>
      <input type="hidden" name="payment_status" value="paid">
      <button class="btn sm" style="margin-top:10px">Konfirmasi sudah dibayar</button>
    </form>
    @elseif(($order->payment_method ?? 'cash') === 'qris')
      <div class="panel"><b>Pembayaran QRIS</b><p class="muted small">Status: {{ $order->paymentLabel() }}. Pembayaran QRIS harus diverifikasi oleh payment gateway; kasir tidak dapat mengubahnya secara manual.</p></div>
    @endif
    <div class="panel">
      <h3 class="serif" style="margin-bottom:8px">Riwayat</h3>
      @forelse($order->histories->sortByDesc('id') as $h)
        <p class="small">{{ $h->created_at->format('d/m H:i') }} ·
          {{ $h->type === 'payment' ? 'Pembayaran' : 'Status' }}:
          {{ $h->type === 'payment' ? (\App\Models\Order::PAYMENTS[$h->new_value] ?? $h->new_value) : (\App\Models\Order::STATUSES[$h->new_value] ?? $h->new_value) }}
          <span class="muted">({{ $h->user->name ?? 'Pelanggan' }})</span></p>
      @empty
        <p class="muted small">Belum ada riwayat perubahan.</p>
      @endforelse
    </div>
    <a class="btn light" href="{{ route('admin.orders') }}">← Kembali</a>
  </div>
</div>
@endsection
