<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SIMORA</title>
    <link rel="icon" type="image/svg+xml" href="{{ URL::to('assets/images/logo-tab.svg') }}">
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        /* ══════════ SKELETON LOADER ══════════ */
        #skeleton-overlay {
            position: fixed;
            inset: 0;
            z-index: 999;
            display: flex;
            background: #F7F7F7;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        #skeleton-overlay.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .sk-left {
            flex: 1.1;
            background: #E62129;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .sk-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .sk-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 28px;
            padding: 48px 44px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.05);
        }

        .sk-block {
            background: linear-gradient(90deg, rgba(255,255,255,0.2) 25%, rgba(255,255,255,0.4) 37%, rgba(255,255,255,0.2) 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s ease infinite;
            border-radius: 10px;
        }
        .sk-card .sk-block {
            background: linear-gradient(90deg, #eee 25%, #f5f5f5 37%, #eee 63%);
            background-size: 400% 100%;
        }

        @keyframes shimmer {
            0% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .sk-title { width: 50%; height: 44px; margin-bottom: 20px; }
        .sk-title-center { width: 30%; height: 34px; margin: 0 auto 32px auto; }
        .sk-line { height: 14px; margin-bottom: 10px; }
        .sk-line.w1 { width: 80%; }
        .sk-line.w2 { width: 60%; }

        .sk-circles { position: absolute; top: 50%; left: 30%; display: flex; flex-direction: column; gap: 16px; }
        .sk-circles .sk-block { border-radius: 50%; }
        .sk-c1 { width: 110px; height: 110px; }
        .sk-c2 { width: 90px; height: 90px; }

        .sk-label { width: 25%; height: 12px; margin-bottom: 12px; }
        .sk-field { width: 100%; height: 50px; border-radius: 999px; margin-bottom: 24px; }
        .sk-btn { width: 100%; height: 50px; border-radius: 9999px; margin-top: 10px; }

        @media (max-width: 768px) {
            .sk-left { display: none; }
        }

        /* ══════════ MAIN CONTENT ══════════ */
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
            top: 65px; /* Sebelumnya 40px - menggeser SIMORA ke bawah */
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
            margin-top: 50px; /* Menambahkan margin-top agar tulisan Selamat Datang ikut turun ke bawah */
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

        /* Cluster Logo & Titik Putih Persis Seperti Desain */
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
            /* Hover dihapus sesuai permintaan */
        }
        
        .logo-badge img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        /* Tata letak persis sesuai gambar */
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

        /* Titik-titik Putih Dekoratif Sesuai Gambar */
        .dot-decor {
            position: absolute;
            background: #fff;
            border-radius: 50%;
        }
        .dot-1 { width: 18px; height: 18px; top: 130px; left: 110px; } /* Titik sedang dekat OSIS/SMK */
        .dot-2 { width: 34px; height: 34px; top: 220px; left: 40px; }  /* Titik besar di bawah OSIS */

        /* ── KANAN ── */
        .right-panel {
            position: relative;
            flex: 1;
            position: relative;
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

        .login-card {
            position: relative;
            z-index: 5;
            background: #FFFFFF;
            border-radius: 24px;
            padding: 50px 48px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        }

        .login-card h2 {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            color: #111;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 24px;
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-size: 13px;
            font-weight: 500;
            color: #111;
            color: #111;
            margin-bottom: 8px;
            margin-left: 2px;
        }

        .input-wrap {
            position: relative;
        }

        /* Input pill-shaped persis gambar */
        .hv-input {
            width: 100%;
            background: #E8E8E8;
            border: none;
            border-radius: 9999px; /* Pill shape sempurna */
            padding: 16px 48px 16px 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #111;
            color: #111;
            outline: none;
        }
        
        .hv-input:focus {
            background: #E0E0E0;
        }

        /* Toggle password icon */
        .toggle-pass {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            border-radius: 9999px;
            padding: 14px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            color: #777;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-login:hover { background: #C91A20; }
        .btn-login:active { transform: scale(0.99); }

        /* Alerts */
        .alert-box {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .error-msg { background: #fee2e2; color: #991b1b; }
        .warn-msg  { background: #FEF3C7; color: #92400E; }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: #E62129;
            color: white;
            border: none;
            border-radius: 9999px; /* Pill shape */
            padding: 16px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #D01C24;
        }

        /* ── RESPONSIVE MOBILE ── */
        @media (max-width: 1024px) {
            .left-panel { padding: 40px; flex: 1; }
            .left-brand h1 { font-size: 48px; }
            .illustration-cluster { margin-left: 0; transform: scale(0.85); transform-origin: left top; }
            .login-card { padding: 40px 32px; }
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
            
            /* Sembunyikan hiasan logo di HP agar tidak terlalu penuh */
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

            .login-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    {{-- ══════════ SKELETON LOADER ══════════ --}}
    <div id="skeleton-overlay">
        <div class="sk-left">
            <div class="sk-block sk-title"></div>
            <div class="sk-block sk-line w1"></div>
            <div class="sk-block sk-line w2"></div>
            <div class="sk-circles">
                <div class="sk-block sk-c1"></div>
                <div class="sk-block sk-c2"></div>
            </div>
        </div>
        <div class="sk-right">
            <div class="sk-card">
                <div class="sk-block sk-title-center"></div>
                <div>
                    <div class="sk-block sk-label"></div>
                    <div class="sk-block sk-field"></div>
                    <div class="sk-block sk-label"></div>
                    <div class="sk-block sk-field"></div>
                    <div class="sk-block sk-btn"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ MAIN CONTENT ══════════ --}}
    <div id="main-content">

        {{-- PANEL KIRI --}}
        <div class="left-panel">
            <div class="ring-bottom-left"></div>

            <div class="brand-tag">
                <img src="{{ asset('assets/images/SIMORA.png') }}" alt="Logo SIMORA" style="height: 36px; width: auto; object-fit: contain; margin: 0 auto; display: block;">
            </div>

            <div class="left-brand">
                <h1>Selamat<br>Datang</h1>
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
            <div class="login-card">

                <h2>Login</h2>

                @if(session('session_expired'))
                <div class="alert-box warn-msg">
                    <i data-lucide="clock" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <p style="margin:0;">{{ session('session_expired') }}</p>
                </div>
                @endif

                @if($errors->any())
                <div class="alert-box error-msg">
                    <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <p style="margin:0;">{{ $errors->first() }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="alert-box error-msg">
                    <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
                    <p style="margin:0;">{{ session('error') }}</p>
                </div>
                @endif

                {{-- Form Login --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="hv-input" 
                                placeholder="Isi email kamu" 
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="hv-input" 
                                placeholder="Isi password kamu" 
                                required 
                                autocomplete="current-password">
                            <button type="button" class="toggle-pass" onclick="togglePassword()">
                                <i data-lucide="eye" id="eyeIcon" style="width:18px;height:18px;"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Login
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        window.addEventListener('load', function () {
            const skeletonOverlay = document.getElementById('skeleton-overlay');
            const mainContent = document.getElementById('main-content');

            setTimeout(function () {
                skeletonOverlay.classList.add('hide');

                requestAnimationFrame(function () {
                    mainContent.classList.add('show');
                });
            }, 500); 
        });
    </script>
</body>
</html>