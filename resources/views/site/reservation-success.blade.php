@extends('layouts.public')
@section('title', 'Permintaan terkirim')
@section('content')
<section class="sec wrap narrow center">
    <p class="eyebrow">Permintaan diterima</p>
    <h1>Terima kasih!</h1>
    <p>Permintaan reservasi Anda sudah kami simpan. <strong>Ini belum merupakan konfirmasi pemesanan.</strong></p>
    <p class="codebox">{{ $res->reservation_code }}</p>
    <p>{{ $res->accommodation?->name }} · {{ $res->check_in->format('d M Y') }} – {{ $res->check_out->format('d M Y') }}<br>Status: <strong>{{ \App\Models\Reservation::STATUSES[$res->status] }}</strong></p>
    <p>Simpan kode di atas untuk referensi.</p>
    @php $wa = $biz->whatsappUrl('Halo, saya sudah mengajukan reservasi dengan kode ' . $res->reservation_code . '.'); @endphp
    <p>@if ($wa)<a class="btn wa" href="{{ $wa }}" target="_blank" rel="noopener">Konfirmasi via WhatsApp</a> @endif<a class="btn ghost" href="{{ route('home') }}">Kembali ke beranda</a></p>
</section>
@endsection
