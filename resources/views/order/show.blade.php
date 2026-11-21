@extends('layouts.app')
@section('title', 'Pesanan ' . $order->order_number)
@push('head')
@if(!in_array($order->order_status, ['completed','cancelled']))<meta http-equiv="refresh" content="30">@endif
<style>
.order-success-wrap{max-width:780px;margin:0 auto}
.order-success-card{background:#fff;border-radius:24px;padding:clamp(24px,5vw,44px);box-shadow:0 12px 36px #2b1d1410}
.order-success-icon{width:58px;height:58px;border-radius:50%;display:grid;place-items:center;background:#e5f4e8;color:#287347;font-size:28px;margin-bottom:18px}
.order-success-number{font-family:Georgia,serif;font-size:clamp(1.8rem,7vw,3rem);word-break:break-word;margin:4px 0 14px}
.order-success-qr{display:grid;place-items:center;margin:22px auto 12px;padding:20px;border:1px dashed #d8c7b2;border-radius:18px;background:#fffaf4;max-width:340px}
.order-success-qr svg{display:block;width:100%;height:auto;max-width:270px}
.order-success-hint{text-align:center;color:var(--muted);font-size:.88rem;max-width:400px;margin:0 auto}
</style>
@endpush
@section('content')
<section class="section"><div class="container order-success-wrap">
  <div class="order-success-card">
    <div class="order-success-icon">✓</div>
    <p class="eyebrow">Arana Space · Konfirmasi</p>
    <h1 class="serif" style="font-size:clamp(2rem,7vw,3.2rem);line-height:1.08;margin:6px 0 12px">Pesanan berhasil dibuat</h1>
    <p class="muted">Nomor pesanan</p>
    <div class="order-success-number">{{ $order->order_number }}</div>
    <div class="row" style="margin:8px 0 14px">
      <span class="pill {{ $order->order_status }}">{{ $order->statusLabel() }}</span>
      <span class="pill {{ $order->payment_status }}">{{ $order->paymentLabel() }}</span>
      <span class="pill">{{ $order->typeLabel() }}</span>
    </div>
    @if($order->order_status !== 'cancelled')
      <div class="order-success-qr">{!! $order->qrSvg(280) !!}</div>
      <p class="order-success-hint">Tunjukkan QR Code ini kepada kasir. Kasir akan memindainya untuk membuka detail pesanan Anda.</p>
    @else
      <div class="alert err">Pesanan ini dibatalkan.</div>
    @endif
    <p class="muted small" style="margin-top:20px">Atas nama {{ $order->customer_name }} · {{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
    <div style="border-top:1px solid #eee;margin:20px 0"></div>
    <h3 class="serif" style="margin-bottom:8px">Rincian pesanan</h3>
    @foreach($order->items as $it)
      <div class="sum-line"><span>{{ $it->quantity }}× {{ $it->product_name }}</span><span>{{ rp($it->line_total) }}</span></div>
    @endforeach
    <div class="sum-line total"><span>Total</span><span>{{ rp($order->grand_total) }}</span></div>
    <a class="btn" style="margin-top:20px" href="{{ route('menu') }}">Kembali ke Menu</a>
  </div>
</div></section>
@endsection
