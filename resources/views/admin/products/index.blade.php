@extends('layouts.admin')
@section('title', 'Produk')
@section('content')
<div class="row" style="justify-content:space-between"><h1 class="serif">Produk</h1><a class="btn" href="{{ route('admin.products.create') }}">+ Tambah Produk</a></div>
<form method="GET" class="row" style="margin:10px 0"><input name="q" value="{{ request('q') }}" placeholder="Cari produk" style="max-width:260px"><button class="btn sm">Cari</button></form>
<div class="table-wrap"><table class="admin-product-table">
  <tr><th>Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Tampil</th><th>Aksi</th></tr>
  @foreach($products as $p)
    <tr>
      <td>
        @if($p->imageUrl())
          <img class="admin-product-thumb" src="{{ $p->imageUrl() }}" alt="Foto {{ $p->name }}" loading="lazy">
        @else
          <div class="admin-product-placeholder">Foto belum tersedia</div>
        @endif
      </td>
      <td><b>{{ $p->name }}</b>@if($p->is_best_seller) <span class="pill">⭐ Best</span>@endif</td>
      <td>{{ $p->category->name }}</td><td>{{ rp($p->price) }}</td>
      <td><span class="pill {{ $p->is_available ? 'ready' : 'cancelled' }}">{{ $p->is_available ? 'Tersedia' : 'Habis' }}</span></td>
      <td>{{ $p->is_active ? 'Ya' : 'Tidak' }}</td>
      <td class="row"><a class="btn sm light" href="{{ route('admin.products.edit', $p) }}">Ubah</a>
        <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form></td></tr>
  @endforeach
</table></div>
{{ $products->links() }}
@endsection
