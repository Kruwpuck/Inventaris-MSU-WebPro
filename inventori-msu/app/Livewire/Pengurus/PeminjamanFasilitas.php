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
        }

        // JS Logic: If both checked, confirm and move (we skipped confirm for now for UX speed, or we can add it later)
        // If both are present, we update the main LoanRequest status to 'completed' so it moves to Riwayat?
        // OR we just keep it approved but filter it out from the active view if both dates are set.

        $record->save();

        // Optional: Update parent status if both are Done? 
        // For strict replication of JS "Move to Riwayat", items disappear from here when both checked.
        // So we just need to ensure render() filters out items with both dates.
    }

    public function render()
    {
        $data = \App\Models\LoanRequest::query()
            ->where('status', 'approved')
            ->whereHas('loanRecord', function ($q) {
                $q->whereNull('picked_up_at')
                    ->orWhereNull('returned_at');
            })
            ->orWhereDoesntHave('loanRecord') // Include those with no record yet
            ->with(['items', 'loanRecord'])
            ->latest('loan_date_start')
            ->get();

        // Filter in memory to be safe about the OR logic with status 'approved'
        // Actually, the above query might be tricky if 'orWhereDoesntHave' is inclusive of other statuses?
        // Let's refine: Approved AND (No Record OR (Record exists AND (pickup null OR return null)))
        $data = \App\Models\LoanRequest::query()
            ->where('status', 'approved')
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
