<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\ContactMessage;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    public function reservation(Request $r)
    {
        $accs = Accommodation::published()->get();
        $selected = $accs->firstWhere('slug', $r->get('akomodasi'))?->id;

        return view('site.reservation', ['accommodations' => $accs, 'selected' => $selected]);
    }

    public function reservationStore(Request $r)
    {
        abort_if($r->filled('website'), 422); // honeypot anti-spam

        $data = $r->validate([
            'guest_name' => ['required', 'string', 'max:120', "regex:/^[\pL\s.'\-]+$/u"],
            'phone' => ['required', 'regex:/^[0-9+\s()\-]{8,20}$/'],
            'email' => 'nullable|email|max:150',
            'accommodation_id' => 'required|integer',
            'check_in' => 'required|date_format:Y-m-d|after_or_equal:today',
            'check_out' => 'required|date_format:Y-m-d|after:check_in',
            'guest_count' => 'required|integer|min:1|max:500',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        $acc = Accommodation::where('status', 'published')->find($data['accommodation_id']);
        if (!$acc) {
            throw ValidationException::withMessages(['accommodation_id' => 'Akomodasi yang dipilih tidak tersedia.']);
        }
        if ($data['guest_count'] > $acc->capacity) {
            throw ValidationException::withMessages(['guest_count' => "Kapasitas {$acc->name} maksimal {$acc->capacity} tamu."]);
        }

        $res = Reservation::create($data + ['reservation_code' => Reservation::newCode(), 'status' => 'pending']);

        return redirect()->route('reservation.success', $res->reservation_code);
    }

    public function success(string $code)
    {
        return view('site.reservation-success', ['res' => Reservation::with('accommodation')->where('reservation_code', $code)->firstOrFail()]);
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function contactStore(Request $r)
    {
        abort_if($r->filled('website'), 422);
        $data = $r->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:150|required_without:phone',
            'phone' => ['nullable', 'required_without:email', 'regex:/^[0-9+\s()\-]{8,20}$/'],
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|min:5|max:5000',
        ]);
        ContactMessage::create($data + ['status' => 'unread']);

        return redirect()->route('contact')->with('success', 'Pesan Anda sudah kami terima. Kami akan menghubungi Anda secepatnya.');
    }
}
