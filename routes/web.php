<?php

use App\Http\Controllers\Admin as A;
use App\Http\Controllers\Site\FormController;
use App\Http\Controllers\Site\SiteController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/* ---------- Public website ---------- */
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/akomodasi', [SiteController::class, 'accommodations'])->name('accommodations');
Route::get('/akomodasi/{accommodation:slug}', [SiteController::class, 'accommodation'])->name('accommodation.show');
Route::get('/fasilitas', [SiteController::class, 'facilities'])->name('facilities');
Route::get('/paket', [SiteController::class, 'packages'])->name('packages');
Route::get('/galeri', [SiteController::class, 'gallery'])->name('gallery');
Route::get('/tentang', [SiteController::class, 'about'])->name('about');
Route::get('/p/{page:slug}', [SiteController::class, 'page'])->name('page.show');
Route::get('/kontak', [FormController::class, 'contact'])->name('contact');
Route::post('/kontak', [FormController::class, 'contactStore'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/reservasi', [FormController::class, 'reservation'])->name('reservation');
Route::post('/reservasi', [FormController::class, 'reservationStore'])->middleware('throttle:5,1')->name('reservation.send');
Route::get('/reservasi/berhasil/{code}', [FormController::class, 'success'])->name('reservation.success');

/* ---------- Admin ---------- */
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    // Helper: daftarkan route CRUD (index/create/store/edit/update/destroy) dengan nama admin.{name}.*
    $crud = function (string $path, string $name, string $ctrl) {
        Route::get($path, [$ctrl, 'index'])->name("$name.index");
        Route::get("$path/tambah", [$ctrl, 'create'])->name("$name.create");
        Route::post($path, [$ctrl, 'store'])->name("$name.store");
        Route::get("$path/{id}/edit", [$ctrl, 'edit'])->name("$name.edit");
        Route::put("$path/{id}", [$ctrl, 'update'])->name("$name.update");
        Route::delete("$path/{id}", [$ctrl, 'destroy'])->name("$name.destroy");
    };

    Route::get('login', [A\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [A\AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');

    // Semua role (super_admin, admin, staff)
    Route::middleware(['auth', RoleMiddleware::class . ':super_admin,admin,staff'])->group(function () {
        Route::post('logout', [A\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [A\DashboardController::class, 'index'])->name('dashboard');
        Route::get('akun', [A\AccountController::class, 'edit'])->name('account.edit');
        Route::put('akun', [A\AccountController::class, 'update'])->name('account.update');

        Route::get('reservasi', [A\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservasi/{id}', [A\ReservationController::class, 'show'])->name('reservations.show');
        Route::put('reservasi/{id}', [A\ReservationController::class, 'update'])->name('reservations.update');

        Route::get('pesan', [A\MessageController::class, 'index'])->name('messages.index');
        Route::get('pesan/{id}', [A\MessageController::class, 'show'])->name('messages.show');
        Route::put('pesan/{id}', [A\MessageController::class, 'update'])->name('messages.update');
        Route::delete('pesan/{id}', [A\MessageController::class, 'destroy'])->name('messages.destroy');
    });

    // Admin & Super Admin: konten
    Route::middleware(['auth', RoleMiddleware::class . ':super_admin,admin'])->group(function () use ($crud) {
        Route::get('pengaturan', [A\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('pengaturan', [A\SettingController::class, 'update'])->name('settings.update');
        $crud('akomodasi', 'accommodations', A\AccommodationController::class);
        $crud('fasilitas', 'facilities', A\FacilityController::class);
        $crud('galeri', 'galleries', A\GalleryController::class);
        $crud('paket', 'packages', A\PackageController::class);
        $crud('halaman', 'pages', A\PageController::class);
        Route::get('log', [A\LogController::class, 'index'])->name('logs');
    });

    // Hanya Super Admin
    Route::middleware(['auth', RoleMiddleware::class . ':super_admin'])->group(function () use ($crud) {
        $crud('pengguna', 'users', A\UserController::class);
    });
});
