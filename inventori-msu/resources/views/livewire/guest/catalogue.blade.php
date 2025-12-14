<div>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('css/barang.css') }}">
  <!-- Custom override from borrower.catalogue -->
  <style>
    .hero {
      border-radius: 26px !important;
      overflow: hidden !important;
      position: relative;
      margin-bottom: 0 !important;
      min-height: auto !important; 
    }
    .hero img {
      border-radius: 26px !important;
      width: 100%;
      height: 320px;
      object-fit: cover;
      display: block;
    }
    .search-section-overlap {
      margin-top: 24px;
      position: relative;
      z-index: 20; 
      padding: 0;
    }
    .search-wrap {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
  </style>
  @endpush

  <main class="container py-4">

    <!-- HERO -->
    <section class="hero mb-4">
        <img src="{{ asset('assets/plaza1.png') }}" 
             alt="{{ $category == 'barang' ? 'Plaza Masjid' : 'Aula' }}"
             class="hero-img-click"
             onclick="document.getElementById('searchInput').focus()"
             style="cursor: pointer;">
        <div class="caption">
          <div>
            <h1 class="mb-2">{{ $category == 'barang' ? 'Peminjaman Barang' : 'Peminjaman Ruang' }}</h1>
            <p class="m-0">Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.</p>
          </div>
        </div>
    </section>
  
    <!-- SEARCH -->
    <section class="mb-3 search-section-overlap">
      <div class="search-wrap">
        <i class="bi bi-search" onclick="document.getElementById('searchInput').focus()" style="cursor: pointer;"></i>
        <!-- Bind Livewire search -->
        <input id="searchInput" type="text" class="form-control search-input"
               wire:model.live.debounce.300ms="search"
               placeholder="Cari {{ $category == 'barang' ? 'barang' : 'ruangan' }}… (mis. {{ $category == 'barang' ? 'proyektor, sound system' : 'aula, rapat' }})"
               autocomplete="off">
        <button id="clearSearch" class="btn btn-clear" type="button" aria-label="Bersihkan pencarian" wire:click="$set('search', '')">
           @if(!empty($search)) <i class="bi bi-x-lg"></i> @endif
        </button>
      </div>
      <small class="text-muted">Ketik untuk memfilter daftar di bawah secara langsung.</small>
    </section>

    <!-- DATEBAR -->
    <section class="datebar-v2 mb-4" id="bookingMetaBar">
        <h3 class="section-title mb-4 d-flex align-items-center gap-2" style="font-size:1.25rem; padding-left: 4px;">
          <i class="bi bi-clock-history"></i> Waktu Peminjaman
        </h3>

        <div class="datebar-content">
            <div class="inputs-row d-flex gap-4 align-items-end mb-4">
                <div class="flex-fill">
                    <label for="dateStart" class="form-label mb-2 fw-bold"><i class="bi bi-calendar-event me-2"></i>Tanggal Pakai</label>
                    <input id="dateStart" type="date" class="form-control" style="padding: 10px;">
                </div>
                <div class="flex-fill">
                    <label for="timeStart" class="form-label mb-2 fw-bold"><i class="bi bi-alarm me-2"></i>Jam Mulai</label>
                    <input id="timeStart" type="time" class="form-control" style="padding: 10px;">
                </div>
                <div class="flex-fill">
                    <label for="durationSel" class="form-label mb-2 fw-bold"><i class="bi bi-hourglass-split me-2"></i>Durasi</label>
                    <select id="durationSel" class="form-select" style="padding: 10px;">
                    <option value="1">1 jam</option>
                    <option value="2">2 jam</option>
                    <option value="3">3 jam</option>
                    <option value="4">4 jam</option>
                    <option value="8">Seharian</option>
                    </select>
                </div>
            </div>
            <div class="action-row">
                <button id="btnSetDates" class="btn btn-success w-100 py-2 fw-bold" style="font-size: 1rem;"><i class="bi bi-search me-2"></i>Cek Ketersediaan</button>
                <div class="small text-muted mt-2 js-daterange text-center" aria-live="polite"></div>
            </div>
        </div>
    </section>
    
    <h3 class="section-title mb-3">Daftar {{ $category == 'barang' ? 'Barang' : 'Ruangan' }}</h3>
  
    <!-- GRID -->
    <div class="row g-4 align-items-stretch items-grid" id="itemsGrid">
        @forelse($items as $item)
        @php
            $cart = session('cart', []);
            $inCart = $cart[$item->id]['quantity'] ?? 0;
            $invData = \App\Models\Inventory::find($item->id); 
            $max = $invData ? $invData->stock : 0;
            if ($category != 'barang') $max = 1;
            $sisa = max(0, $max - $inCart);
        @endphp
        <div class="col-6 col-md-4 col-lg-2-4">
            <article class="item-card tap-anim h-100" data-type="{{ $category == 'barang' ? 'barang' : 'ruang' }}">
                <div class="item-thumb">
                    <img src="{{ asset('assets/' . $item->image_path) }}" 
                         onerror="this.onerror=null; this.src='{{ asset('assets/placeholder.jpg') }}';"
                         alt="{{ $item->name }}">
                    
                    @if($sisa == 0)
                        <span class="badge-status" style="background: #a94442;">Habis</span>
                    @else
                        <span class="badge-status">Active</span>
                    @endif

                    <div class="qty-actions">
                        <button class="qty-btn" wire:click="decrement({{ $item->id }})" 
                                @if($inCart <= 0) disabled @endif
                                aria-label="Kurangi dari keranjang">−</button>
                        <button class="qty-btn" wire:click="addToCart({{ $item->id }})" 
                                @if($sisa <= 0) disabled @endif
                                wire:loading.attr="disabled"
                                aria-label="Tambahkan ke keranjang">＋</button>
                    </div>
                </div>
                <div class="item-body">
                    <div class="item-title">{{ $item->name }}</div>
                    <div class="item-meta">
                        @if($category == 'barang')
                          Sisa : <span class="sisa">{{ $sisa }}</span>
                        @else
                          Tersedia : <span class="sisa">{{ $sisa }}</span>
                        @endif
                    </div>
                </div>
            </article>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted py-5">
              <i class="bi bi-emoji-frown display-4 d-block mb-3"></i>
              Tidak ada item ditemukan.
            </p>
        </div>
        @endforelse
    </section>
  
    <p id="emptyState" class="text-center text-muted d-none mt-4">Tidak ada barang yang cocok dengan pencarian.</p>
  </main>
</div>
