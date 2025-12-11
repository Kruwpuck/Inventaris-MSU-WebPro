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
        }

        $record->save();

        session()->flash('success', 'Status berhasil diperbarui!');
    }

    public function render()
    {
        $data = \App\Models\LoanRequest::query()
            ->where('status', 'approved')
            ->with(['items', 'loanRecord'])
            ->latest('loan_date_start')
            ->get();

        return view('livewire.pengurus.dashboard', [
            'data' => $data
        ])->layout('components.layouts.blank', ['title' => 'Dashboard Pengurus']);
    }
}
