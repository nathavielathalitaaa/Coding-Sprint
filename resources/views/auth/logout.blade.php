<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluar | SIMORA SMK Telkom Sidoarjo</title>
    <link rel="icon" type="image/svg+xml" href="{{ URL::to('assets/images/logo-tab.svg') }}">
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #F7F7F7;
        }

        #main-content {
            display: flex;
            width: 100%;
            min-height: 100vh;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        #main-content.show {
            opacity: 1;
        }

        /* ── KIRI ── */
        .left-panel {
            flex: 1.1;
            background: #E62129;
            position: relative;
            overflow: hidden;
            padding: 60px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px 10%;
            position: relative;
            overflow: hidden;
        }

        .brand-tag {
            position: absolute;
            top: 65px;
            left: 10%;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.02em;
            z-index: 5;
        }

        .left-brand {
            position: relative;
            z-index: 5;
            margin-bottom: 20px;
            margin-top: 50px;
        }

        .left-brand h1 {
            font-size: 60px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        /* Dekorasi Lingkaran Putih (Kiri Bawah) */
        .ring-bottom-left {
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            border: 55px solid #FFFFFF;
            z-index: 1;
        }

        /* Cluster Logo & Titik Putih */
        .illustration-cluster {
            position: relative;
            width: 250px;
            height: 250px;
            margin-top: 100px;
            margin-left: 58%;
            z-index: 5;
        }

        .logo-badge {
            position: absolute;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        
        .logo-badge img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        .badge-mpk { 
            width: 110px; height: 110px; 
            top: 0; left: 80px; 
            z-index: 3; 
        }
        .badge-osis { 
            width: 100px; height: 100px; 
            top: 100px; left: -10px; 
            z-index: 2; 
        }
        .badge-sangtasih { 
            width: 75px; height: 75px; 
            top: 150px; left: 130px; 
            z-index: 4; 
        }

        .dot-decor {
            position: absolute;
            background: #fff;
            border-radius: 50%;
        }
        .dot-1 { width: 18px; height: 18px; top: 130px; left: 110px; }
        .dot-2 { width: 34px; height: 34px; top: 220px; left: 40px; }

        /* ── KANAN ── */
        .right-panel {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #F7F7F7;
            overflow: hidden;
        }

        /* Dekorasi Lingkaran Merah (Kanan Atas) */
        .right-panel::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 400px;
            height: 400px;
            border: 65px solid #E62129;
            border-radius: 50%;
            z-index: 1;
        }

        .logout-card {
            position: relative;
            z-index: 5;
            background: #FFFFFF;
            border-radius: 24px;
            padding: 50px 48px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            text-align: center;
        }

        .logout-card h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            color: #111;
            margin-bottom: 24px;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #FFF1F2;
            color: #E62129;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 4px 12px rgba(230,33,41,0.08);
        }

        .logout-card p {
            font-size: 14px;
            color: #6B7280;
            line-height: 1.6;
            margin-bottom: 36px;
            font-weight: 300;
        }

        .btn-signin {
            width: 100%;
            background: #E62129;
            color: white;
            border: none;
            border-radius: 9999px;
            padding: 16px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-signin:hover {
            background: #D01C24;
        }
        .btn-signin:active {
            transform: scale(0.99);
        }

        .footer-text {
            margin-top: 32px;
            font-size: 11px;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ── RESPONSIVE MOBILE ── */
        @media (max-width: 1024px) {
            .left-panel { padding: 40px; flex: 1; }
            .left-brand h1 { font-size: 48px; }
            .illustration-cluster { margin-left: 0; transform: scale(0.85); transform-origin: left top; }
            .logout-card { padding: 40px 32px; }
        }

        @media (max-width: 768px) {
            #main-content {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .left-panel {
                flex: none;
                min-height: 350px;
                padding: 40px 24px;
                border-radius: 0 0 32px 32px;
                align-items: center;
                text-align: center;
            }

            .brand-tag { position: relative; top: 0; left: 0; margin-bottom: 20px; }
            .left-brand h1 { font-size: 40px; }
            
            .illustration-cluster { display: none; }
            
            .ring-bottom-left { 
                width: 250px; height: 250px; 
                bottom: -100px; left: -100px; 
                border-width: 35px; 
            }

            .right-panel {
                flex: none;
                padding: 40px 20px;
            }
            .right-panel::before {
                width: 300px; height: 300px;
                border-width: 45px;
                top: -80px; right: -80px;
            }

            .logout-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    <div id="main-content">

        {{-- PANEL KIRI --}}
        <div class="left-panel">
            <div class="ring-bottom-left"></div>

            <div class="brand-tag">
                <img src="{{ asset('assets/images/SIMORA.png') }}" alt="Logo SIMORA" style="height: 36px; width: auto; object-fit: contain; margin: 0 auto; display: block;">
            </div>

            <div class="left-brand">
                <h1>Sampai<br>Jumpa</h1>
            </div>

            <div class="illustration-cluster">
                <a href="" class="logo-badge badge-mpk">
                    <img src="{{ asset('assets/images/MPK.jpg') }}" alt="Logo MPK">
                </a>
                <a href="" class="logo-badge badge-osis">
                    <img src="{{ asset('assets/images/OSIS.jpg') }}" alt="Logo OSIS">
                </a>
                <a href="" class="logo-badge badge-sangtasih">
                    <img src="{{ asset('assets/images/ambalan.jpg') }}" alt="Logo Sangtasih">
                </a>
                
                {{-- Titik putih --}}
                <div class="dot-decor dot-1"></div>
                <div class="dot-decor dot-2"></div>
            </div>
        </div>

        {{-- PANEL KANAN --}}
        <div class="right-panel">
            <div class="logout-card">
                <h2>Telah Keluar</h2>

                <div class="icon-circle">
                    <i data-lucide="log-out" style="width: 32px; height: 32px;"></i>
                </div>

                <p>Terima kasih telah menggunakan SIMORA.<br>Anda telah keluar dari akun Anda dengan aman.</p>

                <a href="{{ route('login') }}" class="btn-signin">
                    Kembali Ke Halaman Masuk
                </a>

                <div class="footer-text">
                    SIMORA SMK Telkom Sidoarjo &middot; Sistem Persuratan
                </div>
            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();

        window.addEventListener('load', function () {
            const mainContent = document.getElementById('main-content');
            requestAnimationFrame(function () {
                mainContent.classList.add('show');
            });
        });
    </script>
</body>
</html>
