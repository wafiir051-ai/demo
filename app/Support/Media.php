<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    /** URL gambar upload, atau placeholder SVG bila belum ada foto. */
    public static function url(?string $path, string $label = 'Foto'): string
    {
        if ($path && is_file(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2f4a3a"/><stop offset="1" stop-color="#8aa28a"/></linearGradient></defs><rect width="1200" height="800" fill="url(#g)"/><text x="600" y="410" font-family="Georgia,serif" font-size="44" fill="#ffffffcc" text-anchor="middle">'
            . htmlspecialchars($label, ENT_QUOTES) . '</text></svg>';

        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function rupiah($n): string
    {
        return $n === null || $n === '' ? 'Hubungi kami' : 'Rp ' . number_format((int) $n, 0, ',', '.');
    }
}
