@extends('layouts.public')
@section('title', 'Galeri')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Suasana</p><h1>Galeri</h1></div></section>
<section class="sec wrap">
    @if ($cats->count())
    <nav class="chips" aria-label="Filter kategori"><a href="{{ route('gallery') }}" @class(['on' => !$active])>Semua</a>@foreach ($cats as $c)<a href="{{ route('gallery', ['kategori' => $c]) }}" @class(['on' => $active === $c])>{{ $c }}</a>@endforeach</nav>
    @endif
    <div class="gal">@forelse ($items as $g)<a href="{{ Media::url($g->image_path, $g->title) }}" data-lightbox data-alt="{{ $g->alt_text ?: $g->title }}" data-caption="{{ $g->title }}"><img src="{{ Media::url($g->image_path, $g->title) }}" alt="{{ $g->alt_text ?: $g->title }}" loading="lazy"></a>@empty<p class="empty">Belum ada foto.</p>@endforelse</div>
</section>
@endsection
