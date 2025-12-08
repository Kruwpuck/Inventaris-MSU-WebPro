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
// LIVEWIRE BORROWER / GUEST
// =====================
use App\Livewire\Borrower\Home;
use App\Livewire\Borrower\Catalogue;
use App\Livewire\Borrower\Cart;
use App\Livewire\Borrower\Success;


// =====================
// PUBLIC / BORROWER
// =====================
Route::get('/', Home::class)->name('home');
Route::get('/barang', Catalogue::class)->name('catalogue.barang');
Route::get('/ruangan', Catalogue::class)->name('catalogue.ruangan');
Route::get('/booking-barang', Cart::class)->name('cart');
Route::get('/success', Success::class)->name('success');


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
Route::prefix('pengelola')->name('pengelola.')->group(function () {

    Route::get('/beranda', Beranda::class)->name('beranda');
    Route::get('/laporan', Laporan::class)->name('laporan');

    // Export laporan
    Route::get('/laporan/export/{format}', [LaporanExportController::class, 'export'])
        ->whereIn('format', ['xlsx', 'csv', 'pdf'])
        ->name('laporan.export');

    Route::get('/tambah-barang', TambahHapus::class)->name('tambah');
    Route::get('/approval', Approval::class)->name('approval');
});


Route::prefix('pengurus')->name('pengurus.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/peminjaman-fasilitas', [PeminjamanController::class, 'fasilitas'])->name('fasilitas');
    Route::get('/peminjaman-barang', [PeminjamanController::class, 'barang'])->name('barang');

    Route::post('/toggle-status', [PeminjamanController::class, 'toggleStatus'])->name('toggle');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::post('/riwayat/cancel/{id}', [RiwayatController::class, 'cancel'])->name('cancel');
    Route::post('/riwayat/submit/{id}', [RiwayatController::class, 'submit'])->name('submit');
});


Route::prefix('pengurus')->name('pengurus.')->group(function () {

    Route::get('/dashboard',            [PengurusController::class, 'dashboard'])->name('dashboard');
    Route::get('/peminjaman-fasilitas', [PengurusController::class, 'peminjamanFasilitas'])->name('fasilitas');
    Route::get('/peminjaman-barang',    [PengurusController::class, 'peminjamanBarang'])->name('barang');
    Route::get('/riwayat',              [PengurusController::class, 'riwayat'])->name('riwayat');

    // Ajax routes
    Route::post('/toggle-checklist',    [PengurusController::class, 'toggleChecklist'])->name('toggle');
    Route::post('/override',            [PengurusController::class, 'overrideRiwayat'])->name('override');
    Route::post('/submit',              [PengurusController::class, 'submitRiwayat'])->name('submit');
});


