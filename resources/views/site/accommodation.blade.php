@extends('layouts.public')
@section('title', $a->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $a->description), 155))
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow"><a href="{{ route('accommodations') }}">← Akomodasi</a></p><h1>{{ $a->name }}</h1></div></section>
<section class="sec wrap detail">
    <div>
        <div class="gal big">
            <a href="{{ Media::url($a->featured_image, $a->name) }}" data-lightbox data-alt="{{ $a->name }}" data-caption="{{ $a->name }}"><img src="{{ Media::url($a->featured_image, $a->name) }}" alt="{{ $a->name }}"></a>
            @foreach ($a->images as $img)<a href="{{ Media::url($img->path) }}" data-lightbox data-alt="{{ $a->name }}" data-caption="{{ $a->name }}"><img src="{{ Media::url($img->path) }}" alt="Foto {{ $a->name }}" loading="lazy"></a>@endforeach
        </div>
        <p class="meta">Kapasitas hingga {{ $a->capacity }} tamu @if ($a->bed_description)· {{ $a->bed_description }}@endif</p>
        <div class="prose">{!! nl2br(e($a->description)) !!}</div>
        @if ($a->facilities->count())<h2>Fasilitas</h2><ul class="ticks cols">@foreach ($a->facilities as $f)<li>{{ $f->icon }} {{ $f->name }}</li>@endforeach</ul>@endif
    </div>
    <aside class="box">
        <p class="price lg">{{ $a->price_per_night ? Media::rupiah($a->price_per_night) : 'Hubungi kami' }}@if ($a->price_per_night)<small> / malam</small>@endif</p>
        <h2>Ajukan reservasi</h2>
        @include('partials.reserve-form', ['accommodations' => $accommodations, 'selected' => $a->id])
        @php $wa = $biz->whatsappUrl('Halo, saya ingin bertanya tentang ' . $a->name); @endphp
        @if ($wa)<p><a class="btn wa block" href="{{ $wa }}" target="_blank" rel="noopener">Tanya via WhatsApp</a></p>@endif
    </aside>
</section>
@endsection
