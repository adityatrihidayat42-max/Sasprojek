<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>QR Meja {{ $cafeTable->table_number }}</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"></head>
<body>
<div class="qr-card">
  <h1 class="serif">☕ BrewNest</h1>
  <p class="muted">Scan untuk memesan dari</p>
  <h2 class="serif" style="font-size:2.4rem;margin-bottom:10px">Meja {{ $cafeTable->table_number }}</h2>
  {!! $svg !!}
  <p class="muted small" style="margin:10px 0;word-break:break-all">{{ $url }}</p>
  <button class="btn noprint" onclick="window.print()">🖨 Cetak</button>
  <a class="btn light noprint" href="{{ route('admin.tables.index') }}">Kembali</a>
</div>
</body></html>
