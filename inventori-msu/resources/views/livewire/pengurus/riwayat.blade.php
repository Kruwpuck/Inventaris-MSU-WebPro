<x-pengurus-layout>

    <div class="judul-bawah">
        <h1>Riwayat Peminjaman</h1>
    </div>

    <div class="container">

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Waktu Ambil</th>
                        <th>Waktu Kembali</th>
                        <th>Detail</th>
                        <th>Cancel</th>
                        <th>Submit</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($riwayat as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->borrower_name }}</td>
                            <td>{{ $item->picked_up_at ? $item->picked_up_at->format('d M Y | H:i') : '-' }}</td>
                            <td>{{ $item->returned_at ? $item->returned_at->format('d M Y | H:i') : '-' }}</td>
                            <td>
                                {{-- Menggunakan accessor item_details dari LoanRecord --}}
                                <button class="detail-btn" data-detail="{{ $item->item_details }}">Detail Peminjaman</button>
                            </td>
                            <td>
                                <button class="cancel-btn" data-id="{{ $item->id }}" {{ $item->is_submitted ? 'disabled' : '' }}>
                                    Cancel
                                </button>
                            </td>
                            <td>
                                <button class="submit-btn" data-id="{{ $item->id }}" {{ $item->is_submitted ? 'disabled' : '' }}>
                                    Submit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;">Belum ada riwayat peminjaman</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

    {{-- MODAL --}}
    <div id="modalBg" class="modal-bg">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detail Peminjaman</h5>
                </div>

                <div class="modal-body">
                    <p id="detailContent"></p>
                </div>

                <div class="modal-footer">
                    <button class="close-btn" onclick="closeModal()">Tutup</button>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Modal Logic
            const detailButtons = document.querySelectorAll(".detail-btn");
            const modalBg = document.getElementById("modalBg");
            const detailContent = document.getElementById("detailContent");

            detailButtons.forEach(btn => {
                btn.addEventListener("click", function () {
                    detailContent.textContent = this.getAttribute("data-detail");
                    modalBg.style.display = "flex";
                });
            });

            // Cancel Logic
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if(this.disabled) return;
                    const id = this.getAttribute('data-id');
                    
                    if (confirm("Anda yakin ingin membatalkan peminjaman ini?")) {
                        fetch(`{{ route('pengurus.cancel', '') }}/${id}`, { // Using route with placeholder logic or post body
                             method: 'POST',
                             headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ id: id })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) location.reload();
                        });
                    }
                });
            });

            // Submit Logic
            document.querySelectorAll('.submit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if(this.disabled) return;
                    const id = this.getAttribute('data-id');
                    
                    fetch(`{{ route('pengurus.submit', '') }}/${id}`, {
                         method: 'POST',
                         headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            alert("Data berhasil disubmit!");
                            location.reload();
                        }
                    });
                });
            });
        });

        function closeModal() {
            document.getElementById("modalBg").style.display = "none";
        }
        
        document.getElementById("modalBg").addEventListener("click", function(e) {
            if (e.target === this) closeModal();
        });
    </script>

</x-pengurus-layout>
