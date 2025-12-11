<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;

class Riwayat extends Component
{
    public function render()
    {
        // Riwayat shows items where both pickup and return are completed.
        // OR items that were manually moved there? JS logic says "Move to Riwayat" when both checked.

        $data = \App\Models\LoanRequest::query()
            ->where('status', 'approved')
            ->whereHas('loanRecord', function ($q) {
                $q->whereNotNull('picked_up_at')
                    ->whereNotNull('returned_at');
            })
            ->orWhere('status', 'completed') // Future proofing
            ->with(['items', 'loanRecord'])
            ->latest('created_at')
            ->get();

        return view('livewire.pengurus.riwayat', [
            'data' => $data
        ])->layout('components.layouts.blank', ['title' => 'Riwayat Peminjaman']);
    }

    public function cancel($id)
    {
        // "Cancel" in JS resets the status so it goes back to the main list.
        $request = \App\Models\LoanRequest::find($id);
        if ($request && $request->loanRecord) {
            // Reset one or both to make it appear in the other list again
            // JS Logic: "Anda yakin ingin membatalkan peminjaman ini?" -> resets status.
            // We can just set returned_at to null, so it goes back to "Active Peminjaman"
            $request->loanRecord->update([
                'returned_at' => null,
                'picked_up_at' => null // Optional: fully reset? JS resets 'sudahAmbil' and 'sudahKembali'
            ]);
        }
    }

    public function submit($id)
    {
        // "Submit" in JS sets isSubmitted = true.
        // We can mark status as 'completed' or 'archived'.
        $request = \App\Models\LoanRequest::find($id);
        if ($request) {
            $request->update(['status' => 'completed']);
        }
    }
}
