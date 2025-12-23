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
                       <label class="form-label small fw-bold">Proposal Kegiatan <span class="text-danger">*</span></label>
                       <input type="file" class="form-control" id="requirements" accept=".pdf" required wire:model="document_file" />
                       <div class="form-text">Wajib PDF (max 10MB).</div>
                       @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
                   </div>
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Identitas Peminjam (KTM/KTP/SIM) <span class="text-danger">*</span></label>
                       <input type="file" class="form-control" id="ktpUpload" accept="image/png, image/jpeg, image/jpg" required wire:model="ktp_file" />
                       <div class="form-text">Wajib format gambar (JPG, PNG).</div>
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
            <button class="btn btn-primary btn-book disabled" type="button" id="btnSubmit" disabled style="transition: all 0.3s;">
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
            <button type="button" class="btn btn-primary px-4 fw-bold" id="btnRealSubmit" style="border-radius:12px" wire:click="submit" wire:loading.attr="disabled" wire:target="submit">
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
            // Prevent default label behavior toggle if we want strict flow
            // But usually label click toggles checkbox. 
            // Since Checkbox is disabled, we handle it manually via modal
            e.preventDefault();
            openTerms();
        });
        
        // Checkbox wrapper click
        chk.parentElement.addEventListener('click', (e) => {
             // If clicking the div/gap, trigger modal if not checked
             if (!chk.checked) openTerms();
        });

        if(btnAgree) {
            btnAgree.addEventListener('click', () => {
                chk.disabled = false;
                chk.checked = true;
                // Trigger Livewire update manually if needed, or dispatch event
                chk.dispatchEvent(new Event('change'));
                // Trigger form validation check
                validateBookingForm();
                modal.hide();
            });
        }
    });

    // Form Validation Logic to toggle Submit Button
    function validateBookingForm() {
        const form = document.getElementById('bookingForm');
        const btn = document.getElementById('btnSubmit');
        const chk = document.getElementById('agreeTerms');
        
        let isValid = form.checkValidity();
        
        // Explicitly check terms (though required attribute might handle it, logic implies manual check)
        if (!chk.checked) isValid = false;

        if (isValid) {
            btn.disabled = false;
            btn.classList.remove('disabled', 'btn-secondary');
            btn.classList.add('btn-primary');
        } else {
            btn.disabled = true;
            btn.classList.add('disabled', 'btn-secondary');
            btn.classList.remove('btn-primary');
        }
    }

    // Initialize listeners
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('bookingForm');
        if(form) {
            form.addEventListener('input', validateBookingForm);
            form.addEventListener('change', validateBookingForm);
        }
        // Initial check
        validateBookingForm();
    });
</script>
@endpush
