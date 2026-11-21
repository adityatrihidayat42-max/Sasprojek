@extends('layouts.app')
@section('title', 'Checkout - Arana Space')
@section('content')
<section class="section"><div class="container">
  <h1 class="serif" style="margin-bottom:14px">Checkout {{ $table ? '- Meja ' . $table->table_number : '(Pre-Order)' }}</h1>
  <div class="two-r">
    <form class="panel" method="POST" action="{{ route('checkout.store') }}" onsubmit="this.querySelector('button[type=submit]').disabled=true">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group"><label>Nama *</label><input name="customer_name" value="{{ old('customer_name') }}" required maxlength="80"></div>
      <div class="form-group"><label>Nomor HP {{ $table ? '(opsional)' : '*' }}</label>
        <input name="customer_phone" value="{{ old('customer_phone') }}" {{ $table ? '' : 'required' }} placeholder="08xxxxxxxxxx"></div>
      @unless($table)
        <div class="form-group"><label>Perkiraan waktu ambil (opsional)</label>
          <input type="datetime-local" name="pickup_at" value="{{ old('pickup_at') }}"></div>
        <p class="muted small" style="margin-bottom:14px">Pesanan diambil langsung di Arana Space.</p>
      @endunless
      <div class="form-group"><label>Catatan pesanan (opsional)</label><textarea name="customer_note" rows="3" maxlength="500">{{ old('customer_note') }}</textarea></div>
      <div class="form-group">
        <label>Metode Pembayaran *</label>
        <label class="panel" style="display:flex;align-items:center;gap:10px;margin:8px 0">
          <input type="radio" name="payment_method" value="cash" @checked(old('payment_method','cash') === 'cash') required>
          <span><b>Cash (Tunai)</b><br><small class="muted">Bayar kepada kasir. Pesanan diproses setelah pembayaran dikonfirmasi.</small></span>
        </label>
        <label class="panel" style="display:flex;align-items:center;gap:10px;margin:8px 0">
          <input type="radio" name="payment_method" value="qris" @checked(old('payment_method') === 'qris') required>
          <span><b>QRIS</b><br><small class="muted">Pembayaran QRIS akan menunggu verifikasi penyedia pembayaran.</small></span>
        </label>
      </div>
      <button type="submit" class="btn block">Kirim Pesanan</button>
    </form>
    <div class="panel">
      <h3 class="serif" style="margin-bottom:8px">Pesanan Anda</h3>
      @foreach($lines as $l)
        <div class="sum-line"><span>{{ $l['qty'] }}× {{ $l['product']->name }}</span><span>{{ rp($l['total']) }}</span></div>
      @endforeach
      <hr style="margin:8px 0;border:0;border-top:1px solid #eee">
      @include('partials.summary')
    </div>
  </div>
</div></section>
@endsection
