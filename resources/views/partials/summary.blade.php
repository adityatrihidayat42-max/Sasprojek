<div class="sum-line"><span>Subtotal</span><span>{{ rp($totals['subtotal']) }}</span></div>
@if($totals['promo'])
  <div class="sum-line ok"><span>🎁 {{ $totals['promo']->name }}</span><span>- {{ rp($totals['discount']) }}</span></div>
@endif
<div class="sum-line total"><span>Total</span><span>{{ rp($totals['total']) }}</span></div>
