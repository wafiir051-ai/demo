@extends('layouts.public')
@section('content')
<section class="hero" style="background-image:url('{{ Media::url($biz->hero_image_path, $biz->business_name) }}')">
    <div class="wrap heroin">
        <p class="eyebrow">{{ $biz->business_type }}</p>
        <h1>{{ $biz->tagline ?: $biz->business_name }}</h1>
        <p class="lead">{{ \Illuminate\Support\Str::limit(strip_tags((string) $biz->description), 200) }}</p>
        <p><a class="btn lg" href="{{ route('reservation') }}">Ajukan Reservasi</a> <a class="btn lg light" href="{{ route('accommodations') }}">Lihat Akomodasi</a></p>
    </div>
</section>

<section class="sec wrap intro">
    <div><p class="eyebrow">Selamat datang</p><h2>{{ $biz->business_name }}</h2></div>
    <div><p>{!! nl2br(e($biz->description)) !!}</p><p><a class="more" href="{{ route('about') }}">Selengkapnya tentang kami →</a></p></div>
</section>

<section class="sec alt"><div class="wrap">
    <div class="sechead"><p class="eyebrow">Menginap</p><h2>Akomodasi</h2></div>
    @forelse ($accommodations as $a)@if ($loop->first)<div class="accgrid">@endif @include('partials.acc-card', ['a' => $a]) @if ($loop->last)</div>@endif
    @empty<p class="empty">Belum ada akomodasi yang dipublikasikan.</p>@endforelse
</div></section>

<section class="sec wrap">
    <div class="sechead"><p class="eyebrow">Kenyamanan</p><h2>Fasilitas</h2></div>
    @forelse ($facilities as $f)@if ($loop->first)<div class="facgrid">@endif
        <div class="fac"><span class="ico" aria-hidden="true">{{ $f->icon ?: '✦' }}</span><h3>{{ $f->name }}</h3><p>{{ $f->description }}</p></div>
    @if ($loop->last)</div>@endif @empty<p class="empty">Belum ada fasilitas.</p>@endforelse
</section>

@if ($packages->count())
<section class="sec alt"><div class="wrap">
    <div class="sechead"><p class="eyebrow">Acara &amp; penawaran</p><h2>Paket Unggulan</h2></div>
    <div class="pkgrid">@foreach ($packages as $p) @include('partials.pkg-card', ['p' => $p]) @endforeach</div>
    <p class="center"><a class="more" href="{{ route('packages') }}">Semua paket →</a></p>
</div></section>
@endif

@if ($gallery->count())
<section class="sec wrap">
    <div class="sechead"><p class="eyebrow">Suasana</p><h2>Galeri</h2></div>
    <div class="gal">@foreach ($gallery as $g)<a href="{{ Media::url($g->image_path, $g->title) }}" data-lightbox data-alt="{{ $g->alt_text ?: $g->title }}" data-caption="{{ $g->title }}"><img src="{{ Media::url($g->image_path, $g->title) }}" alt="{{ $g->alt_text ?: $g->title }}" loading="lazy"></a>@endforeach</div>
    <p class="center"><a class="more" href="{{ route('gallery') }}">Lihat semua foto →</a></p>
</section>
@endif

<section class="sec alt"><div class="wrap two contactsec">
    <div><p class="eyebrow">Lokasi &amp; kontak</p><h2>Kunjungi kami</h2>
        @if ($biz->address)<p>{{ $biz->address }}</p>@endif
        @if ($biz->contact_phone)<p>Telepon: {{ $biz->contact_phone }}</p>@endif
        @if ($biz->contact_email)<p>Email: <a href="mailto:{{ $biz->contact_email }}">{{ $biz->contact_email }}</a></p>@endif
        <p>@if ($biz->maps_url)<a class="btn ghost" href="{{ $biz->maps_url }}" target="_blank" rel="noopener">Buka peta</a>@endif
           @if ($biz->whatsappUrl('Halo, saya ingin bertanya tentang ' . $biz->business_name))<a class="btn wa" href="{{ $biz->whatsappUrl('Halo, saya ingin bertanya tentang ' . $biz->business_name) }}" target="_blank" rel="noopener">Chat WhatsApp</a>@endif</p>
    </div>
    <div class="cta"><h2>Siap menginap?</h2><p>Kirim permintaan reservasi dan kami akan menghubungi Anda untuk konfirmasi.</p><a class="btn lg" href="{{ route('reservation') }}">Ajukan Reservasi</a></div>
</div></section>
@endsection
