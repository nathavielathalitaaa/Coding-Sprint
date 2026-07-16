<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SIMORA')</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/simora.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div class="app-shell">
    @include('components.sidebar')

    <div class="main-shell">
      <button class="mobile-nav-toggle" type="button" aria-label="Buka menu">
        <i class="fa-solid fa-bars"></i>
      </button>

      <main class="main-content">
        @yield('content')
      </main>
    </div>
  </div>

  <script src="{{ asset('js/simora.js') }}"></script>
</body>
</html>
