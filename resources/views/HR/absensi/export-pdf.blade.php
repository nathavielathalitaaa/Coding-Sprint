<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; padding: 20px; }
  .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #04A54C; padding-bottom: 12px; }
  .header h2 { font-size: 16px; color: #04A54C; margin-bottom: 4px; }
  .header p { font-size: 11px; color: #555; }
  .summary { display: flex; gap: 12px; margin-bottom: 16px; }
  .summary-item { flex: 1; text-align: center; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; }
  .summary-item .num { font-size: 18px; font-weight: bold; }
  .summary-item .lbl { font-size: 10px; color: #888; margin-top: 2px; }
  .hadir { color: #16a34a; }
  .izin  { color: #2563eb; }
  .sakit { color: #d97706; }
  .alpha { color: #dc2626; }
  .cuti  { color: #7c3aed; }
  table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  thead tr { background: #04A54C; color: white; }
  thead th { padding: 7px 8px; text-align: left; font-size: 11px; font-weight: bold; }
  tbody tr:nth-child(even) { background: #f8fafb; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
  .badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 9px; font-weight: bold; }
  .badge-hadir { background: #dcfce7; color: #166534; }
  .badge-izin  { background: #dbeafe; color: #1e40af; }
  .badge-sakit { background: #fef3c7; color: #92400e; }
  .badge-alpha { background: #fee2e2; color: #991b1b; }
  .badge-cuti  { background: #ede9fe; color: #5b21b6; }
  .footer { margin-top: 16px; font-size: 9px; color: #aaa; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <h2>REKAP ABSENSI KARYAWAN</h2>
  <p>HR Sinergi Hotel &amp; Villa &nbsp;|&nbsp; Periode: {{ \Carbon\Carbon::parse($bulan . '-01')->format('F Y') }}</p>
</div>

<div class="summary">
  <div class="summary-item">
    <div class="num hadir">{{ $ringkasan['hadir'] }}</div>
    <div class="lbl">Hadir</div>
  </div>
  <div class="summary-item">
    <div class="num izin">{{ $ringkasan['izin'] }}</div>
    <div class="lbl">Izin</div>
  </div>
  <div class="summary-item">
    <div class="num sakit">{{ $ringkasan['sakit'] }}</div>
    <div class="lbl">Sakit</div>
  </div>
  <div class="summary-item">
    <div class="num alpha">{{ $ringkasan['alpha'] }}</div>
    <div class="lbl">Alpha</div>
  </div>
  <div class="summary-item">
    <div class="num cuti">{{ $ringkasan['cuti'] }}</div>
    <div class="lbl">Cuti</div>
  </div>
  <div class="summary-item">
    <div class="num" style="color:#1a1a1a;">{{ $absensiList->count() }}</div>
    <div class="lbl">Total</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th width="30">No</th>
      <th width="150">Nama Karyawan</th>
      <th width="80">Tanggal</th>
      <th width="70">Jam Masuk</th>
      <th width="70">Jam Keluar</th>
      <th width="60">Status</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    @forelse($absensiList as $i => $absensi)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $absensi->user?->name ?? '-' }}</td>
      <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</td>
      <td>{{ $absensi->jam_masuk ? substr($absensi->jam_masuk, 0, 5) : '-' }}</td>
      <td>{{ $absensi->jam_keluar ? substr($absensi->jam_keluar, 0, 5) : '-' }}</td>
      <td>
        <span class="badge badge-{{ $absensi->status }}">
          {{ ucfirst($absensi->status) }}
        </span>
      </td>
      <td>{{ $absensi->keterangan ?? '' }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="7" style="text-align:center;padding:20px;color:#aaa;">Tidak ada data absensi</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  Digenerate otomatis oleh sistem HR Sinergi &bull; {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
