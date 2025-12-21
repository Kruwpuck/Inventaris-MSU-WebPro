@push('styles')
<link rel="stylesheet" href="{{ asset('fe-guest/booking-barang.css') }}" />
<style>
  .navbar-masjid {
    height: 60px !important;
    padding: 0 !important;
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
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control" id="loanNumber" placeholder="Contoh: 08123456789" required wire:model="borrower_phone" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_phone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Penanggung jawab</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="pjName" placeholder="Nama lengkap" required wire:model="borrower_name" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">NIM/NIP</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control" id="idNumber" placeholder="Contoh: 21573xxxxx" required wire:model="borrower_nim" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_nim') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" placeholder="nama@contoh.ac.id" required wire:model="borrower_email" />
                <div class="invalid-feedback">Masukkan email yang valid.</div>
              </div>
              @error('borrower_email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <input type="text" class="form-control" id="studyProgram" placeholder="Sistem Informasi / Informatika"
                  required wire:model="borrower_prodi" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_prodi') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
              <label class="form-label">Kegiatan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control" id="purpose" placeholder="Contoh: Kuliah Tamu / Seminar..."
                  required wire:model="borrower_reason" />
                <div class="invalid-feedback">Wajib diisi.</div>
              </div>
              @error('borrower_reason') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">Lokasi Kegiatan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="location" placeholder="Masukkan lokasi kegiatan" required wire:model="location" />
              </div>
              <div class="form-text text-muted">Hanya tersedia untuk kegiatan di lingkungan Telkom University.</div>
              @error('location') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- Start Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Pakai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDate" required wire:model="loan_date_start" />
              </div>
              @error('loan_date_start') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Pakai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="loanTimeStart" required wire:model="loan_time_start" />
              </div>
              @error('loan_time_start') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <!-- End Date & Time -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Kembali</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDateEnd" required wire:model="loan_date_end" />
              </div>
              @error('loan_date_end') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jam Kembali</label>
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
                       <input type="file" class="form-control" id="requirements" accept=".pdf" required wire:model="document_file" />
                       <div class="form-text">Wajib PDF (max 10MB).</div>
                       @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
                   </div>
                   <div class="col-md-6">
                       <label class="form-label small fw-bold">Identitas Peminjam (KTM/KTP/SIM)</label>
                       <input type="file" class="form-control" id="ktpUpload" accept="image/png, image/jpeg, image/jpg" required wire:model="ktp_file" />
                       <div class="form-text">Wajib format gambar (JPG, PNG).</div>
                       @error('ktp_file') <div class="text-danger small">{{ $message }}</div> @enderror
                   </div>
               </div>
            </div>

            <div class="col-12 mt-3">
              <label class="form-label">Deskripsi Kegiatan</label>
              <textarea id="longPurpose" class="form-control" rows="4" placeholder="Tuliskan detail deskripsi kegiatan..."
                required wire:model="borrower_description"></textarea>
              <div class="invalid-feedback">Wajib diisi.</div>
              @error('borrower_description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('guest.catalogue.barang') }}">
              <i class="bi bi-arrow-left-circle me-1"></i>Tambah Barang
            </a>
            <button class="btn btn-outline-danger" type="button" id="btnCancel">
              <i class="bi bi-x-circle me-1"></i>Batalkan
            </button>
            <button class="btn btn-primary btn-book" type="button" id="btnSubmit">
              <span wire:loading.remove wire:target="document_file, ktp_file, submit"><i class="bi bi-check2-circle me-1"></i>Kirim Booking</span>
              <span wire:loading wire:target="document_file"><span class="spinner-border spinner-border-sm me-1"></span>Upload Prp...</span>
              <span wire:loading wire:target="ktp_file"><span class="spinner-border spinner-border-sm me-1"></span>Upload KTP...</span>
              <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm me-1"></span>Memproses...</span>
            </button>
          </div>
        </form>

        <!-- QRIS Donasi -->
        <div class="form-card mt-3" id="qrisDonation" wire:ignore>
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
                  placeholder="Nominal lain (opsional)" wire:model="donation_amount">
              </div>
              <small class="text-muted">Nominal donasi akan ikut tercatat saat submit (simulasi front-end).</small>
            </div>
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
</div>

@push('scripts')
<script src="{{ asset('fe-guest/booking-barang.js') }}?v={{ time() }}"></script>
@endpush
