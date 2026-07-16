<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Keluar - SIMORA</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo-tab.svg') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/stylelogin.css') }}">
  
  {{-- Lucide Icons --}}
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

  <style>
    /* Custom icon wrapper inside card */
    .logout-icon-wrapper {
      width: 72px;
      height: 72px;
      background: #FFF1F2;
      color: #E62129;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 4px 12px rgba(245, 45, 48, 0.08);
    }
    .logout-desc {
      font-size: 14px;
      color: #6B7280;
      line-height: 1.6;
      margin-bottom: 28px;
      font-weight: 300;
      text-align: center;
    }
  </style>
</head>
<body>
  <main class="page-wrapper">
    <section class="panel left-panel">
      <header class="left-header">
        <img src="{{ asset('assets/images/SIMORA.PNG') }}"
           alt="SIMORA"
           class="logo-simora">
      </header>

      <div class="left-content">
        <h1 class="welcome">Sampai<br>Jumpa</h1>
      </div>

      {{-- Orbiting Logo Circle --}}
      <div class="orbit-container">
        <div class="orbit-center"></div>
        <div class="orbit-ring">
          @php
            $orbitLogos = [
              ['file' => 'osis.png',      'label' => 'OSIS'],
              ['file' => 'mpk.png',       'label' => 'MPK'],
              ['file' => 'sangtasih.png', 'label' => 'Sangtasih'],
              ['file' => 'BDI.png',       'label' => 'BDI'],
              ['file' => 'KOMDIS.jpg',    'label' => 'KOMDIS'],
              ['file' => 'PASTEMDA.png',  'label' => 'PASTEMDA'],
              ['file' => 'PMR.jpg',       'label' => 'PMR'],
            ];
          @endphp
          @foreach($orbitLogos as $i => $logo)
            <div class="orbit-item" style="--i:{{ $i }}; --total:7;">
              <div class="orbit-logo">
                <img src="{{ asset('assets/images/' . $logo['file']) }}" alt="{{ $logo['label'] }}">
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="left-decor">
        <span class="small-dot"></span>
      </div>
    </section>

    <section class="panel right-panel">
      <div class="right-decor"></div>

      <div class="card-wrapper">
        <div class="login-card">
          <h2 class="card-title">Telah Keluar</h2>

          <div class="logout-icon-wrapper">
            <i data-lucide="log-out" style="width: 28px; height: 28px;"></i>
          </div>

          <p class="logout-desc">
            Terima kasih telah menggunakan SIMORA.<br>Anda telah keluar dari akun Anda dengan aman.
          </p>

          <a href="{{ route('login') }}" class="btn-submit" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
            Masuk Kembali
          </a>
        </div>
      </div>
    </section>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
