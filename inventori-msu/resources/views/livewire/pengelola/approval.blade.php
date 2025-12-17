{{-- resources/views/livewire/pengelola/approval.blade.php --}}

@push('head')
<style>
  body { background:#fff; font-family:"Poppins",sans-serif; }

  .page-wrap{ padding-top: 110px; padding-bottom: 60px; }

  .section-title{
    font-size: 2.1rem;
    font-weight: 500;
    letter-spacing: .2px;
  }
  .section-subtitle{ color:#6c757d; margin-top: .5rem; }

  .card-soft{
    background:#fff;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    border: 1px solid rgba(0,0,0,.04);
  }

  .table thead th{
    background:#f8f9fa;
    color:#4b5563;
    font-weight: 700;
    font-size: .92rem;
    white-space: nowrap;
    border-bottom: 1px solid rgba(0,0,0,.08) !important;
    vertical-align: middle;
  }
  .table tbody td{ vertical-align: middle; }

  /* ====== BADGE KOTAK (status) — LEBIH KECIL ====== */
  .badge-box{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: .22rem .5rem;
    border-radius: 4px;
    font-weight: 700;
    font-size: .75rem;
    white-space: nowrap;
    min-width: 78px;
    line-height: 1.2;
    border: 1px solid rgba(0,0,0,.08);
  }
  .badge-pending{ background:#f6d36a; color:#3b2f00; }
  .badge-approved{ background:#2e7d32; color:#fff; }
  .badge-rejected{ background:#c62828; color:#fff; }

  /* ====== BUTTON KOTAK (aksi) — LEBIH RINGKAS ====== */
  .btn-box{
    border-radius: 4px !important;
    padding: .28rem .65rem !important;
    font-size: .78rem;
    font-weight: 700;
    line-height: 1.2;
    border-width: 1px !important;
  }
  .btn-approve{
    background:#2e7d32 !important;
    border-color:#2e7d32 !important;
    color:#fff !important;
  }
  .btn-reject{
    background:#c62828 !important;
    border-color:#c62828 !important;
    color:#fff !important;
  }

  .items-list{
    list-style: none;
    padding-left: 0;
    margin: 0;
  }
  .items-list li{ line-height: 1.35; }

  .btn-icon{
    width: 38px;
    height: 38px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,.12);
    background:#fff;
  }
  .btn-icon:hover{ background:#f8f9fa; }

  .table-responsive{ border-radius: 16px; overflow: hidden; }

  @media (max-width: 576px){
    .section-title{ font-size: 1.6rem; }
  }
</style>
@endpush

<div class="container page-wrap">

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- =========================
       PENDING SECTION
  ========================= --}}
  <div class="mb-5">
    <div class="mb-4">
      <div class="section-title">Daftar Pengajuan (Pending)</div>
      <div class="section-subtitle">Tinjau pengajuan peminjaman barang dan fasilitas yang masih tertunda.</div>
    </div>

    <div class="card-soft">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:90px">ID</th>
              <th style="min-width:160px">Peminjam</th>
              <th style="min-width:220px">Barang/Fasilitas</th>
              <th style="min-width:140px">Tgl Pinjam</th>
              <th style="width:130px">Status</th>
              <th style="min-width:190px">Aksi</th>
              <th style="min-width:170px">Proposal</th>
            </tr>
          </thead>

          <tbody>
            @forelse($pendingRequests as $req)
              @php
                // Tgl pinjam: cuma 1 tanggal (start)
                $start = optional($req->loan_date_start)->format('Y-m-d')
                  ?? (is_string($req->loan_date_start) ? $req->loan_date_start : '-');

                $proposalUrl = $req->proposal_url
                  ?? $req->proposal_path
                  ?? $req->proposal
                  ?? null;

                if ($proposalUrl && !str_starts_with($proposalUrl, 'http')) {
                  $proposalUrl = str_starts_with($proposalUrl, '/')
                    ? $proposalUrl
                    : (str_starts_with($proposalUrl, 'storage/') ? asset($proposalUrl) : asset('storage/'.$proposalUrl));
                }
              @endphp

              <tr wire:key="pending-{{ $req->id }}">
                <td><strong>P{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ $req->borrower_name }}</td>

                <td>
                  <ul class="items-list">
                    @foreach($req->items as $item)
                      <li>{{ $item->name }} (x{{ $item->pivot->quantity ?? 1 }})</li>
                    @endforeach
                  </ul>
                </td>

                <td>{{ $start }}</td>

                <td>
                  <span class="badge-box badge-pending">Pending</span>
                </td>

                <td>
                  <div class="d-flex flex-wrap gap-2">
                    <button
                      class="btn btn-approve btn-sm btn-box"
                      wire:click="approve({{ $req->id }})"
                      wire:confirm="Yakin ingin menyetujui pengajuan ini?"
                      type="button"
                    >
                      <i class="bi bi-check-lg me-1"></i>Setuju
                    </button>

                    <button
                      class="btn btn-reject btn-sm btn-box"
                      wire:click="prepareReject({{ $req->id }})"
                      type="button"
                    >
                      <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                  </div>
                </td>

                <td>
                  @if($proposalUrl)
                    <a class="btn btn-outline-primary btn-sm btn-box" href="{{ $proposalUrl }}" target="_blank" rel="noopener">
                      <i class="bi bi-file-earmark-text me-1"></i>Tinjau Proposal
                    </a>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="p-5 text-muted text-center">
                  Tidak ada pengajuan pending saat ini.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

  {{-- =========================
       HISTORY SECTION
  ========================= --}}
  <div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
      <div>
        <div class="section-title">Riwayat Keputusan</div>
        <div class="section-subtitle">Daftar pengajuan yang telah disetujui atau ditolak.</div>
      </div>
    </div>

    <div class="card-soft">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:90px">ID</th>
              <th style="min-width:160px">Peminjam</th>
              <th style="min-width:240px">Item</th>
              <th style="width:140px">Status</th>
              <th style="min-width:220px">Catatan</th>
              <th style="width:90px" class="text-center">Cetak</th>
            </tr>
          </thead>

          <tbody>
            @forelse($historyRequests as $hist)
              @php
                $isApproved = $hist->status === 'approved';
                $note = $hist->rejection_reason ?? '-';
              @endphp

              <tr wire:key="hist-{{ $hist->id }}">
                <td><strong>P{{ str_pad($hist->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ $hist->borrower_name }}</td>

                <td>
                  <ul class="items-list">
                    @foreach($hist->items as $item)
                      <li>{{ $item->name }} (x{{ $item->pivot->quantity ?? 1 }})</li>
                    @endforeach
                  </ul>
                </td>

                <td>
                  @if($isApproved)
                    <span class="badge-box badge-approved">Approved</span>
                  @else
                    <span class="badge-box badge-rejected">Rejected</span>
                  @endif
                </td>

                <td>{{ $note }}</td>

                <td class="text-center">
                  <button class="btn-icon" type="button" onclick="window.print()" title="Cetak">
                    <i class="bi bi-printer"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="p-5 text-muted text-center">
                  Belum ada riwayat keputusan.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

  {{-- MODAL PENOLAKAN --}}
  <div class="modal fade" id="rejectionModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form wire:submit.prevent="reject">
          <div class="modal-header">
            <h5 class="modal-title">Alasan Penolakan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p class="text-muted mb-3">Anda wajib memberikan alasan mengapa pengajuan ini ditolak.</p>

            <div class="mb-3">
              <label class="form-label">Catatan Alasan (Wajib diisi)</label>
              <textarea class="form-control" wire:model="rejectReason" rows="4" required></textarea>
              @error('rejectReason') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-box" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger btn-box">Kirim Penolakan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:init', () => {
    const el = document.getElementById('rejectionModal');
    if (!el) return;

    const rejectionModal = new bootstrap.Modal(el);

    Livewire.on('open-reject-modal', () => rejectionModal.show());
    Livewire.on('close-reject-modal', () => rejectionModal.hide());
  });
</script>
@endpush
