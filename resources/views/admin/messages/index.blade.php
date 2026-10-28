@extends('layouts.admin')
@section('title', 'Pesan Masuk')
@section('crumb', 'Pesan Masuk')
@section('content')
<div class="page-head"><h1>Pesan Masuk</h1></div>
<form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari pesan..." aria-label="Cari">
    <select name="status" aria-label="Status"><option value="">Semua status</option>@foreach (\App\Models\ContactMessage::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
    <button class="btn sm">Filter</button>
</form>
<div class="table-wrap"><table class="tbl">
<thead><tr><th>Pengirim</th><th>Subjek</th><th>Status</th><th>Diterima</th><th>Aksi</th></tr></thead>
<tbody>@forelse ($items as $m)
<tr><td data-label="Pengirim">{{ $m->name }}<br><small>{{ $m->email ?: $m->phone }}</small></td>
<td data-label="Subjek">{{ $m->subject ?: \Illuminate\Support\Str::limit($m->message, 50) }}</td>
<td data-label="Status"><span class="badge s-{{ $m->status }}">{{ \App\Models\ContactMessage::STATUSES[$m->status] }}</span></td>
<td data-label="Diterima">{{ $m->created_at->format('d M H:i') }}</td>
<td data-label="Aksi" class="acts"><a class="btn sm ghost" href="{{ route('admin.messages.show', $m->id) }}">Buka</a>
<form method="post" action="{{ route('admin.messages.destroy', $m->id) }}" onsubmit="return confirm('Hapus pesan ini?')">@csrf @method('DELETE')<button class="btn sm danger">Hapus</button></form></td></tr>
@empty <tr><td colspan="5" class="empty">Tidak ada pesan.</td></tr> @endforelse</tbody></table></div>
{{ $items->links() }}
@endsection
