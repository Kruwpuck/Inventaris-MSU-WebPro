<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;

class Riwayat extends Component
{
    public function render()
    {
        // View items with at least one action taken (picked up or returned)
        // Also include statuses that imply activity: handed_over, returned, completed
        $data = \App\Models\LoanRequest::query()
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
        if ($request && $request->loanRecord) {
            // Prevent cancel if already submitted
            if ($request->loanRecord->is_submitted) {
                return;
            }

            $request->loanRecord->update([
                'returned_at' => null,
                'picked_up_at' => null,
                'is_submitted' => false
            ]);

            // Revert status to approved so it shows up in Dashboard again
            $request->update(['status' => 'approved']);
            
            session()->flash('success', 'Peminjaman dibatalkan.');
        }
    }

    public function submit($id)
    {
        $request = \App\Models\LoanRequest::find($id);
        if ($request) {
            // "Submit" typically means finalizing the process.
            // If status 'complated' exists, use it. Otherwise 'returned' + is_submitted.
            // Assuming 'returned' is the final status in enum.
            $request->update(['status' => 'returned']);
            
            if ($request->loanRecord) {
                $request->loanRecord->update(['is_submitted' => true]);
            }
            
            session()->flash('success', 'Peminjaman diselesaikan.');
        }
    }
}
