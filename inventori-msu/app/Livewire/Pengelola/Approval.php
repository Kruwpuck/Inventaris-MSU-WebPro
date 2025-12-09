<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class Approval extends Component
{
    // Untuk modal rejection
    public $rejectId;
    public $rejectReason;

    public function render()
    {
        $pendingRequests = \App\Models\LoanRequest::where('status', 'pending')
            ->with('items')
            ->latest()
            ->get();

        $historyRequests = \App\Models\LoanRequest::whereIn('status', ['approved', 'rejected'])
            ->with('items')
            ->latest()
            ->get();

        return view('livewire.pengelola.approval', [
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests
        ])
        ->layout('pengelola.layouts.pengelola');
    }

    public function approve($id)
    {
        $req = \App\Models\LoanRequest::findOrFail($id);
        $req->status = 'approved';
        $req->save();

        session()->flash('success', 'Pengajuan berhasil disetujui.');
    }

    public function prepareReject($id)
    {
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->dispatch('open-reject-modal');
    }

    public function reject()
    {
        $this->validate([
            'rejectId'     => 'required|exists:loan_requests,id',
            'rejectReason' => 'required|string|min:3',
        ]);

        $req = \App\Models\LoanRequest::findOrFail($this->rejectId);
        $req->status = 'rejected';
        $req->rejection_reason = $this->rejectReason;
        $req->save();

        session()->flash('success', 'Pengajuan berhasil ditolak.');
        $this->dispatch('close-reject-modal');
    }
}
