@extends('layouts.admin')
@section('title', 'Dashboard')
@section('crumb', 'Dashboard')
@section('content')
<div class="page-head"><h1>Dashboard</h1></div>
<div class="stats">
    @foreach ($stats as $label => $n)<div class="stat"><strong>{{ $n }}</strong><span>{{ $label }}</span></div>@endforeach
</div>
<div class="grid2">
    <section class="card">
        <h2>Reservasi 6 bulan terakhir</h2>
        @php $max = max(1, collect($chart)->max('value')); @endphp
        <div class="bars" role="img" aria-label="Grafik jumlah reservasi per bulan">
            @foreach ($chart as $b)
                <div class="bar"><span class="v">{{ $b['value'] }}</span><i style="height: {{ max(4, round($b['value'] / $max * 100)) }}%"></i><span class="l">{{ $b['label'] }}</span></div>
            @endforeach
        </div>
    </section>
    <section class="card">
        <h2>Reservasi terbaru</h2>
        @forelse ($latestReservations as $r)
            <a class="row" href="{{ route('admin.reservations.show', $r->id) }}"><span><strong>{{ $r->guest_name }}</strong><br><small>{{ $r->reservation_code }} · {{ $r->check_in->format('d M Y') }}</small></span><span class="badge s-{{ $r->status }}">{{ \App\Models\Reservation::STATUSES[$r->status] }}</span></a>
        @empty <p class="muted">Belum ada reservasi.</p> @endforelse
    </section>
</div>
<section class="card">
    <h2>Aktivitas admin terbaru</h2>
    @forelse ($logs as $l)<p class="logline"><small>{{ $l->created_at->format('d M H:i') }}</small> <strong>{{ $l->user?->name ?? 'Sistem' }}</strong> — {{ $l->description }}</p>
    @empty <p class="muted">Belum ada aktivitas.</p> @endforelse
</section>
@endsection
