<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengurus extends Model
{
    // Model ini tidak terkait langsung dengan tabel tertentu.
    protected $table = null;

    /*
    |--------------------------------------------------------------------------
    | OPERASI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public static function getAllPeminjaman()
    {
        return DB::table('peminjaman')->get();
    }

    public static function getPeminjamanByJenis($jenis)
    {
        return DB::table('peminjaman')
            ->where('jenis', $jenis)
            ->get();
    }

    public static function toggleStatus($id, $field)
    {
        return DB::table('peminjaman')
            ->where('id', $id)
            ->update([$field => 1]);
    }


    /*
    |--------------------------------------------------------------------------
    | OPERASI RIWAYAT
    |--------------------------------------------------------------------------
    */

    public static function insertRiwayat($peminjamanId)
    {
        return DB::table('riwayat')->insert([
            'peminjaman_id'      => $peminjamanId,
            'waktu_ambil_real'   => now()->format('d F Y H:i'),
            'waktu_kembali_real' => now()->format('d F Y H:i'),
            'is_submitted'       => false,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    public static function getAllRiwayat()
    {
        return DB::table('riwayat')
            ->join('peminjaman', 'riwayat.peminjaman_id', '=', 'peminjaman.id')
            ->select('riwayat.*', 'peminjaman.nama', 'peminjaman.fasilitas', 'peminjaman.jenis')
            ->get();
    }

    public static function cancelRiwayat($id)
    {
        $riwayat = DB::table('riwayat')->where('id', $id)->first();

        if (!$riwayat) return false;

        // Reset status di tabel peminjaman
        DB::table('peminjaman')
            ->where('id', $riwayat->peminjaman_id)
            ->update([
                'status_ambil'    => 0,
                'status_kembali'  => 0,
            ]);

        // hapus riwayat
        return DB::table('riwayat')->where('id', $id)->delete();
    }

    public static function submitRiwayat($id)
    {
        return DB::table('riwayat')
            ->where('id', $id)
            ->update(['is_submitted' => true]);
    }
}
