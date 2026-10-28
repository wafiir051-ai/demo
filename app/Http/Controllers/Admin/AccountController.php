<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function edit()
    {
        return view('admin.account', ['user' => Auth::user()]);
    }

    public function update(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:120',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|max:100|confirmed',
        ]);
        $u = Auth::user();
        $u->name = $data['name'];
        if (!empty($data['password'])) {
            $u->password = $data['password'];
        }
        $u->save();
        ActivityLog::record('update', 'User', $u->id, 'Memperbarui akun sendiri' . (!empty($data['password']) ? ' (password diganti)' : ''));

        return back()->with('success', 'Akun diperbarui.');
    }
}
