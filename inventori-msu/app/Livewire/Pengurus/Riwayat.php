<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;
use App\Models\LoanRequest;

use Livewire\WithPagination;

class Riwayat extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;

    public $search = '';
    public $showUnsubmitted = true;
    public $showSubmitted = true;

    public function toggleUnsubmitted()
    {
        $this->showUnsubmitted = !$this->showUnsubmitted;
    }

    public function toggleSubmitted()
    {
        $this->showSubmitted = !$this->showSubmitted;
    }

    public function render()
    {
        $baseQuery = LoanRequest::query()
            ->where(function($query) {
                // Selesai (Returned/Completed) OR Sedang Dipinjam (Handed Over)
                $query->whereIn('status', ['returned', 'completed', 'handed_over'])
                // ATAU Booking yang Terlambat (Approved but date passed)
                      ->orWhere(function($sub) {
                          $sub->where('status', 'approved') // Only Booking
                              ->where('loan_date_end', '<', now()); // Overdue
                      });
            })
            ->whereHas('loanRecord', function ($q) {
                $q->whereNotNull('picked_up_at')
                  ->orWhereNotNull('returned_at');
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('borrower_name', 'like', '%' . $this->search . '%')
                        ->orWhere('borrower_phone', 'like', '%' . $this->search . '%')
                        ->orWhereHas('items', function ($i) {
                            $i->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere('loan_date_start', 'like', '%' . $this->search . '%')
                        ->orWhere('loan_date_end', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['items', 'loanRecord']);

        // Data Belum Dikirim (Active Actions)
        $unsubmitted = $this->showUnsubmitted 
            ? (clone $baseQuery)
                ->whereHas('loanRecord', function($q) {
                    $q->where('is_submitted', false);
                })
                ->latest('loan_date_start') // User requested easier sorting: Loan Date is better for active items
                ->paginate($this->perPage, ['*'], 'page_unsubmitted')
            : [];

        // Data Sudah Dikirim (Archive)
        $submitted = $this->showSubmitted
            ? (clone $baseQuery)
                ->whereHas('loanRecord', function($q) {
                    $q->where('is_submitted', true);
                })
                ->latest('loan_date_start') // Sort by loan date as per recommendation
                ->paginate($this->perPage, ['*'], 'page_submitted')
            : [];

        return view('livewire.pengurus.riwayat', [
            'unsubmitted' => $unsubmitted,
            'submitted' => $submitted
        ])->layout('components.layouts.blank', ['title' => 'Riwayat Peminjaman']);
    }

    public function cancel($id)
    {
        $request = LoanRequest::find($id);

        if (!$request || !$request->loanRecord) {
            return;
        }

        // Prevent cancel if already submitted
        if ($request->loanRecord->is_submitted) {
            return;
        }

        $request->loanRecord->update([
            'returned_at' => null,
            'picked_up_at' => null,
            'is_submitted' => false,
        ]);

        // Revert status to approved so it shows up in Dashboard again
        $request->update(['status' => 'approved']);

        // session()->flash('success', 'Peminjaman dibatalkan.');
        $this->dispatch('show-toast', type: 'success', message: 'Peminjaman berhasil dibatalkan.');
    }

    public function checkIsLate($id)
    {
        $request = LoanRequest::find($id);
        if (!$request) return false;

        // Combine date and time to get full end timestamp
        $endDateTime = $request->loan_date_end->copy(); // copy to avoid mutation if it's a Carbon object
        
        if ($request->end_time) {
            $timeParts = explode(':', $request->end_time);
            $endDateTime->setTime($timeParts[0], $timeParts[1]);
        } else {
            // Default to end of day if no time specified? Or keep as is (00:00:00)
            // Usually end_time is mandatory. If not, maybe 23:59:59? 
            // Let's assume strict check against whatever is in DB.
            $endDateTime->setTime(23, 59, 59);
        }

        return now()->greaterThan($endDateTime);
    }

    public function submit($id, $notes = '-')
    {
        $request = LoanRequest::find($id);

        if (!$request) {
            return;
        }

        $request->update(['status' => 'returned']);

        if ($request->loanRecord) {
            $request->loanRecord->update([
                'is_submitted' => true,
                'notes' => $notes ?: '-' // Default to dash if empty
            ]);
        }

        // session()->flash('success', 'Peminjaman diselesaikan.');
        $this->dispatch('show-toast', type: 'success', message: 'Peminjaman berhasil diselesaikan.');
    }
}
