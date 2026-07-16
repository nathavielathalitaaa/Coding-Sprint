<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan · SIMORA</title>
    <link rel="icon" type="image/svg+xml" href="{{ URL::to('assets/images/logo-tab.svg') }}">
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #F6F6F6 0%, #F5F5F7 60%, #ECECEC 100%);
            background-attachment: fixed;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        body::before {
            content: '';
            position: fixed; top: -120px; right: -120px;
            width: 480px; height: 480px; border-radius: 50%;
            background: radial-gradient(circle, rgba(230,33,41,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed; bottom: -80px; left: -80px;
            width: 360px; height: 360px; border-radius: 50%;
            background: radial-gradient(circle, rgba(230,33,41,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        .card {
            background: var(--color-surface); border-radius: var(--radius-card); padding: 52px 48px;
            max-width: 480px; width: 100%;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
            text-align: center; position: relative; z-index: 1;
        }
        .logo { width: 140px; height: auto; display: block; margin: 0 auto 36px; }
        .error-num {
            font-family: 'Poppins', sans-serif;
            font-size: 100px; font-weight: 700;
            color: var(--color-text); line-height: 1;
            margin-bottom: 4px; letter-spacing: -4px;
        }
        .error-num span { color: var(--color-primary); }
        .divider {
            width: 48px; height: 3px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-text));
            border-radius: 20px; margin: 20px auto;
        }
        .error-title {
            font-family: 'Poppins', sans-serif;
            font-size: 22px; font-weight: 700;
            color: var(--color-text); margin-bottom: 10px;
        }
        .error-desc {
            font-size: 13px; font-weight: 300;
            color: var(--color-text-muted); line-height: 1.7; margin-bottom: 32px;
        }
        .btn-group { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--color-text); color: #FFFFFF; border: none;
            border-radius: 999px; padding: 11px 24px;
            font-family: 'Poppins', sans-serif; font-size: 13px;
            font-weight: 500; text-decoration: none; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: var(--color-primary); color: var(--color-surface); }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 7px;
            background: transparent; color: var(--color-text);
            border: 1px solid #D1D5DB; border-radius: 999px;
            padding: 10px 24px; font-family: 'Poppins', sans-serif;
            font-size: 13px; font-weight: 400; text-decoration: none;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-outline:hover { border-color: var(--color-text); color: var(--color-text); }
        .btn-icon { width: 15px; height: 15px; stroke-width: 1.8; }
        .footer-note { margin-top: 28px; font-size: 11px; color: var(--color-text-muted); font-weight: 300; }
        @media (max-width: 480px) { .card { padding: 40px 28px; } .error-num { font-size: 72px; } }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ URL::to('assets/images/SIMORA.png') }}" alt="SIMORA SMK Telkom Sidoarjo" class="logo">
        <div class="error-num">4<span>0</span>4</div>
        <div class="divider"></div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
            Halaman yang Anda cari mungkin telah dihapus, diubah namanya, atau tidak tersedia untuk sementara waktu.
        </p>
        <div class="btn-group">
            <a href="{{ route('home') }}" class="btn-primary">
                <i data-lucide="layout-dashboard" class="btn-icon"></i>
                Kembali ke Dasbor
            </a>
            <a href="javascript:history.back()" class="btn-outline">
                <i data-lucide="arrow-left" class="btn-icon"></i>
                Halaman Sebelumnya
            </a>
        </div>
        <p class="footer-note">SIMORA · SIMORA SMK Telkom Sidoarjo Malang</p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
