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

        session()->flash('success', 'Status berhasil diperbarui!');
    }

    public function render()
    {
        $data = \App\Models\LoanRequest::query()
            ->whereIn('status', ['approved', 'handed_over'])
            ->where(function ($query) {
                $query->doesntHave('loanRecord')
                    ->orWhereHas('loanRecord', function ($q) {
                        $q->whereNull('picked_up_at')
                            ->orWhereNull('returned_at');
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
