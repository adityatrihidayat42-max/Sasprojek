@extends('layouts.app')
@section('title', $promo->name . ' - BrewNest')
@section('content')
<section class="section"><div class="container" style="max-width:760px">
  <div class="promo">
    <span class="tagp">PROMO</span>
    <div class="big serif">{{ $promo->label() }}</div>
    <b style="font-size:1.3rem">{{ $promo->name }}</b>
    <span>{{ $promo->starts_at->translatedFormat('d M Y') }} - {{ $promo->ends_at->translatedFormat('d M Y') }}
      @unless($promo->isRunning()) · <b>(tidak sedang berlaku)</b> @endunless</span>
  </div>
  <div class="panel" style="margin-top:18px">
    <p>{{ $promo->description }}</p>
    @if($promo->product)<p class="muted small" style="margin-top:8px">Berlaku untuk: {{ $promo->product->name }}</p>@endif
    @if($promo->terms)<h3 class="serif" style="margin-top:14px">Syarat &amp; Ketentuan</h3><p class="muted">{{ $promo->terms }}</p>@endif
    <p class="muted small" style="margin-top:10px">Diskon diterapkan otomatis saat checkout.</p>
    <a class="btn" style="margin-top:12px" href="{{ route('menu') }}">Pesan Sekarang →</a>
  </div>
</div></section>
@endsection
