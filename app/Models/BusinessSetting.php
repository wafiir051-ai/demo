<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name', 'business_type', 'tagline', 'description', 'logo_path', 'favicon_path',
        'hero_image_path', 'primary_color', 'secondary_color', 'contact_email', 'contact_phone',
        'whatsapp_number', 'address', 'maps_url', 'instagram_url', 'facebook_url', 'website_title',
        'meta_description', 'is_demo',
    ];

    protected $casts = ['is_demo' => 'boolean'];

    protected static ?self $memo = null;

    public static function current(): self
    {
        if (static::$memo) {
            return static::$memo;
        }
        try {
            return static::$memo = static::query()->first()
                ?? static::query()->create(['business_name' => 'Nama Bisnis Anda']);
        } catch (Throwable $e) {
            return new static(['business_name' => 'Goding Hospitality', 'primary_color' => '#2f4a3a', 'secondary_color' => '#b8895a']);
        }
    }

    public static function forget(): void
    {
        static::$memo = null;
    }

    public function whatsappUrl(string $text = ''): ?string
    {
        $n = preg_replace('/\D+/', '', (string) $this->whatsapp_number);
        if (!$n) {
            return null;
        }
        if (str_starts_with($n, '0')) {
            $n = '62' . substr($n, 1);
        }

        return 'https://wa.me/' . $n . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }
}
