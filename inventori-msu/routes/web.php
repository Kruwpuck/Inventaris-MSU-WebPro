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
Route::get('/', function() {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function() {
    return view('dashboard');
})->name('dashboard');

Route::get('/peminjaman-fasilitas', function() {
    return view('peminjaman-fasilitas');
})->name('fasilitas');

Route::get('/peminjaman-barang', function() {
    return view('peminjaman-barang');
})->name('barang');

Route::get('/riwayat', function() {
    return view('riwayat');
})->name('riwayat');
