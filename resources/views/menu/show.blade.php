@extends('layouts.app')
@section('title', $product->name . ' - BrewNest')
@section('content')
<section class="section"><div class="container">
  <div class="two-r">
    <div class="card"><div class="thumb" style="height:320px;font-size:8rem">
      @if($product->imageUrl())<img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">@else {{ $product->emoji() }} @endif
    </div></div>
    <div class="panel">
      <span class="pill">{{ $product->category->name }}</span>
      <h1 class="serif" style="margin:8px 0">{{ $product->name }}</h1>
      <p class="muted">{{ $product->description }}</p>
      <h2 class="price serif" style="margin:12px 0">{{ rp($product->price) }}</h2>
      @if($product->is_available)
        <form method="POST" action="{{ route('cart.add') }}">@csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <div class="form-group"><label>Jumlah</label><input type="number" name="qty" value="1" min="1" max="99"></div>
          <div class="form-group"><label>Catatan (opsional)</label><input name="note" maxlength="150" placeholder="Contoh: less sugar"></div>
          <button class="btn">🛒 Tambah ke Keranjang</button>
        </form>
      @else
        <span class="pill cancelled">Sedang tidak tersedia</span>
      @endif
    </div>
  </div>
  @if($related->count())
    <h2 class="serif" style="margin:30px 0 14px">Menu Lainnya</h2>
    <div class="grid">@foreach($related as $p) @include('partials.product-card', ['p' => $p]) @endforeach</div>
  @endif
</div></section>
@endsection
