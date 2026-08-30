@push('styles')
<link rel="stylesheet" href="{{ asset('fe-guest/booking-barang.css') }}" />
<style>
  .navbar-masjid {
    height: 66.21px !important;
    padding: 0 !important;
  }
  .navbar-masjid .navbar-brand {
    padding: 0 !important;
    height: 56px !important;
    display: flex;
    align-items: center;
  }
  /* Google Form style asterisk */
  .text-danger {
    color: #d93025 !important; 
  }
  @keyframes fieldHighlightPulse {
    0% {
      box-shadow: 0 0 0 0.3rem rgba(220, 53, 69, 0.7);
      border-color: #dc3545 !important;
    }
    50% {
      box-shadow: 0 0 0 0.55rem rgba(220, 53, 69, 0.4);
      border-color: #dc3545 !important;
    }
    100% {
      box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.2);
      border-color: #dc3545 !important;
    }
  }
  .field-highlight-pulse {
    animation: fieldHighlightPulse 1.2s ease-in-out 2 !important;
    border-color: #dc3545 !important;
  }
  .form-check.field-highlight-pulse {
    background-color: #ffebe9 !important;
    border-color: #dc3545 !important;
  }
  .missing-field-item:hover {
    background-color: #f8d7da !important;
    transform: translateY(-1px);
  }
</style>
@endpush

<div>
  <main class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="page-title m-0">Keterangan Peminjaman Inventory MSU</h1>
    </div>

    <div class="row g-4">
      <!-- LEFT: Panel Barang (tabs horizontal) -->
      <aside class="col-lg-5" wire:ignore>
        <div class="summary-card">
          <!-- Tabs -->
          <ul class="nav nav-tabs msu-item-tabs px-3 pt-3" id="itemTabs" role="tablist">
            <!-- Diisi via JS -->
          </ul>

          <!-- Panel isi per barang -->
          <div class="tab-content p-3" id="itemTabContent">
            <!-- Diisi via JS -->
          </div>
        </div>
      </aside>

      <!-- RIGHT: Ringkasan Keranjang + Form -->
      <section class="col-lg-7">
        <!-- Ringkasan Keranjang -->
        <h5 class="mb-2 d-flex justify-content-between align-items-center">
          <span><i class="bi bi-bag-check me-1"></i> Ringkasan Keranjang</span>
          <button id="clearCartBtn" class="btn btn-sm btn-outline-danger" wire:ignore>
            <i class="bi bi-trash"></i> Hapus Semua
          </button>
        </h5>
        <div id="cartList" class="mb-3" wire:ignore></div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @foreach($errors->all() as $error)
            <div class="alert alert-danger py-1">{{ $error }}</div>
        @endforeach

        <!-- Form Booking -->
        <form id="bookingForm" class="form-card needs-validation" wire:submit.prevent="submit" novalidate>
          <div class="row g-3">
            <!-- NOMOR PEMINJAMAN -->
            <!-- NOMOR PEMINJAMAN -->
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control" id="loanNumber" placeholder="Contoh: 08123456789" required wire:model="borrower_phone" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_phone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Penanggung jawab <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="pjName" placeholder="Nama lengkap" required wire:model="borrower_name" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">NIM/NIP <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control" id="idNumber" placeholder="Contoh: 21573xxxxx" required wire:model="borrower_nim" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_nim') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" placeholder="nama@contoh.ac.id" required wire:model="borrower_email" />
                <div class="invalid-feedback">Masukkan email yang valid.</div>
              </div>
              @error('borrower_email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <input type="text" class="form-control" id="studyProgram" placeholder="Sistem Informasi / Informatika"
                  required wire:model="borrower_prodi" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_prodi') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Kategori Peminjam <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-people"></i></span>
                <select class="form-select" id="borrowerCategory" required wire:model="borrower_category">
                  <option value="">Pilih Kategori</option>
                  <option value="Wajihah">Wajihah</option>
                  <option value="Civitas Akademika">Civitas Akademika</option>
                  <option value="Umum">Umum</option>
                </select>
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_category') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
              <label class="form-label">Kegiatan <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control" id="purpose" placeholder="Contoh: Kuliah Tamu / Seminar..."
                  required wire:model="borrower_reason" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_reason') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">Lokasi Kegiatan <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="location" placeholder="Masukkan lokasi kegiatan" required wire:model="location" />
              </div>
              <div class="form-text text-muted">Hanya tersedia untuk kegiatan di lingkungan Telkom University.</div>
              @error('location') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- Start Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Pakai <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDate" required wire:model="loan_date_start" />
              </div>
              @error('loan_date_start') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Pakai <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="loanTimeStart" required wire:model="loan_time_start" />
              </div>
              @error('loan_time_start') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- End Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDateEnd" required wire:model="loan_date_end" />
              </div>
              @error('loan_date_end') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Kembali <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="loanTimeEnd" required wire:model="loan_time_end" />
              </div>
              @error('loan_time_end') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- Uploads -->
            <div class="col-12 mt-4">
               <h6 class="fw-bold"><i class="bi bi-cloud-upload me-2"></i>Upload Dokumen</h6>
               <div class="row g-3">
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Proposal Kegiatan</label>
                       <input type="file" class="form-control" id="requirements" accept="application/pdf,.pdf" wire:model="document_file" />
                       <div class="form-text">Format PDF (max 10MB). Opsional.</div>
                       <div wire:loading wire:target="document_file" class="text-primary small mt-1">
                           <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Mengunggah proposal...
                       </div>
                       @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
                   </div>
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Identitas Peminjam (KTM/KTP/SIM) <span class="text-danger">*</span></label>
                       {{-- accept="image/*" disengaja: daftar mime sempit membuat HEIC/WEBP
                            tersaring diam-diam di file picker tanpa pesan apa pun. Format yang
                            benar-benar diterima ditegakkan di server (mimes:jpeg,jpg,png,webp)
                            supaya penolakannya disertai penjelasan. --}}
                       {{-- Tanpa atribut `required`: Livewire mengosongkan input native
                            setelah upload selesai, sehingga form.checkValidity() menganggap
                            kolom ini kosong justru ketika filenya sudah aman di server —
                            mengunci tombol Kirim. Kewajibannya ditegakkan aturan server
                            'ktp_file' => 'required|...'. --}}
                       <input type="file" class="form-control" id="ktpUpload" accept="image/*" wire:model="ktp_file" />
                       <div class="form-text">Wajib format gambar: JPG, PNG, atau WEBP (max 10MB).</div>
                       <div wire:loading wire:target="ktp_file" class="text-primary small mt-1">
                           <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Mengunggah identitas...
                       </div>
                       @error('ktp_file') <div class="text-danger small">{{ $message }}</div> @enderror
                   </div>
               </div>
            </div>

            <div class="col-12 mt-3">
              <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
              <textarea id="longPurpose" class="form-control" rows="4" placeholder="Tuliskan detail deskripsi kegiatan..."
                required wire:model="borrower_description"></textarea>
              <div class="invalid-feedback">Wajib diisi.</div>
              @error('borrower_description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- Syarat & Ketentuan (Highlighted) -->
            <div class="col-12 mt-4">
                <div class="form-check p-3 border border-2 border-warning rounded-3 bg-light-warning" style="background-color: #fff8e1;">
                    <!-- Checkbox ini akan memicu modal jika user 'klik' (via JS) -->
                    <input class="form-check-input mt-1" type="checkbox" id="agreeTerms" wire:model="agree_terms" disabled
                           style="transform: scale(1.3); margin-right: 12px; border-color: #ffc107; margin-left:1px !important; float: left;">
                    <label class="form-check-label small fw-bold text-dark d-block" for="agreeTerms" style="margin-left: 2rem; padding-top: 2px;">
                        Saya menyetujui <a href="#" id="linkTerms" class="text-decoration-underline text-primary fw-extrabold" style="font-size: 1.05em;">Syarat & Ketentuan</a> serta memberikan izin kepada MSU untuk mengelola data saya untuk keperluan pelacakan inventaris.
                    </label>
                </div>
                @error('agree_terms') <div class="text-danger small mt-1 fw-bold">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('guest.catalogue.barang') }}">
              <i class="bi bi-arrow-left-circle me-1"></i>Tambah Barang
            </a>
            <button class="btn btn-outline-danger" type="button" id="btnCancel">
              <i class="bi bi-x-circle me-1"></i>Batalkan
            </button>
            <button class="btn btn-secondary btn-book fw-bold text-white shadow-sm" type="button" id="btnSubmit" style="transition: all 0.25s ease-in-out; cursor: pointer; filter: none !important; opacity: 1 !important;">
              <span wire:loading.remove wire:target="document_file, ktp_file, submit"><i class="bi bi-check2-circle me-1"></i>Kirim Booking</span>
              <span wire:loading wire:target="document_file"><span class="spinner-border spinner-border-sm me-1"></span>Upload Prp...</span>
              <span wire:loading wire:target="ktp_file"><span class="spinner-border spinner-border-sm me-1"></span>Upload KTP...</span>
              <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
            </button>
          </div>
        </form>

        <!-- QRIS Donasi -->
        <div class="form-card mt-3 text-center" id="qrisDonation" wire:ignore>
          <div class="d-flex justify-content-center align-items-center mb-3">
              <i class="bi bi-qr-code fs-4 me-2"></i>
              <h5 class="m-0 fw-normal">Donasi QRIS (Opsional)</h5>
          </div>
          <div class="d-flex justify-content-center flex-column align-items-center">
              <img src="{{ asset('fe-guest/qris msu.jpg') }}" alt="QRIS" class="qris-img shadow-sm rounded" style="max-width: 250px;">
              <div class="form-text mt-3 fw-bold text-muted" style="font-size: 0.95rem;">Scan QRIS di atas untuk berdonasi seikhlasnya. Terima kasih 🙏</div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Modal Konfirmasi Hapus (Generic) -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true" style="z-index: 2050;" wire:ignore>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-body p-4 text-center">
          <div class="mb-3 text-warning">
            <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
          </div>
          <h5 class="mb-2 fw-bold" id="confirmDelTitle">Konfirmasi Hapus</h5>
          <p class="text-muted mb-4" id="confirmDelMsg">Apakah anda yakin?</p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal"
              style="border-radius:12px">Tidak</button>
            <button type="button" class="btn btn-danger px-4 fw-bold" id="btnConfirmDelAction"
              style="border-radius:12px">Ya, Hapus</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Konfirmasi Submit -->
  <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true" style="z-index: 2055;" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-body p-4 text-center">
          <div class="mb-3 text-primary">
            <i class="bi bi-clipboard-check" style="font-size: 3rem;"></i>
          </div>
          <h5 class="mb-2 fw-bold">Konfirmasi Data</h5>
          <p class="text-muted mb-4">Pastikan data yang Anda input sudah benar. Lanjutkan pengiriman?</p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal"
              style="border-radius:12px" wire:loading.attr="disabled" wire:target="submit">Cek Lagi</button>
            <button type="button" class="btn btn-primary px-4 fw-bold" id="btnRealSubmit" data-bs-dismiss="modal" style="border-radius:12px" wire:click="submit" wire:loading.attr="disabled" wire:target="submit">
              <span wire:loading.remove wire:target="submit">Ya, Kirim</span>
              <span wire:loading wire:target="submit">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Memproses...
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Notifikasi Data Belum Lengkap -->
  <div class="modal fade" id="missingFieldsModal" tabindex="-1" aria-hidden="true" style="z-index: 2060;" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header bg-danger text-white border-0 py-3 rounded-top-4">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2 m-0">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i> Data Belum Lengkap
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-secondary mb-3 fw-semibold" style="font-size: 0.95rem;">
            Mohon melengkapi data berikut sebelum mengirim booking:
          </p>
          <div id="missingFieldsList" class="d-flex flex-column gap-2 mb-3" style="max-height: 260px; overflow-y: auto;">
            <!-- Diisi via JS -->
          </div>
          <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 m-0 rounded-3 border-0 bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
            <span>Klik salah satu item di atas untuk langsung menuju ke lokasi data yang belum diisi.</span>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-danger w-100 rounded-3 py-2 fw-bold" id="btnFocusFirstMissing">
            <i class="bi bi-geo-alt-fill me-1"></i> Lengkapi Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SYARAT & KETENTUAN -->
  <div class="modal fade" id="termsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
      <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
          <div class="modal-content border-0 shadow-lg rounded-4">
              <div class="modal-header bg-light border-0">
                  <h5 class="modal-title fw-bold"><i class="bi bi-file-text me-2"></i>Syarat & Ketentuan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                  <p class="mb-3">Selamat datang di sistem Inventaris Masjid Syamsul Ulum (MSU). Sebelum menggunakan layanan kami, mohon baca dokumen ini dengan saksama. Dengan melakukan peminjaman, Anda dianggap telah menyetujui poin-poin di bawah ini.</p>
  
                  <h6 class="fw-bold text-dark mt-4">1. Pengumpulan Data Pribadi</h6>
                  <p>Pihak MSU mengumpulkan data pribadi Anda untuk keperluan administrasi peminjaman barang, yang meliputi namun tidak terbatas pada:</p>
                  <ul>
                      <li><strong>Identitas Diri:</strong> Nama Lengkap dan NIM (Nomor Induk Mahasiswa).</li>
                      <li><strong>Kontak:</strong> Nomor WhatsApp/Telepon dan Email.</li>
                      <li><strong>Data Peminjaman:</strong> Jenis barang, durasi peminjaman, dan tujuan penggunaan.</li>
                  </ul>
  
                  <h6 class="fw-bold text-dark mt-4">2. Penggunaan Data (Purpose of Use)</h6>
                  <p>Data yang Anda berikan akan digunakan oleh pengurus MSU secara bertanggung jawab untuk:</p>
                  <ul>
                      <li><strong>Pelacakan (Tracking):</strong> Memantau keberadaan aset masjid yang sedang dipinjam.</li>
                      <li><strong>Komunikasi:</strong> Menghubungi peminjam jika terjadi keterlambatan pengembalian atau masalah pada barang.</li>
                      <li><strong>Audit Internal:</strong> Sebagai laporan berkala mengenai utilitas barang inventaris MSU.</li>
                      <li><strong>Verifikasi:</strong> Memastikan peminjam adalah civitas akademika Telkom University yang sah.</li>
                  </ul>
  
                  <h6 class="fw-bold text-dark mt-4">3. Keamanan dan Penyimpanan Data</h6>
                  <p>Data Anda disimpan secara digital dalam database sistem Inventaris MSU. Pihak MSU berkomitmen untuk menjaga kerahasiaan data tersebut dan tidak akan memberikan, menjual, atau menyebarluaskan data Anda kepada pihak ketiga di luar kepentingan internal MSU dan Telkom University.</p>
  
                  <h6 class="fw-bold text-dark mt-4">4. Tanggung Jawab Peminjam</h6>
                  <p>Dengan menyetujui ketentuan ini, Anda menyatakan bahwa:</p>
                  <ul>
                      <li>Data yang diberikan adalah benar dan akurat.</li>
                      <li>Bersedia dihubungi melalui media komunikasi yang didaftarkan terkait urusan peminjaman.</li>
                      <li>Bertanggung jawab penuh atas kondisi barang yang dipinjam hingga kembali ke pihak MSU.</li>
                  </ul>
  
                  <h6 class="fw-bold text-dark mt-4">5. Persetujuan (Consent)</h6>
                  <p>Dengan melanjutkan proses peminjaman pada sistem ini, Anda memberikan persetujuan eksplisit kepada pihak pengurus MSU untuk menyimpan dan mengolah data pribadi Anda sesuai dengan tujuan yang telah disebutkan di atas.</p>
              </div>
              <div class="modal-footer border-0 pt-0">
                  <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-primary rounded-pill px-4" id="btnAgreeTerms">
                      <i class="bi bi-check2-circle me-2"></i>Saya Setuju
                  </button>
              </div>
          </div>
      </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('fe-guest/booking-barang.js') }}?v={{ time() }}"></script>
<script>
    // Logic Modal Syarat & Ketentuan
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('termsModal');
        const modal = new bootstrap.Modal(modalEl);
        const link = document.getElementById('linkTerms');
        const chk = document.getElementById('agreeTerms');
        const btnAgree = document.getElementById('btnAgreeTerms');
        const labelText = document.querySelector('label[for="agreeTerms"]');

        function openTerms() {
            modal.show();
        }

        if(link) link.addEventListener('click', (e) => { e.preventDefault(); openTerms(); });
        if(labelText) labelText.addEventListener('click', (e) => {
            if (chk.checked) {
                // If already checked, let it uncheck (default behavior)
                // But we still need to manage the disabled state/visuals if we want strict control
                // For now, let the default behavior happen (it will toggle the checkbox)
                // Do NOT preventDefault here if we want it to uncheck.
            } else {
                // If unchecked, prevent default check and show modal
                e.preventDefault();
                openTerms();
            }
        });
        
        // Checkbox wrapper click
        chk.parentElement.addEventListener('click', (e) => {
             // Don't trigger if clicked directly on checkbox or link
             if (e.target === chk || e.target === link) return;
             
             // If unchecked, open modal. If checked, do nothing (let label/input handle it)
             if (!chk.checked) openTerms();
        });

        if(btnAgree) {
            btnAgree.addEventListener('click', () => {
                chk.disabled = false;
                chk.checked = true;
                chk.dispatchEvent(new Event('change'));
                chk.dispatchEvent(new Event('input'));
                @this.set('agree_terms', true);
                // Trigger form validation check
                validateBookingForm();
                modal.hide();
            });
        }
    });

    function getMissingBookingFields() {
        const missing = [];

        // 1. Cart check
        const cartCount = (window.MSUCart?.count() || 0);
        const cartListEl = document.getElementById('cartList');
        const hasCartItems = cartCount > 0 || (cartListEl && cartListEl.querySelectorAll('.cart-item-row, .cart-item, li, div.d-flex').length > 0);

        if (!hasCartItems) {
            missing.push({
                id: 'cartList',
                name: 'Keranjang Peminjaman (Belum ada barang/ruangan yang dipilih)',
                icon: 'bi-bag-x'
            });
        }

        // 2. Form fields check
        const fields = [
            { id: 'loanNumber', name: 'Nomor Telepon', icon: 'bi-telephone' },
            { id: 'pjName', name: 'Penanggung Jawab', icon: 'bi-person-badge' },
            { id: 'idNumber', name: 'NIM / NIP', icon: 'bi-hash' },
            { id: 'email', name: 'Email', icon: 'bi-envelope' },
            { id: 'studyProgram', name: 'Program Studi / Unit', icon: 'bi-mortarboard' },
            { id: 'borrowerCategory', name: 'Kategori Peminjam', icon: 'bi-people' },
            { id: 'purpose', name: 'Kegiatan', icon: 'bi-clipboard-check' },
            { id: 'location', name: 'Lokasi Kegiatan', icon: 'bi-geo-alt' },
            { id: 'loanDate', name: 'Tanggal Pakai', icon: 'bi-calendar-event' },
            { id: 'loanTimeStart', name: 'Jam Pakai', icon: 'bi-clock' },
            { id: 'loanDateEnd', name: 'Tanggal Kembali', icon: 'bi-calendar-event' },
            { id: 'loanTimeEnd', name: 'Jam Kembali', icon: 'bi-clock' },
            { id: 'ktpUpload', name: 'Identitas Peminjam (KTM/KTP/SIM)', icon: 'bi-card-image' },
            { id: 'longPurpose', name: 'Deskripsi Kegiatan', icon: 'bi-file-text' }
        ];

        fields.forEach(f => {
            const el = document.getElementById(f.id);
            if (!el) return;

            let isMissing = false;
            if (el.type === 'file') {
                const hasNativeFile = el.files && el.files.length > 0;
                let hasLivewireFile = false;
                try {
                    if (typeof @this !== 'undefined') {
                        const lwVal = @this.ktp_file;
                        if (lwVal && lwVal !== '' && lwVal !== null) {
                            hasLivewireFile = true;
                        }
                    }
                } catch (e) {}

                if (!hasNativeFile && !hasLivewireFile) {
                    isMissing = true;
                }
            } else if (el.tagName === 'SELECT') {
                if (!el.value || el.value.trim() === '') isMissing = true;
            } else {
                if (!el.value || el.value.trim() === '') {
                    isMissing = true;
                } else if (el.type === 'email' && !el.checkValidity()) {
                    isMissing = true;
                }
            }

            if (isMissing) {
                missing.push({
                    id: f.id,
                    name: f.name,
                    icon: f.icon
                });
            }
        });

        // 3. T&C Checkbox
        const chk = document.getElementById('agreeTerms');
        let isAgreed = false;
        if (chk && chk.checked) {
            isAgreed = true;
        } else {
            try {
                if (typeof @this !== 'undefined' && @this.agree_terms) {
                    isAgreed = true;
                }
            } catch (e) {}
        }

        if (!isAgreed) {
            missing.push({
                id: 'agreeTerms',
                name: 'Persetujuan Syarat & Ketentuan',
                icon: 'bi-check-square'
            });
        }

        return missing;
    }

    function scrollToAndHighlightField(targetId) {
        let el = document.getElementById(targetId);
        if (!el) return;

        if (targetId === 'agreeTerms') {
            const container = el.closest('.form-check');
            if (container) el = container;
        }

        el.scrollIntoView({ behavior: 'smooth', block: 'center' });

        const focusable = document.getElementById(targetId);
        if (focusable && typeof focusable.focus === 'function' && !focusable.disabled) {
            setTimeout(() => focusable.focus(), 450);
        } else if (targetId === 'agreeTerms') {
            const link = document.getElementById('linkTerms');
            if (link) setTimeout(() => link.focus(), 450);
        }

        el.classList.remove('field-highlight-pulse');
        void el.offsetWidth;
        el.classList.add('field-highlight-pulse');
        setTimeout(() => {
            el.classList.remove('field-highlight-pulse');
        }, 2600);
    }

    function showMissingFieldsAlert(missing) {
        const form = document.getElementById('bookingForm');
        if (form) form.classList.add('was-validated');

        missing.forEach(m => {
            const el = document.getElementById(m.id);
            if (el) el.classList.add('is-invalid');
        });

        const listContainer = document.getElementById('missingFieldsList');
        if (listContainer) {
            listContainer.innerHTML = missing.map(item => `
                <button type="button" 
                        class="btn btn-outline-danger text-start d-flex align-items-center justify-content-between p-2.5 rounded-3 missing-field-item" 
                        data-target-id="${item.id}"
                        style="transition: all 0.2s;">
                    <span class="d-flex align-items-center gap-2 fw-medium text-dark small">
                        <i class="bi ${item.icon} text-danger fs-5"></i>
                        <span>${item.name}</span>
                    </span>
                    <span class="badge text-bg-danger rounded-pill px-2 py-1 small">
                        Ke Lokasi <i class="bi bi-arrow-right ms-1"></i>
                    </span>
                </button>
            `).join('');

            listContainer.querySelectorAll('.missing-field-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target-id');
                    const modalEl = document.getElementById('missingFieldsModal');
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();

                    setTimeout(() => {
                        scrollToAndHighlightField(targetId);
                    }, 300);
                });
            });
        }

        const btnFocusFirst = document.getElementById('btnFocusFirstMissing');
        if (btnFocusFirst) {
            btnFocusFirst.onclick = () => {
                const modalEl = document.getElementById('missingFieldsModal');
                const modalInst = bootstrap.Modal.getInstance(modalEl);
                if (modalInst) modalInst.hide();

                setTimeout(() => {
                    scrollToAndHighlightField(missing[0].id);
                }, 300);
            };
        }

        const missingModalEl = document.getElementById('missingFieldsModal');
        if (missingModalEl) {
            const modalInst = bootstrap.Modal.getOrCreateInstance(missingModalEl);
            modalInst.show();
        }
    }

    function validateBookingForm() {
        const missing = getMissingBookingFields();
        const btn = document.getElementById('btnSubmit');
        if (!btn) return;

        btn.disabled = false; // Always clickable so clicking pops up missing fields alert

        // Remove any blur or opacity restriction to keep text 100% crisp and clear
        btn.style.filter = 'none';
        btn.style.opacity = '1';
        btn.classList.remove('opacity-75', 'disabled');

        if (missing.length === 0) {
            // Data complete -> Crisp Vibrant Blue
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
            btn.style.backgroundColor = '#0d6efd';
            btn.style.borderColor = '#0d6efd';
            btn.style.color = '#ffffff';
        } else {
            // Data incomplete -> Solid Crisp Grey (clearly distinct from blue, non-blurry)
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
            btn.style.backgroundColor = '#6c757d';
            btn.style.borderColor = '#6c757d';
            btn.style.color = '#ffffff';
        }
    }

    // Initialize listeners & observers
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('bookingForm');
        const btnSubmit = document.getElementById('btnSubmit');

        if (form) {
            ['input', 'change', 'keyup', 'paste', 'blur'].forEach(evt => {
                form.addEventListener(evt, validateBookingForm);
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                btnSubmit?.click();
            });

            // Observe DOM updates on form (e.g., Livewire re-renders)
            const observer = new MutationObserver(() => {
                validateBookingForm();
            });
            observer.observe(form, { childList: true, subtree: true, attributes: true });
        }

        if (btnSubmit) {
            btnSubmit.addEventListener('click', (e) => {
                e.preventDefault();
                const missing = getMissingBookingFields();
                if (missing.length > 0) {
                    showMissingFieldsAlert(missing);
                } else {
                    const confirmModalEl = document.getElementById('confirmSubmitModal');
                    if (confirmModalEl) {
                        const confirmModal = bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                        confirmModal.show();
                    }
                }
            });
        }

        // Livewire hook updates
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => queueMicrotask(validateBookingForm));
            });
            Livewire.hook('morph.updated', () => queueMicrotask(validateBookingForm));
        }

        // Lightweight periodic check to ensure button state stays accurate
        setInterval(validateBookingForm, 400);

        validateBookingForm();
    });
</script>
@endpush
