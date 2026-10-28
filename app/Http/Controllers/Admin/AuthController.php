<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required|string']);

        if (Auth::attempt($cred + ['is_active' => true], $r->boolean('remember'))) {
            $r->session()->regenerate();
            ActivityLog::record('login', 'User', Auth::id(), 'Login ke dashboard');

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withInput($r->only('email'))->withErrors(['email' => 'Email/password salah, atau akun tidak aktif.']);
    }

    public function logout(Request $r)
    {
        ActivityLog::record('logout', 'User', Auth::id(), 'Logout dari dashboard');
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
