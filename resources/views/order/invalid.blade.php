@extends('layouts.app')
@section('content')
<section class="section"><div class="container" style="max-width:560px;text-align:center">
  <div style="font-size:4rem">🚫</div>
  <h1 class="serif">QR tidak valid</h1>
  <p class="muted" style="margin:10px 0 18px">QR Code meja ini tidak ditemukan atau sudah tidak berlaku. Silakan hubungi staf BrewNest.</p>
  <a class="btn" href="{{ route('menu') }}">Lihat Menu</a>
</div></section>
@endsection
