<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class Approval extends Component
{
    // Modal Reject
    public $rejectId;
    public $rejectReason;

    // Modal Approve
    public $approveId;

    // Modal Cetak (BARU)
    public $selectedRequest;

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
        ])->layout('pengelola.layouts.pengelola');
    }

    // ===== APPROVE =====
    public function prepareApprove($id)
    {
        $this->approveId = $id;
        $this->dispatch('open-approve-modal');
    }

    public function approveConfirmed()
    {
        $this->validate([
            'approveId' => 'required|exists:loan_requests,id',
        ]);

        $req = \App\Models\LoanRequest::findOrFail($this->approveId);
        $req->status = 'approved';
        $req->save();

        session()->flash('success', 'Pengajuan berhasil disetujui.');

        $this->approveId = null;
        $this->dispatch('close-approve-modal');
    }

    // ===== REJECT =====
    public function prepareReject($id)
    {
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->dispatch('open-reject-modal');
    }

    public function reject()
    {
        $this->validate([
            'rejectId' => 'required|exists:loan_requests,id',
            'rejectReason' => 'required|string|min:3',
        ]);

        $req = \App\Models\LoanRequest::findOrFail($this->rejectId);
        $req->status = 'rejected';
        $req->rejection_reason = $this->rejectReason;
        $req->save();

        session()->flash('success', 'Pengajuan berhasil ditolak.');
        $this->dispatch('close-reject-modal');
    }

    // ===== CETAK (SHOW DETAIL) =====
    public function showDetails($id)
    {
        // Ambil data beserta relasi items
        $this->selectedRequest = \App\Models\LoanRequest::with('items')->find($id);

        // Buka modal cetak di frontend
        $this->dispatch('open-print-modal');
    }
}