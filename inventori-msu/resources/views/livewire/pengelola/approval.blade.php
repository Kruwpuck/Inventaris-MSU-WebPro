{{-- resources/views/livewire/pengelola/approval.blade.php --}}

@push('head')
<style>
  body { background:#fff; font-family:"Poppins",sans-serif; }

  .page-wrap{ padding-top: 110px; padding-bottom: 60px; }

  .section-title{ font-size: 2.1rem; font-weight: 500; letter-spacing: .2px; }
  .section-subtitle{ color:#6c757d; margin-top: .5rem; }

  .card-soft{
    background:#fff;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    border: 1px solid rgba(0,0,0,.04);
  }

  .table thead th{
    background:#f8f9fa; color:#4b5563; font-weight: 700;
    font-size: .92rem; white-space: nowrap;
    border-bottom: 1px solid rgba(0,0,0,.08) !important;
    vertical-align: middle;
  }
  .table tbody td{ vertical-align: middle; }

  /* BADGE & BUTTONS */
  .badge-box{ display:inline-flex; align-items:center; justify-content:center; padding: .22rem .5rem; border-radius: 4px; font-weight: 700; font-size: .75rem; min-width: 78px; border: 1px solid rgba(0,0,0,.08); }
  .badge-pending{ background:#f6d36a; color:#3b2f00; }
  .badge-approved{ background:#2e7d32; color:#fff; }
  .badge-rejected{ background:#c62828; color:#fff; }

  .btn-box{ border-radius: 4px !important; padding: .28rem .65rem !important; font-size: .78rem; font-weight: 700; border-width: 1px !important; }
  .btn-approve{ background:#2e7d32 !important; border-color:#2e7d32 !important; color:#fff !important; }
  .btn-reject{ background:#c62828 !important; border-color:#c62828 !important; color:#fff !important; }

  .items-list{ list-style: none; padding-left: 0; margin: 0; }
  .btn-icon{ width: 38px; height: 38px; display:inline-flex; align-items:center; justify-content:center; border-radius: 10px; border: 1px solid rgba(0,0,0,.12); background:#fff; border: none; cursor: pointer; }
  .btn-icon:hover{ background:#f8f9fa; }
  .table-responsive{ border-radius: 16px; overflow: hidden; }

  /* =========================================
     CSS KHUSUS PRINT (LAYOUT SURAT RESMI A4)
     ========================================= */
  @media print {
      @page {
          size: A4;
          margin: 0;
      }
      body {
          margin: 0; padding: 0; background-color: white;
      }
      body * {
          visibility: hidden;
      }
      
      #modalCetak, #areaCetak, #areaCetak * {
          visibility: visible;
      }

      #modalCetak {
          position: absolute; left: 0; top: 0;
          width: 100%; margin: 0; padding: 0;
          background: white !important;
      }
      .modal-dialog {
          max-width: 100%; margin: 0;
      }
      .modal-content {
          border: none; box-shadow: none;
      }

      #areaCetak {
          width: 100%;
          padding: 2cm;
          font-family: "Times New Roman", Times, serif !important;
          color: black !important;
          font-size: 12pt;
          line-height: 1.5;
      }

      .modal-header, .modal-footer, .btn-close, .no-print {
          display: none !important;
      }
      
      table { page-break-inside: auto; }
      tr { page-break-inside: avoid; page-break-after: auto; }
      .surat-table, .surat-item-table { width: 100% !important; border-collapse: collapse !important; }
      .surat-item-table th, .surat-item-table td { border: 1px solid #000 !important; }
      
      .kop-logo { max-height: 80px; width: auto; -webkit-print-color-adjust: exact; }
  }

  @media (max-width: 576px){ .section-title{ font-size: 1.6rem; } }
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
              <th>ID</th>
              <th>Peminjam</th>
              <th>Barang/Fasilitas</th>
              <th>Tgl Pinjam</th>
              <th>Status</th>
              <th>Aksi</th>
              <th>Proposal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendingRequests as $req)
              @php
                $start = optional($req->loan_date_start)->format('Y-m-d') ?? '-';
                $proposalLink = $req->proposal_path ? asset('storage/' . $req->proposal_path) : '#';
              @endphp

              <tr wire:key="pending-{{ $req->id }}">
                <td><strong>P{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ $req->borrower_name }}</td>
                <td>
                  <ul class="items-list">
                    @foreach($req->items as $item)
                      <li>- {{ $item->name }} (x{{ $item->pivot->quantity ?? 1 }})</li>
                    @endforeach
                  </ul>
                </td>
                <td>{{ $start }}</td>
                <td><span class="badge-box badge-pending">Pending</span></td>
                <td>
                  <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-approve btn-sm btn-box" wire:click="prepareApprove({{ $req->id }})" type="button">
                      <i class="bi bi-check-lg me-1"></i>Setuju
                    </button>
                    <button class="btn btn-reject btn-sm btn-box" wire:click="prepareReject({{ $req->id }})" type="button">
                      <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                  </div>
                </td>
                <td>
                  @if($req->proposal_path)
                    <a class="btn btn-outline-primary btn-sm btn-box" href="{{ $proposalLink }}" target="_blank">
                      <i class="bi bi-file-earmark-text me-1"></i> Review
                    </a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="p-5 text-center text-muted">Tidak ada pengajuan pending.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- =========================
       HISTORY SECTION
  ========================= --}}
  <div class="mb-5">
    <div class="section-title">Riwayat Keputusan</div>
    <div class="section-subtitle mb-3">Daftar pengajuan yang telah disetujui atau ditolak.</div>

    <div class="card-soft">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Peminjam</th>
              <th>Item</th>
              <th>Status</th>
              <th>Catatan</th>
              <th class="text-center">Cetak</th>
            </tr>
          </thead>
          <tbody>
            @forelse($historyRequests as $hist)
              <tr wire:key="hist-{{ $hist->id }}">
                <td><strong>P{{ str_pad($hist->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ $hist->borrower_name }}</td>
                <td>
                  <ul class="items-list">
                    @foreach($hist->items as $item)
                      <li>- {{ $item->name }} (x{{ $item->pivot->quantity ?? 1 }})</li>
                    @endforeach
                  </ul>
                </td>
                <td>
                  @if($hist->status == 'approved')
                    <span class="badge-box badge-approved">Approved</span>
                  @else
                    <span class="badge-box badge-rejected">Rejected</span>
                  @endif
                </td>
                <td>{{ $hist->rejection_reason ?? '-' }}</td>
                <td class="text-center">
                  <button class="btn-icon" type="button" wire:click="showDetails({{ $hist->id }})" title="Cetak Bukti">
                    <i class="bi bi-printer"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="p-5 text-center text-muted">Belum ada riwayat.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL APPROVE --}}
  <div class="modal fade" id="approveModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Persetujuan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0">Yakin ingin menyetujui pengajuan ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-box" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-approve btn-box" wire:click="approveConfirmed">
            <i class="bi bi-check-lg me-1"></i>Ya, Setuju
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL REJECT --}}
  <div class="modal fade" id="rejectionModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form wire:submit.prevent="reject">
          <div class="modal-header">
            <h5 class="modal-title">Alasan Penolakan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-3">Wajib memberikan alasan penolakan.</p>
            <textarea class="form-control" wire:model="rejectReason" rows="4" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-box" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger btn-box">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- =========================================
       MODAL CETAK (LAYOUT FIXED & SCROLLABLE)
       ========================================= --}}
  <div class="modal fade" id="modalCetak" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        
        <div class="modal-header d-print-none">
            <h5 class="modal-title">Pratinjau Cetak</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-0">
            {{-- AREA YANG AKAN DICETAK --}}
            <div id="areaCetak">
                @if($selectedRequest)
                    
                    {{-- KOP SURAT --}}
                    <table style="width: 100%; border-bottom: 3px double #000; margin-bottom: 25px; padding-bottom: 10px;">
                        <tr>
                            <td width="100" style="vertical-align: middle; text-align: left;">
                                <img src="{{ asset('aset/logo.png') }}" alt="Logo" class="kop-logo" style="height: 80px; width: auto;">
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <h2 style="margin: 0; font-size: 20px; font-weight: bold; text-transform: uppercase;">Masjid Syamsul Ulum</h2>
                                <p style="margin: 2px 0; font-size: 14px;">Telkom University, Bandung, Jawa Barat</p>
                                <p style="margin: 0; font-size: 12px;">Email: msu@telkomuniversity.ac.id | Web: msu.telkomuniversity.ac.id</p>
                            </td>
                            <td width="100"></td>
                        </tr>
                    </table>

                    {{-- JUDUL SURAT --}}
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h3 style="text-decoration: underline; margin-bottom: 5px; font-weight: bold; font-size: 16pt;">BUKTI PERSETUJUAN PEMINJAMAN</h3>
                        <span style="font-size: 12pt;">Nomor: MSU/LOAN/{{ date('Y') }}/{{ str_pad($selectedRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <p style="margin-bottom: 15px;">Dengan ini menerangkan bahwa permohonan peminjaman fasilitas/barang yang diajukan oleh:</p>

                    <table style="width: 100%; margin-bottom: 20px;">
                        <tr>
                            <td width="180"><strong>Nama Peminjam</strong></td>
                            <td width="10">:</td>
                            <td>{{ $selectedRequest->borrower_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pengajuan</strong></td>
                            <td>:</td>
                            <td>{{ $selectedRequest->created_at->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>:</td>
                            <td>
                                <span style="border: 2px solid #000; padding: 2px 8px; font-weight: bold; font-size: 11pt;">
                                    {{ $selectedRequest->status == 'approved' ? 'DISETUJUI' : 'DITOLAK' }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <p>Detail barang atau fasilitas yang {{ $selectedRequest->status == 'approved' ? 'diizinkan untuk dipinjam' : 'diajukan' }} adalah sebagai berikut:</p>

                    {{-- TABEL BARANG --}}
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #000;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="border: 1px solid #000; padding: 8px; text-align: center;" width="50">No</th>
                                <th style="border: 1px solid #000; padding: 8px;">Nama Barang / Fasilitas</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: center;" width="120">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedRequest->items as $index => $item)
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                                <td style="border: 1px solid #000; padding: 8px;">{{ $item->name }}</td>
                                <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $item->pivot->quantity }} unit</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="margin-top: 25px;">
                        <p><strong>Catatan Tambahan:</strong></p>
                        <ul style="margin-top: 5px;">
                            {{-- TANGGAL PINJAM --}}
                            <li>Tanggal Peminjaman: {{ optional($selectedRequest->loan_date_start)->format('d-m-Y') }}</li>
                            
                            {{-- TANGGAL KEMBALI --}}
                            <li>Tanggal Pengembalian: {{ optional($selectedRequest->loan_date_end)->format('d-m-Y') ?? '-' }}</li>

                            {{-- JAM MULAI & DURASI & JAM PENGEMBALIAN --}}
                            <li>Jam Mulai: {{ $selectedRequest->start_time ?? '-' }} WIB</li>
                            <li>Durasi Pinjam: {{ $selectedRequest->duration ?? 0 }} Jam</li>
                            <li>
                                Jam Pengembalian: 
                                @if($selectedRequest->start_time && $selectedRequest->duration)
                                    {{ \Carbon\Carbon::parse($selectedRequest->start_time)->addHours((int)$selectedRequest->duration)->format('H:i') }} WIB
                                @else
                                    -
                                @endif
                            </li>

                            {{-- ALASAN TOLAK (JIKA ADA) --}}
                            @if($selectedRequest->status == 'rejected')
                                <li>Alasan Penolakan: {{ $selectedRequest->rejection_reason }}</li>
                            @endif
                        </ul>
                    </div>

                    <p style="margin-top: 20px;">Demikian bukti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

                    {{-- TANDA TANGAN --}}
                    <table style="width: 100%; margin-top: 60px;">
                        <tr>
                            <td align="center" width="50%">
                                <p>Peminjam,</p>
                                <br><br><br><br>
                                <p style="text-decoration: underline; font-weight: bold;">{{ $selectedRequest->borrower_name }}</p>
                            </td>
                            <td align="center" width="50%">
                                <p>Bandung, {{ date('d F Y') }}</p>
                                <p>Pengelola MSU,</p>
                                <br><br><br><br>
                                <p style="text-decoration: underline; font-weight: bold;">{{ Auth::user()->name ?? 'Admin Pengelola' }}</p>
                            </td>
                        </tr>
                    </table>

                @else
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="modal-footer bg-light d-print-none">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button onclick="window.print()" class="btn btn-primary" {{ !$selectedRequest ? 'disabled' : '' }}>
            <i class="bi bi-printer me-1"></i> Cetak Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  document.addEventListener('livewire:init', () => {
    // Approve Modal
    const approveEl = document.getElementById('approveModal');
    const approveModal = approveEl ? new bootstrap.Modal(approveEl) : null;

    // Reject Modal
    const rejectEl = document.getElementById('rejectionModal');
    const rejectionModal = rejectEl ? new bootstrap.Modal(rejectEl) : null;

    // Print Modal
    const printEl = document.getElementById('modalCetak');
    const printModal = printEl ? new bootstrap.Modal(printEl) : null;

    // Events
    Livewire.on('open-approve-modal', () => approveModal && approveModal.show());
    Livewire.on('close-approve-modal', () => approveModal && approveModal.hide());

    Livewire.on('open-reject-modal', () => rejectionModal && rejectionModal.show());
    Livewire.on('close-reject-modal', () => rejectionModal && rejectionModal.hide());

    Livewire.on('open-print-modal', () => printModal && printModal.show());
  });
</script>
@endpush