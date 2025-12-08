@extends('pengurus.layout')

@section('content')

<div class="hero">
    <img src="/pengurus-assets/img/bg.jpg" class="hero-img">

    <div class="hero-text">
        <p>Satu langkah menuju <b>kemudahan beraktivitas</b> di MSU</p>
        <p>Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.</p>
    </div>
</div>

<div class="btn-wrapper">
    <a class="btn-main" href="{{ route('pengurus.fasilitas') }}">
        Peminjaman Hari ini
    </a>
</div>

@endsection
