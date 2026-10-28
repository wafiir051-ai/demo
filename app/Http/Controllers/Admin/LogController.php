<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $r)
    {
        $q = ActivityLog::with('user')->latest();
        if ($s = trim((string) $r->get('q'))) {
            $q->where(fn ($w) => $w->where('description', 'like', "%$s%")->orWhere('action', 'like', "%$s%")->orWhere('entity_type', 'like', "%$s%"));
        }

        return view('admin.logs', ['items' => $q->paginate(25)->withQueryString()]);
    }
}
