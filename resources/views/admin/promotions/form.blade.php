@extends('layouts.admin')
@section('title', 'Form Promo')
@section('content')
@php $fmt = fn ($d) => $d ? $d->format('Y-m-d\TH:i') : ''; @endphp
<h1 class="serif">{{ $promo->exists ? 'Ubah Promo' : 'Tambah Promo' }}</h1>
<form class="panel" method="POST" action="{{ $promo->exists ? route('admin.promotions.update', $promo) : route('admin.promotions.store') }}">
  @csrf @if($promo->exists) @method('PUT') @endif
  <div class="form-group"><label>Nama promo *</label><input name="name" value="{{ old('name', $promo->name) }}" required></div>
  <div class="cols">
    <div class="form-group"><label>Jenis diskon *</label><select name="discount_type">
      <option value="percentage" @selected(old('discount_type', $promo->discount_type) === 'percentage')>Persentase (%)</option>
      <option value="fixed" @selected(old('discount_type', $promo->discount_type) === 'fixed')>Potongan nominal (Rp)</option></select></div>
    <div class="form-group"><label>Nilai diskon *</label><input type="number" step="any" min="0" name="discount_value" value="{{ old('discount_value', $promo->discount_value) }}" required></div>
  </div>
  <div class="form-group"><label>Berlaku untuk</label><select name="product_id"><option value="">Semua menu</option>
    @foreach($products as $p)<option value="{{ $p->id }}" @selected(old('product_id', $promo->product_id) == $p->id)>{{ $p->name }}</option>@endforeach</select></div>
  <div class="cols">
    <div class="form-group"><label>Mulai *</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $fmt($promo->starts_at)) }}" required></div>
    <div class="form-group"><label>Berakhir *</label><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $fmt($promo->ends_at)) }}" required></div>
  </div>
  <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="3">{{ old('description', $promo->description) }}</textarea></div>
  <div class="form-group"><label>Syarat &amp; ketentuan</label><textarea name="terms" rows="2">{{ old('terms', $promo->terms) }}</textarea></div>
  <input type="hidden" name="is_active" value="0"><label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promo->is_active))> Aktif</label>
  <p class="muted small" style="margin:8px 0">Jika beberapa promo berlaku, sistem memakai satu promo dengan potongan terbesar.</p>
  <button class="btn">Simpan</button> <a class="btn light" href="{{ route('admin.promotions.index') }}">Batal</a>
</form>
@endsection
