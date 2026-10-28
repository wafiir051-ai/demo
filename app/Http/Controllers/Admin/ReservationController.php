<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $r)
    {
        $q = Reservation::with('accommodation')->latest();
        if ($s = trim((string) $r->get('q'))) {
            $q->where(fn ($w) => $w->where('reservation_code', 'like', "%$s%")->orWhere('guest_name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if (array_key_exists($r->get('status'), Reservation::STATUSES)) {
            $q->where('status', $r->get('status'));
        }
        if ($r->filled('from') && strtotime($r->get('from'))) {
            $q->where('check_in', '>=', $r->get('from'));
        }
        if ($r->filled('to') && strtotime($r->get('to'))) {
            $q->where('check_in', '<=', $r->get('to'));
        }

        return view('admin.reservations.index', ['items' => $q->paginate(12)->withQueryString()]);
    }

    public function show(string $id)
    {
        return view('admin.reservations.show', ['res' => Reservation::with('accommodation')->findOrFail($id)]);
    }

    public function update(Request $r, string $id)
    {
        $data = $r->validate([
            'status' => 'required|in:' . implode(',', array_keys(Reservation::STATUSES)),
            'admin_notes' => 'nullable|string|max:3000',
        ]);

        $error = null;
        $res = null;
        DB::transaction(function () use ($id, $data, &$error, &$res) {
            $res = Reservation::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($data['status'] === 'confirmed' && $res->accommodation_id) {
                $clash = Reservation::where('id', '!=', $res->id)
                    ->where('accommodation_id', $res->accommodation_id)
                    ->where('status', 'confirmed')
                    ->where('check_in', '<', $res->check_out->toDateString())
                    ->where('check_out', '>', $res->check_in->toDateString())
                    ->exists();
                if ($clash) {
                    $error = 'Tanggal bentrok dengan reservasi lain yang sudah dikonfirmasi untuk akomodasi yang sama.';

                    return;
                }
            }
            $old = $res->status;
            $res->update($data);
            ActivityLog::record('update', 'Reservation', $res->id, "Reservasi {$res->reservation_code}: status {$old} -> {$res->status}");
        });

        return $error ? back()->with('error', $error) : back()->with('success', 'Reservasi diperbarui.');
    }
}
