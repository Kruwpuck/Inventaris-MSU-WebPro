<div x-data="bookingForm">
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/booking-barang.css') }}">
  @endpush

  <main class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="page-title drop-in m-0">Keterangan Peminjaman Inventory MSU</h1>
      <span class="badge rounded-pill text-bg-warning-subtle border reveal-up">
        <i class="bi bi-lightning-charge-fill me-1"></i> Booking
      </span>
    </div>

    @if(session()->has('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
      <!-- LEFT: Panel Barang (tabs horizontal) -->
      <aside class="col-lg-5" wire:ignore>
        <div class="summary-card reveal-up">
          <ul class="nav nav-tabs msu-item-tabs px-3 pt-3" id="itemTabs" role="tablist"></ul>
          <div class="tab-content p-3" id="itemTabContent"></div>
        </div>
      </aside>

      <!-- RIGHT: Ringkasan Keranjang + Form -->
      <section class="col-lg-7">
        <h5 class="mb-2 d-flex justify-content-between align-items-center">
          <span><i class="bi bi-bag-check me-1"></i> Ringkasan Keranjang</span>
          <button id="clearCartBtn" class="btn btn-sm btn-outline-danger" type="button"><i class="bi bi-trash"></i> Hapus Semua</button>
        </h5>
        <div id="cartList" class="mb-3" wire:ignore></div>

        <form wire:submit.prevent="submit" id="bookingForm" class="form-card needs-validation" novalidate>
          <input type="hidden" id="cartJsonInput" wire:model="cart_json">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control" id="loanNumber" wire:model="borrower_phone" placeholder="Contoh: 08123456789" required />
              </div>
              @error('borrower_phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Penanggung jawab</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="pjName" wire:model="borrower_name" placeholder="Nama lengkap" required />
              </div>
              @error('borrower_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

             <div class="col-md-6">
              <label class="form-label">NIM/NIP</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control" id="idNumber" wire:model="borrower_nim" placeholder="Contoh: 21573xxxxx" required />
              </div>
              @error('borrower_nim') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" wire:model="borrower_email" placeholder="nama@contoh.ac.id" required />
              </div>
              @error('borrower_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <input type="text" class="form-control" id="studyProgram" wire:model="borrower_prodi" placeholder="Contoh: Informatika" required />
              </div>
              @error('borrower_prodi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-12">
              <label class="form-label">Keperluan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control" id="purpose" wire:model="borrower_reason" placeholder="Contoh: Kuliah tamu..." required />
              </div>
              @error('borrower_reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Peminjaman</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="loanDate" wire:model.change="loan_date_start" required />
              </div>
              @error('loan_date_start') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3">
              <label class="form-label">Jam Mulai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control" id="startTime" wire:model.change="loan_time_start" required />
              </div>
              @error('loan_time_start') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

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
              </div>
              @error('loan_duration') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Upload Dokumen Persyaratan</label>
              <div x-data="{ isUploading: false, progress: 0 }"
                   x-on:livewire-upload-start="isUploading = true"
                   x-on:livewire-upload-finish="isUploading = false"
                   x-on:livewire-upload-error="isUploading = false"
                   x-on:livewire-upload-progress="progress = $event.detail.progress">
                  <input class="form-control" type="file" id="requirements" wire:model="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                  <div x-show="isUploading" class="progress mt-2" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" :style="`width: ${progress}%`"></div>
                  </div>
                  <div class="form-text">Format: PDF/JPG/PNG (maks 10 MB).</div>
                  @error('document_file') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Deskripsi Keperluan</label>
              <textarea id="longPurpose" wire:model.blur="borrower_description" class="form-control" rows="4" placeholder="Tuliskan detail..." required></textarea>
              @error('borrower_description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('guest.catalogue.barang') }}"><i class="bi bi-arrow-left-circle me-1"></i>Tambah Barang</a>
            <button class="btn btn-outline-danger" type="button" id="btnCancel"><i class="bi bi-x-circle me-1"></i>Batalkan</button>
            <button class="btn btn-primary btn-book" type="submit" id="btnSubmit" wire:loading.attr="disabled">
              <span wire:loading.remove><i class="bi bi-check2-circle me-1"></i>Kirim Booking</span>
              <span wire:loading>Memproses...</span>
            </button>
          </div>
        </form>

        <!-- QRIS (Optional) -->
        <div class="form-card mt-3 reveal-up" id="qrisDonation" wire:ignore>
          <h5 class="mb-2"><i class="bi bi-qr-code me-1"></i> Donasi QRIS (Opsional)</h5>
          <!-- ... static QRIS content ... -->
          <div class="row g-3 align-items-center">
             <div class="col-md-5 text-center">
              <img src="{{ asset('assets/qris.png') }}" onerror="this.src='https://placehold.co/200?text=QRIS'" alt="QRIS" class="qris-img" style="max-width:150px">
            </div>
            <div class="col-md-7">
               <label class="form-label">Nominal Donasi</label>
               <input type="number" class="form-control" placeholder="Nominal lkhlas">
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
  @script
  <script>
      Alpine.data('bookingForm', () => ({
          init() {
              this.syncCart();
              window.addEventListener('msu:cart-updated', () => {
                  this.syncCart();
              });
          },
          syncCart() {
              if (window.MSUCart) {
                  const items = window.MSUCart.get();
                  this.$wire.set('cart_json', JSON.stringify(items));
              }
          }
      }));
  </script>
  @endscript

  @push('scripts')
  <script src="{{ asset('js/booking-barang.js') }}?v={{ time() }}"></script>
  @endpush
</div>
