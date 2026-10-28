@extends('layouts.admin')
@section('title', $c['title'])
@section('crumb', $c['title'])
@section('content')
<div class="page-head"><h1>{{ $c['title'] }}</h1><a class="btn" href="{{ route('admin.' . $c['route'] . '.create') }}">+ Tambah</a></div>
<form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari..." aria-label="Cari">
    @if (!empty($c['has_status']))
        <select name="status" aria-label="Filter status">
            <option value="">Semua status</option>
            <option value="published" @selected(request('status') === 'published')>Dipublikasikan</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
    @endif
    <button class="btn sm">Terapkan</button>
</form>
<div class="table-wrap">
<table class="tbl">
    <thead><tr>@foreach ($c['columns'] as $col)<th>{{ $col['label'] }}</th>@endforeach<th>Aksi</th></tr></thead>
    <tbody>
    @forelse ($items as $item)
        <tr>
            @foreach ($c['columns'] as $col)
                @php $v = data_get($item, $col['key']); $type = $col['type'] ?? 'text'; @endphp
                <td data-label="{{ $col['label'] }}">
                    @if ($type === 'image')<img class="thumb" src="{{ Media::url($v, 'Foto') }}" alt="" loading="lazy">
                    @elseif ($type === 'money'){{ Media::rupiah($v) }}
                    @elseif ($type === 'status')<span class="badge s-{{ $v }}">{{ $v === 'published' ? 'Publik' : 'Draft' }}</span>
                    @elseif ($type === 'bool')<span class="badge {{ $v ? 's-published' : 's-draft' }}">{{ $v ? 'Ya' : 'Tidak' }}</span>
                    @elseif ($type === 'datetime'){{ optional($v)->format('d M Y H:i') }}
                    @else{{ isset($col['map']) ? ($col['map'][$v] ?? $v) : \Illuminate\Support\Str::limit((string) $v, 60) }}@endif
                </td>
            @endforeach
            <td data-label="Aksi" class="acts">
                <a class="btn sm ghost" href="{{ route('admin.' . $c['route'] . '.edit', $item->id) }}">Edit</a>
                <form method="post" action="{{ route('admin.' . $c['route'] . '.destroy', $item->id) }}" onsubmit="return confirm('Hapus data ini? Tindakan tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')<button class="btn sm danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="{{ count($c['columns']) + 1 }}" class="empty">Belum ada data.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $items->links() }}
@endsection
