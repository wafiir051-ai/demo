<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>@yield('title', 'Dashboard') — Admin {{ $biz->business_name }}</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $u = auth()->user();
    $menu = [
        ['admin.dashboard', 'admin.dashboard', 'Dashboard', ['super_admin','admin','staff']],
        ['admin.settings.edit', 'admin.settings.*', 'Pengaturan Bisnis', ['super_admin','admin']],
        ['admin.accommodations.index', 'admin.accommodations.*', 'Akomodasi', ['super_admin','admin']],
        ['admin.facilities.index', 'admin.facilities.*', 'Fasilitas', ['super_admin','admin']],
        ['admin.galleries.index', 'admin.galleries.*', 'Galeri', ['super_admin','admin']],
        ['admin.packages.index', 'admin.packages.*', 'Paket dan Acara', ['super_admin','admin']],
        ['admin.reservations.index', 'admin.reservations.*', 'Reservasi', ['super_admin','admin','staff']],
        ['admin.messages.index', 'admin.messages.*', 'Pesan Masuk', ['super_admin','admin','staff']],
        ['admin.pages.index', 'admin.pages.*', 'Halaman Website', ['super_admin','admin']],
        ['admin.users.index', 'admin.users.*', 'Pengguna dan Role', ['super_admin']],
        ['admin.logs', 'admin.logs', 'Activity Log', ['super_admin','admin']],
        ['admin.account.edit', 'admin.account.*', 'Pengaturan Akun', ['super_admin','admin','staff']],
    ];
@endphp
<div class="shell">
    <aside class="side" id="side">
        <div class="brand">{{ $biz->business_name }}<small>Admin Dashboard</small></div>
        <nav aria-label="Menu admin">
            @foreach ($menu as [$route, $pattern, $label, $roles])
                @if (in_array($u->role, $roles, true))
                    <a href="{{ route($route) }}" @class(['on' => request()->routeIs($pattern)])>{{ $label }}</a>
                @endif
            @endforeach
        </nav>
        <a class="viewsite" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat website ↗</a>
    </aside>
    <div class="main">
        <header class="top">
            <button class="burger" type="button" aria-label="Buka menu" onclick="document.getElementById('side').classList.toggle('open')">☰</button>
            <nav class="crumb" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">Admin</a> / <span>@yield('crumb', 'Dashboard')</span></nav>
            <form class="hsearch" method="get" action="{{ route('admin.reservations.index') }}" role="search">
                <input type="search" name="q" placeholder="Cari reservasi (kode/nama/telepon)" aria-label="Cari reservasi">
            </form>
            <div class="who"><span>{{ $u->name }} · {{ \App\Models\User::ROLES[$u->role] ?? $u->role }}</span>
                <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="btn sm ghost">Keluar</button></form>
            </div>
        </header>
        <main class="content">
            @if (session('success'))<div class="alert ok" role="status">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="alert err" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="alert err" role="alert"><strong>Periksa kembali isian:</strong><ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
