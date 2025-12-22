@push('head')
  <style>
    body {
      background: #fff;
      font-family: "Poppins", sans-serif;
    }

    .form-card {
      border-radius: 22px;
    }

    .pill {
      border-radius: 9999px;
    }

    /* --- PERBAIKAN TOMBOL DAFTAR (FIX HOVER) --- */
    .btn-msu {
      background-color: #0b492c !important;
      /* Hijau Default */
      border-color: #0b492c !important;
      color: #ffffff !important;
      /* Teks Putih */
      transition: all 0.3s ease;
    }

    /* Saat Hover/Focus: Tetap Hijau Gelap & Teks Putih */
    .btn-msu:hover,
    .btn-msu:focus,
    .btn-msu:active {
      background-color: #093e25 !important;
      /* Hijau lebih gelap */
      border-color: #093e25 !important;
      color: #ffffff !important;
      transform: translateY(-1px);
      /* Efek naik dikit */
      box-shadow: 0 4px 12px rgba(11, 73, 44, 0.3);
    }

    /* ------------------------------------------- */

    .preview {
      height: 360px;
      object-fit: cover;
      border-radius: 18px;
    }

    @media (max-width:991.98px) {
      .preview {
        height: 240px;
      }
    }

    /* Styling Input Group agar rapi */
    .input-group .form-control.pill {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }

    .input-group .input-group-text.pill {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      border-left: 0;
      background: #e9ecef;
    }

    /* Mencegah kedip saat loading AlpineJS */
    [x-cloak] {
      display: none !important;
    }
  </style>
@endpush

<div class="container pt-5">
  <div style="height:84px"></div>

  <section class="text-center mb-4">
    <p class="mb-1 fw-semibold" style="letter-spacing:.06em;color:#2a6a55">PENAMBAHAN BARANG/RUANGAN</p>
    <h2 class="fw-bolder text-uppercase" style="color:#32435a">Masjid Syamsul Ulum</h2>
    <div class="mx-auto" style="width:120px;height:3px;background:#2a6a55;border-radius:2px"></div>
  </section>

  {{-- Alert Sukses --}}
  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <section class="pb-5">
    <div class="row g-4 align-items-stretch">

      {{-- KIRI: FOTO --}}
      <div class="col-12 col-lg-5">
        <div class="card form-card shadow h-100">
          <div class="card-body">
            <h5 class="mb-3">Foto Barang/Ruangan</h5>

            @if ($image)
              <img class="w-100 preview mb-3 shadow-sm" src="{{ $image->temporaryUrl() }}" alt="Preview Upload">
            @else
              <img class="w-100 preview mb-3 shadow-sm"
                src="https://images.unsplash.com/photo-1594322436404-5a0526db4d13?q=80&w=800&auto=format&fit=crop"
                alt="Preview Default" onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
            @endif

            <label class="btn btn-outline-secondary pill w-100">
              <i class="bi bi-upload me-2"></i> Pilih Foto
              <input type="file" class="d-none" accept="image/png, image/jpeg, image/jpg" wire:model="image">
            </label>
            <small class="text-muted d-block mt-2 text-center">Format JPG/PNG, maksimal 5MB.</small>

            @error('image') 
                <div class="text-danger small mt-1">
                    @if ($message === 'The image failed to upload.')
                        Ukuran gambar tidak boleh lebih dari 5MB.
                    @else
                        {{ $message }}
                    @endif
                </div> 
            @enderror

            <div wire:loading wire:target="image" class="text-center small text-primary mt-2">
              <span class="spinner-border spinner-border-sm" role="status"></span> Mengunggah...
            </div>
          </div>
        </div>
      </div>

      {{-- KANAN: FORM --}}
      <div class="col-12 col-lg-7">
        <div class="card form-card shadow h-100">
          {{-- x-data: Mengaktifkan AlpineJS untuk interaksi instan --}}
          <div class="card-body" x-data="{ kategori: @entangle('category') }">
            <h5 class="text-center fw-bold mb-4">Form Pendaftaran</h5>

            <form wire:submit.prevent="save" class="row g-3">

              {{-- 1. NAMA --}}
              <div class="col-12">
                <label class="form-label text-muted">Nama*</label>
                <input type="text" class="form-control pill" wire:model="name"
                  placeholder="Contoh: Proyektor / Ruang Tamu VIP">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- 2. KATEGORI --}}
              <div class="col-12">
                <label class="form-label text-muted">Kategori*</label>
                {{-- x-model menghubungkan dropdown ke AlpineJS agar UI berubah instan --}}
                <select class="form-select pill" wire:model="category" x-model="kategori">
                  <option value="">Pilih kategori</option>
                  <option value="Barang">Barang</option>
                  <option value="Ruangan">Ruangan</option>
                </select>
                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- 3. DESKRIPSI --}}
              <div class="col-12">
                <label class="form-label text-muted">Deskripsi</label>
                <textarea class="form-control" rows="3" style="border-radius:18px" wire:model="description"
                  placeholder="Spesifikasi singkat..."></textarea>
                @error('description') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- GRID SYSTEM: STATUS & INPUT DINAMIS --}}

              {{-- Kolom Kiri: Status --}}
              <div class="col-12 col-md-6">
                <label class="form-label text-muted">Status</label>
                <select class="form-select pill" wire:model="status">
                  <option value="Tersedia">Tersedia</option>
                  <option value="Tidak Tersedia">Tidak Tersedia</option>
                  <option value="Perawatan">Perawatan</option>
                  <option value="Dipakai">Dipakai</option>
                </select>
                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Kolom Kanan: Input Dinamis (Muncul Instan pakai x-show) --}}
              <div class="col-12 col-md-6">

                {{-- Jika Barang -> Muncul STOK --}}
                <div x-show="kategori == 'Barang'" x-cloak>
                  <label class="form-label text-muted">Stok Barang*</label>
                  <input type="number" min="0" class="form-control pill" wire:model="stock" placeholder="Jml Stok">
                  @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Jika Ruangan -> Muncul KAPASITAS --}}
                <div x-show="kategori == 'Ruangan'" x-cloak>
                  <label class="form-label text-muted">Kapasitas Ruangan*</label>
                  <div class="input-group">
                    <input type="number" min="1" class="form-control pill" wire:model="capacity"
                      placeholder="Max Orang">
                    <span class="input-group-text pill">orang</span>
                  </div>
                  @error('capacity') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Placeholder (Jika belum pilih kategori) --}}
                <div x-show="!kategori" x-cloak>
                  <label class="form-label text-muted">&nbsp;</label>
                  <input type="text" class="form-control pill" disabled style="background-color: #f8f9fa; border:none;">
                </div>

              </div>

              {{-- Tombol Aksi --}}
              <div class="col-12 d-flex justify-content-end pt-3">
                <button type="button" wire:click="resetForm" class="btn btn-outline-secondary pill px-4 me-2">
                  Bersihkan
                </button>

                <button type="submit" class="btn btn-msu pill px-4">
                  <div wire:loading wire:target="save" class="spinner-border spinner-border-sm text-white me-1"
                    role="status"></div>
                  <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle me-1"></i></span>
                  Daftar
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>