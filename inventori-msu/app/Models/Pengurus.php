<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    // Model helper, tidak punya tabel sendiri
    protected $table = null;

    /*
    |--------------------------------------------------------------------------
    | OPERASI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public static function getAllPeminjaman()
    {
        return LoanRequest::with(['items', 'loanRecord'])->get();
    }

    public static function getPeminjamanByJenis($jenis)
    {
        return LoanRequest::whereHas('items', function ($q) use ($jenis) {
                $q->where('category', $jenis);
            })
            ->with(['items', 'loanRecord'])
            ->get();
    }

    /**
     * Helper untuk mengubah status checkbox dashboard items
     * $type: 'ambil' atau 'kembali'
     */
    public static function toggleStatus($loanRequestId, $type)
    {
        $loan = LoanRequest::find($loanRequestId);
        if (!$loan) return false;

        // Pastikan record ada
        $record = LoanRecord::firstOrCreate(
            ['loan_request_id' => $loanRequestId]
        );

        if ($type === 'ambil') {
            $record->update(['picked_up_at' => now()]);
            // Update status request
            $loan->update(['status' => 'handed_over']);
        } elseif ($type === 'kembali') {
            $record->update(['returned_at' => now()]);
            // Update status request
            $loan->update(['status' => 'returned']);
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | OPERASI RIWAYAT
    |--------------------------------------------------------------------------
    */

    /**
     * Dipanggil saat kedua checkbox (ambil & kembali) sudah tercentang.
     * Dalam implementasi baru, ini mungkin redundan karena toggleStatus sudah handle,
     * tapi kita biarkan untuk kompatibilitas controller lama jika perlu.
     */
    public static function insertRiwayat($loanRequestId)
    {
        // No-op karena updateOrCreate di toggleStatus sudah menghandle
        return true;
    }

    public static function getAllRiwayat()
    {
        // Riwayat adalah yang sudah kembali (completed)
        return LoanRecord::whereNotNull('picked_up_at')
            ->whereNotNull('returned_at')
            ->with(['loanRequest.items']) // Eager load untuk akses detail
            ->latest()
            ->get();
    }

    public static function cancelRiwayat($id) // $id adalah loan_record id
    {
        $record = LoanRecord::find($id);
        if (!$record) return false;

        $loanRequestId = $record->loan_request_id;
        
        // Hapus record riwayat
        $record->delete();

        // Kembalikan status loan request ke 'approved' (siap diambil) 
        // atau 'handed_over' (sedang dipinjam) tergantung maunya.
        // Asumsi cancel riwayat berarti membatalkan penyelesaian, jadi reset total.
        LoanRequest::where('id', $loanRequestId)->update(['status' => 'approved']);

        return true;
    }

    public static function submitRiwayat($id)
    {
        return LoanRecord::where('id', $id)
            ->update(['is_submitted' => true]);
    }
}
