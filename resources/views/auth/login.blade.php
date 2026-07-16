  <!doctype html>
  <html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMORA</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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

          <div class="org-logos">
            <div class="logo-circle logo-mpk">
             <img src="{{ asset('assets/images/mpk.png') }}">
            </div>

            <div class="logo-row">
              <div class="logo-circle logo-osis">
               <img src="{{ asset('assets/images/osis.png') }}" alt="OSIS">
              </div>
              <div class="logo-circle logo-school">
                <img src="{{ asset('assets/images/sangtasih.png') }}">
              </div>
            </div>
          </div>
        </div>

        <div class="left-decor">
          <span class="small-dot"></span>
          <span class="big-cut"></span>
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
              <input id="password" name="password" class="form-input" type="password" placeholder="" autocomplete="current-password">

              <button type="submit" class="btn-submit">Login</button>
            </form>
          </div>
        </div>
      </section>
    </main>

    <script src="{{ asset('js/script.js') }}"></script>
  </body>
  </html>
