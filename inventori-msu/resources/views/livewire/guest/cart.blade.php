<div>
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/booking-barang.css') }}">
    <!-- Fix for animation visibility if classes persist -->
    <style>
        .reveal-up, .drop-in { opacity: 1 !important; transform: none !important; }
    </style>
  @endpush

  <main class="container py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="page-title m-0">Keterangan Peminjaman Inventory MSU</h1>
      <span class="badge rounded-pill text-bg-warning-subtle border text-warning-emphasis">
        <i class="bi bi-lightning-charge-fill me-1"></i> Booking
      </span>
    </div>

    <div class="row g-4">
      <!-- LEFT: Panel Barang (tabs horizontal) -->
      <aside class="col-lg-5">
        <div class="summary-card bg-white">
          <!-- Tabs -->
          @if(count($cart) > 0)
          <ul class="nav nav-tabs msu-item-tabs px-3 pt-3" role="tablist">
              @foreach($cart as $cId => $item)
                 <li class="nav-item">
                    <button type="button" 
                            class="nav-link {{ $activeItemId == $cId ? 'active' : '' }}"
                            wire:click="setActiveItem({{ $cId }})">
                      {{ $item['name'] }}
                      <span class="badge text-bg-success ms-2">{{ $item['quantity'] }}x</span>
                    </button>
                 </li>
              @endforeach
          </ul>

          <!-- Panel isi per barang -->
          <div class="tab-content p-3">
             @if($activeItemId && isset($cart[$activeItemId]))
                @php 
                    $item = $cart[$activeItemId];
                    $inv = \App\Models\Inventory::find($item['id']);
                    $maxStock = $inv && $inv->category == 'barang' ? $inv->stock : 1;
                    $inCart = $item['quantity'];
                    $sisa = max(0, $maxStock - $inCart);
                @endphp
                <div class="item-panel">
                    <div class="summary-thumb mb-3">
                        <img src="{{ asset('assets/' . $item['image_path']) }}" 
                             alt="{{ $item['name'] }}"
                             onerror="this.src='{{ asset('assets/placeholder.jpg') }}'">
                        <span class="badge-status">Active</span>
                    </div>
                    <div class="text-center">
                        <div class="title h4 mb-1">{{ $item['name'] }}</div>
                        <div class="text-muted">Dipinjam: <b><span class="qty-display-text">{{ $item['quantity'] }}</span>x</b></div>
                        
                        <div class="d-flex justify-content-center gap-2 mt-2">
                             <button class="btn btn-qty" wire:click="decrement({{ $item['id'] }})"><i class="bi bi-dash-lg"></i></button>
                             <div class="qty-display">{{ $item['quantity'] }}</div>
                             <button class="btn btn-qty" wire:click="increment({{ $item['id'] }})" @if($sisa <= 0) disabled @endif><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <div class="small text-muted d-block mt-1">Atur jumlah yang akan dipinjam</div>
                    </div>

                    <div class="mini-calendar mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <button class="cal-nav" type="button"><i class="bi bi-chevron-left"></i></button>
                          <strong class="cal-title">{{ date('F Y') }}</strong>
                          <button class="cal-nav" type="button"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="calendar-legend mb-2">
                          <span class="legend-box booked"></span><small class="ms-1 me-3">Terbooking</small>
                          <span class="legend-box today"></span><small class="ms-1">Hari ini</small>
                        </div>
                        <div class="calendar-grid">
                            @foreach(['S','S','R','K','J','S','M'] as $d) <span class="muted">{{ $d }}</span> @endforeach
                            @for($i=1; $i<=30; $i++)
                                <span class="day {{ $i == date('j') ? 'today' : '' }} {{ in_array($i, [10,20]) ? 'booked' : '' }}">{{ $i }}</span>
                            @endfor
                        </div>
                        <div class="booking-list mt-3">
                            <div class="booking-list-header">
                              <span class="bl-title">Info Peminjaman</span>
                              <span class="date-label">{{ date('d F Y') }}</span>
                            </div>
                            <div class="booking-list-body mt-2 small">
                                <div class="booking-list-empty">Belum ada peminjaman tercatat.</div>
                            </div>
                        </div>
                    </div>
                </div>
             @endif
          </div>
          @else
              <div class="p-5 text-center text-muted">Keranjang Anda kosong.</div>
          @endif
        </div>
      </aside>

      <!-- RIGHT: Ringkasan Keranjang + Form -->
      <section class="col-lg-7">
        <!-- Ringkasan Keranjang -->
        <h5 class="mb-2 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bag-check me-1"></i> Ringkasan Keranjang</span>
            @if(count($cart) > 0)
            <button wire:click="clearCart" wire:confirm="Yakin ingin menghapus semua dari keranjang?" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i> Hapus Semua
            </button>
            @endif
        </h5>
        <div id="cartList" class="mb-3">
             @if(count($cart) > 0)
                <ul class="list-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    @foreach($cart as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center border-light">
                        <div>
                            <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                            <div class="small text-muted">{{ isset($item['category']) && $item['category'] == 'barang' ? 'Barang' : 'Fasilitas' }}</div>
                        </div>
                        <span class="badge bg-success rounded-pill">{{ $item['quantity'] }}x</span>
                    </li>
                    @endforeach
                </ul>
             @else
                <div class="alert alert-light border">Keranjang kosong.</div>
             @endif
        </div>

        <!-- Form Booking -->
        <form wire:submit="submit" class="form-card needs-validation" style="position: relative;">
          <!-- Loading Overlay -->
          <!-- Loading Overlay -->
          <div wire:loading.flex wire:target="submit" class="position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 align-items-center justify-content-center" style="z-index: 10; border-radius: 20px;">
              <div class="spinner-border text-primary" role="status"></div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-receipt-cutoff"></i></span>
                <input type="text" class="form-control @error('borrower_phone') is-invalid @enderror" wire:model="borrower_phone" placeholder="Contoh: 08123456789">
                @error('borrower_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Penanggung Jawab</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control @error('borrower_name') is-invalid @enderror" wire:model="borrower_name" placeholder="Nama lengkap">
                @error('borrower_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">NIM/NIP</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" class="form-control @error('borrower_nim') is-invalid @enderror" wire:model="borrower_nim" placeholder="Contoh: 21573xxxxx">
                @error('borrower_nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control @error('borrower_email') is-invalid @enderror" wire:model="borrower_email" placeholder="nama@contoh.ac.id">
                @error('borrower_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Program Studi / Unit</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <select class="form-select @error('borrower_prodi') is-invalid @enderror" wire:model="borrower_prodi">
                  <option value="">Pilih</option>
                  <option>Sistem Informasi</option>
                  <option>Informatika</option>
                  <option>Teknologi Informasi</option>
                  <option>Unit / Organisasi</option>
                </select>
                @error('borrower_prodi') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Keperluan</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clipboard-check"></i></span>
                <input type="text" class="form-control @error('borrower_reason') is-invalid @enderror" wire:model="borrower_reason" placeholder="Contoh: Kuliah tamu / kegiatan...">
                @error('borrower_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Peminjaman</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control @error('loan_date_start') is-invalid @enderror" wire:model="loan_date_start">
                @error('loan_date_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Jam Mulai</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" class="form-control @error('loan_time_start') is-invalid @enderror" wire:model="loan_time_start">
                @error('loan_time_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-md-3">
              <label class="form-label">Durasi</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-hourglass-split"></i></span>
                <select class="form-select @error('loan_duration') is-invalid @enderror" wire:model="loan_duration">
                  <option value="">Pilih</option>
                  <option value="1">1 jam</option>
                  <option value="2">2 jam</option>
                  <option value="3">3 jam</option>
                  <option value="4">4 jam</option>
                  <option value="8">Seharian</option>
                </select>
                @error('loan_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Upload Dokumen Persyaratan</label>
              <input class="form-control @error('document_file') is-invalid @enderror" type="file" wire:model="document_file" accept=".pdf,.jpg,.jpeg,.png">
              <div class="form-text">
                Format: PDF/JPG/PNG (maks 10 MB). Unduh & tandatangani
                <a href="#" target="_blank">Pakta Peminjaman Barang</a>.
              </div>
              @error('document_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
              <label class="form-label">Deskripsi Keperluan</label>
              <textarea class="form-control @error('borrower_description') is-invalid @enderror" wire:model="borrower_description" rows="4" placeholder="Tuliskan detail singkat keperluan..."></textarea>
              @error('borrower_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end mt-4">
            <a class="btn btn-outline-secondary" href="{{ route('guest.catalogue.barang') }}">
              <i class="bi bi-arrow-left-circle me-1"></i>Tambah Barang
            </a>
            <button class="btn btn-primary btn-book" type="submit" @if(count($cart) == 0) disabled @endif>
              <i class="bi bi-check2-circle me-1"></i>Kirim Booking
            </button>
          </div>
        </form>

        <!-- QRIS Donasi -->
        <div class="form-card mt-3">
          <h5 class="mb-2"><i class="bi bi-qr-code me-1"></i> Donasi QRIS (Opsional)</h5>
          <div class="row g-3 align-items-center">
            <div class="col-md-5 text-center">
              <img src="{{ asset('assets/qris.png') }}" alt="QRIS" class="qris-img" style="max-width:200px;">
              <div class="form-text mt-2">Scan untuk donasi. Terima kasih 🙏</div>
            </div>
            <div class="col-md-7">
              <label class="form-label">Nominal Donasi</label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                 <button type="button" class="btn btn-outline-success btn-sm" wire:click="$set('donation_amount', 10000)">Rp10.000</button>
                 <button type="button" class="btn btn-outline-success btn-sm" wire:click="$set('donation_amount', 20000)">Rp20.000</button>
                 <button type="button" class="btn btn-outline-success btn-sm" wire:click="$set('donation_amount', 50000)">Rp50.000</button>
              </div>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control" wire:model="donation_amount" placeholder="Nominal lain (opsional)">
              </div>
              <small class="text-muted">Nominal donasi akan ikut tercatat saat submit.</small>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</div>
