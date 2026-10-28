<?php

namespace App\Http\Controllers\Admin;

use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Facility;
use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AccommodationController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => Accommodation::class, 'route' => 'accommodations', 'title' => 'Akomodasi', 'label' => 'name',
            'folder' => 'accommodations', 'has_status' => true, 'slug_from' => 'name', 'search' => ['name', 'description'],
            'order' => ['sort_order', 'asc'], 'extra_view' => 'admin.partials.accommodation-extra',
            'columns' => [
                ['key' => 'featured_image', 'label' => 'Foto', 'type' => 'image'],
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'capacity', 'label' => 'Kapasitas'],
                ['key' => 'price_per_night', 'label' => 'Harga/malam', 'type' => 'money'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Nama akomodasi', 'type' => 'text', 'rules' => 'required|string|max:150'],
                ['name' => 'slug', 'label' => 'Slug URL (kosongkan untuk otomatis)', 'type' => 'slug'],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:5000'],
                ['name' => 'capacity', 'label' => 'Kapasitas (tamu)', 'type' => 'number', 'rules' => 'required|integer|min:1|max:200'],
                ['name' => 'bed_description', 'label' => 'Keterangan tempat tidur', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'price_per_night', 'label' => 'Harga per malam (Rp, kosongkan bila tidak ditampilkan)', 'type' => 'number', 'rules' => 'nullable|integer|min:0'],
                ['name' => 'featured_image', 'label' => 'Foto utama', 'type' => 'image'],
                ['name' => 'status', 'label' => 'Status publikasi', 'type' => 'select', 'options' => ['published' => 'Dipublikasikan', 'draft' => 'Draft (disembunyikan)'], 'rules' => 'required|in:published,draft', 'default' => 'published'],
                ['name' => 'sort_order', 'label' => 'Urutan tampil (angka kecil tampil lebih dulu)', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:9999', 'default' => 0],
            ],
        ];
    }

    protected function extra(Model $item): array
    {
        return ['facilities' => Facility::orderBy('sort_order')->get(), 'selected' => $item->exists ? $item->facilities()->pluck('facilities.id')->all() : [], 'images' => $item->exists ? $item->images : collect()];
    }

    protected function extraRules(?Model $item): array
    {
        return [
            'facility_ids' => 'nullable|array', 'facility_ids.*' => 'integer|exists:facilities,id',
            'gallery_images' => 'nullable|array|max:10', 'gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
            'remove_images' => 'nullable|array', 'remove_images.*' => 'integer',
        ];
    }

    protected function afterSave(Model $item, Request $r): void
    {
        $item->facilities()->sync($r->input('facility_ids', []));
        foreach (AccommodationImage::where('accommodation_id', $item->id)->whereIn('id', $r->input('remove_images', []))->get() as $img) {
            Media::delete($img->path);
            $img->delete();
        }
        foreach ($r->file('gallery_images', []) as $file) {
            AccommodationImage::create(['accommodation_id' => $item->id, 'path' => $file->store('accommodations', 'public')]);
        }
    }

    protected function beforeDelete(Model $item): ?string
    {
        foreach ($item->images as $img) {
            Media::delete($img->path);
        }

        return null;
    }
}
