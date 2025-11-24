<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// PENGELOLA (JANGAN DI PINDAH PINDAH!!!!!!!)
use App\Livewire\Pengelola\Beranda;
use App\Livewire\Pengelola\Laporan; 
use App\Livewire\Pengelola\TambahHapus;
use App\Livewire\Pengelola\Approval;

//PENGURUS (JANGAN DI GANGGU!!!!!!!!!!)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\PengurusController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

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

// PENGELOLA 
Route::prefix('pengelola')->name('pengelola.')->group(function () {
    Route::get('/beranda', Beranda::class)->name('beranda');
    Route::get('/laporan', Laporan::class)->name('laporan');
    Route::get('/tambah-barang', TambahHapus::class)->name('tambah');
    Route::get('/approval', Approval::class)->name('approval');
}); 

// PENGURUS
Route::middleware(['auth','pengurus'])->group(function () {
    Route::get('/pengurus/dashboard', [PengurusController::class,'dashboard'])->name('pengurus.dashboard');
    Route::get('/pengurus/pinjam', [PengurusController::class,'pinjamFasilitas'])->name('pengurus.pinjam');
    Route::get('/pengurus/riwayat', [PengurusController::class,'riwayat'])->name('pengurus.riwayat');

    Route::post('/pengurus/toggle-status', [PengurusController::class,'toggleStatus'])->name('pengurus.toggleStatus');
});
