<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengurus;

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

        $data = \DB::table('peminjaman')
            ->whereDate('waktu_pinjam', $today)
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

        if ($request->type === 'ambil') {
            Pengurus::toggleStatus($request->id, 'status_ambil');
        } elseif ($request->type === 'kembali') {
            Pengurus::toggleStatus($request->id, 'status_kembali');

            // Jika dua-duanya checklist → masukkan riwayat
            $p = \DB::table('peminjaman')->where('id', $request->id)->first();
            if ($p->status_ambil == 1 && $p->status_kembali == 1) {
                Pengurus::insertRiwayat($request->id);
            }
        }

        return response()->json(['success' => true]);
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
            'id' => 'required|integer'
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
            'id' => 'required|integer'
        ]);

        Pengurus::submitRiwayat($request->id);

        return response()->json(['success' => true]);
    }
}
