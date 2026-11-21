<div class="card">
  <a href="{{ route('menu.show', $p->slug) }}" class="thumb">
    @if($p->imageUrl())<img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}">@else {{ $p->emoji() }} @endif
    @if($p->is_best_seller)<span class="tag">Best Seller</span>@endif
  </a>
  <div class="card-body">
    <h3 class="serif">{{ $p->name }}</h3>
    <p class="muted small">{{ \Illuminate\Support\Str::limit($p->description, 60) }}</p>
    <div class="card-foot">
      <b class="price">{{ rp($p->price) }}</b>
      @if($p->is_available)
        <form method="POST" action="{{ route('cart.add') }}">@csrf
          <input type="hidden" name="product_id" value="{{ $p->id }}">
          <button class="btn sm dark">🛒 Tambah</button>
        </form>
      @else
        <span class="pill cancelled">Habis</span>
      @endif
    </div>
  </div>
</div>
