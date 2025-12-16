<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;

class Riwayat extends Component
{
    public function render()
    {
<<<<<<< HEAD
=======
        // View items with at least one action taken (picked up or returned)
        // Also include statuses that imply activity: handed_over, returned, completed
>>>>>>> 485abd5ca0e092fcd41540a7589f5eb19fad224c
        $data = \App\Models\LoanRequest::query()
            ->whereIn('status', ['approved', 'handed_over', 'returned', 'completed'])
            ->whereHas('loanRecord', function ($q) {
                // Show in Riwayat if AT LEAST ONE timestamp is set
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
<<<<<<< HEAD
        $request = \App\Models\LoanRequest::find($id);
        if ($request && $request->loanRecord) {
=======
        if ($request && $request->loanRecord) {
            // Prevent cancel if already submitted
            if ($request->loanRecord->is_submitted) {
                return;
            }

>>>>>>> 485abd5ca0e092fcd41540a7589f5eb19fad224c
            $request->loanRecord->update([
                'returned_at' => null,
                'picked_up_at' => null,
                'is_submitted' => false
            ]);

            // Revert status to approved so it shows up in Dashboard again
            $request->update(['status' => 'approved']);
<<<<<<< HEAD

=======
            
>>>>>>> 485abd5ca0e092fcd41540a7589f5eb19fad224c
            session()->flash('success', 'Peminjaman dibatalkan.');
        }
    }

    public function submit($id)
    {
        $request = \App\Models\LoanRequest::find($id);
        if ($request) {
<<<<<<< HEAD
            // Check if status 'completed' is valid or use 'returned' + is_submitted
            // Assuming 'completed' is NOT in migration, we use 'returned' + is_submitted flag
            // But if user meant "Finish workflow", we might need a status that hides it?
            // Since migration only has 'returned', we stick to 'returned'.
            // But if we want it to stay in Riwayat (which uses 'returned'), it's fine.
            
            // Or if user really wants 'completed' and added it manually:
            // For safety with strictly 'handed_over'/'returned':
=======
            // "Submit" typically means finalizing the process.
            // If status 'complated' exists, use it. Otherwise 'returned' + is_submitted.
            // Assuming 'returned' is the final status in enum.
>>>>>>> 485abd5ca0e092fcd41540a7589f5eb19fad224c
            $request->update(['status' => 'returned']);
            
            if ($request->loanRecord) {
                $request->loanRecord->update(['is_submitted' => true]);
            }
<<<<<<< HEAD

=======
            
>>>>>>> 485abd5ca0e092fcd41540a7589f5eb19fad224c
            session()->flash('success', 'Peminjaman diselesaikan.');
        }
    }
}
