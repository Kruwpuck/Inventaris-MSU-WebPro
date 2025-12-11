<div x-data="bookingForm">
  <main class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="page-title drop-in m-0">Keterangan Peminjaman Inventory MSU</h1>
      <span class="badge rounded-pill text-bg-warning-subtle border reveal-up">
        <i class="bi bi-lightning-charge-fill me-1"></i> Booking
      </span>
    </div>

    <!-- Livewire Error Alert (Keeping this as safeguard) -->
    @if(session()->has('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
      <!-- LEFT: Panel Barang (tabs horizontal) -->
      <!-- wire:ignore because JS heavily modifies the DOM here for Tabs/Calendar -->
      <aside class="col-lg-5" wire:ignore>
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
          <button id="clearCartBtn" class="btn btn-sm btn-outline-danger" type="button">
            <i class="bi bi-trash"></i> Hapus Semua
          </button>
        </h5>
        <div id="cartList" class="mb-3" wire:ignore></div>

        <!-- Form Booking -->
        <form wire:submit.prevent="submit" id="bookingFormSecure" class="form-card needs-validation" novalidate>
          
          <!-- Hidden Input for Cart JSON -->
          <input type="hidden" id="cartJsonInput" wire:model="cart_json">

          <div class="row g-3">
            <!-- NOMOR PEMINJAMAN (Phone) -->
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control" id="loanNumber" wire:model="borrower_phone" placeholder="Contoh: 08123456789" required />
                @error('borrower_phone') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Penanggung Jawab (Name) -->
            <div class="col-md-6">
              <label class="form-label">Penanggung jawab</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="pjName" wire:model="borrower_name" placeholder="Nama lengkap" required />
                @error('borrower_name') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- NIM/NIP -->
            <div class="col-md-6">
              <label class="form-label">NIM/NIP</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control" id="idNumber" wire:model="borrower_nim" placeholder="Contoh: 21573xxxxx" required />
                @error('borrower_nim') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" wire:model="borrower_email" placeholder="nama@contoh.ac.id" required />
                @error('borrower_email') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Prodi -->
            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <input type="text" class="form-control" id="studyProgram" wire:model="borrower_prodi" placeholder="Contoh: Informatika / Organisasi BEM" required />
                @error('borrower_prodi') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Keperluan -->
            <div class="col-12">
              <label class="form-label">Keperluan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control" id="purpose" wire:model="borrower_reason" placeholder="Contoh: Kuliah tamu / kegiatan..." required />
                @error('borrower_reason') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Tanggal -->
            <div class="col-md-6">
              <label class="form-label">Tanggal Peminjaman</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDate" wire:model.change="loan_date_start" required />
                @error('loan_date_start') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Jam Mulai -->
            <div class="col-md-3">
              <label class="form-label">Jam Mulai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="startTime" wire:model.change="loan_time_start" required />
                @error('loan_time_start') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Durasi -->
            <div class="col-md-3">
              <label class="form-label">Durasi</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hourglass-split"></i></span>
                <select class="form-select" id="duration" wire:model.change="loan_duration" required>
                  <option value="" selected disabled>Pilih</option>
                  <option value="1">1 jam</option>
                  <option value="2">2 jam</option>
                  <option value="3">3 jam</option>
                  <option value="4">4 jam</option>
                  <option value="8">Seharian</option>
                </select>
                @error('loan_duration') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Upload -->
            <div class="col-12">
              <label class="form-label">Upload Dokumen Persyaratan</label>
              <div
                  x-data="{ isUploading: false, progress: 0 }"
                  x-on:livewire-upload-start="isUploading = true"
                  x-on:livewire-upload-finish="isUploading = false"
                  x-on:livewire-upload-error="isUploading = false"
                  x-on:livewire-upload-progress="progress = $event.detail.progress"
              >
                  <input class="form-control" type="file" id="requirements" wire:model="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                  
                  <!-- File Upload Progress Bar -->
                  <div x-show="isUploading" class="progress mt-2" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" :style="`width: ${progress}%`" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  
                  <div class="form-text">
                    Format: PDF/JPG/PNG (maks 10 MB). Unduh & tandatangani
                    <a href="#" target="_blank">Pakta Peminjaman Barang</a>,
                    lalu unggah bersama dokumen pendukung.
                  </div>
                  @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Deskripsi Detail -->
            <div class="col-12">
              <label class="form-label">Deskripsi Keperluan</label>
              <textarea id="longPurpose" wire:model.blur="borrower_description" class="form-control" rows="4" placeholder="Tuliskan detail singkat keperluan…" required></textarea>
              @error('borrower_description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('catalogue.barang') }}">
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
        <div class="form-card mt-3 reveal-up" id="qrisDonation" wire:ignore>
          <h5 class="mb-2"><i class="bi bi-qr-code me-1"></i> Donasi QRIS (Opsional)</h5>
          <div class="row g-3 align-items-center">
            <div class="col-md-5 text-center">
              <img src="{{ asset('assets/qris.png') }}" onerror="this.src='https://placehold.co/200?text=QRIS'" alt="QRIS" class="qris-img">
              <div class="form-text mt-2">Scan untuk donasi. Terima kasih 🙏</div>
            </div>
            <div class="col-md-7">
              <label class="form-label">Nominal Donasi</label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi" data-amt="10000">Rp10.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi" data-amt="20000">Rp20.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi" data-amt="50000">Rp50.000</button>
                <button type="button" class="btn btn-outline-success btn-sm btn-donasi" data-amt="100000">Rp100.000</button>
              </div>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" min="0" step="1000" class="form-control" id="donationAmount" placeholder="Nominal lain (opsional)">
              </div>
              <small class="text-muted">Nominal donasi akan ikut tercatat saat submit (simulasi front-end).</small>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/booking-barang.css') }}">
@endpush

@script
<script>
    Alpine.data('bookingForm', () => ({
        init() {
            // Restore Booking Meta (Date, Time, Duration) from LocalStorage
            this.restoreBookingMeta();

            // Sync Cart from Global Object
            this.syncCart();

            // Listen for global cart changes to update Livewire
            window.addEventListener('msu:cart-updated', () => {
                this.syncCart();
            });
        },

        restoreBookingMeta() {
            try {
                const meta = JSON.parse(localStorage.getItem('msu_booking_meta') || '{}');
                
                // Use $wire.set (deferred) to avoid multiple requests if possible, 
                // but for reliability we just set them.
                if (meta.tanggal) this.$wire.set('loan_date_start', meta.tanggal);
                if (meta.mulai)   this.$wire.set('loan_time_start', meta.mulai);
                if (meta.durasi)  this.$wire.set('loan_duration', meta.durasi);
                
            } catch (e) {
                console.error("Failed to restore booking meta", e);
            }
        },

        saveEmailToLS() {
            const email = document.getElementById('email')?.value;
            if(email) localStorage.setItem('lastBookingEmail', email);
        },

        syncCart() {
            if (window.MSUCart) {
                const items = window.MSUCart.get();
                // We set the cart_json property for the backend
                this.$wire.set('cart_json', JSON.stringify(items));
            }
        }
    }));
</script>
@endscript

@push('scripts')
{{-- Load the UI logic (Tabs, Calendar) --}}
<script src="{{ asset('js/booking-barang-secure.js') }}?v=2"></script>
@endpush
