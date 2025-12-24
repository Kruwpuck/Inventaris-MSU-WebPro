{{-- resources/views/livewire/pengelola/approval.blade.php --}}

@push('head')
  <style>
    body {
      background: #fff;
      font-family: "Poppins", sans-serif;
    }

    .page-wrap {
      padding-top: 110px;
      padding-bottom: 60px;
    }

    .section-title {
      font-size: 2.1rem;
      font-weight: 500;
      letter-spacing: .2px;
    }

    .section-subtitle {
      color: #6c757d;
      margin-top: .5rem;
    }

    .card-soft {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
      border: 1px solid rgba(0, 0, 0, .04);
    }

    .table thead th {
      background: #f8f9fa;
      color: #4b5563;
      font-weight: 700;
      font-size: .92rem;
      white-space: nowrap;
      border-bottom: 1px solid rgba(0, 0, 0, .08) !important;
      vertical-align: middle;
    }

    .table tbody td {
      vertical-align: middle;
    }

    /* BADGE & BUTTONS */
    .badge-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: .22rem .5rem;
      border-radius: 4px;
      font-weight: 700;
      font-size: .75rem;
      min-width: 78px;
      border: 1px solid rgba(0, 0, 0, .08);
    }

    .badge-pending {
      background: #f6d36a;
      color: #3b2f00;
    }

    .badge-approved {
      background: #2e7d32;
      color: #fff;
    }

    .badge-rejected {
      background: #c62828;
      color: #fff;
    }

    .btn-box {
      border-radius: 4px !important;
      padding: .28rem .65rem !important;
      font-size: .78rem;
      font-weight: 700;
      border-width: 1px !important;
    }

    .btn-approve {
      background: #2e7d32 !important;
      border-color: #2e7d32 !important;
      color: #fff !important;
      transition: all 0.2s ease;
    }

    .btn-approve:hover {
      background: #1b5e20 !important;
      border-color: #1b5e20 !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(46, 125, 50, 0.3);
    }

    .btn-approve:active {
      transform: translateY(0);
      box-shadow: 0 2px 3px rgba(46, 125, 50, 0.2);
    }

    .btn-reject {
      background: #c62828 !important;
      border-color: #c62828 !important;
      color: #fff !important;
      transition: all 0.2s ease;
    }

    .btn-reject:hover {
      background: #b71c1c !important;
      border-color: #b71c1c !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(198, 40, 40, 0.3);
    }

    .btn-reject:active {
      transform: translateY(0);
      box-shadow: 0 2px 3px rgba(198, 40, 40, 0.2);
    }

    .items-list {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }

    .btn-icon {
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      border: 1px solid rgba(0, 0, 0, .12);
      background: #fff;
      border: none;
      cursor: pointer;
    }

    .btn-icon:hover {
      background: #f8f9fa;
    }

    .table-responsive {
      border-radius: 16px;
      overflow-x: auto;
    }

    /* =========================================
               CSS KHUSUS PREVIEW (LAYAR)
               Agar tidak terlalu nge-zoom/besar di modal
             ========================================= */
    @media screen {
      #areaCetak {
        padding: 20px !important;
      }

      #areaCetak h2 {
        font-size: 16px !important;
      }

      #areaCetak h3 {
        font-size: 14px !important;
      }

      #areaCetak p,
      #areaCetak td,
      #areaCetak li,
      #areaCetak span,
      #areaCetak div {
        font-size: 12px !important;
      }

      #areaCetak .kop-logo {
        height: 50px !important;
        width: auto !important;
      }
    }

    /* =========================================
               CSS KHUSUS PRINT (LAYOUT SURAT RESMI A4)
               ========================================= */
    @media print {
      @page {
        size: A4;
        margin: 0;
      }

      body {
        margin: 0;
        padding: 0;
        background-color: white;
      }

      body * {
        visibility: hidden;
      }

      #modalCetak,
      #areaCetak,
      #areaCetak * {
        visibility: visible;
      }

      #modalCetak {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        background: white !important;
      }

      .modal-dialog {
        max-width: 100%;
        margin: 0;
      }

      .modal-content {
        border: none;
        box-shadow: none;
      }

      #areaCetak {
        width: 100%;
        padding: 2cm;
        font-family: "Times New Roman", Times, serif !important;
        color: black !important;
        font-size: 12pt;
        line-height: 1.5;
      }

      .modal-header,
      .modal-footer,
      .btn-close,
      .no-print {
        display: none !important;
      }

      table {
        page-break-inside: auto;
      }

      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }

      .surat-table,
      .surat-item-table {
        width: 100% !important;
        border-collapse: collapse !important;
      }

      .surat-item-table th,
      .surat-item-table td {
        border: 1px solid #000 !important;
      }

      .kop-logo {
        max-height: 80px;
        width: auto;
        -webkit-print-color-adjust: exact;
      }
    }

    @media (max-width: 576px) {
      .section-title {
        font-size: 1.6rem;
      }
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
    <section class="text-center mb-4">
      <h3 class="fw-bold text-uppercase" style="color:#32435a">Daftar Pengajuan (Pending)</h3>
      <div class="mx-auto" style="width:120px;height:3px;background:#2a6a55;border-radius:2px"></div>
    </section>

    <div class="card-soft">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
          <thead>
            <tr>
              <th style="width: 80px;">ID</th>
              <th style="width: 60px;" class="text-center">Profil</th>
              <th style="width: 180px;">Peminjam</th>
              <th style="min-width: 200px;">Barang/Fasilitas</th>
              <th style="min-width: 180px;">Kegiatan & Lokasi</th>
              <th style="width: 180px;">Jadwal Peminjaman</th>
              <th style="min-width: 200px;">Deskripsi</th>
              <th style="width: 100px;">Status</th>
              <th style="width: 160px;">Aksi</th>
              <th style="width: 100px;">Proposal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendingRequests as $req)
              @php
                $start = optional($req->loan_date_start)->format('d M Y') ?? '-';
                $end = optional($req->loan_date_end)->format('d M Y') ?? '-';
                $proposalLink = $req->proposal_path ? asset('storage/' . $req->proposal_path) : '#';

                $startTimeV = $req->start_time ? "({$req->start_time})" : "";
                $endTimeV = $req->end_time ? "({$req->end_time})" : "";
              @endphp

              <tr wire:key="pending-{{ $req->id }}">
                <td><strong>P{{ str_pad($req->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-light border btn-icon mx-auto shadow-sm" style="width:32px;height:32px;"
                    wire:click="showBorrowerDetails({{ $req->id }})" title="Lihat Profil Peminjam">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                  </button>
                </td>
                <td>
                  <div class="fw-bold text-truncate" style="max-width: 160px;" title="{{ $req->borrower_name }}">
                    {{ $req->borrower_name }}
                  </div>
                  <div class="small text-muted">{{ $req->department ?? 'Umum' }}</div>
                </td>
                <td>
                  <ul class="items-list small">
                    @foreach($req->items as $item)
                      <li>&bull; {{ $item->name }} <span class="text-muted">(x{{ $item->pivot->quantity ?? 1 }})</span></li>
                    @endforeach
                  </ul>
                </td>
                <td>
                  <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"
                    title="{{ $req->borrower_reason }}">
                    {{ $req->borrower_reason ?? '-' }}
                  </div>
                  <div class="small text-muted d-flex align-items-center mt-1">
                    <i class="bi bi-geo-alt me-1 text-danger"></i>
                    <span class="text-truncate"
                      style="max-width: 150px;">{{ $req->activity_location ?? 'Telkom University' }}</span>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column" style="font-size: 0.85rem;">
                    <span class="fw-semibold text-dark">{{ $start }} <span
                        class="text-muted small">{{ $startTimeV }}</span></span>
                    <span class="text-muted small my-0" style="line-height:1;">s/d</span>
                    <span class="fw-semibold text-dark">{{ $end }} <span
                        class="text-muted small">{{ $endTimeV }}</span></span>
                  </div>
                </td>
                <td style="max-width: 200px; white-space: normal;">
                  @php
                    $desc = $req->activity_description ?? '-';
                    $limit = 50;
                    $isLong = strlen($desc) > $limit;
                    $showDesc = \Illuminate\Support\Str::limit($desc, $limit, '...');
                  @endphp

                  @if($isLong)
                    <div x-data="{ expanded: false }">
                      <span x-show="!expanded">
                        {{ $showDesc }}
                        <a href="#" @click.prevent="expanded = true"
                          class="fw-bold text-primary text-decoration-none small d-block mt-1">Lihat Selengkapnya</a>
                      </span>
                      <span x-show="expanded">
                        {{ $desc }}
                        <a href="#" @click.prevent="expanded = false"
                          class="fw-bold text-secondary text-decoration-none small d-block mt-1">Tutup</a>
                      </span>
                    </div>
                  @else
                    {{ $desc }}
                  @endif
                </td>
                <td><span class="badge-box badge-pending">Pending</span></td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-approve btn-sm btn-box px-2" wire:click="prepareApprove({{ $req->id }})"
                      type="button">
                      <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-reject btn-sm btn-box px-2" wire:click="prepareReject({{ $req->id }})"
                      type="button">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div>
                </td>
                <td>
                  @if($req->proposal_path)
                    <a class="btn btn-outline-primary btn-sm btn-box px-2" href="{{ $proposalLink }}" target="_blank">
                      <i class="bi bi-file-earmark-text"></i> PDF
                    </a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="p-5 text-center text-muted">Tidak ada pengajuan pending.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        {{ $pendingRequests->links() }}
      </div>
    </div>
  </div>

  {{-- =========================
  HISTORY SECTION
  ========================= --}}
  <div class="mb-5">
    <section class="text-center mb-4">
      <h3 class="fw-bold text-uppercase" style="color:#32435a">Riwayat Keputusan</h3>
      <div class="mx-auto" style="width:120px;height:3px;background:#2a6a55;border-radius:2px"></div>
    </section>

    <div class="card-soft">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
          <thead>
            <tr>
              <th style="width: 70px;">ID</th>
              <th style="width: 50px;" class="text-center">Profil</th>
              <th style="width: 160px;">Peminjam</th>
              <th style="width: 240px;">Item</th>
              <th style="width: 170px;">Jadwal</th>
              <th style="width: 100px;">Status</th>
              <th style="width: 220px;">Catatan</th>
              <th style="width: 70px;" class="text-center">Cetak</th>
            </tr>
          </thead>
          <tbody>
            @forelse($historyRequests as $hist)
              <tr wire:key="hist-{{ $hist->id }}">
                <td><strong>P{{ str_pad($hist->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-light border btn-icon mx-auto shadow-sm" style="width:32px;height:32px;"
                    wire:click="showBorrowerDetails({{ $hist->id }})" title="Lihat Profil Peminjam">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                  </button>
                </td>
                <td>
                  <div class="fw-bold text-truncate" style="max-width: 160px;" title="{{ $hist->borrower_name }}">
                    {{ $hist->borrower_name }}
                  </div>
                </td>
                <td>
                  <ul class="items-list small">
                    @foreach($hist->items as $item)
                      <li>&bull; {{ $item->name }} <span class="text-muted">(x{{ $item->pivot->quantity ?? 1 }})</span></li>
                    @endforeach
                  </ul>
                </td>
                <td>
                  @php
                    $hStart = optional($hist->loan_date_start)->format('d M Y') ?? '-';
                    $hEnd = optional($hist->loan_date_end)->format('d M Y') ?? '-';

                    $hStartTimeV = $hist->start_time ? "({$hist->start_time})" : "";
                    $hEndTimeV = $hist->end_time ? "({$hist->end_time})" : "";
                  @endphp
                  <div class="d-flex flex-column" style="font-size: 0.85rem;">
                    <span class="fw-semibold text-dark">{{ $hStart }} <span
                        class="text-muted small">{{ $hStartTimeV }}</span></span>
                    <span class="text-muted small my-0" style="line-height:1;">s/d</span>
                    <span class="fw-semibold text-dark">{{ $hEnd }} <span
                        class="text-muted small">{{ $hEndTimeV }}</span></span>
                  </div>
                </td>
                <td>
                  @if($hist->status == 'approved')
                    <span class="badge-box badge-approved">Approved</span>
                  @else
                    <span class="badge-box badge-rejected">Rejected</span>
                  @endif
                </td>
                <td class="small text-muted"
                  style="max-width: 220px; white-space: normal; word-wrap: break-word; word-break: break-all;">
                  @php
                    $reasonCheck = $hist->rejection_reason ?? '-';
                    // Limit characters to handle long single words
                    $limit = 50;
                    $truncated = \Illuminate\Support\Str::limit($reasonCheck, $limit, '...');
                    $isLong = strlen($reasonCheck) > $limit;
                  @endphp

                  @if($isLong)
                    <div x-data="{ expanded: false }">
                      <span x-show="!expanded">
                        {{ $truncated }}
                        <div class="mt-1">
                          <a href="#" @click.prevent="expanded = true" class="fw-bold text-primary text-decoration-none"
                            style="font-size: 0.75rem;">Lihat</a>
                        </div>
                      </span>
                      <span x-show="expanded">
                        {{ $reasonCheck }}
                        <div class="mt-1">
                          <a href="#" @click.prevent="expanded = false" class="fw-bold text-secondary text-decoration-none"
                            style="font-size: 0.75rem;">Tutup</a>
                        </div>
                      </span>
                    </div>
                  @else
                    {{ $reasonCheck }}
                  @endif
                </td>
                <td class="text-center">
                  <button class="btn-icon shadow-sm" type="button" wire:click="showDetails({{ $hist->id }})"
                    title="Cetak Bukti">
                    <i class="bi bi-printer"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="p-5 text-center text-muted">Belum ada riwayat.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        {{ $historyRequests->links() }}
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
          <button type="button" class="btn btn-approve btn-box" wire:click="approveConfirmed"
            wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="approveConfirmed">
              <i class="bi bi-check-lg me-1"></i>Ya, Setuju
            </span>
            <span wire:loading wire:target="approveConfirmed">
              <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              Memproses...
            </span>
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
            <button type="submit" class="btn btn-danger btn-box" wire:loading.attr="disabled" wire:target="reject">
              <span wire:loading.remove wire:target="reject">Kirim</span>
              <span wire:loading wire:target="reject">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Mengirim...
              </span>
            </button>
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
                    <img src="{{ asset('aset/logo.png') }}" alt="Logo" class="kop-logo"
                      style="height: 80px; width: auto;">
                  </td>
                  <td style="text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 20px; font-weight: bold; text-transform: uppercase;">Masjid Syamsul
                      Ulum</h2>
                    <p style="margin: 2px 0; font-size: 14px;">Telkom University, Bandung, Jawa Barat</p>
                    <p style="margin: 0; font-size: 12px;">Email: msu@telkomuniversity.ac.id | Web:
                      msu.telkomuniversity.ac.id</p>
                  </td>
                  <td width="100"></td>
                </tr>
              </table>

              {{-- JUDUL SURAT --}}
              <div style="text-align: center; margin-bottom: 30px;">
                <h3 style="text-decoration: underline; margin-bottom: 5px; font-weight: bold; font-size: 16pt;">BUKTI
                  PERSETUJUAN PEMINJAMAN</h3>
                <span style="font-size: 12pt;">Nomor:
                  MSU/LOAN/{{ date('Y') }}/{{ str_pad($selectedRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
              </div>

              <p style="margin-bottom: 15px;">Dengan ini menerangkan bahwa permohonan peminjaman fasilitas/barang yang
                diajukan oleh:</p>

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

              <p>Detail barang atau fasilitas yang
                {{ $selectedRequest->status == 'approved' ? 'diizinkan untuk dipinjam' : 'diajukan' }} adalah sebagai
                berikut:
              </p>

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
                      <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $item->pivot->quantity }}
                        unit</td>
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
                      {{ \Carbon\Carbon::parse($selectedRequest->start_time)->addHours((int) $selectedRequest->duration)->format('H:i') }}
                      WIB
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
                    <p style="text-decoration: underline; font-weight: bold;">
                      {{ Auth::user()->name ?? 'Admin Pengelola' }}
                    </p>
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

  {{-- MODAL DETAIL PEMINJAM --}}
  <div class="modal fade" id="borrowerModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
        @if($selectedBorrower)
          <div class="modal-header border-0 bg-primary text-white">
            <h5 class="modal-title fw-bold">Detail Peminjam</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="text-center mb-4">
              @if($selectedBorrower->ktp_path)
                <div class="mb-3">
                  <img src="{{ asset('storage/' . $selectedBorrower->ktp_path) }}" alt="Foto KTP"
                    class="img-fluid rounded shadow-sm border" style="max-height: 200px; object-fit: cover;">
                  <div class="small text-muted mt-1 fst-italic">Foto Identitas (KTP)</div>
                </div>
              @else
                <div
                  class="bg-light rounded d-flex align-items-center justify-content-center mx-auto mb-3 text-muted border"
                  style="width:100%; height:150px;">
                  <i class="bi bi-person-badge fs-1"></i>
                  <span class="ms-2">Tidak ada foto KTP</span>
                </div>
              @endif
            </div>

            <div class="d-grid gap-3">
              <div class="bg-light p-3 rounded border">
                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">Nama Lengkap
                  (Penanggung Jawab)</label>
                <div class="fw-bold text-dark fs-5">{{ $selectedBorrower->borrower_name }}</div>
              </div>

              <div class="row g-2">
                <div class="col-6">
                  <div class="bg-light p-3 rounded border h-100">
                    <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">NIM /
                      NIP</label>
                    <div class="fw-semibold text-dark">{{ $selectedBorrower->nim_nip ?? '-' }}</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="bg-light p-3 rounded border h-100">
                    <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">Unit /
                      Departemen</label>
                    <div class="fw-semibold text-dark">{{ $selectedBorrower->department ?? '-' }}</div>
                  </div>
                </div>
              </div>

              <div class="bg-light p-3 rounded border">
                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">Kontak</label>
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-envelope me-2 text-secondary"></i>
                    <span>{{ $selectedBorrower->borrower_email }}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-whatsapp me-2 text-success"></i>
                    <span>{{ $selectedBorrower->borrower_phone }}</span>
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $selectedBorrower->borrower_phone) }}"
                      target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 ms-auto rounded-pill"
                      style="font-size:0.75rem;">Chat</a>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">Tutup</button>
          </div>
        @else
          <div class="modal-body text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Memuat...</p>
          </div>
        @endif
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

      // Borrower Modal
      const borrowerEl = document.getElementById('borrowerModal');
      const borrowerModal = borrowerEl ? new bootstrap.Modal(borrowerEl) : null;

      // Events
      Livewire.on('open-approve-modal', () => approveModal && approveModal.show());
      Livewire.on('close-approve-modal', () => approveModal && approveModal.hide());

      Livewire.on('open-reject-modal', () => rejectionModal && rejectionModal.show());
      Livewire.on('close-reject-modal', () => rejectionModal && rejectionModal.hide());

      Livewire.on('open-print-modal', () => printModal && printModal.show());

      Livewire.on('open-borrower-modal', () => borrowerModal && borrowerModal.show());
    });
  </script>
@endpush