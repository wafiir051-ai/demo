@extends('layouts.admin')
@section('title', ($item->exists ? 'Edit ' : 'Tambah ') . $c['title'])
@section('crumb')<a href="{{ route('admin.' . $c['route'] . '.index') }}">{{ $c['title'] }}</a> / {{ $item->exists ? 'Edit' : 'Tambah' }}@endsection
@section('content')
<div class="page-head"><h1>{{ $item->exists ? 'Edit' : 'Tambah' }} {{ $c['title'] }}</h1></div>
<form class="card form" method="post" enctype="multipart/form-data"
      action="{{ $item->exists ? route('admin.' . $c['route'] . '.update', $item->id) : route('admin.' . $c['route'] . '.store') }}">
    @csrf
    @if ($item->exists) @method('PUT') @endif
    @foreach ($c['fields'] as $f)
        @php
            $n = $f['name'];
            $val = old($n, $item->exists ? $item->$n : ($f['default'] ?? ''));
        @endphp
        <label @class(['chk' => $f['type'] === 'checkbox'])>
            @if ($f['type'] === 'checkbox')
                <input type="hidden" name="{{ $n }}" value="0">
                <input type="checkbox" name="{{ $n }}" value="1" @checked(old($n, $item->exists ? $item->$n : true))> {{ $f['label'] }}
            @else
                <span>{{ $f['label'] }}</span>
                @if ($f['type'] === 'textarea')<textarea name="{{ $n }}" rows="{{ $f['rows'] ?? 5 }}">{{ $val }}</textarea>
                @elseif ($f['type'] === 'select')<select name="{{ $n }}">@foreach ($f['options'] as $k => $lbl)<option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $lbl }}</option>@endforeach</select>
                @elseif ($f['type'] === 'number')<input type="number" name="{{ $n }}" value="{{ $val }}" min="0">
                @elseif ($f['type'] === 'email')<input type="email" name="{{ $n }}" value="{{ $val }}">
                @elseif ($f['type'] === 'password')<input type="password" name="{{ $n }}" autocomplete="new-password">
                @elseif ($f['type'] === 'image')
                    <input type="file" name="{{ $n }}" accept="image/png,image/jpeg,image/webp" onchange="const p=this.parentNode.querySelector('.prev');p.src=URL.createObjectURL(this.files[0]);p.hidden=false">
                    <img class="prev" alt="Pratinjau" @if ($item->exists && $item->$n) src="{{ Media::url($item->$n) }}" @else hidden @endif>
                @else<input type="text" name="{{ $n }}" value="{{ $val }}">
                @endif
            @endif
        </label>
    @endforeach
    @if (!empty($c['extra_view'])) @include($c['extra_view'], ['item' => $item] + $extra) @endif
    <div class="formacts"><button class="btn" type="submit">Simpan</button><a class="btn ghost" href="{{ route('admin.' . $c['route'] . '.index') }}">Batal</a></div>
</form>
@endsection
