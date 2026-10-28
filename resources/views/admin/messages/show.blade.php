@extends('layouts.admin')
@section('title', 'Pesan')
@section('crumb')<a href="{{ route('admin.messages.index') }}">Pesan Masuk</a> / Detail @endsection
@section('content')
<div class="page-head"><h1>{{ $m->subject ?: 'Pesan dari ' . $m->name }}</h1></div>
<section class="card">
<dl class="dl"><dt>Nama</dt><dd>{{ $m->name }}</dd><dt>Email</dt><dd>{{ $m->email ?: '—' }}</dd><dt>Telepon</dt><dd>{{ $m->phone ?: '—' }}</dd><dt>Diterima</dt><dd>{{ $m->created_at->format('d M Y H:i') }}</dd></dl>
<p class="msgbody">{!! nl2br(e($m->message)) !!}</p>
<form class="filters" method="post" action="{{ route('admin.messages.update', $m->id) }}">@csrf @method('PUT')
<select name="status" aria-label="Status">@foreach (\App\Models\ContactMessage::STATUSES as $k => $l)<option value="{{ $k }}" @selected($m->status === $k)>{{ $l }}</option>@endforeach</select>
<button class="btn sm">Ubah status</button></form>
</section>
@endsection
