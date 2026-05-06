<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: {{ $settings['font_family'] }}, sans-serif; font-size: 12px; color: #1a1a1a; margin: 0; padding: 30px; }
  .header { text-align: center; border-bottom: 2px solid {{ $settings['accent_color'] }}; padding-bottom: 16px; margin-bottom: 24px; }
  .header h2 { font-size: 16px; margin: 0 0 4px; color: {{ $settings['accent_color'] }}; }
  .header p { margin: 2px 0; font-size: 11px; color: #555; }
  .logo-box { margin-bottom: 12px; }
  .logo-box img { max-height: 50px; }
  .info-grid { display: table; width: 100%; margin-bottom: 24px; }
  .info-row { display: table-row; }
  .info-label { display: table-cell; width: 140px; font-weight: bold; padding: 3px 0; color: #555; font-size: 11px; }
  .info-value { display: table-cell; padding: 3px 0; font-size: 11px; }
  .ttd-section { margin-top: 32px; }
  .ttd-section h3 { font-size: 12px; font-weight: bold; color: {{ $settings['accent_color'] }}; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 16px; }
  .ttd-grid { display: table; width: 100%; table-layout: fixed; }

  .ttd-col {
      display: table-cell;
      text-align: center;
      vertical-align: top;
      padding: 0 6px;
      border-right: 1px solid #f0f0f0;
  }
  .ttd-col:last-child { border-right: none; }

  .ttd-label { font-size: 10px; font-weight: bold; color: #444; margin-bottom: 8px; }

  /* Kotak TTD — border solid, display:table agar DomPDF render center */
  .ttd-box {
      width: 130px; height: 82px;
      border: 1.5px solid #9ca3af;
      border-radius: 4px;
      display: table;
      margin: 0 auto 8px auto;
  }
  .ttd-box-inner {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
  }
  .ttd-box-inner img { max-width: 120px; max-height: 72px; }

  /* Kotak kosong jika belum TTD */
  .ttd-empty {
      width: 130px; height: 82px;
      border: 1.5px dashed #d1d5db;
      border-radius: 4px;
      display: table;
      margin: 0 auto 8px auto;
  }
  .ttd-empty-inner {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
      font-size: 9px;
      color: #9ca3af;
  }

  .ttd-name { font-size: 10px; font-weight: bold; border-top: 1px solid #333; padding-top: 4px; margin-top: 4px; }
  .ttd-date { font-size: 9px; color: #888; margin-top: 2px; }
  .ttd-note { font-size: 9px; color: #888; margin-top: 2px; font-style: italic; }
  .footer { margin-top: 40px; font-size: 9px; color: #aaa; text-align: center; border-top: 1px solid #f0f0f0; padding-top: 12px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; background: #dcfce7; color: #166534; }
</style>
</head>
<body>

<div class="header">
  @if($logo_base64)
    <div class="logo-box">
      <img src="{{ $logo_base64 }}" alt="Logo">
    </div>
  @endif
  <h2>LEMBAR PERSETUJUAN DOKUMEN</h2>
  <p><strong>{{ $surat->nomor_surat }}</strong></p>
  <p>{{ $settings['company_name'] }}</p>
</div>

<div class="info-grid">
  <div class="info-row">
    <div class="info-label">Jenis Dokumen</div>
    <div class="info-value">: {{ ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Perihal</div>
    <div class="info-value">: {{ $surat->perihal }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Pembuat</div>
    <div class="info-value">: {{ $surat->user->name ?? '-' }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Tanggal Dibuat</div>
    <div class="info-value">: {{ $surat->created_at->format('d M Y') }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Status</div>
    <div class="info-value">: <span class="badge">Disetujui Penuh</span></div>
  </div>
</div>

<div class="ttd-section">
  <h3>TANDA TANGAN PERSETUJUAN</h3>
  <div class="ttd-grid">
    @foreach($steps as $step)
    <div class="ttd-col">
        <div class="ttd-label">{{ $step['label'] }}</div>

        @if($step['ttd_base64'])
            <div class="ttd-box">
                <div class="ttd-box-inner">
                    <img src="{{ $step['ttd_base64'] }}" alt="TTD">
                </div>
            </div>
        @else
            <div class="ttd-empty">
                <div class="ttd-empty-inner">Belum TTD</div>
            </div>
        @endif

        <div class="ttd-name">{{ $step['name'] }}</div>
        <div class="ttd-date">{{ $step['actioned_at'] ?? '-' }}</div>
        @if($step['catatan'])
            <div class="ttd-note">"{{ $step['catatan'] }}"</div>
        @endif
    </div>
    @endforeach
  </div>
</div>

<div class="footer">
  {{ $settings['footer_text'] }} &bull; {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
