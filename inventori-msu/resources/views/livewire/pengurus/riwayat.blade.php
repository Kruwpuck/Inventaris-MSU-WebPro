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

                <tbody id="riwayatTable">
                    {{-- Diisi JS --}}
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

    {{-- INIT --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            initRiwayat();
        });
    </script>

</x-pengurus-layout>
