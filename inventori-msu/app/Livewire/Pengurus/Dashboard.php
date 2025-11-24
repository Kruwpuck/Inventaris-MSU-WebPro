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
      @foreach($data as $d)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $d->nama }}</td>
          <td>{{ $d->waktu_pengambilan ? $d->waktu_pengambilan->format('d M Y | H:i') : '-' }}</td>
          <td>{{ $d->waktu_pengembalian ? $d->waktu_pengembalian->format('d M Y | H:i') : '-' }}</td>
          <td><button class="detail-btn" data-detail="{{ $d->fasilitas }}">Detail Peminjaman</button></td>
          <td><input type="checkbox" class="toggle-status" data-id="{{ $d->id }}" data-type="ambil" {{ $d->sudah_ambil ? 'checked' : '' }}></td>
          <td><input type="checkbox" class="toggle-status" data-id="{{ $d->id }}" data-type="kembali" {{ $d->sudah_kembali ? 'checked' : '' }}></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Modal same as before -->
<div class="modal-bg" id="modalBg"> ... </div>

@endsection
