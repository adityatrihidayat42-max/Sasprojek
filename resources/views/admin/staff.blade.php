@extends('layouts.admin')
@section('title', 'Staf')
@section('content')
<h1 class="serif">Akun Staf</h1>
<form class="panel row" method="POST" action="{{ route('admin.staff.store') }}">@csrf
  <input name="name" placeholder="Nama" required style="max-width:170px">
  <input type="email" name="email" placeholder="Email" required style="max-width:220px">
  <input type="password" name="password" placeholder="Kata sandi (min 8)" required style="max-width:190px">
  <select name="role" style="max-width:140px"><option value="cashier">Kasir</option><option value="kitchen">Barista</option><option value="admin">Admin</option></select>
  <button class="btn sm">+ Tambah</button>
</form>
<div class="table-wrap"><table>
  <tr><th>Nama</th><th>Email</th><th>Peran</th><th></th></tr>
  @foreach($users as $u)
    <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td><span class="pill">{{ $u->role }}</span></td>
      <td>@if($u->id !== auth()->id())<form method="POST" action="{{ route('admin.staff.destroy', $u) }}" onsubmit="return confirm('Hapus akun?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form>@endif</td></tr>
  @endforeach
</table></div>
@endsection
