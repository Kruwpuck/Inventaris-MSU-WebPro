<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;

class PeminjamanFasilitas extends Component
{
    public function toggleStatus($id, $type)
    {
        $request = \App\Models\LoanRequest::find($id);
        if (!$request)
            return;

        $record = $request->loanRecord()->firstOrCreate([
            'loan_request_id' => $request->id
        ]);

        if ($type === 'ambil') {
            $record->picked_up_at = $record->picked_up_at ? null : now();
        } elseif ($type === 'kembali') {
            $record->returned_at = $record->returned_at ? null : now();
            
            // Auto-check 'ambil' if 'kembali' is checked
            if ($record->returned_at && !$record->picked_up_at) {
                $record->picked_up_at = $record->returned_at;
            }
        }

        $record->save();
        if ($record->returned_at) {
            $request->status = 'returned';
        } elseif ($record->picked_up_at) {
            $request->status = 'handed_over';
        } else {
            $request->status = 'approved';
        }
        $request->save();

        $message = 'Status berhasil diperbarui!';
        if ($type === 'ambil' && $record->picked_up_at) {
            $message = 'Fasilitas berhasil diambil.';
        } elseif ($type === 'kembali' && $record->returned_at) {
            $message = 'Fasilitas berhasil dikembalikan.';
        }

        // session()->flash('success', 'Status berhasil diperbarui!');
        $this->dispatch('show-toast', type: 'success', message: $message);
    }

    public $search = '';

    public function render()
    {
        // Filter: Approved/HandedOver AND (No Record OR (Record exists but NOT BOTH timestamps are set))
        $data = \App\Models\LoanRequest::query()
            ->whereIn('status', ['approved', 'handed_over'])
            ->where(function ($query) {
                $query->doesntHave('loanRecord')
                    ->orWhereHas('loanRecord', function ($q) {
                        $q->whereNull('picked_up_at')
                            ->orWhereNull('returned_at');
                    });
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
            ->with(['items', 'loanRecord'])
            ->latest('loan_date_start')
            ->get();

        return view('livewire.pengurus.peminjaman-fasilitas', [
            'data' => $data
        ])->layout('components.layouts.blank', ['title' => 'Peminjaman Fasilitas']);
    }
}
