@extends('layouts.public')
@section('title', 'Akomodasi')
@section('content')
<section class="pagehead"><div class="wrap"><p class="eyebrow">Menginap</p><h1>Akomodasi</h1></div></section>
<section class="sec wrap">
    @forelse ($items as $a)@if ($loop->first)<div class="accgrid">@endif @include('partials.acc-card', ['a' => $a]) @if ($loop->last)</div>@endif
    @empty<p class="empty">Belum ada akomodasi yang dipublikasikan.</p>@endforelse
</section>
@endsection
