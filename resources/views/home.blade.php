@extends('layouts.app')
@section('content')
<section class="hero" style="background:#4b2818 url('{{ asset('images/arana-space-hero.svg') }}') center/cover no-repeat">
  <div class="container hero-grid">
    <div>
      <div class="eyebrow">— Good Food, Good Mood —</div>
      <h1 class="serif">Temukan Rasa, Temukan Ruang di Arana Space</h1>
      <p>Nikmati kopi favorit, hidangan lezat, dan suasana nyaman untuk setiap cerita.</p>
      <div class="hero-actions">
        <a class="btn" href="{{ route('menu') }}">☕ Pesan Sekarang →</a>
        <a class="btn outline" href="{{ route('scan') }}">▦ Scan QR Meja</a>
      </div>
    </div>
    <div class="hero-cup" aria-hidden="true">☕</div>
  </div>
</section>

<div class="container">
  <div class="features">
    <div class="feature"><span>⚡</span><b>Pemesanan Online</b><small>Pesan lebih cepat tanpa antre</small></div>
    <div class="feature"><span>📖</span><b>Menu Lengkap</b><small>Lihat menu dan harga dengan mudah</small></div>
    <div class="feature"><span>▦</span><b>Scan QR di Meja</b><small>Pesan langsung dari meja Anda</small></div>
    <div class="feature"><span>🎁</span><b>Promo Menarik</b><small>Dapatkan diskon dan promo terbaru</small></div>
    <div class="feature"><span>🪑</span><b>Reservasi Meja</b><small>Booking tempat dengan mudah</small></div>
  </div>
</div>

<section class="section">
  <div class="container fav">
    <div>
      <div class="eyebrow">— Our Menu</div>
      <h2 class="serif">Menu Favorit Arana Space</h2>
      <p class="muted">Pilihan kopi, makanan ringan, dan minuman segar untuk menemani harimu.</p>
      <a class="btn dark" style="margin-top:14px" href="{{ route('menu') }}">Lihat Semua Menu →</a>
    </div>
    <div class="grid">
      @foreach($favorites as $p) @include('partials.product-card', ['p' => $p]) @endforeach
    </div>
  </div>
</section>

<section>
  <div class="container two">
    <div class="promo" style="background:#f3dfc5 url('{{ asset('images/arana-space-promo.svg') }}') center/cover no-repeat;color:#4b2818">
      @if($promo)
        <span class="tagp">PROMO SPESIAL</span>
        <div class="big serif">{{ $promo->label() }}</div>
        <b style="font-size:1.4rem">untuk {{ $promo->product->name ?? 'Menu Pilihan' }}!</b>
        <span>Berlaku sampai {{ $promo->ends_at->translatedFormat('d F Y') }}</span>
        <div><a class="btn" href="{{ route('promotions') }}">🎁 Lihat Promo →</a></div>
      @else
        <span class="tagp">PROMO</span>
        <div class="big serif">Nantikan promo terbaru!</div>
        <div><a class="btn" href="{{ route('promotions') }}">Lihat Promo →</a></div>
      @endif
    </div>
    <div class="scanbox">
      <h2 class="serif">Scan QR di Meja</h2>
      <p class="muted">Pindai QR Code di meja untuk melihat menu dan memesan langsung.</p>
      <div><a class="btn dark" href="{{ route('scan') }}">▦ Scan Sekarang</a></div>
    </div>
  </div>
</section>

<section id="tentang" class="about">
  <div class="container">
    <div>
      <h2 class="serif">Tentang Arana Space</h2>
      <p class="muted" style="margin:8px 0 14px">Arana Space adalah ruang nyaman untuk menikmati kopi, makanan, dan waktu berkualitas bersama orang-orang terdekat.</p>
    </div>
    <div>
      <h2 class="serif">Reservasi Meja</h2>
      <p class="muted" style="margin:8px 0 14px">Rencanakan kunjunganmu dan pesan meja lebih awal.</p>
      <a class="btn" href="{{ route('reservation') }}">🪑 Reservasi Sekarang →</a>
    </div>
  </div>
</section>
@endsection
