@extends('layouts.public')
@section('title', 'Fasilitas')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Kenyamanan</p><h1>Fasilitas</h1></div></section>
<section class="sec wrap">
    @forelse ($items as $f)@if ($loop->first)<div class="facgrid big">@endif
        <div class="fac">@if ($f->image_path)<img src="{{ Media::url($f->image_path, $f->name) }}" alt="{{ $f->name }}" loading="lazy">@endif<span class="ico" aria-hidden="true">{{ $f->icon ?: '✦' }}</span><h3>{{ $f->name }}</h3><p>{{ $f->description }}</p></div>
    @if ($loop->last)</div>@endif @empty<p class="empty">Belum ada fasilitas yang dipublikasikan.</p>@endforelse
</section>
@endsection
