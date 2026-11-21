@extends('layouts.app')
@section('title', 'Masuk - BrewNest')
@section('content')
<div class="container"><form class="panel auth" method="POST" action="{{ route('login.post') }}">@csrf
  <h1 class="serif" style="margin-bottom:12px">Masuk Staf</h1>
  <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
  <div class="form-group"><label>Kata sandi</label><input type="password" name="password" required></div>
  <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
  <button class="btn block" style="margin-top:12px">Masuk</button>
</form></div>
@endsection
