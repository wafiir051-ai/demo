@extends('layouts.admin')
@section('title', 'Reservasi')
@section('crumb', 'Reservasi')
@section('content')
<div class="page-head"><h1>Reservasi</h1></div>
<form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Kode / nama / telepon / email" aria-label="Cari">
    <select name="status" aria-label="Status"><option value="">Semua status</option>@foreach (\App\Models\Reservation::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
    <label class="inl">Check-in dari <input type="date" name="from" value="{{ request('from') }}"></label>
    <label class="inl">sampai <input type="date" name="to" value="{{ request('to') }}"></label>
    <button class="btn sm">Filter</button>
</form>
<div class="table-wrap"><table class="tbl">
<thead><tr><th>Kode</th><th>Tamu</th><th>Kontak</th><th>Akomodasi</th><th>Check-in</th><th>Check-out</th><th>Tamu</th><th>Status</th><th>Diminta</th><th>Aksi</th></tr></thead>
<tbody>@forelse ($items as $r)
<tr>
<td data-label="Kode">{{ $r->reservation_code }}</td><td data-label="Tamu">{{ $r->guest_name }}</td>
<td data-label="Kontak">{{ $r->phone }}<br><small>{{ $r->email }}</small></td>
<td data-label="Akomodasi">{{ $r->accommodation?->name ?? '—' }}</td>
<td data-label="Check-in">{{ $r->check_in->format('d M Y') }}</td><td data-label="Check-out">{{ $r->check_out->format('d M Y') }}</td>
<td data-label="Tamu">{{ $r->guest_count }}</td>
<td data-label="Status"><span class="badge s-{{ $r->status }}">{{ \App\Models\Reservation::STATUSES[$r->status] }}</span></td>
<td data-label="Diminta">{{ $r->created_at->format('d M H:i') }}</td>
<td data-label="Aksi"><a class="btn sm ghost" href="{{ route('admin.reservations.show', $r->id) }}">Detail</a></td>
</tr>
@empty <tr><td colspan="10" class="empty">Tidak ada reservasi.</td></tr> @endforelse</tbody></table></div>
{{ $items->links() }}
@endsection
