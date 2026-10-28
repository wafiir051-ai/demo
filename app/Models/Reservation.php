<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    public const STATUSES = [
        'pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'rejected' => 'Ditolak',
        'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = ['reservation_code', 'guest_name', 'phone', 'email', 'accommodation_id', 'check_in', 'check_out', 'guest_count', 'special_requests', 'status', 'admin_notes'];

    protected function casts(): array
    {
        return ['check_in' => 'date:Y-m-d', 'check_out' => 'date:Y-m-d'];
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public static function newCode(): string
    {
        do {
            $code = 'GDG-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('reservation_code', $code)->exists());

        return $code;
    }
}
