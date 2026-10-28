<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BusinessSetting;
use App\Support\Media;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['s' => BusinessSetting::current()]);
    }

    public function update(Request $r)
    {
        $img = 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072';
        $data = $r->validate([
            'business_name' => 'required|string|max:150',
            'business_type' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:5000',
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'contact_email' => 'nullable|email|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'whatsapp_number' => ['nullable', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'address' => 'nullable|string|max:500',
            'maps_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:300',
            'facebook_url' => 'nullable|url|max:300',
            'website_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:300',
            'logo' => $img,
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,webp,ico|max:1024',
            'hero_image' => $img,
        ]);

        $s = BusinessSetting::current();
        $s->fill(collect($data)->except(['logo', 'favicon', 'hero_image'])->all());
        $s->is_demo = $r->boolean('is_demo');
        foreach (['logo' => 'logo_path', 'favicon' => 'favicon_path', 'hero_image' => 'hero_image_path'] as $input => $col) {
            if ($r->hasFile($input)) {
                Media::delete($s->$col);
                $s->$col = $r->file($input)->store('branding', 'public');
            }
        }
        $s->save();
        BusinessSetting::forget();
        ActivityLog::record('update', 'BusinessSetting', $s->id, 'Mengubah pengaturan bisnis');

        return back()->with('success', 'Pengaturan bisnis tersimpan dan langsung tampil di website publik.');
    }
}
