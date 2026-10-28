@extends('layouts.public')
@section('title', 'Tentang')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Profil</p><h1>{{ $page?->title ?? 'Tentang ' . $biz->business_name }}</h1></div></section>
<section class="sec wrap narrow prose">
    @if ($page){!! \Illuminate\Support\Str::markdown((string) $page->content, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
    @else<p>{!! nl2br(e($biz->description)) !!}</p>@endif
</section>
@endsection
