<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap');
  
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { 
    font-family: Arial, sans-serif; 
    font-size: 11px; 
    color: #374151; 
    padding: 20px; 
  }
  .header { 
    text-align: center; 
    margin-bottom: 24px; 
    border-bottom: 2px solid #1A2B24; 
    padding-bottom: 12px; 
  }
  .header h2 { 
    font-family: 'Playfair Display', serif;
    font-size: 18px; 
    color: #1A2B24; 
    margin-bottom: 6px; 
    font-weight: 700;
  }
  .header p { 
    font-size: 11px; 
    color: #6B7280; 
  }
  .summary-table { 
    width: 100%; 
    text-align: center; 
    margin-bottom: 20px; 
    border-collapse: collapse;
  }
  .summary-table td { 
    border: 1px solid #E5E7EB; 
    padding: 12px 8px; 
    border-radius: 8px; 
    width: 20%; 
    background-color: #F9FAFB;
  }
  .summary-table .num { 
    font-family: 'Playfair Display', serif;
    font-size: 22px; 
    font-weight: 600; 
    margin-bottom: 4px;
  }
  .summary-table .lbl { 
    font-size: 10px; 
    color: #6B7280; 
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  table.data-table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 8px; 
  }
  .data-table thead tr { 
    background: #4F6560; 
    color: white; 
  }
  .data-table thead th { 
    padding: 8px; 
    text-align: left; 
    font-size: 11px; 
    font-weight: 600; 
    font-family: 'Playfair Display', serif;
    letter-spacing: 0.3px;
  }
  .data-table tbody tr:nth-child(even) { 
    background: #F3F4F6; 
  }
  .data-table tbody td { 
    padding: 7px 8px; 
    border-bottom: 1px solid #E5E7EB; 
    font-size: 10px; 
  }
  .footer { 
    margin-top: 24px; 
    font-size: 9px; 
    color: #9CA3AF; 
    text-align: right; 
    border-top: 1px solid #E5E7EB;
    padding-top: 8px;
  }
</style>
</head>
<body>

<div class="header">
  <h2>REKAP ABSENSI KARYAWAN BULANAN</h2>
  <p>HR Sinergi Hotel &amp; Villa &nbsp;|&nbsp; Periode: {{ \Carbon\Carbon::parse($bulan . '-01')->format('F Y') }}</p>
</div>

<table class="summary-table">
  <tr>
    <td>
      <div class="num" style="color:#1A2B24;">{{ $ringkasan['total_karyawan'] }}</div>
      <div class="lbl">Total Karyawan</div>
    </td>
    <td>
      <div class="num" style="color:#2E7D5E;">{{ $ringkasan['total_hadir'] }}</div>
      <div class="lbl">Total Hari Hadir</div>
    </td>
    <td>
      <div class="num" style="color:#991B1B;">{{ $ringkasan['total_alfa'] }}</div>
      <div class="lbl">Total Tidak Hadir</div>
    </td>
    <td>
      <div class="num" style="color:#92400E;">{{ $ringkasan['total_terlambat_kali'] }}</div>
      <div class="lbl">Total Terlambat (Kali)</div>
    </td>
    <td>
      <div class="num" style="color:#1E40AF;">{{ $ringkasan['total_lembur_jam'] }}</div>
      <div class="lbl">Total Lembur (Jam)</div>
    </td>
  </tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th width="30">No</th>
      <th width="150">Nama Karyawan</th>
      <th width="100">Departemen</th>
      <th width="80">Hari Dibutuhkan</th>
      <th width="60">Hadir</th>
      <th width="70">Tidak Hadir</th>
      <th width="70">Terlambat (Kali)</th>
      <th width="80">Terlambat (Menit)</th>
      <th width="70">Lembur (Jam)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rekapList as $i => $absensi)
    @php $data = $absensi->rekap; @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $absensi->user?->name ?? '-' }}</td>
      <td>{{ $absensi->user?->profile?->jabatan ?? '-' }}</td>
      <td>{{ $data['hari_dibutuhkan'] ?? 0 }}</td>
      <td>{{ $data['hari_hadir'] ?? 0 }}</td>
      <td>{{ $data['hari_tidak_hadir'] ?? 0 }}</td>
      <td>{{ $data['terlambat_count'] ?? 0 }}</td>
      <td>{{ $data['terlambat_menit'] ?? 0 }}</td>
      <td>{{ isset($data['lembur_menit']) ? round($data['lembur_menit']/60, 2) : 0 }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="9" style="text-align:center;padding:20px;color:#aaa;">Tidak ada data rekap absensi</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  Digenerate otomatis oleh sistem HR Sinergi &bull; {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
