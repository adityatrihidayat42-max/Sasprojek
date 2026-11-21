@extends('layouts.app')
@section('title', $promo->name . ' - Arana Space')
@section('content')
<section class="section"><div class="container promo-detail">
  <a class="promo-back" href="{{ route('promotions') }}"><span aria-hidden="true">←</span> Semua Promo</a>

  <div class="promo-hero" style="--promo-photo:url('{{ $promo->photoUrl() }}')">
    <span class="promo-badge">✦ Promo Spesial</span>
    <h1 class="serif">{{ $promo->label() }}</h1>
    <div class="promo-hero-name">{{ $promo->name }}</div>
    <div class="promo-period">
      <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
      {{ $promo->starts_at->translatedFormat('d M Y') }} — {{ $promo->ends_at->translatedFormat('d M Y') }}
    </div>
    <span class="promo-status {{ $promo->isRunning() ? '' : 'ended' }}">{{ $promo->isRunning() ? '● Sedang Berlaku' : 'Berakhir' }}</span>
  </div>

  <div class="promo-detail-grid">
    <div class="panel">
      <h2 class="promo-section-title serif">Tentang Promo</h2>
      <p class="promo-lead">{{ $promo->description }}</p>

      @if($promo->terms)
        <div class="promo-terms">
          <h3 class="serif">Syarat &amp; Ketentuan</h3>
          <p>{{ $promo->terms }}</p>
        </div>
      @endif
    </div>

    <aside class="promo-info">
      @if($promo->product)
        <div class="promo-product">
          @if($promo->product->imageUrl())
            <img src="{{ $promo->product->imageUrl() }}" alt="{{ $promo->product->name }}">
          @else
            <span class="ph" aria-hidden="true">{{ $promo->product->emoji() }}</span>
          @endif
          <div>
            <b>{{ $promo->product->name }}</b>
            <small>{{ rp($promo->product->price) }} / porsi</small>
          </div>
        </div>
      @endif

      <div class="promo-note">
        <span aria-hidden="true">✓</span>
        <span><b>Otomatis saat checkout.</b> Tidak perlu kode promo — potongan langsung diterapkan pada pesanan yang memenuhi syarat.</span>
      </div>

      <a class="btn block" href="{{ route('menu') }}">Pesan Sekarang <span aria-hidden="true">→</span></a>
    </aside>
  </div>

  @if($others->isNotEmpty())
    <div class="promo-related">
      <h2 class="serif">Promo Lainnya</h2>
      <div class="promo-grid">
        @foreach($others as $p)
          @include('partials.promo-card', ['p' => $p])
        @endforeach
      </div>
    </div>
  @endif
</div></section>
@endsection
