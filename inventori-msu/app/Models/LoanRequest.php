<?php

namespace App\Models;

use App\Observers\LoanRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(LoanRequestObserver::class)]
class LoanRequest extends Model
{
    protected $fillable = [
        'borrower_name',
        'borrower_email',
        'borrower_phone',
        'department',             // ✅ Baru
        'nim_nip',                // ✅ Baru
        'borrower_reason',        // Keperluan
        'proposal_path',          // ✅ tambah ini
        'loan_date_start',
        'loan_date_end',
        'start_time',
        'end_time',
        'ktp_path',             // ✅ Baru
        'activity_location',    // ✅ Baru
        'activity_description',
        'description', // ✅ Baru
        'borrower_category',      // ✅ Baru (Wajihah / Civitas Akademika / Umum)
        'status',
        'rejection_reason',
        'donation_amount', // ✅ Baru
        'duration', // ✅ Added
    ];

    public static function autoRejectExpiredPending()
    {
        $now = \Carbon\Carbon::now();

        $pendingRequests = static::query()
            ->whereIn('status', ['pending', 'PENDING'])
            ->get();

        foreach ($pendingRequests as $req) {
            $shouldReject = false;
            $reason = null;

            // Parse loan start datetime
            $startDate = \Carbon\Carbon::parse($req->loan_date_start);
            if ($req->start_time) {
                $timeParts = explode(':', $req->start_time);
                $startDate->setTime((int)$timeParts[0], (int)$timeParts[1], isset($timeParts[2]) ? (int)$timeParts[2] : 0);
            } else {
                $startDate->setTime(0, 0, 0);
            }

            // Rule 2: Waktu peminjaman sudah tiba / terlewat tapi belum ada keputusan (auto-reject)
            if ($now->greaterThanOrEqualTo($startDate)) {
                $shouldReject = true;
                $reason = 'Sistem Auto-Reject: Peminjaman belum mendapat keputusan pengelola hingga waktu peminjaman dimulai.';
            } 
            // Rule 1: Civitas Akademika & Umum yang mengajukan di bawah H-3 (H-2, H-1, Hari H)
            elseif (in_array($req->borrower_category, ['Civitas Akademika', 'Umum'])) {
                $createdDay = \Carbon\Carbon::parse($req->created_at)->startOfDay();
                $loanStartDay = \Carbon\Carbon::parse($req->loan_date_start)->startOfDay();
                $daysDiff = $createdDay->diffInDays($loanStartDay, false);

                if ($daysDiff < 3) {
                    $shouldReject = true;
                    $reason = 'Sistem Auto-Reject: Pengajuan peminjaman untuk kategori Civitas Akademika / Umum wajib diajukan minimal H-3 sebelum tanggal peminjaman.';
                }
            }

            if ($shouldReject) {
                $req->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason
                ]);
            }
        }
    }

    protected $casts = [
        'loan_date_start' => 'datetime',
        'loan_date_end' => 'datetime',
    ];

    // daftar inventory yang dipinjam + qty di pivot
    public function items()
    {
        return $this->belongsToMany(Inventory::class, 'loan_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // akses pivot loan_items langsung (lebih fleksibel)
    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function loanRecord(): HasOne
    {
        return $this->hasOne(LoanRecord::class);
    }

    public static function autoCompleteUnpicked()
    {
        $expiredRequests = static::query()
            ->where('status', 'approved')
            ->whereDoesntHave('loanRecord', function($q) {
                $q->whereNotNull('picked_up_at');
            })
            ->get();

        foreach ($expiredRequests as $req) {
            $endDateTime = $req->loan_date_end->copy();
            if ($req->end_time) {
                $timeParts = explode(':', $req->end_time);
                $endDateTime->setTime($timeParts[0], $timeParts[1]);
            } else {
                $endDateTime->setTime(23, 59, 59);
            }

            if (now()->greaterThan($endDateTime)) {
                $req->update(['status' => 'returned']);
                $record = $req->loanRecord()->firstOrCreate([
                    'loan_request_id' => $req->id
                ]);
                $record->update([
                    'is_submitted' => true,
                    'notes' => 'Sistem (Autokirim): Waktu sudah habis tapi peminjam tidak pernah datang mengambil barang'
                ]);
            }
        }
    }

    /**
     * Get the UI friendly status label.
     */
    public function getStatusUiAttribute()
    {
        $today = \Carbon\Carbon::today();
        $jatuhTempo = $this->loan_date_end;

        switch ($this->status) {
            case 'returned':
                $actualReturn = null;
                if ($this->loanRecord && $this->loanRecord->returned_at) {
                    $actualReturn = \Carbon\Carbon::parse($this->loanRecord->returned_at);
                }

                if ($this->loanRecord && $this->loanRecord->is_submitted) {
                    if ($this->loanRecord->notes === 'Sistem (Autokirim): Waktu sudah habis tapi peminjam tidak pernah datang mengambil barang') {
                        return 'Batal';
                    }
                    if ($actualReturn && $actualReturn->gt($jatuhTempo)) {
                        return 'Terlambat';
                    }
                    return 'Selesai';
                }
                return 'Sudah Kembali';

            case 'handed_over':
                return $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';

            case 'approved':
                return 'Siap Diambil';

            case 'pending':
                return 'Menunggu Approve';

            case 'rejected':
                return 'Ditolak';

            default:
                return $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
        }
    }
}
