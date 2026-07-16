  <!doctype html>
  <html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMORA</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo-tab.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylelogin.css') }}">
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
          <h1 class="welcome">Selamat<br>Datang</h1>
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
            <h2 class="card-title">Login</h2>

            <form id="loginForm" class="login-form" action="{{ route('login') }}" method="POST" novalidate>
              @csrf
              <label for="email" class="form-label">Email</label>
              <input id="email" name="email" class="form-input" type="email" placeholder="" autocomplete="email">

              <label for="password" class="form-label">Password</label>
              <div class="password-wrapper">
                <input id="password" name="password" class="form-input" type="password" placeholder="" autocomplete="current-password">
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Lihat password">
                  <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>

              <button type="submit" class="btn-submit">Login</button>
            </form>
          </div>
        </div>
      </section>
    </main>

    <script>
      const toggleBtn = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      const iconEye = document.getElementById('icon-eye');
      const iconEyeOff = document.getElementById('icon-eye-off');
      if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
          const isPassword = passwordInput.type === 'password';
          passwordInput.type = isPassword ? 'text' : 'password';
          iconEye.style.display = isPassword ? 'none' : '';
          iconEyeOff.style.display = isPassword ? '' : 'none';
          toggleBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
        });
      }
    </script>
  </body>
  </html>

