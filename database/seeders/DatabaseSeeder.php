<?php

namespace Database\Seeders;

use App\Models\{Accommodation, ActivityLog, BusinessSetting, ContactMessage, Facility, Gallery, Package, Page, Reservation, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun lokal untuk demo. GANTI password sebelum dipakai sungguhan.
        foreach ([
            ['Super Admin Demo', 'superadmin@goding.test', 'super_admin'],
            ['Admin Demo', 'admin@goding.test', 'admin'],
            ['Staf Demo', 'staff@goding.test', 'staff'],
        ] as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('password'), 'role' => $role, 'is_active' => true]);
        }

        BusinessSetting::query()->delete();
        BusinessSetting::create([
            'business_name' => 'Villa Bukit Pinus (Demo)',
            'business_type' => 'Villa & Penginapan',
            'tagline' => 'Menginap tenang di antara pepohonan pinus',
            'description' => "Ini adalah data demo. Ganti seluruh teks ini lewat Dashboard > Pengaturan Bisnis.\n\nVilla Bukit Pinus adalah contoh bisnis penginapan untuk menguji sistem Goding Hospitality.",
            'primary_color' => '#2f4a3a', 'secondary_color' => '#b8895a',
            'contact_email' => 'halo@contoh.test', 'contact_phone' => '0812-0000-0000', 'whatsapp_number' => '0812-0000-0000',
            'address' => 'Jl. Contoh No. 1, Kota Contoh (alamat demo)', 'maps_url' => 'https://maps.google.com',
            'instagram_url' => 'https://instagram.com/goding.id', 'website_title' => 'Villa Bukit Pinus (Demo) — Penginapan',
            'meta_description' => 'Situs demo penginapan yang dibuat dengan Goding Hospitality.', 'is_demo' => true,
        ]);

        $fac = collect([
            ['Kolam Renang', '🏊', 'Kolam renang pribadi untuk tamu (contoh data demo).'],
            ['Wi-Fi Cepat', '📶', 'Akses internet di seluruh area.'],
            ['Dapur Lengkap', '🍳', 'Peralatan masak dan makan tersedia.'],
            ['Area BBQ', '🔥', 'Area panggang di taman.'],
            ['Parkir Luas', '🚗', 'Parkir untuk beberapa kendaraan.'],
            ['Taman & Gazebo', '🌲', 'Ruang santai terbuka.'],
        ])->map(fn ($f, $i) => Facility::create(['name' => $f[0], 'slug' => Str::slug($f[0]), 'icon' => $f[1], 'description' => $f[2], 'status' => 'published', 'sort_order' => $i]));

        $acc = collect([
            ['Villa Keluarga Pinus', 8, '3 kamar tidur: 1 king, 2 twin', 1800000, 'Villa luas untuk keluarga atau rombongan kecil.'],
            ['Cottage Pasangan', 2, '1 kamar tidur: 1 queen', 750000, 'Cottage hangat untuk berdua.'],
            ['Kamar Standar Taman', 3, '1 kamar tidur: 1 double + 1 single', 500000, 'Kamar sederhana menghadap taman.'],
        ])->map(function ($a, $i) use ($fac) {
            $m = Accommodation::create(['name' => $a[0], 'slug' => Str::slug($a[0]), 'capacity' => $a[1], 'bed_description' => $a[2], 'price_per_night' => $a[3], 'description' => "[Data demo] {$a[4]}", 'status' => 'published', 'sort_order' => $i]);
            $m->facilities()->sync($fac->shuffle()->take(4)->pluck('id'));

            return $m;
        });

        $p1 = Package::create(['name' => 'Paket Gathering Keluarga', 'slug' => 'paket-gathering-keluarga', 'package_type' => 'gathering', 'capacity' => 30, 'price' => 4500000, 'description' => '[Data demo] Paket untuk acara keluarga besar.', 'terms' => 'Pemesanan minimal 7 hari sebelumnya (contoh).', 'status' => 'published']);
        foreach (['Sewa area gathering 6 jam', 'Area BBQ', 'Welcome drink', 'Parkir'] as $i => $l) {
            $p1->items()->create(['label' => $l, 'sort_order' => $i]);
        }
        $p2 = Package::create(['name' => 'Paket Akhir Pekan', 'slug' => 'paket-akhir-pekan', 'package_type' => 'stay', 'capacity' => 4, 'price' => 2200000, 'description' => '[Data demo] Menginap 2 malam.', 'status' => 'published']);
        foreach (['2 malam menginap', 'Sarapan', 'Late check-out (sesuai ketersediaan)'] as $i => $l) {
            $p2->items()->create(['label' => $l, 'sort_order' => $i]);
        }

        foreach ([['Teras villa', 'Kamar'], ['Kolam renang', 'Fasilitas'], ['Taman pagi hari', 'Lingkungan'], ['Ruang keluarga', 'Kamar'], ['Area BBQ', 'Fasilitas'], ['Jalur setapak pinus', 'Lingkungan']] as $i => [$t, $c]) {
            Gallery::create(['title' => $t, 'category' => $c, 'alt_text' => "$t (foto placeholder demo)", 'status' => 'published', 'sort_order' => $i]);
        }

        Page::create(['title' => 'Tentang Kami', 'slug' => 'tentang', 'status' => 'published', 'content' => "## Tentang\n\n[Data demo] Tuliskan kisah dan nilai bisnis Anda di sini. Halaman ini bisa diedit di **Dashboard > Halaman Website** dengan Markdown."]);
        Page::create(['title' => 'Kebijakan Pemesanan', 'slug' => 'kebijakan-pemesanan', 'status' => 'published', 'content' => "- [Data demo] Permintaan reservasi bukan konfirmasi pemesanan.\n- Tim kami akan menghubungi Anda untuk konfirmasi."]);

        foreach ([['Tamu Contoh A', 'pending', 'GDG-DEMO-0001', 5], ['Tamu Contoh B', 'confirmed', 'GDG-DEMO-0002', 12], ['Tamu Contoh C', 'completed', 'GDG-DEMO-0003', -10]] as $i => [$n, $s, $code, $d]) {
            Reservation::create(['reservation_code' => $code, 'guest_name' => $n, 'phone' => '0800-0000-000' . $i, 'accommodation_id' => $acc[$i % 3]->id, 'check_in' => now()->addDays($d)->toDateString(), 'check_out' => now()->addDays($d + 2)->toDateString(), 'guest_count' => 2, 'status' => $s, 'special_requests' => 'Data demo']);
        }

        ContactMessage::create(['name' => 'Pengunjung Contoh', 'email' => 'contoh@contoh.test', 'subject' => 'Tanya paket gathering', 'message' => '[Data demo] Apakah tersedia untuk 25 orang?', 'status' => 'unread']);
        ContactMessage::create(['name' => 'Pengunjung Contoh 2', 'phone' => '0800-1111-2222', 'subject' => 'Jam check-in', 'message' => '[Data demo] Jam check-in berapa?', 'status' => 'read']);

        ActivityLog::record('seed', 'System', null, 'Data demo dibuat oleh seeder', User::where('role', 'super_admin')->value('id'));
    }
}
