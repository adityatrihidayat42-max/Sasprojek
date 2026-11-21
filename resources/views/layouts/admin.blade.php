<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') - Arana Space</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/arana-space-icon.svg') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
@php $role = auth()->user()->role; @endphp
<div class="admin">
  <aside class="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="admin-brand"><img src="{{ asset('images/arana-space-logo.svg') }}" alt="Arana Space"><span><small>{{ strtoupper($role) }} PANEL</small></span></a>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="nav-icon">▦</span> Dashboard</a>
    <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><span class="nav-icon">▤</span> Pesanan</a>
    @if(in_array($role, ['admin','cashier']))
      <a href="{{ route('admin.shifts') }}" class="{{ request()->routeIs('admin.shifts*') ? 'active' : '' }}"><span class="nav-icon">◷</span> Shift Kasir</a>
    @endif
    @if(in_array($role, ['admin','cashier']))
      <a href="{{ route('admin.orders.scan') }}" class="{{ request()->routeIs('admin.orders.scan*') ? 'active' : '' }}"><span class="nav-icon">▣</span> Scan QR Pesanan</a>
      <a href="{{ route('admin.reservations') }}" class="{{ request()->routeIs('admin.reservations*') ? 'active' : '' }}"><span class="nav-icon">▣</span> Reservasi</a>
    @endif
    @if($role === 'admin')
      <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}"><span class="nav-icon">◈</span> Produk</a>
      <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}"><span class="nav-icon">▧</span> Kategori</a>
      <a href="{{ route('admin.tables.index') }}" class="{{ request()->routeIs('admin.tables*') ? 'active' : '' }}"><span class="nav-icon">▦</span> Meja &amp; QR</a>
      <a href="{{ route('admin.promotions.index') }}" class="{{ request()->routeIs('admin.promotions*') ? 'active' : '' }}"><span class="nav-icon">✧</span> Promo</a>
      <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}"><span class="nav-icon">♙</span> Staf</a>
    @endif
    <a href="{{ route('home') }}"><span class="nav-icon">↗</span> Lihat Website</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="lnk"><span class="nav-icon">⇥</span> Keluar ({{ auth()->user()->name }})</button></form>
  </aside>
  <main class="admin-main">
    @if(session('success'))<div class="alert ok">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
  </main>
</div>
</body>
</html>
