<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'BrewNest Coffee Shop')</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a href="{{ route('home') }}" class="brand"><span style="font-size:2rem">☕</span><span><b>BrewNest</b><small>COFFEE SHOP</small></span></a>
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
<footer>
  <div class="container foot">
    <div><div class="brand" style="color:#fff"><span style="font-size:2rem">☕</span><span><b>BrewNest</b><small>COFFEE SHOP</small></span></div>
      <p style="margin-top:10px">Good coffee, good mood.</p></div>
    <div><h4>Quick Links</h4>
      <a href="{{ route('home') }}">Home</a><a href="{{ route('menu') }}">Menu</a><a href="{{ route('promotions') }}">Promo</a>
      <a href="{{ route('scan') }}">Scan Meja</a><a href="{{ route('reservation') }}">Reservasi</a></div>
    <div><h4>Contact Us</h4><p>📞 +62 812 3456 7890</p><p>📍 Jl. Kopi No.123, Bandung</p><p>✉️ hello@brewnest.id</p></div>
    <div><h4>Follow Us</h4><a href="#">Instagram</a><a href="#">TikTok</a><a href="#">YouTube</a><a href="#">Facebook</a></div>
  </div>
  <div class="copy">© {{ date('Y') }} BrewNest. All Rights Reserved.</div>
</footer>
</body>
</html>
