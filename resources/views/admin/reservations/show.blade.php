@extends('layouts.admin')
@section('title', 'Reservasi ' . $res->reservation_code)
@section('crumb')<a href="{{ route('admin.reservations.index') }}">Reservasi</a> / {{ $res->reservation_code }}@endsection
@section('content')
<div class="page-head"><h1>{{ $res->reservation_code }}</h1><span class="badge s-{{ $res->status }}">{{ \App\Models\Reservation::STATUSES[$res->status] }}</span></div>
<div class="grid2">
<section class="card"><h2>Detail permintaan</h2>
<dl class="dl">
<dt>Nama</dt><dd>{{ $res->guest_name }}</dd>
<dt>Telepon</dt><dd>{{ $res->phone }} <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D+/', '', $res->phone)) }}" target="_blank" rel="noopener">WhatsApp ↗</a></dd>
<dt>Email</dt><dd>{{ $res->email ?: '—' }}</dd>
<dt>Akomodasi</dt><dd>{{ $res->accommodation?->name ?? '(dihapus)' }}</dd>
<dt>Check-in</dt><dd>{{ $res->check_in->format('d M Y') }}</dd>
<dt>Check-out</dt><dd>{{ $res->check_out->format('d M Y') }}</dd>
<dt>Jumlah tamu</dt><dd>{{ $res->guest_count }}</dd>
<dt>Catatan tamu</dt><dd>{{ $res->special_requests ?: '—' }}</dd>
<dt>Diminta pada</dt><dd>{{ $res->created_at->format('d M Y H:i') }}</dd>
</dl></section>
<section class="card"><h2>Proses</h2>
<form class="form" method="post" action="{{ route('admin.reservations.update', $res->id) }}" onsubmit="return confirm('Simpan perubahan status/catatan reservasi ini?')">
@csrf @method('PUT')
<label><span>Status</span><select name="status">@foreach (\App\Models\Reservation::STATUSES as $k => $l)<option value="{{ $k }}" @selected(old('status', $res->status) === $k)>{{ $l }}</option>@endforeach</select></label>
<label><span>Catatan internal (tidak terlihat tamu)</span><textarea name="admin_notes" rows="5">{{ old('admin_notes', $res->admin_notes) }}</textarea></label>
<p class="muted small">Mengonfirmasi akan dicek bentrok tanggal dengan reservasi lain yang sudah dikonfirmasi untuk akomodasi yang sama.</p>
<div class="formacts"><button class="btn">Simpan</button></div>
</form></section>
</div>
@endsection
