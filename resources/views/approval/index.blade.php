@extends('layouts.app')

@section('title', 'Persetujuan - SIMORA')

@section('content')
<section class="page-section">
  <x-page-header title="Persetujuan" subtitle="Dokumen yang menunggu persetujuan Anda" />

  <div class="approval-list">
    <article class="approval-item">
      <div class="approval-copy">
        <h3>Surat Keterangan Aktif</h3>
        <p>Pengajuan dari Tim Akademik · Menunggu verifikasi</p>
      </div>
      <button class="action-btn primary" type="button">Approve</button>
    </article>
    <article class="approval-item">
      <div class="approval-copy">
        <h3>Surat Izin Kegiatan</h3>
        <p>Pengajuan dari Sekretariat · Perlu tinjauan akhir</p>
      </div>
      <button class="action-btn primary" type="button">Approve</button>
    </article>
    <article class="approval-item">
      <div class="approval-copy">
        <h3>Surat Pemberitahuan</h3>
        <p>Pengajuan dari Divisi Humas · Siap disetujui</p>
      </div>
      <button class="action-btn primary" type="button">Approve</button>
    </article>
  </div>
</section>
@endsection
