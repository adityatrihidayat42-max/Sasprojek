@extends('layouts.admin')
@section('title', 'Promo')
@section('content')
<div class="row" style="justify-content:space-between"><h1 class="serif">Promo</h1><a class="btn" href="{{ route('admin.promotions.create') }}">+ Tambah Promo</a></div>
<div class="table-wrap" style="margin-top:12px"><table>
  <tr><th>Nama</th><th>Diskon</th><th>Berlaku untuk</th><th>Periode</th><th>Status</th><th></th></tr>
  @forelse($promos as $p)
    <tr><td><b>{{ $p->name }}</b></td><td>{{ $p->label() }}</td><td>{{ $p->product->name ?? 'Semua menu' }}</td>
      <td>{{ $p->starts_at->format('d/m/y') }} - {{ $p->ends_at->format('d/m/y') }}</td>
      <td><span class="pill {{ $p->isRunning() ? 'ready' : 'cancelled' }}">{{ $p->isRunning() ? 'Berjalan' : 'Tidak aktif' }}</span></td>
      <td class="row"><a class="btn sm light" href="{{ route('admin.promotions.edit', $p) }}">Ubah</a>
        <form method="POST" action="{{ route('admin.promotions.destroy', $p) }}" onsubmit="return confirm('Hapus promo?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form></td></tr>
  @empty
    <tr><td colspan="6" class="muted">Belum ada promo.</td></tr>
  @endforelse
</table></div>
@endsection
