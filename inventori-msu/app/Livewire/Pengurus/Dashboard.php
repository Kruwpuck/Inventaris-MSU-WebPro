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

        // Ensure LoanRecord exists
        $record = $request->loanRecord()->firstOrCreate([
            'loan_request_id' => $request->id
        ]);

        if ($type === 'ambil') {
            $record->picked_up_at = $record->picked_up_at ? null : now();
        } elseif ($type === 'kembali') {
            $record->returned_at = $record->returned_at ? null : now();
            
            // Auto-check 'ambil' if 'kembali' is checked (Logic from Step 70)
            if ($record->returned_at && !$record->picked_up_at) {
                $record->picked_up_at = $record->returned_at;
            }
        }

        $record->save();

        // Sync Status to LoanRequest
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
            // Filter: Show if NO Record OR (Record exists but NOT BOTH timestamps are set)
            ->where(function ($query) {
                $query->whereDoesntHave('loanRecord')
                      ->orWhereHas('loanRecord', function ($q) {
                          $q->whereNull('picked_up_at')
                            ->orWhereNull('returned_at');
                      });
            })
            // Filter: Only show loans for today
            ->whereDate('loan_date_start', \Carbon\Carbon::today())
            ->with(['items', 'loanRecord'])
            ->latest('loan_date_start')
            ->get();

        return view('livewire.pengurus.dashboard', [
            'data' => $data
        ])->layout('components.layouts.blank', ['title' => 'Dashboard Pengurus']);
    }
}
