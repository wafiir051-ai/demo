<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => User::class, 'route' => 'users', 'title' => 'Pengguna', 'label' => 'name', 'folder' => 'users',
            'search' => ['name', 'email'], 'order' => ['id', 'asc'],
            'columns' => [
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'role', 'label' => 'Role', 'map' => User::ROLES],
                ['key' => 'is_active', 'label' => 'Aktif', 'type' => 'bool'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'rules' => 'required|string|max:120'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => fn ($i) => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($i->id)]],
                ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => User::ROLES, 'rules' => 'required|in:super_admin,admin,staff', 'default' => 'staff'],
                ['name' => 'is_active', 'label' => 'Akun aktif', 'type' => 'checkbox'],
                ['name' => 'password', 'label' => 'Password (minimal 8 karakter; saat edit, isi hanya untuk reset password)', 'type' => 'password',
                    'rules' => fn ($i) => [$i->exists ? 'nullable' : 'required', 'string', 'min:8', 'max:100']],
            ],
        ];
    }

    protected function beforeSave(Model $item, Request $r): void
    {
        if (!$item->exists) {
            return;
        }
        $demotes = $r->input('role') !== 'super_admin' || !$r->boolean('is_active');
        if ($item->id === Auth::id() && ($r->input('role') !== $item->role || !$r->boolean('is_active'))) {
            throw ValidationException::withMessages(['role' => 'Anda tidak dapat mengubah role atau menonaktifkan akun Anda sendiri.']);
        }
        if ($item->role === 'super_admin' && $demotes && !User::where('role', 'super_admin')->where('is_active', true)->where('id', '!=', $item->id)->exists()) {
            throw ValidationException::withMessages(['role' => 'Harus tersisa minimal satu Super Admin aktif.']);
        }
    }

    protected function beforeDelete(Model $item): ?string
    {
        if ($item->id === Auth::id()) {
            return 'Anda tidak dapat menghapus akun sendiri.';
        }
        if ($item->role === 'super_admin' && !User::where('role', 'super_admin')->where('is_active', true)->where('id', '!=', $item->id)->exists()) {
            return 'Harus tersisa minimal satu Super Admin aktif.';
        }

        return null;
    }
}
