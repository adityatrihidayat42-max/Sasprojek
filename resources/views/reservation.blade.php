@extends('layouts.app')
@section('title', 'Reservasi Meja - BrewNest')
@section('content')
<section class="section"><div class="container" style="max-width:560px">
  <h1 class="serif" style="margin-bottom:6px">Reservasi Meja</h1>
  <p class="muted" style="margin-bottom:14px">Isi formulir berikut. Staf kami akan menghubungi Anda untuk konfirmasi.</p>
  <form class="panel" method="POST" action="{{ route('reservation.store') }}">@csrf
    <div class="form-group"><label>Nama *</label><input name="name" value="{{ old('name') }}" required></div>
    <div class="form-group"><label>Nomor HP *</label><input name="phone" value="{{ old('phone') }}" required></div>
    <div class="cols">
      <div class="form-group"><label>Tanggal &amp; jam *</label><input type="datetime-local" name="reserved_at" value="{{ old('reserved_at') }}" required></div>
      <div class="form-group"><label>Jumlah orang *</label><input type="number" name="guests" value="{{ old('guests', 2) }}" min="1" max="30" required></div>
    </div>
    <div class="form-group"><label>Catatan (opsional)</label><textarea name="note" rows="3">{{ old('note') }}</textarea></div>
    <button class="btn block">Kirim Reservasi</button>
  </form>
</div></section>
@endsection
