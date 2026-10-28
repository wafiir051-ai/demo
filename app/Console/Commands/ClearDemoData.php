<?php

namespace App\Console\Commands;

use App\Models\{Accommodation, ActivityLog, BusinessSetting, ContactMessage, Facility, Gallery, Package, Page, Reservation};
use App\Support\Media;
use Illuminate\Console\Command;

class ClearDemoData extends Command
{
    protected $signature = 'goding:clear-demo {--force : Lewati konfirmasi}';

    protected $description = 'Hapus data demo (konten, reservasi, pesan, log). Akun pengguna dan migration tidak disentuh.';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Hapus SEMUA konten, reservasi, pesan, dan log? Akun pengguna tetap ada.')) {
            return self::FAILURE;
        }
        foreach (Accommodation::with('images')->get() as $a) {
            Media::delete($a->featured_image);
            $a->images->each(fn ($i) => Media::delete($i->path));
        }
        Facility::all()->each(fn ($f) => Media::delete($f->image_path));
        Gallery::all()->each(fn ($g) => Media::delete($g->image_path));
        Package::all()->each(fn ($p) => Media::delete($p->featured_image));
        Page::all()->each(fn ($p) => Media::delete($p->featured_image));
        foreach ([Accommodation::class, Facility::class, Gallery::class, Package::class, Page::class, Reservation::class, ContactMessage::class, ActivityLog::class] as $m) {
            $m::query()->delete();
        }
        BusinessSetting::query()->update(['is_demo' => false]);
        $this->info('Data demo dihapus. Silakan isi data bisnis Anda lewat /admin.');

        return self::SUCCESS;
    }
}
