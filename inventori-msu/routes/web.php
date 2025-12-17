<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// =====================
// LIVEWIRE PENGELOLA
// =====================
use App\Livewire\Pengelola\Beranda;
use App\Livewire\Pengelola\Laporan;
use App\Livewire\Pengelola\TambahHapus;
use App\Livewire\Pengelola\Approval;

// =====================
// LIVEWIRE PENGURUS
// =====================
use App\Http\Controllers\PengurusController;

// =====================
// CONTROLLER EXPORT
// =====================
use App\Http\Controllers\Pengelola\LaporanExportController;

// =====================
// LIVEWIRE GUEST (Baru)
// =====================
use App\Livewire\Guest\Home;
use App\Livewire\Guest\Catalogue;
use App\Livewire\Guest\Cart;
use App\Livewire\Guest\Success;


// =====================
// PUBLIC
// =====================
Route::get('/', Home::class)->name('guest.home');
Route::get('/barang', Catalogue::class)->name('guest.catalogue.barang');
Route::get('/ruangan', Catalogue::class)->name('guest.catalogue.ruangan');
Route::get('/booking-barang', Cart::class)->name('guest.cart');
Route::redirect('/cart', '/booking-barang');
Route::get('/success', Success::class)->name('guest.success');


// =====================
// DASHBOARD + SETTINGS (USER LOGIN UMUM)
// =====================
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});


// =====================
// PENGELOLA SECTION (ADMIN)
// =====================
Route::prefix('pengelola')->name('pengelola.')->middleware(['auth'])->group(function () {

    Route::get('/beranda', Beranda::class)->name('beranda');
    Route::get('/laporan', Laporan::class)->name('laporan');

    // Export laporan
    Route::get('/laporan/export/{format}', [LaporanExportController::class, 'export'])
        ->whereIn('format', ['xlsx', 'csv', 'pdf'])
        ->name('laporan.export');

    Route::get('/tambah-barang', TambahHapus::class)->name('tambah');
    Route::get('/approval', Approval::class)->name('approval');
});


// =====================
// PENGURUS SECTION
// =====================
Route::prefix('pengurusinventoryMSU')->name('pengurus.')->middleware(['auth'])->group(function () {

    // Livewire Dashboard Pengurus
    Route::get('/dashboard', \App\Livewire\Pengurus\Dashboard::class)->name('dashboard');
    Route::get('/peminjaman-fasilitas', \App\Livewire\Pengurus\PeminjamanFasilitas::class)->name('fasilitas');
    Route::get('/riwayat', \App\Livewire\Pengurus\Riwayat::class)->name('riwayat');
});

// =====================
// API CART (SESSION BASED FOR FE GUEST)
// =====================
Route::prefix('api/cart')->group(function() {
    Route::get('/', [\App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/add', [\App\Http\Controllers\Api\CartController::class, 'add']);
    Route::post('/update', [\App\Http\Controllers\Api\CartController::class, 'update']);
    Route::post('/clear', [\App\Http\Controllers\Api\CartController::class, 'clear']);
});

// =====================
// API BOOKING (SESSION BASED FOR FE GUEST)
// =====================
Route::prefix('api/peminjaman')->group(function() {
    Route::get('/', [\App\Http\Controllers\Api\LoanController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\LoanController::class, 'store']);
    Route::get('/check', [\App\Http\Controllers\Api\LoanController::class, 'check']);
});
