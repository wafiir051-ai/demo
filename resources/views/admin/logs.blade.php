@extends('layouts.admin')
@section('title', 'Activity Log')
@section('crumb', 'Activity Log')
@section('content')
<div class="page-head"><h1>Activity Log</h1></div>
<form class="filters" method="get"><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas..." aria-label="Cari"><button class="btn sm">Cari</button></form>
<div class="table-wrap"><table class="tbl">
<thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Keterangan</th></tr></thead>
<tbody>@forelse ($items as $l)
<tr><td data-label="Waktu">{{ $l->created_at->format('d M Y H:i') }}</td><td data-label="Pengguna">{{ $l->user?->name ?? 'Sistem' }}</td><td data-label="Aksi"><span class="badge">{{ $l->action }}</span></td><td data-label="Keterangan">{{ $l->description }}</td></tr>
@empty <tr><td colspan="4" class="empty">Belum ada aktivitas.</td></tr> @endforelse</tbody></table></div>
{{ $items->links() }}
@endsection
