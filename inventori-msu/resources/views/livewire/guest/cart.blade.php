@push('styles')
<link rel="stylesheet" href="{{ asset('fe-guest/booking-barang.css') }}" />
@endpush

<div>
  <main class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="page-title drop-in m-0">Keterangan Peminjaman Inventory MSU</h1>
    </div>

    <div class="row g-4">
      <!-- LEFT: Panel Barang (tabs horizontal) -->
      <aside class="col-lg-5">
        <div class="summary-card reveal-up">
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
          <button id="clearCartBtn" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-trash"></i> Hapus Semua
          </button>
        </h5>
        <div id="cartList" class="mb-3"></div>

        <!-- Form Booking -->
        <form id="bookingForm" class="form-card reveal-up needs-validation" novalidate>
          <div class="row g-3">
            <!-- NOMOR PEMINJAMAN -->
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control" id="loanNumber" placeholder="Contoh: 08123456789" required />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Penanggung jawab</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="pjName" placeholder="Nama lengkap" required />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">NIM/NIP</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control" id="idNumber" placeholder="Contoh: 21573xxxxx" required />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" placeholder="nama@contoh.ac.id" required />
                <div class="invalid-feedback">Masukkan email yang valid.</div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <input type="text" class="form-control" id="studyProgram" placeholder="Sistem Informasi / Informatika"
                  required />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
            </div>
            <div class="col-md-12">
              <label class="form-label">Kegiatan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control" id="purpose" placeholder="Contoh: Kuliah Tamu / Seminar..."
                  required />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
            </div>

            <div class="col-md-12">
              <label class="form-label">Lokasi Kegiatan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="location" placeholder="Masukkan lokasi kegiatan" required />
              </div>
              <div class="form-text text-muted">Hanya tersedia untuk kegiatan di lingkungan Telkom University.</div>
            </div>

            <!-- Start Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Pakai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDate" required />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Pakai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="loanTimeStart" required />
              </div>
            </div>

            <!-- End Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Kembali</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDateEnd" required />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Kembali</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="loanTimeEnd" required />
              </div>
            </div>

            <!-- Uploads -->
            <div class="col-12 mt-4">
               <h6 class="fw-bold"><i class="bi bi-cloud-upload me-2"></i>Upload Dokumen</h6>
               <div class="row g-3">
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Proposal Kegiatan</label>
                       <input type="file" class="form-control" id="requirements" accept=".pdf" required />
                       <div class="form-text">Wajib PDF (max 10MB).</div>
                   </div>
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Identitas Peminjam (KTM/KTP/SIM)</label>
                       <input type="file" class="form-control" id="ktpUpload" required />
                       <div class="form-text">Bebas tipe atau format file.</div>
                   </div>
               </div>
            </div>

            <div class="col-12 mt-3">
              <label class="form-label">Deskripsi Kegiatan</label>
              <textarea id="longPurpose" class="form-control" rows="4" placeholder="Tuliskan detail deskripsi kegiatan..."
                required></textarea>
              <div class="invalid-feedback">Wajib diisi.</div>
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4 reveal-up">
            <a class="btn btn-outline-secondary" href="{{ route('guest.catalogue.barang') }}">
              <i class="bi bi-arrow-left-circle me-1"></i>Tambah Barang
            </a>
            <button class="btn btn-outline-danger" type="button" id="btnCancel">
              <i class="bi bi-x-circle me-1"></i>Batalkan
            </button>
            <button class="btn btn-primary btn-book" type="submit" id="btnSubmit" wire:loading.attr="disabled" @click="saveEmailToLS">
              <span wire:loading.remove wire:target="document_file, submit"><i class="bi bi-check2-circle me-1"></i>Kirim Booking</span>
              <span wire:loading wire:target="document_file"><span class="spinner-border spinner-border-sm me-1"></span>Uploading...</span>
              <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
            </button>
          </div>
        </form>

        <!-- QRIS Donasi -->
        <div class="form-card mt-3 reveal-up" id="qrisDonation">
          <h5 class="mb-2"><i class="bi bi-qr-code me-1"></i> Donasi QRIS (Opsional)</h5>
          <div class="row g-3 align-items-center">
            <div class="col-md-5 text-center">
              <img src="{{ asset('fe-guest/qris msu.jpg') }}" alt="QRIS" class="qris-img">
              <div class="form-text mt-2">Scan untuk donasi. Terima kasih 🙏</div>
            </div>
            <div class="col-md-7">
              <label class="form-label">Nominal Donasi</label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi"
                  data-amt="10000">Rp10.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi"
                  data-amt="20000">Rp20.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi"
                  data-amt="50000">Rp50.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi"
                  data-amt="100000">Rp100.000</button>
              </div>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" min="0" step="1000" class="form-control" id="donationAmount"
                  placeholder="Nominal lain (opsional)">
              </div>
              <small class="text-muted">Nominal donasi akan ikut tercatat saat submit (simulasi front-end).</small>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Modal Konfirmasi Hapus (Generic) -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true" style="z-index: 2050;">
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
  <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true" style="z-index: 2055;">
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
              style="border-radius:12px">Cek Lagi</button>
            <button type="button" class="btn btn-primary px-4 fw-bold" id="btnRealSubmit" style="border-radius:12px">Ya,
              Kirim</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('fe-guest/booking-barang.js') }}?v={{ time() }}"></script>
@endpush
