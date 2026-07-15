@extends('layouts.app')

@section('title', 'Daftar Surat - SIMORA')

@section('content')
<section class="page-section">
  <x-page-header title="Daftar surat" subtitle="Semua surat yang telah Anda ajukan" />

  <div class="panel-head">
    <label class="search-field" aria-label="Cari surat">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" placeholder="Cari surat...">
    </label>
    <a href="{{ url('/surat/create') }}" class="action-btn primary">+ Ajukan surat</a>
  </div>

  <div class="table-card">
    <table class="data-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Jenis Surat</th>
          <th>Perihal</th>
          <th>Tanggal</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>001</td>
          <td>Surat Keterangan</td>
          <td>Permohonan aktif kuliah</td>
          <td>12 Juli 2026</td>
          <td><span class="status-pill approved">Disetujui</span></td>
        </tr>
        <tr>
          <td>002</td>
          <td>Surat Izin</td>
          <td>Permohonan cuti</td>
          <td>10 Juli 2026</td>
          <td><span class="status-pill processing">Diproses</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>
@endsection
