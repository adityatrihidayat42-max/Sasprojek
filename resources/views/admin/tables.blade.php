@extends('layouts.admin')
@section('title', 'Meja & QR')
@section('content')
<h1 class="serif">Meja &amp; QR Code</h1>
<p class="muted small" style="margin-bottom:10px">Buka halaman ini lewat alamat yang akan dipakai pelanggan (mis. IP/domain coffee shop) sebelum mencetak QR, karena QR memuat alamat tersebut.</p>
<form class="panel row" method="POST" action="{{ route('admin.tables.store') }}">@csrf
  <input name="table_number" placeholder="Nomor/nama meja" required style="max-width:220px"><button class="btn sm">+ Tambah Meja</button>
</form>
<div class="table-wrap"><table>
  <tr><th>Meja</th><th>Aktif</th><th>Pesanan</th><th>QR</th></tr>
  @foreach($tables as $t)
    <tr>
      <td><form method="POST" action="{{ route('admin.tables.update', $t) }}" class="row">@csrf @method('PUT')
        <input name="table_number" value="{{ $t->table_number }}" style="max-width:110px" required>
        <input type="hidden" name="is_active" value="0"><label class="check"><input type="checkbox" name="is_active" value="1" @checked($t->is_active)>Aktif</label>
        <button class="btn sm light">Simpan</button></form></td>
      <td>{{ $t->is_active ? 'Ya' : 'Tidak' }}</td>
      <td>{{ $t->orders_count }}</td>
      <td class="row">
        <a class="btn sm" target="_blank" href="{{ route('admin.tables.print', $t) }}">🖨 Lihat/Cetak QR</a>
        <form method="POST" action="{{ route('admin.tables.regenerate', $t) }}" onsubmit="return confirm('QR lama tidak akan berlaku. Lanjut?')">@csrf<button class="btn sm light">Buat ulang</button></form>
        <form method="POST" action="{{ route('admin.tables.destroy', $t) }}" onsubmit="return confirm('Hapus meja?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form>
      </td>
    </tr>
  @endforeach
</table></div>
@endsection
