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

                // Kirim notifikasi email penolakan otomatis ke peminjam
                try {
                    \Illuminate\Support\Facades\Mail::to($req->borrower_email)->send(new \App\Mail\LoanRejected($req));
                    \Illuminate\Support\Facades\Log::info("Email auto-reject terkirim ke: {$req->borrower_email} untuk pengajuan #{$req->id}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim email auto-reject #{$req->id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Accessor untuk datetime mulai yang lengkap
     */
    public function getStartDateTimeAttribute()
    {
        $d = $this->loan_date_start ? \Carbon\Carbon::parse($this->loan_date_start) : null;
        if (!$d) return null;
        if ($this->start_time) {
            $parts = explode(':', $this->start_time);
            $d->setTime((int)$parts[0], (int)$parts[1], isset($parts[2]) ? (int)$parts[2] : 0);
        } else {
            $d->setTime(0, 0, 0);
        }
        return $d;
    }

    /**
     * Accessor untuk datetime selesai yang lengkap
     */
    public function getEndDateTimeAttribute()
    {
        $d = $this->loan_date_end ? \Carbon\Carbon::parse($this->loan_date_end) : ($this->loan_date_start ? \Carbon\Carbon::parse($this->loan_date_start) : null);
        if (!$d) return null;
        if ($this->end_time) {
            $parts = explode(':', $this->end_time);
            $d->setTime((int)$parts[0], (int)$parts[1], isset($parts[2]) ? (int)$parts[2] : 0);
        } else {
            $d->setTime(23, 59, 59);
        }
        return $d;
    }

    /**
     * Ambil peminjaman yang bertabrakan waktu dengan rentang tanggal dan jam yang diberikan.
     * Logika overlap: (StartA < EndB) dan (EndA > StartB)
     */
    public static function getOverlappingLoans(\Carbon\Carbon $start, \Carbon\Carbon $end, array $statuses = ['approved', 'handed_over'], $excludeId = null)
    {
        return static::with(['items', 'loanItems.inventory'])
            ->whereIn('status', $statuses)
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('id', '!=', $excludeId);
            })
            ->get()
            ->filter(function ($loan) use ($start, $end) {
                $loanStart = $loan->start_date_time;
                $loanEnd = $loan->end_date_time;
                if (!$loanStart || !$loanEnd) return false;
                return $start->lt($loanEnd) && $end->gt($loanStart);
            });
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
