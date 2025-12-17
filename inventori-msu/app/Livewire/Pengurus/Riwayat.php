<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;
use App\Models\LoanRequest;

class Riwayat extends Component
{
    public function render()
    {
        $data = LoanRequest::query()
            ->whereIn('status', ['approved', 'handed_over', 'returned', 'completed'])
            ->whereHas('loanRecord', function ($q) {
                $q->whereNotNull('picked_up_at')
                  ->orWhereNotNull('returned_at');
            })
            ->with(['items', 'loanRecord'])
            ->latest('created_at')
            ->get();

        return view('livewire.pengurus.riwayat', [
            'data' => $data
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

        session()->flash('success', 'Peminjaman dibatalkan.');
    }

    public function submit($id)
    {
        $request = LoanRequest::find($id);

        if (!$request) {
            return;
        }

        $request->update(['status' => 'returned']);

        if ($request->loanRecord) {
            $request->loanRecord->update(['is_submitted' => true]);
        }

        session()->flash('success', 'Peminjaman diselesaikan.');
    }
}
