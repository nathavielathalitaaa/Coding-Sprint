@extends('layouts.app')

@section('title', 'Dashboard - SIMORA')

@section('content')
<section class="page-section">
  <x-page-header title="Dashboard" subtitle="Ringkasan surat yang sedang diproses" />

  <div class="stats-grid">
    <article class="stat-card">
      <div class="stat-value">26</div>
      <div class="stat-label">Telah diajukan</div>
    </article>
    <article class="stat-card">
      <div class="stat-value">7</div>
      <div class="stat-label">Sedang diproses</div>
    </article>
    <article class="stat-card">
      <div class="stat-value">21</div>
      <div class="stat-label">Telah disetujui</div>
    </article>
  </div>

  <section class="panel-block">
    <div class="panel-head">
      <h2>Daftar Surat</h2>
      <div class="toolbar">
        <label class="search-field" aria-label="Cari surat">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" placeholder="Cari surat...">
        </label>
        <a href="{{ url('/surat/create') }}" class="action-btn primary">+ Ajukan surat</a>
      </div>
    </div>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Jenis Surat</th>
            <th>Perihal</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>001</td>
            <td>Surat Keterangan</td>
            <td>Permohonan aktif kuliah</td>
            <td><span class="status-pill approved">Disetujui</span></td>
          </tr>
          <tr>
            <td>002</td>
            <td>Surat Izin</td>
            <td>Permohonan cuti</td>
            <td><span class="status-pill processing">Diproses</span></td>
          </tr>
          <tr>
            <td>003</td>
            <td>Surat Pemberitahuan</td>
            <td>Informasi kegiatan</td>
            <td><span class="status-pill draft">Draft</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</section>
@endsection
