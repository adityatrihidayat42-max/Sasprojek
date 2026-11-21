@extends('layouts.admin')
@section('title', 'Form Produk')
@section('content')
<h1 class="serif">{{ $product->exists ? 'Ubah Produk' : 'Tambah Produk' }}</h1>
<form class="panel" method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
  @csrf @if($product->exists) @method('PUT') @endif
  <div class="cols">
    <div class="form-group"><label>Nama *</label><input name="name" value="{{ old('name', $product->name) }}" required></div>
    <div class="form-group"><label>Kategori *</label><select name="category_id" required>
      @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
  </div>
  <div class="cols">
    <div class="form-group"><label>Harga (Rp) *</label><input type="number" name="price" min="0" step="500" value="{{ old('price', $product->price) }}" required></div>
    <div class="form-group"><label>Foto (jpg/png/webp, maks 2 MB)</label><input type="file" name="image" accept="image/*"></div>
  </div>
  @if($product->image)<p class="muted small" style="margin-bottom:10px">Foto saat ini: <a class="link" target="_blank" href="{{ $product->imageUrl() }}">lihat</a> (unggah baru untuk mengganti)</p>@endif
  <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="3">{{ old('description', $product->description) }}</textarea></div>
  <div class="form-group">
    <input type="hidden" name="is_available" value="0"><label class="check"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $product->is_available))> Tersedia</label>
    <input type="hidden" name="is_active" value="0"><label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Tampil di website</label>
    <input type="hidden" name="is_best_seller" value="0"><label class="check"><input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller))> Best seller</label>
  </div>
  <button class="btn">Simpan</button> <a class="btn light" href="{{ route('admin.products.index') }}">Batal</a>
</form>
@endsection
