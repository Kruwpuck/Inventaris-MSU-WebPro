{{-- resources/views/livewire/pengelola/beranda.blade.php --}}

@push('head')
    <style>
        /* ===== tombol titik 3 (hapus) ===== */
        .btn-menu-3dot {
            background: transparent !important;
            border: none !important;
            padding: 2px 4px !important;
            width: auto;
            height: auto;
            box-shadow: none !important;
            color: #111 !important;
            opacity: .85;
            transition: all .2s ease;
            line-height: 1;
            z-index: 5;
            border-radius: 8px;
        }

        .btn-menu-3dot:hover {
            opacity: 1;
            transform: scale(1.08);
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(2px);
        }

        /* ===== tombol edit biar hover+klik ga ilang ===== */
        .btn-edit-msu {
            background: #0b492c !important;
            border-color: #0b492c !important;
            color: #fff !important;
            transition: all .2s ease;
            box-shadow: none !important;
        }

        .btn-edit-msu:hover {
            filter: brightness(0.93);
            transform: translateY(-1px);
        }

        .btn-edit-msu:active {
            transform: translateY(0);
            filter: brightness(0.88);
        }

        .btn-edit-msu:focus,
        .btn-edit-msu:focus-visible {
            outline: none !important;
            box-shadow: 0 0 0 .2rem rgba(11, 73, 44, .25) !important;
        }
    </style>
@endpush

<div>
    {{-- HERO --}}
    <section class="position-relative w-100" style="margin-top: 80px;">
        {{-- Background Image: Scales naturally with width --}}
        <img src="{{ asset('aset/ramadhan.png') }}" alt="Background" class="w-100 h-auto d-block">

        {{-- Overlay Content --}}
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center">
            <img src="{{ asset('aset/MSU.png') }}" alt="Syamsul Ulum" class="shadow-strong rounded-3"
                style="width: 80%; max-width: 900px; height: auto; object-fit: contain;">
        </div>
    </section>

    <div class="container">

        {{-- FLASH --}}
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- SEARCH (tanpa tombol X; kalau input dikosongkan, otomatis balik normal) --}}
        <form wire:submit.prevent="search" class="row justify-content-center mt-4">
            <div class="col-11 col-md-8 col-lg-6">
                <div class="input-group shadow-sm rounded-pill p-1 bg-white border">
                    <input wire:model.live.debounce.300ms="q" type="text"
                        class="form-control border-0 rounded-pill shadow-none ps-4 bg-transparent"
                        placeholder="Cari barang atau fasilitas..." />

                    <button class="btn btn-success rounded-pill px-4" type="submit"
                        style="background-color:#0b492c; border-color:#0b492c;">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>


            </div>
        </form>

        {{-- HEADER + DROPDOWN --}}
        <section class="text-center mt-5">
            <h4 class="fw-light mb-2">
                Satu langkah menuju <b class="text-success">kemudahan beraktivitas</b> di MSU
            </h4>
            <p class="text-muted mb-3">
                Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.
            </p>

            <div class="text-start">
                <div class="dropdown d-inline-block">
                    <button id="kategoriBtn" class="btn btn-success dropdown-toggle rounded-pill px-4 py-2"
                        type="button" data-bs-toggle="dropdown" style="background-color:#0b492c;border-color:#0b492c">
                        Barang
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-switch="barang">Barang</a></li>
                        <li><a class="dropdown-item" href="#" data-switch="ruangan">Ruangan</a></li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- GRID BARANG --}}
        <section id="gridBarang" class="mt-4 pb-5">
            <div class="row g-4 justify-content-center">
                @forelse($barangs as $b)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3" wire:key="barang-{{ $b->id }}">
                        <div class="card h-100 border-0 rounded-4 shadow-strong mx-auto position-relative {{ !($b->is_active ?? true) ? 'item-disabled' : '' }}"
                            style="max-width:16rem">

                            {{-- titik 3 hapus --}}
                            <button class="btn-menu-3dot position-absolute top-0 end-0 m-2" type="button"
                                wire:click.stop="confirmDelete({{ $b->id }})" title="Hapus">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>

                            @php
                                $imgSrc = asset('aset/default.png');
                                if ($b->image_path) {
                                    if (file_exists(public_path('aset/' . $b->image_path))) {
                                        $imgSrc = asset('aset/' . $b->image_path);
                                    } else {
                                        $imgSrc = asset('storage/' . $b->image_path);
                                    }
                                }
                            @endphp
                            <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $b->name }}"
                                style="height:160px;object-fit:cover" />

                            <div class="card-body py-3 px-3">
                                <h6 class="card-title fw-semibold mb-1">{{ $b->name }}</h6>
                                <p class="card-text text-muted small mb-2">{{ $b->description }}</p>
                                <p class="item-stok text-secondary small mb-3">
                                    Stok: <b>{{ $b->stock }} unit</b>
                                </p>

                                <button class="btn btn-sm rounded-pill px-3 py-1 btn-edit-msu"
                                    wire:click="openEdit({{ $b->id }})">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mt-4">
                        {{ $q ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Belum ada data barang.' }}
                    </p>
                @endforelse
            </div>
        </section>

        {{-- GRID RUANGAN --}}
        <section id="gridRuangan" class="mt-4 pb-5 d-none">
            <div class="row g-4 justify-content-center">
                @forelse($fasilitas as $f)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3" wire:key="ruangan-{{ $f->id }}">
                        <div class="card h-100 border-0 rounded-4 shadow-strong mx-auto position-relative {{ !($f->is_active ?? true) ? 'item-disabled' : '' }}"
                            style="max-width:16rem">

                            {{-- titik 3 hapus --}}
                            <button class="btn-menu-3dot position-absolute top-0 end-0 m-2" type="button"
                                wire:click.stop="confirmDelete({{ $f->id }})" title="Hapus">
                                <i class="bi bi-three-dots-vertical fs-5"></i>
                            </button>

                            @php
                                $imgSrc = asset('aset/default.png');
                                if ($f->image_path) {
                                    if (file_exists(public_path('aset/' . $f->image_path))) {
                                        $imgSrc = asset('aset/' . $f->image_path);
                                    } else {
                                        $imgSrc = asset('storage/' . $f->image_path);
                                    }
                                }
                            @endphp
                            <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $f->name }}"
                                style="height:160px;object-fit:cover" />

                            <div class="card-body py-3 px-3">
                                <h6 class="card-title fw-semibold mb-1">{{ $f->name }}</h6>
                                <p class="card-text text-muted small mb-2">{{ $f->description }}</p>
                                <p class="item-stok text-secondary small mb-3">
                                    Kapasitas: <b>{{ $f->capacity }} orang</b>
                                </p>

                                <button class="btn btn-sm rounded-pill px-3 py-1 btn-edit-msu"
                                    wire:click="openEdit({{ $f->id }})">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mt-4">
                        {{ $q ? 'Tidak ada ruangan/fasilitas yang cocok dengan pencarian.' : 'Belum ada data fasilitas/ruangan.' }}
                    </p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveEdit">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Item
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Item</label>
                            <input type="text" class="form-control" wire:model.defer="editName" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" wire:model.defer="editDescription" rows="3"
                                required></textarea>
                        </div>

                        {{-- kalau barang tampil stok --}}
                        @if($editCategory === 'barang')
                            <div class="mb-3">
                                <label class="form-label">Stok (unit/akun)</label>
                                <input type="number" class="form-control" wire:model.defer="editStock" min="0" required />
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">Kapasitas (orang)</label>
                                <input type="number" class="form-control" wire:model.defer="editCapacity" min="0"
                                    required />
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="deleteItem">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-trash me-2"></i>Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Yakin mau hapus item ini? Tindakan ini tidak bisa dibatalkan.
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger" type="submit">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-edit-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            });

            Livewire.on('close-edit-modal', () => {
                const el = document.getElementById('editModal');
                const modal = bootstrap.Modal.getInstance(el);
                modal?.hide();
            });

            Livewire.on('open-delete-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });

            Livewire.on('close-delete-modal', () => {
                const el = document.getElementById('deleteModal');
                const modal = bootstrap.Modal.getInstance(el);
                modal?.hide();
            });
        });

        // dropdown switch (barang/ruangan)
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('click', function (e) {
                const item = e.target.closest('[data-switch]');
                if (!item) return;
                e.preventDefault();

                const tipe = item.getAttribute('data-switch');
                const gridBarang = document.getElementById('gridBarang');
                const gridRuangan = document.getElementById('gridRuangan');
                const kategoriBtn = document.getElementById('kategoriBtn');

                if (tipe === 'barang') {
                    gridBarang.classList.remove('d-none');
                    gridRuangan.classList.add('d-none');
                    kategoriBtn.textContent = 'Barang';
                } else {
                    gridBarang.classList.add('d-none');
                    gridRuangan.classList.remove('d-none');
                    kategoriBtn.textContent = 'Ruangan';
                }
            });
        });
    </script>
@endpush