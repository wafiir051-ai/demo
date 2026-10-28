<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $r)
    {
        $q = ContactMessage::latest();
        if ($s = trim((string) $r->get('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('subject', 'like', "%$s%")->orWhere('message', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if (array_key_exists($r->get('status'), ContactMessage::STATUSES)) {
            $q->where('status', $r->get('status'));
        }

        return view('admin.messages.index', ['items' => $q->paginate(12)->withQueryString()]);
    }

    public function show(string $id)
    {
        $m = ContactMessage::findOrFail($id);
        if ($m->status === 'unread') {
            $m->update(['status' => 'read']);
        }

        return view('admin.messages.show', ['m' => $m]);
    }

    public function update(Request $r, string $id)
    {
        $data = $r->validate(['status' => 'required|in:' . implode(',', array_keys(ContactMessage::STATUSES))]);
        $m = ContactMessage::findOrFail($id);
        $m->update($data);
        ActivityLog::record('update', 'ContactMessage', $m->id, "Mengubah status pesan #{$m->id} menjadi {$m->status}");

        return back()->with('success', 'Status pesan diperbarui.');
    }

    public function destroy(string $id)
    {
        $m = ContactMessage::findOrFail($id);
        ActivityLog::record('delete', 'ContactMessage', $m->id, "Menghapus pesan #{$m->id}");
        $m->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Pesan dihapus.');
    }
}
