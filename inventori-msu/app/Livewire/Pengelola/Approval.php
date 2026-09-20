<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

use Livewire\WithPagination;

class Approval extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Modal Reject
    public $rejectId;
    public $rejectReason;

    // Modal Approve
    public $approveId;
    public $requestToApprove;
    public $approvalConflictsApproved = [];
    public $approvalConflictsPending = [];

    // Modal Cetak (BARU)
    public $selectedRequest;

    // Modal Detail Peminjam
    public $selectedBorrower;

    public function render()
    {
        // Auto-reject any pending requests that are past start date/time or submitted < H-3 for Civitas/Umum
        \App\Models\LoanRequest::autoRejectExpiredPending();

        $pendingRequests = \App\Models\LoanRequest::whereIn('status', ['pending', 'PENDING'])
            ->with(['items', 'loanItems.inventory'])
            ->latest()
            ->paginate(10, ['*'], 'pendingPage');

        $historyRequests = \App\Models\LoanRequest::whereIn('status', ['approved', 'APPROVED', 'rejected', 'REJECTED'])
            ->with('items')
            ->latest()
            ->paginate(10, ['*'], 'historyPage');

        return view('livewire.pengelola.approval', [
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests
        ])->layout('pengelola.layouts.pengelola');
    }

    // ===== APPROVE =====
    public function prepareApprove($id)
    {
        $this->approveId = $id;
        $req = \App\Models\LoanRequest::with(['items', 'loanItems.inventory'])->findOrFail($id);
        $this->requestToApprove = $req;

        $start = $req->start_date_time;
        $end = $req->end_date_time;

        $this->approvalConflictsApproved = [];
        $this->approvalConflictsPending = [];

        if ($start && $end) {
            $overlappingApproved = \App\Models\LoanRequest::getOverlappingLoans(
                $start, 
                $end, 
                ['approved', 'APPROVED', 'handed_over', 'HANDED_OVER', 'on_loan', 'ON_LOAN'], 
                $req->id
            );
            $overlappingPending = \App\Models\LoanRequest::getOverlappingLoans(
                $start, 
                $end, 
                ['pending', 'PENDING'], 
                $req->id
            );

            foreach ($req->loanItems as $li) {
                $inv = $li->inventory;
                if (!$inv) continue;

                // 1. Cek bentrok dengan yang SUDAH APPROVED
                $approvedItemsCount = 0;
                $approvedLoanList = [];

                foreach ($overlappingApproved as $oApp) {
                    foreach ($oApp->loanItems as $oLi) {
                        if ($oLi->inventory_id == $inv->id || ($oLi->inventory && $oLi->inventory->name == $inv->name)) {
                            $approvedItemsCount += $oLi->quantity;
                            $approvedLoanList[] = [
                                'id' => $oApp->id,
                                'borrower_name' => $oApp->borrower_name,
                                'reason' => $oApp->borrower_reason,
                                'time' => ($oApp->start_time ? substr($oApp->start_time, 0, 5) : '00:00') . ' - ' . ($oApp->end_time ? substr($oApp->end_time, 0, 5) : '23:59'),
                                'date' => optional($oApp->loan_date_start)->format('d M Y'),
                                'quantity' => $oLi->quantity,
                            ];
                        }
                    }
                }

                if ($inv->category === 'ruangan' && $approvedItemsCount > 0) {
                    $this->approvalConflictsApproved[] = [
                        'type' => 'ruangan',
                        'item_name' => $inv->name,
                        'message' => "Ruangan '{$inv->name}' sudah DISETUJUI untuk peminjaman lain pada jadwal yang beririsan!",
                        'loans' => $approvedLoanList
                    ];
                } elseif ($inv->category === 'barang' && ($approvedItemsCount + $li->quantity) > $inv->stock) {
                    $sisa = max(0, $inv->stock - $approvedItemsCount);
                    $this->approvalConflictsApproved[] = [
                        'type' => 'barang',
                        'item_name' => $inv->name,
                        'message' => "Stok '{$inv->name}' tidak mencukupi. Total: {$inv->stock}, sudah di-approve: {$approvedItemsCount} (Sisa: {$sisa}), pengajuan ini meminta: {$li->quantity}.",
                        'loans' => $approvedLoanList
                    ];
                }

                // 2. Cek bentrok dengan sesama PENDING
                $pendingLoanList = [];
                foreach ($overlappingPending as $oPend) {
                    foreach ($oPend->loanItems as $pLi) {
                        if ($pLi->inventory_id == $inv->id || ($pLi->inventory && $pLi->inventory->name == $inv->name)) {
                            $pendingLoanList[] = [
                                'id' => $oPend->id,
                                'borrower_name' => $oPend->borrower_name,
                                'reason' => $oPend->borrower_reason,
                                'time' => ($oPend->start_time ? substr($oPend->start_time, 0, 5) : '00:00') . ' - ' . ($oPend->end_time ? substr($oPend->end_time, 0, 5) : '23:59'),
                                'date' => optional($oPend->loan_date_start)->format('d M Y'),
                                'quantity' => $pLi->quantity,
                            ];
                        }
                    }
                }

                if (count($pendingLoanList) > 0) {
                    $this->approvalConflictsPending[] = [
                        'type' => $inv->category,
                        'item_name' => $inv->name,
                        'message' => "Terdapat " . count($pendingLoanList) . " pengajuan PENDING lain yang juga memohon '{$inv->name}' di rentang jam beririsan.",
                        'loans' => $pendingLoanList
                    ];
                }
            }
        }

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

        session()->flash('success', "Pengajuan P" . str_pad($req->id, 3, '0', STR_PAD_LEFT) . " berhasil disetujui.");

        // Kirim Email Notifikasi
        try {
            \Illuminate\Support\Facades\Mail::to($req->borrower_email)->send(new \App\Mail\LoanApproved($req));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim email approve: " . $e->getMessage());
            session()->flash('warning', 'Pengajuan disetujui, tapi email notifikasi gagal terkirim.');
        }
        $this->approveId = null;
        $this->requestToApprove = null;
        $this->approvalConflictsApproved = [];
        $this->approvalConflictsPending = [];
        $this->dispatch('close-approve-modal');
    }

    // ===== UNDO DECISION =====
    public function undoDecision($id)
    {
        $req = \App\Models\LoanRequest::findOrFail($id);
        $req->status = 'pending';
        $req->rejection_reason = null;
        $req->save();

        session()->flash('success', "Keputusan untuk pengajuan P" . str_pad($req->id, 3, '0', STR_PAD_LEFT) . " ({$req->borrower_name}) berhasil dibatalkan dan dikembalikan ke Daftar Pengajuan Pending.");
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

        // Kirim Email Notifikasi
        try {
            \Illuminate\Support\Facades\Mail::to($req->borrower_email)->send(new \App\Mail\LoanRejected($req));
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error("Gagal kirim email reject: " . $e->getMessage());
             session()->flash('warning', 'Pengajuan ditolak, tapi email gagal terkirim.');
        }

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

    // ===== SHOW BORROWER DETAILS =====
    public function showBorrowerDetails($id)
    {
        $this->selectedBorrower = \App\Models\LoanRequest::findOrFail($id);
        $this->dispatch('open-borrower-modal');
    }
}