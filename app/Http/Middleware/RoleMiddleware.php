<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        if (!$user || !$user->is_active) {
            abort(403, 'Akun tidak aktif atau tidak memiliki akses.');
        }
        if ($roles && !in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
