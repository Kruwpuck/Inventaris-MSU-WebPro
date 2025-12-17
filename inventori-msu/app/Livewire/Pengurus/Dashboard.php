<?php

namespace App\Livewire\Pengurus;

use Livewire\Component;

class Dashboard extends Component
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
            
            // Auto-check 'ambil' if 'kembali' is checked (Logic from Step 70
            // Auto-check 'ambil' if 'kembali' is check
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
        // Filter: approved or handed_over. Hide if returned (or both dates set).
        // Since we update status to 'returned' when returned_at is set, we can just filter by status.
        // But to be consistent with "Wait until both set" if that was the rule?
        // JS Logic: "Jika dua-duanya sudah centang -> hapus".
        // If we strictly follow JS, we should wait until both are checked.
        // My previous implementation (Step 96) used: 
        // ->whereIn('status', ['approved', 'handed_over']) AND (No Record OR Not Both Set)
        
        $data = \App\Models\LoanRequest::query()
            ->whereIn('status', ['approved', 'handed_over'])

            // Filter: Show if NO Record OR (Record exists but NOT BOTH timestamps are set)

            ->where(function ($query) {
                $query->whereDoesntHave('loanRecord')
                      ->orWhereHas('loanRecord', function ($q) {
                          $q->whereNull('picked_up_at')
                            ->orWhereNull('returned_at');
                      });
            })

            // Filter: Only show loans for toda
            ->whereDate('loan_date_start', \Carbon\Carbon::today())
            ->with(['items', 'loanRecord'])
            ->latest('loan_date_start')
            ->get();

        return view('livewire.pengurus.dashboard', [
            'data' => $data
        ])->layout('components.layouts.blank', ['title' => 'Dashboard Pengurus']);
    }
}
