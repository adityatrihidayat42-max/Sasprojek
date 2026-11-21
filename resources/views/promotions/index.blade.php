@extends('layouts.app')
@section('title', 'Promo - Arana Space')
@section('content')
<section class="section"><div class="container">
  <div class="promo-head">
    <div class="eyebrow">— Promo Spesial —</div>
    <h1 class="serif">Promo Arana Space</h1>
    <p>Nikmati kopi dan hidangan favorit dengan harga lebih hemat. Pilih promo yang sedang berlaku, diskon otomatis saat checkout.</p>
  </div>

  @if($promos->isEmpty())
    <div class="promo-empty">
      <div class="empty-icon" aria-hidden="true">✦</div>
      <h2 class="serif">Belum ada promo aktif</h2>
      <p>Nantikan penawaran terbaru dari Arana Space. Sambil menunggu, jelajahi menu favorit kami dulu, ya.</p>
      <a class="btn" href="{{ route('menu') }}">Lihat Menu <span aria-hidden="true">→</span></a>
    </div>
  @else
    <div class="promo-grid">
      @foreach($promos as $p)
        @include('partials.promo-card', ['p' => $p])
      @endforeach
    </div>
  @endif
</div></section>
@endsection
