@extends('layouts.app')
@section('title', 'Menu - BrewNest')
@section('content')
<section class="section"><div class="container">
  <h1 class="serif">Menu BrewNest</h1>
  <form method="GET" class="row" style="margin:14px 0">
    <input name="q" value="{{ request('q') }}" placeholder="Cari menu..." style="max-width:320px">
    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
    <button class="btn">Cari</button>
  </form>
  <div class="chips">
    <a class="chip {{ request('category') ? '' : 'active' }}" href="{{ route('menu') }}">Semua</a>
    @foreach($categories as $c)
      <a class="chip {{ request('category') == $c->id ? 'active' : '' }}" href="{{ route('menu', ['category' => $c->id]) }}">{{ $c->name }}</a>
    @endforeach
  </div>
  @if($products->isEmpty())
    <p class="muted">Menu tidak ditemukan.</p>
  @else
    <div class="grid">@foreach($products as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
  @endif
</div></section>
@endsection
