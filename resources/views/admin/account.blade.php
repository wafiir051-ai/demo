@extends('layouts.admin')
@section('title', 'Pengaturan Akun')
@section('crumb', 'Pengaturan Akun')
@section('content')
<div class="page-head"><h1>Pengaturan Akun</h1></div>
<form class="card form" method="post" action="{{ route('admin.account.update') }}">
    @csrf @method('PUT')
    <label><span>Email (hanya Super Admin yang dapat mengubah lewat menu Pengguna)</span><input type="email" value="{{ $user->email }}" disabled></label>
    <label><span>Nama</span><input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
    <h2>Ganti password (opsional)</h2>
    <label><span>Password saat ini</span><input type="password" name="current_password" autocomplete="current-password"></label>
    <label><span>Password baru (min. 8 karakter)</span><input type="password" name="password" autocomplete="new-password"></label>
    <label><span>Ulangi password baru</span><input type="password" name="password_confirmation" autocomplete="new-password"></label>
    <div class="formacts"><button class="btn">Simpan</button></div>
</form>
@endsection
