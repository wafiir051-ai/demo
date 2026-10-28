<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUSES = ['unread' => 'Belum dibaca', 'read' => 'Dibaca', 'replied' => 'Sudah dibalas', 'archived' => 'Diarsipkan'];

    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'status'];
}
