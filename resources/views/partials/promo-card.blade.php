@php
    $running = $p->isRunning();
@endphp
<a class="promo-card" style="--promo-photo:url('{{ $p->photoUrl() }}')" href="{{ route('promotions.show', $p->slug) }}">
  <div>
    <div class="promo-card-top">
      <span class="promo-badge">✦ Promo</span>
      <span class="promo-status {{ $running ? '' : 'ended' }}">{{ $running ? '● Aktif' : 'Berakhir' }}</span>
    </div>
    <div class="promo-card-discount serif">{{ $p->label() }}</div>
    <div class="promo-card-title">{{ $p->name }}</div>
    @if($p->product)<div class="promo-card-product">untuk {{ $p->product->name }}</div>@endif
  </div>
  <div class="promo-card-foot">
    <span class="date">
      <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
      s.d. {{ $p->ends_at->translatedFormat('d M Y') }}
    </span>
    <span class="promo-card-go">Lihat Detail <span aria-hidden="true">→</span></span>
  </div>
</a>
