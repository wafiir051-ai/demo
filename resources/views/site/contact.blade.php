@extends('layouts.public')
@section('title', 'Kontak')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Hubungi kami</p><h1>Kontak</h1></div></section>
<section class="sec wrap two contactsec">
    <div>
        <h2>Informasi</h2>
        @if ($biz->address)<p>{{ $biz->address }}</p>@endif
        @if ($biz->contact_phone)<p>Telepon: {{ $biz->contact_phone }}</p>@endif
        @if ($biz->contact_email)<p>Email: <a href="mailto:{{ $biz->contact_email }}">{{ $biz->contact_email }}</a></p>@endif
        <p>@if ($biz->maps_url)<a class="btn ghost" href="{{ $biz->maps_url }}" target="_blank" rel="noopener">Buka peta</a> @endif
           @if ($biz->whatsappUrl())<a class="btn wa" href="{{ $biz->whatsappUrl('Halo, saya ingin bertanya.') }}" target="_blank" rel="noopener">WhatsApp</a>@endif</p>
        <p>@if ($biz->instagram_url)<a href="{{ $biz->instagram_url }}" target="_blank" rel="noopener">Instagram</a> @endif @if ($biz->facebook_url)· <a href="{{ $biz->facebook_url }}" target="_blank" rel="noopener">Facebook</a>@endif</p>
    </div>
    <form class="rform" method="post" action="{{ route('contact.send') }}">
        @csrf
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <h2>Kirim pesan</h2>
        @if ($errors->any())<div class="alert err" role="alert"><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <label>Nama<input type="text" name="name" value="{{ old('name') }}" required></label>
        <div class="two"><label>Email<input type="email" name="email" value="{{ old('email') }}"></label><label>Telepon<input type="tel" name="phone" value="{{ old('phone') }}"></label></div>
        <label>Subjek<input type="text" name="subject" value="{{ old('subject') }}"></label>
        <label>Pesan<textarea name="message" rows="5" required>{{ old('message') }}</textarea></label>
        <button class="btn lg" type="submit">Kirim Pesan</button>
    </form>
</section>
@endsection
