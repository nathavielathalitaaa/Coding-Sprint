<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding — SIMORA SMK Telkom Sidoarjo</title>

    <link rel="icon" type="image/svg+xml" href="{{ URL::to('assets/images/logo-tab.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --color-primary: #E62129;
            --color-primary-dark: #C91A20;
            --color-surface: #FFFFFF;
            --color-bg-light: #F5F5F7;
            --color-text: #111111;
            --color-text-muted: #6B7280;
            --color-border: #E5E7EB;
            --radius-card: 28px;
            --radius-input: 9999px;
            --radius-pill: 9999px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-width: 900px;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: row;
            overflow-x: auto;
            overflow-y: hidden;
            background: linear-gradient(145deg, #F5C6C8 0%, var(--color-primary) 50%, var(--color-text) 100%);
        }

        /* ── KIRI ── */
        .left-panel {
            flex: 1.4;
            min-width: 420px;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 52px 40px;
            position: relative;
            overflow: hidden;
            height: 100vh;
        }

        /* ── KANAN ── */
        .right-panel {
            flex: 1;
            min-width: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            background: transparent;
            overflow-y: auto;
            height: 100vh;
        }        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.15);
            top: -100px; right: -150px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.1);
            bottom: -80px; left: -80px;
        }

        .left-brand {
            text-align: center;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            justify-content: center;
        }

        .left-brand h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: white;
            line-height: 1.15;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .left-brand h1 em {
            font-style: italic;
            font-weight: 400;
        }

        .left-brand p {
            font-size: 13px;
            color: rgba(255,255,255,0.78);
            font-weight: 300;
            letter-spacing: 0.01em;
            max-width: 320px;
            line-height: 1.65;
            margin-bottom: 0;
        }

        .deco-card {
            position: absolute;
            bottom: 48px; left: 48px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1;
        }
        .deco-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
        }
        .deco-text p { font-size: 13px; color: white; font-weight: 500; margin: 0; }
        .deco-text span { font-size: 11px; color: rgba(255,255,255,0.65); }

        /* ── CARD ── */
        .ob-card {
            background: #FFFFFF;
            border-radius: 24px;
            padding: 28px 28px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.18);
            /* Tidak stretch ke full height */
            align-self: center;
        }

        .ob-card h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .ob-card .subtitle {
            font-size: 12px;
            font-weight: 300;
            color: #6B7280;
            margin-bottom: 6px;
        }

        /* ── PROGRESS STEPS ── */
        .steps {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 32px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .step-item:last-child { flex: 0; }

        .step-circle {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid #E5E7EB;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 500;
            color: #9CA3AF;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .step-item.active .step-circle {
            background: var(--color-text);
            border-color: var(--color-text);
            color: white;
        }

        .step-item.done .step-circle {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .step-label {
            font-size: 12px;
            font-weight: 500;
            color: #9CA3AF;
        }
        .step-item.active .step-label,
        .step-item.done .step-label { color: var(--color-text); }

        .step-line {
            flex: 1;
            height: 1px;
            background: #E5E7EB;
            margin: 0 12px;
        }
        .step-line.done { background: var(--color-primary); }

        /* ── FORM ELEMENTS ── */
        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .hv-input {
            width: 100%;
            background: #F0F4F2;
            border: none;
            border-radius: 9999px;
            padding: 13px 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #111111;
            outline: none;
            transition: box-shadow 0.2s;
            letter-spacing: 0.05em;
        }
        .hv-input:focus { box-shadow: 0 0 0 2px var(--color-primary); }
        .hv-input::placeholder { color: #9CA3AF; letter-spacing: 0; }

        /* ── UPLOAD AREA ── */
        .upload-area {
            border: 2px dashed var(--color-border);
            border-radius: 16px;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--color-bg-light);
        }
        .upload-area:hover {
            border-color: var(--color-primary);
            background: #FEE2E2;
        }
        .upload-area input { display: none; }
        .upload-icon {
            width: 44px; height: 44px;
            background: var(--color-bg-light);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            color: var(--color-text);
        }
        .upload-title {
            font-size: 14px;
            font-weight: 500;
            color: #111111;
            margin-bottom: 4px;
        }
        .upload-sub {
            font-size: 12px;
            color: #6B7280;
            font-weight: 300;
        }

        /* ── PREVIEW TTD ── */
        #ttd-preview {
            max-height: 100px;
            object-fit: contain;
            margin-top: 16px;
            border-radius: 8px;
            display: none;
            margin-left: auto;
            margin-right: auto;
        }

        .ttd-saved-box {
            background: var(--color-bg-light);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin-bottom: 24px;
        }
        .ttd-saved-box img {
            max-height: 100px;
            object-fit: contain;
            display: block;
            margin: 0 auto 10px;
        }
        .ttd-saved-label {
            font-size: 11px;
            font-weight: 500;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
        .ttd-check {
            font-size: 12px;
            color: var(--color-text);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* ── INFO BOX ── */
        .info-box {
            background: var(--color-bg-light);
            border-left: 3px solid var(--color-primary);
            border-radius: 0 10px 10px 0;
            padding: 12px 16px;
            font-size: 12px;
            color: var(--color-text);
            margin-bottom: 20px;
            font-weight: 300;
            line-height: 1.5;
        }

        /* ── ERROR ── */
        .error-msg {
            background: #fee2e2;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: #991b1b;
            margin-top: 6px;
        }

        /* ── BUTTON ── */
        .btn-submit {
            width: 100%;
            background: var(--color-text);
            color: white;
            border: none;
            border-radius: 9999px;
            padding: 14px 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { background: #222222; }
        .btn-submit:active { transform: scale(0.99); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }

    </style>
</head>
<body>

{{-- PANEL KIRI --}}
<div class="left-panel">

    {{-- Tengah: judul, deskripsi, dan logo --}}
    <div class="left-brand">
        <h1>SIMORA</h1>
        <p style="font-size:11px;color:rgba(255,255,255,0.6);font-weight:500;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:12px;">Sistem Informasi Manajemen Organisasi & Persuratan</p>
        <p>Sistem Persuratan Terpadu untuk operasional organisasi siswa SMK Telkom Sidoarjo yang lebih efisien dan terstruktur.</p>

        {{-- Lingkaran logo — tepat di bawah teks --}}
        <div style="display:flex;flex-wrap:nowrap;align-items:center;justify-content:center;gap:10px;margin-top:32px;width:100%;">
            @foreach([
                ['osis.png',    'OSIS'],
                ['mpk.png',     'MPK'],
                ['sangtasih.png','Sangtasih'],
                ['BDI.png',     'BDI'],
                ['KOMDIS.jpg',  'Komdis'],
                ['PASTEMDA.png','Pastemda'],
                ['PMR.jpg',     'PMR'],
            ] as [$file,$label])
            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;min-width:0;">
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,0.92);border:2px solid rgba(255,255,255,0.55);overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:transform .2s;position:relative;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="{{ URL::to('assets/images/'.$file) }}" alt="{{ $label }}" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:52px;height:52px;object-fit:contain;">
                </div>
                <span style="font-size:8px;font-weight:600;color:rgba(255,255,255,0.85);letter-spacing:0.03em;text-transform:uppercase;text-align:center;white-space:nowrap;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>{{-- /left-panel --}}

{{-- PANEL KANAN --}}
<div class="right-panel">
    <div class="ob-card">

        <h2>Selamat Datang,<br>{{ $user->name }}</h2>
        <p class="subtitle">Lengkapi profil Anda sebelum mulai menggunakan sistem</p>
        <p style="font-family:'Poppins',sans-serif;font-size:11px;color:#9CA3AF;font-style:italic;margin-top:2px;margin-bottom:20px;line-height:1.5;">Tanda tangan digital dan PIN digunakan untuk memverifikasi persetujuan dokumen resmi.</p>

        {{-- Progress Steps --}}
        <div class="steps">
            <div class="step-item {{ $step === 'ttd' ? 'active' : ($user->ttd_path ? 'done' : '') }}">
                <div class="step-circle">
                    @if($user->ttd_path)
                        <i data-lucide="check" style="width:14px;height:14px;"></i>
                    @else
                        1
                    @endif
                </div>
                <span class="step-label">Tanda Tangan</span>
            </div>
            <div class="step-line {{ $user->ttd_path ? 'done' : '' }}"></div>
            <div class="step-item {{ $step === 'pin' ? 'active' : ($user->pin ? 'done' : '') }}">
                <div class="step-circle">
                    @if($user->pin)
                        <i data-lucide="check" style="width:14px;height:14px;"></i>
                    @else
                        2
                    @endif
                </div>
                <span class="step-label">PIN Approval</span>
            </div>
        </div>

        {{-- STEP 1: Upload TTD --}}
        @if($step === 'ttd')
        <div x-data="{ loading: false }">
            <form method="POST" action="{{ route('onboarding.ttd') }}" enctype="multipart/form-data" @submit="loading = true">
                @csrf

                <div class="form-group">
                    <label>Unggah Tanda Tangan Digital</label>
                    <div class="upload-area" onclick="document.getElementById('ttd').click()">
                        <div class="upload-icon">
                            <i data-lucide="upload" style="width:20px;height:20px;"></i>
                        </div>
                        <p class="upload-title">Klik untuk mengunggah file</p>
                        <p class="upload-sub">Format PNG, JPG, JPEG · Maks. 2MB</p>
                        <input type="file" id="ttd" name="ttd" accept=".png,.jpg,.jpeg"
                            @change="
                                const file = $el.files[0];
                                if (!file) return;
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = document.getElementById('ttd-preview');
                                    img.src = e.target.result;
                                    img.style.display = 'block';
                                };
                                reader.readAsDataURL(file);
                            ">
                        <img id="ttd-preview" alt="Preview TTD">
                    </div>
                    @error('ttd')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="info-box">
                    Tanda tangan digital Anda akan digunakan pada dokumen resmi yang Anda setujui. Gunakan tanda tangan asli dengan latar belakang transparan (PNG) untuk hasil terbaik.
                </div>

                <button class="btn-submit" type="submit" :disabled="loading">
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan & Lanjutkan'"></span>
                    <i data-lucide="arrow-right" style="width:16px;height:16px;" x-show="!loading"></i>
                </button>
            </form>
        </div>
        @endif

        {{-- STEP 2: Set PIN --}}
        @if($step === 'pin')
        <div x-data="{ loading: false }">
            <div class="ttd-saved-box">
                <p class="ttd-saved-label">Tanda Tangan Tersimpan</p>
                <img src="{{ route('profile.ttd.preview') }}" alt="Tanda Tangan">
                <div class="ttd-check">
                    <i data-lucide="check-circle" style="width:14px;height:14px;"></i>
                    Tanda tangan berhasil disimpan
                </div>
            </div>

            <form method="POST" action="{{ route('onboarding.pin') }}" @submit="loading = true">
                @csrf

                <div class="form-group">
                    <label>PIN Approval (6 digit)</label>
                    <input type="password" name="pin" class="hv-input"
                           maxlength="6" required placeholder="••••••"
                           style="letter-spacing: 6px; text-align: center; font-size: 18px;">
                    @error('pin')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi PIN</label>
                    <input type="password" name="pin_confirmation" class="hv-input"
                           maxlength="6" required placeholder="••••••"
                           style="letter-spacing: 6px; text-align: center; font-size: 18px;">
                    @error('pin_confirmation')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="info-box">
                    PIN 6 digit ini digunakan untuk mengkonfirmasi tanda tangan digital Anda saat menyetujui dokumen. Jangan bagikan PIN kepada siapapun.
                </div>

                <button class="btn-submit" type="submit" :disabled="loading">
                    <span x-text="loading ? 'Memproses...' : 'Selesai & Masuk Sistem'"></span>
                    <i data-lucide="log-in" style="width:16px;height:16px;" x-show="!loading"></i>
                </button>
            </form>
        </div>
        @endif

    </div>
</div>

<script>
    lucide.createIcons();

    // PIN: hanya angka
    document.querySelectorAll('input[maxlength="6"]').forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
        });
        input.addEventListener('keypress', (e) => {
            if (!/[0-9]/.test(e.key)) e.preventDefault();
        });
    });
</script>

</body>
</html>
