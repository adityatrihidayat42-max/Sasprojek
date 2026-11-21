@extends('layouts.app')
@section('title', 'Promo - BrewNest')
@section('content')
<section class="section"><div class="container">
  <h1 class="serif" style="margin-bottom:14px">Promo BrewNest</h1>
  @if($promos->isEmpty())
    <p class="muted">Belum ada promo aktif. Nantikan penawaran terbaru!</p>
  @else
    <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
      @foreach($promos as $p)
        <a class="promo" href="{{ route('promotions.show', $p->slug) }}">
          <span class="tagp">PROMO</span>
          <div class="big serif">{{ $p->label() }}</div>
          <b>{{ $p->name }}</b>
          <span class="small">Sampai {{ $p->ends_at->translatedFormat('d F Y') }}</span>
        </a>
      @endforeach
    </div>
  @endif
</div></section>
@endsection
