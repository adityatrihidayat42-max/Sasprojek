@extends('layouts.app')
@section('title', 'Scan Meja - BrewNest')
@section('content')
<section class="section"><div class="container" style="max-width:560px">
  <div class="panel" style="text-align:center">
    <div style="font-size:3.5rem">▦</div>
    <h1 class="serif">Scan QR di Meja</h1>
    <p class="muted" style="margin:8px 0 16px">Buka kamera HP Anda, arahkan ke QR Code di meja, lalu ketuk tautan yang muncul. Anda bisa langsung memesan tanpa membuat akun.</p>
    <hr style="border:0;border-top:1px solid #eee;margin:14px 0">
    <p class="small muted" style="margin-bottom:8px">QR tidak bisa dipindai? Masukkan nomor meja:</p>
    <form method="POST" action="{{ route('scan.go') }}" class="row" style="justify-content:center">@csrf
      <input name="table_number" placeholder="Nomor meja" value="{{ old('table_number') }}" style="max-width:180px" required>
      <button class="btn">Pesan dari Meja</button>
    </form>
  </div>
</div></section>
@endsection
