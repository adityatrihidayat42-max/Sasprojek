<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Arana Space — Coffee & Eatery')</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/arana-space-icon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a href="{{ route('home') }}" class="brand"><img src="{{ asset('images/arana-space-logo.svg') }}" alt="Arana Space" style="width:190px;height:auto"></a>
    <ul class="menu">
      <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
      <li><a href="{{ route('menu') }}" class="{{ request()->routeIs('menu*') ? 'active' : '' }}">Menu</a></li>
      <li><a href="{{ route('promotions') }}" class="{{ request()->routeIs('promotions*') ? 'active' : '' }}">Promo</a></li>
      <li><a href="{{ route('scan') }}" class="{{ request()->routeIs('scan') ? 'active' : '' }}">Scan Meja</a></li>
      <li><a href="{{ route('reservation') }}" class="{{ request()->routeIs('reservation') ? 'active' : '' }}">Reservasi</a></li>
      <li><a href="{{ route('home') }}#tentang">Tentang</a></li>
    </ul>
    <div class="nav-actions">
      <a href="{{ route('cart') }}" class="cart-link">🛒@if(\App\Support\Cart::count())<span class="badge">{{ \App\Support\Cart::count() }}</span>@endif</a>
      @auth
        <a class="btn sm outline" href="{{ route('admin.dashboard') }}">Dashboard</a>
      @else
        <a class="btn sm outline" href="{{ route('login') }}">👤 Masuk</a>
      @endauth
    </div>
  </div>
</header>
@php $tbl = \App\Support\Cart::table(); @endphp
@if($tbl)<div class="table-banner">🪑 Anda memesan dari <b>Meja {{ $tbl->table_number }}</b></div>@endif
<main>
  <div class="container">
    @if(session('success'))<div class="alert ok">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  </div>
  @yield('content')
</main>
<footer class="site-footer">
  <div class="container footer-grid">
    <section class="footer-brand">
      <a href="{{ route('home') }}" class="footer-logo"><img src="{{ asset('images/arana-space-logo.svg') }}" alt="Arana Space"></a>
      <p class="footer-tagline">Good Food, Good Mood.</p>
      <p class="footer-description">Tempat menikmati kopi, hidangan lezat, dan momen santai dalam suasana yang hangat.</p>
    </section>
    <nav class="footer-column" aria-label="Navigasi">
      <h4>Jelajahi</h4>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('menu') }}">Menu</a>
      <a href="{{ route('promotions') }}">Promo</a>
      <a href="{{ route('scan') }}">Scan Meja</a>
      <a href="{{ route('reservation') }}">Reservasi</a>
    </nav>
    <section class="footer-column">
      <h4>Hubungi Kami</h4>
      <p><span class="footer-symbol">☎</span><span>+62 812 3456 7890</span></p>
      <p><span class="footer-symbol">⌖</span><span>Indonesia</span></p>
      <p><span class="footer-symbol">✉</span><a href="mailto:hello@aranaspace.id">hello@aranaspace.id</a></p>
    </section>
    <section class="footer-column">
      <h4>Ikuti Kami</h4>
      <p class="footer-social-note">Ikuti kabar terbaru, menu spesial, dan promo Arana Space.</p>
      <div class="footer-socials">
        <a href="#" aria-label="Instagram">Instagram <span>↗</span></a>
        <a href="#" aria-label="TikTok">TikTok <span>↗</span></a>
        <a href="#" aria-label="YouTube">YouTube <span>↗</span></a>
      </div>
    </section>
  </div>
  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <span>© {{ date('Y') }} Arana Space. All rights reserved.</span>
      <span>Made for good moments <b>✦</b></span>
    </div>
  </div>
</footer>
</body>
</html>
