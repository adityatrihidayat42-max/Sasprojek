@extends('layouts.app')
@section('content')
<section class="hero" style="--hero-photo:url('https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=2000&q=85')">
  <div class="container hero-grid">
    <div>
      <div class="eyebrow">— Good Food, Good Mood —</div>
      <h1 class="serif">Temukan Rasa, Temukan Ruang di Arana Space</h1>
      <p>Nikmati kopi favorit, hidangan lezat, dan suasana nyaman untuk setiap cerita.</p>
      <div class="hero-actions">
        <a class="btn" href="{{ route('menu') }}">Pesan Sekarang <span aria-hidden="true">→</span></a>
        <a class="btn outline" href="{{ route('scan') }}">Scan QR Meja</a>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <div class="features">
    <div class="feature">
      <span class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M13 2 5.5 13h5L10 22l8.5-12h-5L13 2Z"/></svg></span>
      <b>Pemesanan Online</b><small>Pesan lebih cepat tanpa antre</small>
    </div>
    <div class="feature">
      <span class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 5.5A3.5 3.5 0 0 1 7.5 2H20v17H7.5A3.5 3.5 0 0 0 4 22V5.5Z"/><path d="M4 5.5V22M8 6h8M8 10h8"/></svg></span>
      <b>Menu Lengkap</b><small>Lihat menu dan harga dengan mudah</small>
    </div>
    <div class="feature">
      <span class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 3v18M16 3v18M3 8h18M3 16h18"/></svg></span>
      <b>Scan QR di Meja</b><small>Pesan langsung dari meja Anda</small>
    </div>
    <div class="feature">
      <span class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 12v9H4v-9M2 7h20v5H2zM12 7v14"/><path d="M12 7H7.5a2.5 2.5 0 1 1 2.3-3.5L12 7Zm0 0h4.5a2.5 2.5 0 1 0-2.3-3.5L12 7Z"/></svg></span>
      <b>Promo Menarik</b><small>Dapatkan diskon dan promo terbaru</small>
    </div>
    <div class="feature">
      <span class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V8h14v13M8 8V4h8v4M9 12h.01M15 12h.01M9 16h.01M15 16h.01"/></svg></span>
      <b>Reservasi Meja</b><small>Booking tempat dengan mudah</small>
    </div>
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
    <div class="promo" style="--promo-photo:url('{{ $promo?->photoUrl() ?? \App\Models\Promotion::defaultPhoto() }}')">
      @if($promo)
        <span class="tagp">✦ Promo Spesial</span>
        <div class="big serif">{{ $promo->label() }}</div>
        <b style="font-size:1.4rem">untuk {{ $promo->product->name ?? 'Menu Pilihan' }}!</b>
        <span>Berlaku sampai {{ $promo->ends_at->translatedFormat('d F Y') }}</span>
        <div><a class="btn" href="{{ route('promotions.show', $promo->slug) }}">Lihat Promo →</a></div>
      @else
        <span class="tagp">Promo</span>
        <div class="big serif">Nantikan promo terbaru!</div>
        <span>Selalu ada kejutan menu spesial untukmu.</span>
        <div><a class="btn" href="{{ route('promotions') }}">Lihat Promo →</a></div>
      @endif
    </div>
    <div class="scanbox">
      <h2 class="serif">Scan QR di Meja</h2>
      <p class="muted">Pindai QR Code di meja untuk melihat menu dan memesan langsung.</p>
      <div><a class="btn dark" href="{{ route('scan') }}">Scan Sekarang</a></div>
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
      <a class="btn" href="{{ route('reservation') }}">Reservasi Sekarang →</a>
    </div>
  </div>
</section>
@endsection
