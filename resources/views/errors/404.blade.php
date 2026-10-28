@extends('layouts.public')
@section('title', 'Halaman tidak ditemukan')
@section('content')
<section class="sec wrap narrow center"><p class="eyebrow">Error 404</p><h1>Halaman tidak ditemukan</h1><p>Alamat yang Anda tuju tidak ada atau sudah dipindahkan.</p><p><a class="btn" href="{{ route('home') }}">Kembali ke beranda</a></p></section>
@endsection
