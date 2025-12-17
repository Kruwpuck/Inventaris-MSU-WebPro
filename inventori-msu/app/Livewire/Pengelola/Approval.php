<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Approval extends Component
{
    public $rejectId;
    public $rejectReason;

    public function render()
    {
        $pendingRequests = LoanRequest::query()
            ->where('status', 'pending')
            ->with('items')
            ->latest('id')
            ->get();

        $historyRequests = LoanRequest::query()
            ->whereIn('status', ['approved', 'rejected'])
            ->with('items')
            ->latest('id')
            ->get();

        return view('livewire.pengelola.approval', [
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests,
        ])->layout('pengelola.layouts.pengelola');
    }

    public function approve($id)
    {
        $req = LoanRequest::findOrFail($id);
        $req->status = 'approved';

        // optional: simpan siapa yg proses (kalau kolomnya ada)
        $this->setIfColumnExists($req, 'processed_by', Auth::user()?->name);
        $this->setIfColumnExists($req, 'approved_by', Auth::user()?->name);

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

        $req = LoanRequest::findOrFail($this->rejectId);
        $req->status = 'rejected';
        $req->rejection_reason = $this->rejectReason;

        // optional: simpan siapa yg proses (kalau kolomnya ada)
        $this->setIfColumnExists($req, 'processed_by', Auth::user()?->name);
        $this->setIfColumnExists($req, 'rejected_by', Auth::user()?->name);

        $req->save();

        session()->flash('success', 'Pengajuan berhasil ditolak.');
        $this->dispatch('close-reject-modal');

        $this->reset(['rejectId', 'rejectReason']);
    }

    private function setIfColumnExists(LoanRequest $model, string $column, $value): void
    {
        if ($value === null) return;

        try {
            if (Schema::hasColumn($model->getTable(), $column)) {
                $model->{$column} = $value;
            }
        } catch (\Throwable $e) {
            // amanin aja kalau Schema/DB belum siap
        }
    }
}
