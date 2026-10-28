@extends('layouts.public')
@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $page->content), 155))
@section('content')
<section class="pagehead"><div class="wrap"><h1>{{ $page->title }}</h1></div></section>
<section class="sec wrap narrow prose">
    @if ($page->featured_image)<img class="feat" src="{{ Media::url($page->featured_image, $page->title) }}" alt="{{ $page->title }}">@endif
    {!! \Illuminate\Support\Str::markdown((string) $page->content, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
</section>
@endsection
