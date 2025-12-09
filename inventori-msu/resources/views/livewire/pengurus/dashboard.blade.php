@extends('layouts.app')

@section('title','Dashboard Pengurus')

@section('content')
<section class="hero">
  <img src="{{ asset('Assets/gedung.png') }}">
  <div class="hero-subtext">
    <p>Satu langkah menuju <b>kemudahan beraktivitas</b> di MSU</p>
  </div>
</section>

<section class="judul-bawah"><h1>Peminjaman Hari ini</h1></section>

<div class="container">
  <table>
    <thead>
      <tr>
        <th>No</th><th>Nama</th><th>Waktu Ambil</th><th>Waktu Kembali</th><th>Fasilitas</th><th>Ambil</th><th>Terima</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->borrower_name }}</td>
          {{-- Gunakan optional chaining atau cek relation --}}
          <td>
             {{ optional($d->loanRecord)->picked_up_at ? $d->loanRecord->picked_up_at->format('d M Y | H:i') : '-' }}
          </td>
          <td>
             {{ optional($d->loanRecord)->returned_at ? $d->loanRecord->returned_at->format('d M Y | H:i') : '-' }}
          </td>
          {{-- Item details: join names --}}
          <td>
             <button class="detail-btn" data-detail="{{ $d->items->pluck('name')->join(', ') }}">Detail Peminjaman</button>
          </td>
          
          {{-- Checkbox actions --}}
          <td>
             <input type="checkbox" class="toggle-status" data-id="{{ $d->id }}" data-type="ambil" 
             {{ optional($d->loanRecord)->picked_up_at ? 'checked' : '' }}>
          </td>
          <td>
             <input type="checkbox" class="toggle-status" data-id="{{ $d->id }}" data-type="kembali"
             {{ optional($d->loanRecord)->returned_at ? 'checked' : '' }}>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center">Tidak ada peminjaman hari ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="modal-bg" id="modalBg">
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

<script>
    function closeModal() {
        document.getElementById('modalBg').style.display = 'none';
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        // Modal logic
        const modalBg = document.getElementById('modalBg');
        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('detailContent').textContent = this.getAttribute('data-detail');
                modalBg.style.display = 'flex';
            });
        });

        // Toggle logic
        document.querySelectorAll('.toggle-status').forEach(cb => {
            cb.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                
                fetch('{{ route("pengurus.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id, type: type })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Status berhasil diperbarui');
                        location.reload(); 
                    } else {
                        alert('Gagal memperbarui satus');
                        this.checked = !this.checked; // revert
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
@endsection
