@extends('layouts.admin')
@section('title', 'Pengaturan Bisnis')
@section('crumb', 'Pengaturan Bisnis')
@section('content')
@php
    $sections = [
        'Identitas' => [['business_name', 'Nama bisnis', 'text'], ['business_type', 'Jenis bisnis (mis. Villa, Hotel)', 'text'], ['tagline', 'Tagline (headline di hero)', 'text'], ['description', 'Deskripsi bisnis', 'textarea']],
        'Kontak' => [['contact_email', 'Email kontak', 'email'], ['contact_phone', 'Telepon', 'text'], ['whatsapp_number', 'Nomor WhatsApp (mis. 0812xxxxxxx)', 'text'], ['address', 'Alamat', 'textarea'], ['maps_url', 'Tautan Google Maps', 'url'], ['instagram_url', 'URL Instagram', 'url'], ['facebook_url', 'URL Facebook', 'url']],
        'SEO dasar' => [['website_title', 'Judul website (title)', 'text'], ['meta_description', 'Meta description', 'textarea']],
    ];
@endphp
<div class="page-head"><h1>Pengaturan Bisnis</h1></div>
<form class="card form" method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @foreach ($sections as $title => $fields)
        <h2>{{ $title }}</h2>
        @foreach ($fields as [$n, $label, $type])
            <label><span>{{ $label }}</span>
                @if ($type === 'textarea')<textarea name="{{ $n }}" rows="3">{{ old($n, $s->$n) }}</textarea>
                @else<input type="{{ $type }}" name="{{ $n }}" value="{{ old($n, $s->$n) }}">@endif
            </label>
        @endforeach
        @if ($title === 'Identitas')
            <h2>Tampilan</h2>
            <div class="two">
                <label><span>Warna utama</span><input type="color" name="primary_color" value="{{ old('primary_color', $s->primary_color) }}"></label>
                <label><span>Warna sekunder</span><input type="color" name="secondary_color" value="{{ old('secondary_color', $s->secondary_color) }}"></label>
            </div>
            @foreach (['logo' => ['Logo', 'logo_path'], 'favicon' => ['Favicon', 'favicon_path'], 'hero_image' => ['Foto hero', 'hero_image_path']] as $name => [$label, $col])
                <label><span>{{ $label }}</span>
                    <input type="file" name="{{ $name }}" accept="image/png,image/jpeg,image/webp{{ $name === 'favicon' ? ',image/x-icon' : '' }}" onchange="const p=this.parentNode.querySelector('.prev');p.src=URL.createObjectURL(this.files[0]);p.hidden=false">
                    <img class="prev" alt="Pratinjau {{ $label }}" @if ($s->$col) src="{{ Media::url($s->$col) }}" @else hidden @endif>
                </label>
            @endforeach
        @endif
    @endforeach
    <h2>Mode demo</h2>
    <label class="chk"><input type="hidden" name="is_demo" value="0"><input type="checkbox" name="is_demo" value="1" @checked(old('is_demo', $s->is_demo))> Tampilkan label "data demo" di website publik</label>
    <div class="formacts"><button class="btn">Simpan pengaturan</button></div>
</form>
@endsection
