<?php

namespace App\Http\Controllers\Admin;

use App\Models\Facility;

class FacilityController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => Facility::class, 'route' => 'facilities', 'title' => 'Fasilitas', 'label' => 'name',
            'folder' => 'facilities', 'has_status' => true, 'slug_from' => 'name', 'search' => ['name', 'description'],
            'order' => ['sort_order', 'asc'],
            'columns' => [
                ['key' => 'image_path', 'label' => 'Foto', 'type' => 'image'],
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'sort_order', 'label' => 'Urutan'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Nama fasilitas', 'type' => 'text', 'rules' => 'required|string|max:150'],
                ['name' => 'slug', 'label' => 'Slug URL (kosongkan untuk otomatis)', 'type' => 'slug'],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
                ['name' => 'icon', 'label' => 'Ikon (emoji atau simbol singkat)', 'type' => 'text', 'rules' => 'nullable|string|max:8'],
                ['name' => 'image_path', 'label' => 'Foto', 'type' => 'image'],
                ['name' => 'status', 'label' => 'Status publikasi', 'type' => 'select', 'options' => ['published' => 'Dipublikasikan', 'draft' => 'Draft (disembunyikan)'], 'rules' => 'required|in:published,draft', 'default' => 'published'],
                ['name' => 'sort_order', 'label' => 'Urutan tampil (angka kecil tampil lebih dulu)', 'type' => 'number', 'rules' => 'nullable|integer|min:0|max:9999', 'default' => 0],
            ],
        ];
    }
}
