<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Catat aktivitas. Jangan pernah mengirim password/token ke $description. */
    public static function record(string $action, ?string $type = null, ?int $id = null, ?string $description = null, ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'entity_type' => $type,
            'entity_id' => $id,
            'description' => $description,
        ]);
    }
}
