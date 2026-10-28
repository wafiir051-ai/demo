<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@hasSection('title')@yield('title') — {{ $biz->business_name }}@else{{ $biz->website_title ?: $biz->business_name }}@endif</title>
<meta name="description" content="@yield('description', $biz->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $biz->description), 155))">
@if ($biz->favicon_path)<link rel="icon" href="{{ Media::url($biz->favicon_path) }}">@endif
<style>:root{--primary: {{ preg_match('/^#[0-9a-f]{6}$/i', $biz->primary_color) ? $biz->primary_color : '#2f4a3a' }};--secondary: {{ preg_match('/^#[0-9a-f]{6}$/i', $biz->secondary_color) ? $biz->secondary_color : '#b8895a' }};}</style>
<link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
<a class="skip" href="#main">Lewati ke konten</a>
@if ($biz->is_demo)<div class="demo">Mode demo: seluruh data di situs ini adalah contoh dan belum diverifikasi.</div>@endif
<header class="nav" id="nav">
    <div class="wrap navin">
        <a class="logo" href="{{ route('home') }}">@if ($biz->logo_path)<img src="{{ Media::url($biz->logo_path) }}" alt="{{ $biz->business_name }}">@else{{ $biz->business_name }}@endif</a>
        <button class="burger" type="button" aria-label="Buka menu" aria-expanded="false" onclick="const n=document.getElementById('nav');const o=n.classList.toggle('open');this.setAttribute('aria-expanded',o)">☰</button>
        <nav class="links" aria-label="Menu utama">
            <a href="{{ route('home') }}" @class(['on' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('accommodations') }}" @class(['on' => request()->routeIs('accommodation*')])>Akomodasi</a>
            <a href="{{ route('facilities') }}" @class(['on' => request()->routeIs('facilities')])>Fasilitas</a>
            <a href="{{ route('packages') }}" @class(['on' => request()->routeIs('packages')])>Paket &amp; Acara</a>
            <a href="{{ route('gallery') }}" @class(['on' => request()->routeIs('gallery')])>Galeri</a>
            <a href="{{ route('about') }}" @class(['on' => request()->routeIs('about')])>Tentang</a>
            <a href="{{ route('contact') }}" @class(['on' => request()->routeIs('contact')])>Kontak</a>
            <a class="btn" href="{{ route('reservation') }}">Reservasi</a>
        </nav>
    </div>
</header>
<main id="main">
    @if (session('success'))<div class="wrap"><div class="alert ok" role="status">{{ session('success') }}</div></div>@endif
    @yield('content')
</main>
<footer class="foot">
    <div class="wrap fgrid">
        <div><strong class="flogo">{{ $biz->business_name }}</strong><p>{{ $biz->tagline }}</p></div>
        <div><h3>Kontak</h3>
            @if ($biz->address)<p>{{ $biz->address }}</p>@endif
            @if ($biz->contact_phone)<p>Telp: {{ $biz->contact_phone }}</p>@endif
            @if ($biz->contact_email)<p><a href="mailto:{{ $biz->contact_email }}">{{ $biz->contact_email }}</a></p>@endif
            @if ($biz->whatsappUrl())<p><a href="{{ $biz->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a></p>@endif
        </div>
        <div><h3>Jelajahi</h3>
            <p><a href="{{ route('accommodations') }}">Akomodasi</a></p><p><a href="{{ route('packages') }}">Paket &amp; Acara</a></p><p><a href="{{ route('gallery') }}">Galeri</a></p>
            @foreach ($footerPages as $p)<p><a href="{{ route('page.show', $p->slug) }}">{{ $p->title }}</a></p>@endforeach
        </div>
        <div><h3>Ikuti kami</h3>
            @if ($biz->instagram_url)<p><a href="{{ $biz->instagram_url }}" target="_blank" rel="noopener">Instagram</a></p>@endif
            @if ($biz->facebook_url)<p><a href="{{ $biz->facebook_url }}" target="_blank" rel="noopener">Facebook</a></p>@endif
            @if ($biz->maps_url)<p><a href="{{ $biz->maps_url }}" target="_blank" rel="noopener">Lihat di peta</a></p>@endif
        </div>
    </div>
    <div class="wrap credit">© {{ date('Y') }} {{ $biz->business_name }} · Website oleh <a href="https://www.godingofficial.com/" target="_blank" rel="noopener">Goding</a> — <em>Build Your Digital Presence with Goding.</em> · <a href="https://instagram.com/goding.id" target="_blank" rel="noopener">@goding.id</a></div>
</footer>
<div class="lightbox" id="lb" hidden role="dialog" aria-modal="true" aria-label="Pratinjau foto"><button type="button" aria-label="Tutup" id="lbx">×</button><img alt=""><p></p></div>
<script>
(function(){var lb=document.getElementById('lb');if(!lb)return;var im=lb.querySelector('img'),cp=lb.querySelector('p');
function close(){lb.hidden=true}
document.addEventListener('click',function(e){var a=e.target.closest('[data-lightbox]');if(a){e.preventDefault();im.src=a.href;im.alt=a.dataset.alt||'';cp.textContent=a.dataset.caption||'';lb.hidden=false;document.getElementById('lbx').focus();return}
if(e.target===lb||e.target.id==='lbx')close()});
document.addEventListener('keydown',function(e){if(e.key==='Escape')close()});})();
</script>
</body>
</html>
