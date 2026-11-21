@extends('layouts.admin')
@section('title', 'Reservasi')
@section('content')
<h1 class="serif">Reservasi Meja</h1>
<div class="table-wrap"><table>
  <tr><th>Waktu</th><th>Nama</th><th>HP</th><th>Tamu</th><th>Catatan</th><th>Status</th></tr>
  @forelse($reservations as $r)
    <tr><td>{{ $r->reserved_at->translatedFormat('d M Y H:i') }}</td><td>{{ $r->name }}</td><td>{{ $r->phone }}</td><td>{{ $r->guests }}</td><td>{{ $r->note }}</td>
      <td><form method="POST" action="{{ route('admin.reservations.update', $r) }}" class="row">@csrf @method('PATCH')
        <select name="status" style="max-width:150px">@foreach(\App\Models\Reservation::STATUSES as $k => $v)<option value="{{ $k }}" @selected($r->status === $k)>{{ $v }}</option>@endforeach</select>
        <button class="btn sm light">Simpan</button></form></td></tr>
  @empty
    <tr><td colspan="6" class="muted">Belum ada reservasi.</td></tr>
  @endforelse
</table></div>
{{ $reservations->links() }}
@endsection
