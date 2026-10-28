<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;

class PageController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'model' => Page::class, 'route' => 'pages', 'title' => 'Halaman Website', 'label' => 'title',
            'folder' => 'pages', 'has_status' => true, 'slug_from' => 'title', 'search' => ['title', 'content'],
            'order' => ['id', 'desc'],
            'columns' => [
                ['key' => 'title', 'label' => 'Judul'],
                ['key' => 'slug', 'label' => 'Slug'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'updated_at', 'label' => 'Diubah', 'type' => 'datetime'],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Judul halaman', 'type' => 'text', 'rules' => 'required|string|max:150'],
                ['name' => 'slug', 'label' => 'Slug URL (kosongkan untuk otomatis; halaman "tentang" dipakai di menu Tentang)', 'type' => 'slug'],
                ['name' => 'content', 'label' => 'Konten (mendukung Markdown; HTML mentah dinonaktifkan demi keamanan)', 'type' => 'textarea', 'rows' => 14, 'rules' => 'nullable|string|max:60000'],
                ['name' => 'featured_image', 'label' => 'Gambar utama', 'type' => 'image'],
                ['name' => 'meta_title', 'label' => 'Meta title (SEO)', 'type' => 'text', 'rules' => 'nullable|string|max:160'],
                ['name' => 'meta_description', 'label' => 'Meta description (SEO)', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:300'],
                ['name' => 'status', 'label' => 'Status publikasi', 'type' => 'select', 'options' => ['published' => 'Dipublikasikan', 'draft' => 'Draft (disembunyikan)'], 'rules' => 'required|in:published,draft', 'default' => 'draft'],
            ],
        ];
    }
}
