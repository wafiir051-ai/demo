@extends('layouts.public')
@section('title', 'Paket & Acara')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Acara &amp; penawaran</p><h1>Paket &amp; Acara</h1></div></section>
<section class="sec wrap">
    @forelse ($items as $p)@if ($loop->first)<div class="pkgrid">@endif @include('partials.pkg-card', ['p' => $p]) @if ($loop->last)</div>@endif
    @empty<p class="empty">Belum ada paket yang dipublikasikan.</p>@endforelse
</section>
@endsection
