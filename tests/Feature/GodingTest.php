<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GodingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        \App\Models\BusinessSetting::forget();
    }

    public function test_halaman_publik_tampil(): void
    {
        foreach (['/', '/akomodasi', '/fasilitas', '/paket', '/galeri', '/tentang', '/kontak', '/reservasi'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/halaman-yang-tidak-ada')->assertNotFound();
    }

    public function test_admin_wajib_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/akomodasi')->assertRedirect('/login');
    }

    public function test_login_admin_dan_dashboard(): void
    {
        $this->post('/admin/login', ['email' => 'admin@goding.test', 'password' => 'password'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
        $this->post('/admin/login', ['email' => 'admin@goding.test', 'password' => 'salah'])->assertSessionHasErrors('email');
    }

    public function test_staff_tidak_boleh_ke_pengaturan_dan_pengguna(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->actingAs($staff)->get('/admin/reservasi')->assertOk();
        $this->actingAs($staff)->get('/admin/pengaturan')->assertForbidden();
        $this->actingAs($staff)->get('/admin/pengguna')->assertForbidden();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get('/admin/pengaturan')->assertOk();
        $this->actingAs($admin)->get('/admin/pengguna')->assertForbidden();
    }

    public function test_reservasi_publik_tersimpan_dengan_kode_dan_status_pending(): void
    {
        $acc = Accommodation::first();
        $res = $this->post('/reservasi', [
            'guest_name' => 'Budi Santoso', 'phone' => '081234567890', 'accommodation_id' => $acc->id,
            'check_in' => now()->addDays(3)->toDateString(), 'check_out' => now()->addDays(5)->toDateString(), 'guest_count' => 2,
        ]);
        $row = Reservation::where('guest_name', 'Budi Santoso')->firstOrFail();
        $this->assertSame('pending', $row->status);
        $res->assertRedirect('/reservasi/berhasil/' . $row->reservation_code);
        $this->get('/reservasi/berhasil/' . $row->reservation_code)->assertOk()->assertSee($row->reservation_code);
    }

    public function test_reservasi_ditolak_bila_checkout_tidak_setelah_checkin(): void
    {
        $acc = Accommodation::first();
        $this->post('/reservasi', [
            'guest_name' => 'Budi', 'phone' => '081234567890', 'accommodation_id' => $acc->id,
            'check_in' => now()->addDays(3)->toDateString(), 'check_out' => now()->addDays(3)->toDateString(), 'guest_count' => 2,
        ])->assertSessionHasErrors('check_out');
    }

    public function test_crud_akomodasi_dan_status_reservasi(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->post('/admin/akomodasi', ['name' => 'Unit Uji', 'capacity' => 4, 'status' => 'published'])->assertRedirect('/admin/akomodasi');
        $this->assertDatabaseHas('accommodations', ['name' => 'Unit Uji', 'slug' => 'unit-uji']);

        $r = Reservation::where('status', 'pending')->first();
        $this->actingAs($admin)->put('/admin/reservasi/' . $r->id, ['status' => 'confirmed', 'admin_notes' => 'ok'])->assertSessionHasNoErrors();
        $this->assertSame('confirmed', $r->fresh()->status);
    }
}
