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
// LIVEWIRE BORROWER / GUEST
// =====================
use App\Livewire\Borrower\Home;
use App\Livewire\Borrower\Catalogue;
use App\Livewire\Borrower\Cart;
use App\Livewire\Borrower\Success;

// =====================
// CONTROLLER EXPORT
// =====================
use App\Http\Controllers\Pengelola\LaporanExportController;


// =====================
// PUBLIC / BORROWER
// =====================
Route::get('/', Home::class)->name('home');
Route::get('/barang', Catalogue::class)->name('catalogue.barang');
Route::get('/ruangan', Catalogue::class)->name('catalogue.ruangan');
Route::get('/booking-barang', Cart::class)->name('cart');
Route::get('/success', Success::class)->name('success');


// =====================
// DASHBOARD + SETTINGS
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
// PENGELOLA
// =====================
Route::prefix('pengelola')->name('pengelola.')->group(function () {

    Route::get('/beranda', Beranda::class)->name('beranda');
    Route::get('/laporan', Laporan::class)->name('laporan');

    // ✅ ROUTE EXPORT LAPORAN
    Route::get('/laporan/export/{format}', [LaporanExportController::class, 'export'])
        ->whereIn('format', ['xlsx','csv','pdf'])
        ->name('laporan.export');

    Route::get('/tambah-barang', TambahHapus::class)->name('tambah');
    Route::get('/approval', Approval::class)->name('approval');
}); 

// =====================
// PENGURUS (BLADE STATIC)
// =====================
Route::get('/pengurus/dashboard', function () {
    return view('dashboard');
})->name('pengurus.dashboard');

Route::get('/pengurus/peminjaman-fasilitas', function () {
    return view('peminjaman-fasilitas');
})->name('pengurus.fasilitas');

Route::get('/pengurus/peminjaman-barang', function () {
    return view('peminjaman-barang');
})->name('pengurus.barang');

Route::get('/pengurus/riwayat', function () {
    return view('riwayat');
})->name('pengurus.riwayat');