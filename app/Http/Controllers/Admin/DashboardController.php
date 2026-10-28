<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Package;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'Akomodasi' => Accommodation::count(),
            'Paket' => Package::count(),
            'Total reservasi' => Reservation::count(),
            'Reservasi menunggu' => Reservation::where('status', 'pending')->count(),
            'Reservasi dikonfirmasi' => Reservation::where('status', 'confirmed')->count(),
            'Pesan belum dibaca' => ContactMessage::where('status', 'unread')->count(),
        ];

        $start = now()->subMonths(5)->startOfMonth();
        $byMonth = Reservation::where('created_at', '>=', $start)->pluck('created_at')->groupBy(fn ($d) => $d->format('Y-m'))->map->count();
        $chart = [];
        for ($i = 0; $i < 6; $i++) {
            $m = $start->copy()->addMonths($i);
            $chart[] = ['label' => $m->translatedFormat('M Y'), 'value' => $byMonth[$m->format('Y-m')] ?? 0];
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'latestReservations' => Reservation::with('accommodation')->latest()->limit(5)->get(),
            'logs' => ActivityLog::with('user')->latest()->limit(8)->get(),
        ]);
    }
}
