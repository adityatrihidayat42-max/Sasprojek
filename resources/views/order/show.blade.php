@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)
@push('head')
@if(!in_array($order->order_status, ['completed','cancelled']))<meta http-equiv="refresh" content="30">@endif
@endpush
@section('content')
<section class="section"><div class="container" style="max-width:780px">
  <div class="panel">
    <div class="muted small">Nomor pesanan</div>
    <h1 class="serif">{{ $order->order_number }}</h1>
    <div class="row" style="margin:8px 0">
      <span class="pill {{ $order->order_status }}">{{ $order->statusLabel() }}</span>
      <span class="pill {{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
      <span class="pill">{{ $order->typeLabel() }}</span>
    </div>
    @if($order->order_status === 'cancelled')
      <div class="alert err">Pesanan ini dibatalkan.</div>
    @else
      @php $flow = ['pending','confirmed','preparing','ready','completed']; $idx = array_search($order->order_status, $flow); @endphp
      <div class="timeline">
        @foreach($flow as $i => $s)<span class="step {{ $i <= $idx ? 'on' : '' }}">{{ \App\Models\Order::STATUSES[$s] }}</span>@endforeach
      </div>
    @endif
    <p class="muted small">Atas nama {{ $order->customer_name }} · {{ $order->created_at->translatedFormat('d M Y H:i') }}
      @if($order->pickup_at) · Ambil: {{ $order->pickup_at->translatedFormat('d M Y H:i') }} @endif</p>
    <p class="muted small">Halaman ini diperbarui otomatis. Simpan nomor pesanan Anda.</p>
  </div>
  <div class="panel">
    <h3 class="serif" style="margin-bottom:8px">Rincian</h3>
    @foreach($order->items as $it)
      <div class="sum-line"><span>{{ $it->quantity }}× {{ $it->product_name }}@if($it->note) <span class="muted small">({{ $it->note }})</span>@endif</span><span>{{ rp($it->line_total) }}</span></div>
    @endforeach
    <hr style="margin:8px 0;border:0;border-top:1px solid #eee">
    <div class="sum-line"><span>Subtotal</span><span>{{ rp($order->subtotal) }}</span></div>
    @if($order->discount_total > 0)
      <div class="sum-line ok"><span>🎁 {{ $order->promotion_name }}</span><span>- {{ rp($order->discount_total) }}</span></div>
    @endif
    <div class="sum-line total"><span>Total</span><span>{{ rp($order->grand_total) }}</span></div>
    @if($order->customer_note)<p class="muted small" style="margin-top:8px">Catatan: {{ $order->customer_note }}</p>@endif
  </div>
  <a class="btn dark" href="{{ route('menu') }}">Pesan Lagi</a>
</div></section>
@endsection
