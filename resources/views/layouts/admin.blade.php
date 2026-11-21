<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') - BrewNest</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
@php $role = auth()->user()->role; @endphp
<div class="admin">
  <aside class="sidebar">
    <div class="brand"><span style="font-size:1.8rem">☕</span><span><b>BrewNest</b><small>{{ strtoupper($role) }}</small></span></div>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
    <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">🧾 Pesanan</a>
    @if(in_array($role, ['admin','cashier']))
      <a href="{{ route('admin.reservations') }}" class="{{ request()->routeIs('admin.reservations*') ? 'active' : '' }}">🪑 Reservasi</a>
    @endif
    @if($role === 'admin')
      <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}">☕ Produk</a>
      <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">🗂 Kategori</a>
      <a href="{{ route('admin.tables.index') }}" class="{{ request()->routeIs('admin.tables*') ? 'active' : '' }}">▦ Meja &amp; QR</a>
      <a href="{{ route('admin.promotions.index') }}" class="{{ request()->routeIs('admin.promotions*') ? 'active' : '' }}">🎁 Promo</a>
      <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}">👥 Staf</a>
    @endif
    <a href="{{ route('home') }}">🌐 Lihat Website</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="lnk">🚪 Keluar ({{ auth()->user()->name }})</button></form>
  </aside>
  <main class="admin-main">
    @if(session('success'))<div class="alert ok">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
  </main>
</div>
</body>
</html>
