@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

@include('partials.hero') {{-- Jika mau modular --}}

<section class="judul-bawah">
    <h1>Peminjaman Hari ini</h1>
</section>

<div class="dashboard">
    … semua card dashboard kamu disini …
</div>

@endsection
