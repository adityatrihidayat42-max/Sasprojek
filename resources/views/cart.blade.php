@extends('layouts.app')
@section('title', 'Keranjang - BrewNest')
@section('content')
<section class="section"><div class="container">
  <h1 class="serif" style="margin-bottom:14px">Keranjang</h1>
  @if(!$lines)
    <p class="muted">Keranjang masih kosong. <a class="link" href="{{ route('menu') }}">Lihat menu →</a></p>
  @else
  <div class="two-r">
    <div class="panel">
      @foreach($lines as $l)
        <div class="cart-row">
          <div class="cart-thumb">{{ $l['product']->emoji() }}</div>
          <div class="grow">
            <b>{{ $l['product']->name }}</b>
            <div class="muted small">{{ rp($l['product']->price) }}
              @unless($l['product']->is_available) <span class="pill cancelled">Tidak tersedia</span> @endunless</div>
            <form method="POST" action="{{ route('cart.update', $l['product']->id) }}" class="row" style="margin-top:8px">@csrf @method('PATCH')
              <input type="number" name="qty" value="{{ $l['qty'] }}" min="0" max="99" style="width:80px">
              <input name="note" value="{{ $l['note'] }}" placeholder="Catatan (opsional)" maxlength="150" style="max-width:240px">
              <button class="btn sm light">Ubah</button>
            </form>
          </div>
          <div style="text-align:right"><b>{{ rp($l['total']) }}</b>
            <form method="POST" action="{{ route('cart.remove', $l['product']->id) }}" style="margin-top:8px">@csrf @method('DELETE')
              <button class="btn sm danger">Hapus</button></form>
          </div>
        </div>
      @endforeach
    </div>
    <div class="panel">
      <h3 class="serif" style="margin-bottom:8px">Ringkasan</h3>
      @include('partials.summary')
      <a class="btn block" style="margin-top:14px" href="{{ route('checkout') }}">Lanjut ke Checkout →</a>
    </div>
  </div>
  @endif
</div></section>
@endsection
