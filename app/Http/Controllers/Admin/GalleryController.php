<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gallery;

class GalleryController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => Gallery::class, 'route' => 'galleries', 'title' => 'Galeri', 'label' => 'title',
            'folder' => 'galleries', 'has_status' => true, 'search' => ['title', 'category', 'alt_text'],
            'order' => ['sort_order', 'asc'],
            'columns' => [
                ['key' => 'image_path', 'label' => 'Foto', 'type' => 'image'],
                ['key' => 'title', 'label' => 'Judul'],
                ['key' => 'category', 'label' => 'Kategori'],
                ['key' => 'sort_order', 'label' => 'Urutan'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'rules' => 'required|string|max:150'],
                ['name' => 'image_path', 'label' => 'Gambar (jpg/png/webp, maks 3 MB)', 'type' => 'image', 'required_on_create' => true],
                ['name' => 'category', 'label' => 'Kategori (mis. Kamar, Lingkungan)', 'type' => 'text', 'rules' => 'nullable|string|max:60'],
                ['name' => 'alt_text', 'label' => 'Alt text (deskripsi gambar untuk aksesibilitas/SEO)', 'type' => 'text', 'rules' => 'nullable|string|max:200'],
                ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => 'nullable|string|max:1000'],
                ['name' => 'status', 'label' => 'Status publikasi', 'type' => 'select', 'options' => ['published' => 'Dipublikasikan', 'draft' => 'Draft (disembunyikan)'], 'rules' => 'required|in:published,draft', 'default' => 'published'],
                ['name' => 'sort_order', 'label' => 'Urutan tampil (angka kecil tampil lebih dulu)', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:9999', 'default' => 0],
            ],
        ];
    }
}
