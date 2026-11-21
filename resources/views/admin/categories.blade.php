@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<h1 class="serif">Kategori</h1>
<form class="panel row" method="POST" action="{{ route('admin.categories.store') }}">@csrf
  <input name="name" placeholder="Nama kategori" required style="max-width:220px">
  <input name="description" placeholder="Deskripsi (opsional)" style="max-width:280px">
  <input type="hidden" name="is_active" value="1">
  <button class="btn sm">+ Tambah</button>
</form>
@foreach($categories as $c)
  <div class="panel row">
    <form method="POST" action="{{ route('admin.categories.update', $c) }}" class="row" style="flex:1">@csrf @method('PUT')
      <input name="name" value="{{ $c->name }}" required style="max-width:200px">
      <input name="description" value="{{ $c->description }}" placeholder="Deskripsi" style="max-width:260px">
      <input type="hidden" name="is_active" value="0"><label class="check"><input type="checkbox" name="is_active" value="1" @checked($c->is_active)>Aktif</label>
      <span class="muted small">{{ $c->products_count }} produk</span>
      <button class="btn sm light">Simpan</button>
    </form>
    <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form>
  </div>
@endforeach
@endsection
