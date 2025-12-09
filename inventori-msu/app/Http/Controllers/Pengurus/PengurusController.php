<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengurus;
use App\Models\LoanRequest;
use App\Models\LoanRecord;

class PengurusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    | Menampilkan peminjaman hari ini saja.
    */
    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        // Mengambil Request yang mulai hari ini dan sudah di-approve/diproses
        $data = LoanRequest::whereDate('loan_date_start', $today)
             ->whereIn('status', ['approved', 'handed_over', 'returned'])
            ->with(['items', 'loanRecord'])
            ->get();

        return view('livewire.pengurus.dashboard', compact('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAMAN FASILITAS
    |--------------------------------------------------------------------------
    */
    public function peminjamanFasilitas()
    {
        $data = Pengurus::getPeminjamanByJenis('fasilitas');
        return view('livewire.pengurus.peminjaman-fasilitas', compact('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAMAN BARANG
    |--------------------------------------------------------------------------
    */
    public function peminjamanBarang()
    {
        $data = Pengurus::getPeminjamanByJenis('barang');
        return view('livewire.pengurus.peminjaman-barang', compact('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE CHECKLIST (AMBIL / KEMBALI)
    |--------------------------------------------------------------------------
    */
    public function toggleChecklist(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'type' => 'required|string'
        ]);

        // Menggunakan helper di model Pengurus yang sudah direfactor
        $success = Pengurus::toggleStatus($request->id, $request->type);

        return response()->json(['success' => $success]);
    }

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT PEMINJAMAN
    |--------------------------------------------------------------------------
    */
    public function riwayat()
    {
        $riwayat = Pengurus::getAllRiwayat();
        return view('livewire.pengurus.riwayat', compact('riwayat'));
    }

    /*
    |--------------------------------------------------------------------------
    | OVERRIDE (KEMBALIKAN CHECKLIST KE PEMINJAMAN)
    |--------------------------------------------------------------------------
    */
    public function overrideRiwayat(Request $request)
    {
        $request->validate([
            'id' => 'required|integer' // loan_record id
        ]);

        $result = Pengurus::cancelRiwayat($request->id);

        return response()->json(['success' => (bool) $result]);
    }

    /*
    |--------------------------------------------------------------------------
    | SUBMIT KE PENGELOLA
    |--------------------------------------------------------------------------
    */
    public function submitRiwayat(Request $request)
    {
        $request->validate([
            'id' => 'required|integer' // loan_record id
        ]);

        Pengurus::submitRiwayat($request->id);

        return response()->json(['success' => true]);
    }
}
