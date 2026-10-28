<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PackageController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => Package::class, 'route' => 'packages', 'title' => 'Paket dan Acara', 'label' => 'name',
            'folder' => 'packages', 'has_status' => true, 'slug_from' => 'name', 'search' => ['name', 'description'],
            'order' => ['id', 'desc'], 'extra_view' => 'admin.partials.package-extra',
            'columns' => [
                ['key' => 'featured_image', 'label' => 'Foto', 'type' => 'image'],
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'package_type', 'label' => 'Jenis', 'map' => Package::TYPES],
                ['key' => 'price', 'label' => 'Harga', 'type' => 'money'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Nama paket', 'type' => 'text', 'rules' => 'required|string|max:150'],
                ['name' => 'slug', 'label' => 'Slug URL (kosongkan untuk otomatis)', 'type' => 'slug'],
                ['name' => 'package_type', 'label' => 'Jenis paket', 'type' => 'select', 'options' => Package::TYPES, 'rules' => 'required|in:' . implode(',', array_keys(Package::TYPES)), 'default' => 'gathering'],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:5000'],
                ['name' => 'capacity', 'label' => 'Kapasitas (orang)', 'type' => 'number', 'rules' => 'nullable|integer|min:1|max:5000'],
                ['name' => 'price', 'label' => 'Harga (Rp, kosongkan bila tidak ditampilkan)', 'type' => 'number', 'rules' => 'nullable|integer|min:0'],
                ['name' => 'featured_image', 'label' => 'Foto', 'type' => 'image'],
                ['name' => 'terms', 'label' => 'Syarat dan ketentuan', 'type' => 'textarea', 'rules' => 'nullable|string|max:3000'],
                ['name' => 'status', 'label' => 'Status publikasi', 'type' => 'select', 'options' => ['published' => 'Dipublikasikan', 'draft' => 'Draft (disembunyikan)'], 'rules' => 'required|in:published,draft', 'default' => 'published'],
            ],
        ];
    }

    protected function extra(Model $item): array
    {
        return ['items_text' => $item->exists ? $item->items->pluck('label')->implode("\n") : ''];
    }

    protected function extraRules(?Model $item): array
    {
        return ['items_text' => 'nullable|string|max:3000'];
    }

    protected function afterSave(Model $item, Request $r): void
    {
        $item->items()->delete();
        $lines = array_filter(array_map('trim', preg_split('/\R/', (string) $r->input('items_text'))));
        foreach (array_values($lines) as $i => $label) {
            PackageItem::create(['package_id' => $item->id, 'label' => mb_substr($label, 0, 190), 'sort_order' => $i]);
        }
    }
}
