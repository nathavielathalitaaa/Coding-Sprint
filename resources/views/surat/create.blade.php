@extends('layouts.master')
@section('content')

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-sans font-bold text-[#111111]">Buat Surat Baru</h1>
                <p class="text-[13px] font-light text-[#6B7280] mt-1">Isi formulir di bawah untuk mengajukan surat baru</p>
            </div>
            <a href="{{ route('surat.index') }}"
               class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
                Kembali
            </a>
        </div>

        {{-- Card --}}
        <div style="background: var(--color-surface); border-radius: var(--radius-card); padding: 36px; border: 1px solid var(--color-border); box-shadow: 0 4px 20px rgba(0,0,0,0.03);">

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl" style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;">
                    <p class="text-sm font-semibold text-red-800 mb-2">Terjadi Kesalahan:</p>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 32px; margin-bottom: 32px;">

                    {{-- Kolom Kiri: Form fields --}}
                    <div class="flex flex-col gap-6">

                        {{-- Nama Organisasi --}}
                        <div>
                            <label for="organisasi_id" class="block text-sm font-bold text-[#111111] mb-2">
                                Organisasi Pengaju <span class="text-red-500">*</span>
                            </label>
                            <select id="organisasi_id" name="organisasi_id"
                                style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; background: #ffffff; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px rgba(230,33,41,0.2)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                                onchange="handleOrganisasiChange(this)"
                                required>
                                <option value="">-- Pilih Organisasi --</option>
                                @foreach($organisasis as $org)
                                    <option value="{{ $org->id }}" data-tipe="{{ $org->tipe }}" @if(old('organisasi_id') == $org->id) selected @endif>
                                        {{ $org->nama }} ({{ $org->tipe_label }})
                                    </option>
                                @endforeach
                            </select>
                            @error('organisasi_id')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Jenis Surat --}}
                        <div>
                            <label for="surat_type_id" class="block text-sm font-bold text-[#111111] mb-2">
                                Jenis Surat <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_surat" name="surat_type_id" disabled
                                style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; background: #ffffff; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px rgba(230,33,41,0.2)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                                onchange="handleSuratTypeChange(this)"
                                required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                @foreach($suratTypes as $type)
                                    <option value="{{ $type->id }}"
                                        data-kode="{{ $type->kode }}"
                                        data-organisasi-tipe="{{ $type->organisasi_tipe ?? '' }}"
                                        data-requires-kegiatan-detail="{{ $type->requires_kegiatan_detail ? '1' : '0' }}"
                                        @if(old('surat_type_id') == $type->id) selected @endif>
                                        {{ $type->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('surat_type_id')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Komisi (khusus MPK) --}}
                        <div id="komisi-group" style="display:none;">
                            <label for="komisi_id" class="block text-sm font-bold text-[#111111] mb-2">
                                Komisi <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-gray-400 ml-1">(wajib untuk surat MPK)</span>
                            </label>
                            <select id="komisi_id" name="komisi_id"
                                style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; background: #ffffff; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px rgba(230,33,41,0.2)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                                <option value="">-- Pilih Komisi --</option>
                                @foreach($komisis as $k)
                                    <option value="{{ $k->id }}" @if(old('komisi_id') == $k->id) selected @endif>
                                        {{ $k->nama }} ({{ $k->organisasi->nama ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('komisi_id')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Perihal --}}
                        <div>
                            <label for="perihal" class="block text-sm font-bold text-[#111111] mb-2">
                                Perihal <span class="text-red-500">*</span>
                            </label>
                            <textarea id="perihal" name="perihal" rows="4"
                                style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; background: #ffffff; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px rgba(230,33,41,0.2)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                                placeholder="Jelaskan perihal surat Anda" required>{{ old('perihal') }}</textarea>
                            @error('perihal')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Detail Kegiatan (muncul bila requires_kegiatan_detail = true) --}}
                        <div id="kegiatan-detail-section" style="display:none;">
                            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:16px; padding:20px 24px;">
                                <div class="flex items-center gap-2 mb-4">
                                    <i data-lucide="calendar-range" style="width:16px;height:16px;color:var(--color-primary);"></i>
                                    <h3 style="font-family:'Poppins',sans-serif;font-weight:600;font-size:13px;color:var(--color-primary);margin:0;">
                                        Detail Kegiatan
                                    </h3>
                                    <span style="font-size:11px;color:#4ade80;margin-left:4px;">(digunakan untuk generate surat turunan)</span>
                                </div>

                                <div class="flex flex-col gap-4">

                                    {{-- Nama Kegiatan --}}
                                    <div>
                                        <label for="nama_kegiatan" class="block text-sm font-bold text-[#111111] mb-1.5">
                                            Nama Kegiatan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="nama_kegiatan" name="nama_kegiatan"
                                            value="{{ old('nama_kegiatan') }}"
                                            placeholder="cth. Pelantikan Pengurus OSIS 2026"
                                            style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1fae5;font-size:14px;background:#ffffff;outline:none;transition:all 0.2s;"
                                            onfocus="this.style.borderColor='#4ade80';this.style.boxShadow='0 0 0 2px rgba(74,222,128,0.2)'"
                                            onblur="this.style.borderColor='#d1fae5';this.style.boxShadow='none'">
                                        @error('nama_kegiatan')
                                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                        {{-- Warning badge duplikat --}}
                                        <div id="warn-duplikat" style="display:none;" class="mt-2 flex items-start gap-2 px-3 py-2.5 rounded-2xl border border-amber-200 bg-amber-50">
                                            <span style="font-size:13px;line-height:1;">⚠️</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-amber-800 mb-0.5">Mirip kegiatan lain, cek dulu</p>
                                                <p id="warn-duplikat-detail" class="text-[11px] text-amber-700 leading-relaxed"></p>
                                            </div>
                                            <button type="button" onclick="dismissWarn('warn-duplikat')"
                                                class="shrink-0 text-amber-400 hover:text-amber-600 text-base leading-none mt-0.5">✕</button>
                                        </div>
                                    </div>

                                    {{-- Tanggal Mulai & Selesai --}}
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                        <div>
                                            <label for="tanggal_mulai" class="block text-sm font-bold text-[#111111] mb-1.5">
                                                Tanggal Mulai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                                value="{{ old('tanggal_mulai') }}"
                                                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1fae5;font-size:14px;background:#ffffff;outline:none;transition:all 0.2s;"
                                                onfocus="this.style.borderColor='#4ade80';this.style.boxShadow='0 0 0 2px rgba(74,222,128,0.2)'"
                                                onblur="this.style.borderColor='#d1fae5';this.style.boxShadow='none'">
                                            @error('tanggal_mulai')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="tanggal_selesai" class="block text-sm font-bold text-[#111111] mb-1.5">
                                                Tanggal Selesai
                                                <span class="text-xs font-normal text-gray-400 ml-1">(opsional)</span>
                                            </label>
                                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                                value="{{ old('tanggal_selesai') }}"
                                                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1fae5;font-size:14px;background:#ffffff;outline:none;transition:all 0.2s;"
                                                onfocus="this.style.borderColor='#4ade80';this.style.boxShadow='0 0 0 2px rgba(74,222,128,0.2)'"
                                                onblur="this.style.borderColor='#d1fae5';this.style.boxShadow='none'">
                                            @error('tanggal_selesai')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Lokasi --}}
                                    <div>
                                        <label for="lokasi" class="block text-sm font-bold text-[#111111] mb-1.5">
                                            Lokasi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="lokasi" name="lokasi"
                                            value="{{ old('lokasi') }}"
                                            placeholder="cth. Aula Sekolah / GOR / Lapangan"
                                            style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1fae5;font-size:14px;background:#ffffff;outline:none;transition:all 0.2s;"
                                            onfocus="this.style.borderColor='#4ade80';this.style.boxShadow='0 0 0 2px rgba(74,222,128,0.2)'"
                                            onblur="this.style.borderColor='#d1fae5';this.style.boxShadow='none'">
                                        @error('lokasi')
                                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                        {{-- Warning badge konflik --}}
                                        <div id="warn-konflik" style="display:none;" class="mt-2 flex items-start gap-2 px-3 py-2.5 rounded-2xl border border-orange-200 bg-orange-50">
                                            <span style="font-size:13px;line-height:1;">🚫</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-bold text-orange-800 mb-0.5">Potensi konflik jadwal lokasi</p>
                                                <p id="warn-konflik-detail" class="text-[11px] text-orange-700 leading-relaxed"></p>
                                            </div>
                                            <button type="button" onclick="dismissWarn('warn-konflik')"
                                                class="shrink-0 text-orange-400 hover:text-orange-600 text-base leading-none mt-0.5">✕</button>
                                        </div>
                                    </div>

                                    {{-- Deskripsi Singkat --}}
                                    <div>
                                        <label for="deskripsi_singkat" class="block text-sm font-bold text-[#111111] mb-1.5">
                                            Deskripsi Singkat
                                            <span class="text-xs font-normal text-gray-400 ml-1">(opsional)</span>
                                        </label>
                                        <textarea id="deskripsi_singkat" name="deskripsi_singkat" rows="3"
                                            placeholder="Jelaskan secara singkat tujuan dan gambaran umum kegiatan"
                                            style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid #d1fae5;font-size:14px;background:#ffffff;outline:none;transition:all 0.2s;resize:vertical;"
                                            onfocus="this.style.borderColor='#4ade80';this.style.boxShadow='0 0 0 2px rgba(74,222,128,0.2)'"
                                            onblur="this.style.borderColor='#d1fae5';this.style.boxShadow='none'">{{ old('deskripsi_singkat') }}</textarea>
                                        @error('deskripsi_singkat')
                                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Ketentuan File PDF Notice --}}
                        <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 20px; padding: 16px 20px; margin-bottom: 16px;">
                            <div class="flex items-center gap-2 mb-2">
                                <i data-lucide="info" style="width: 16px; height: 16px; color: #92400E;"></i>
                                <h3 style="font-family: 'Poppins', sans-serif; font-weight: 500; font-size: 13px; color: #92400E; margin: 0;">Ketentuan File PDF</h3>
                            </div>
                            <div style="font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 12px; color: #B45309; line-height: 1.6;">
                                <p class="mb-2">Untuk hasil terbaik, gunakan file PDF yang dihasilkan langsung dari browser (misalnya melalui fitur Save as PDF) atau dari aplikasi pengolah dokumen standar.</p>
                                <p class="mb-1">Beberapa kondisi yang dapat menyebabkan file tidak dapat diproses tanda tangan digital:</p>
                                <ul class="list-disc ml-5 mb-2">
                                    <li>File menggunakan teknik kompresi yang tidak didukung sistem</li>
                                    <li>File merupakan hasil scan atau konversi dari gambar</li>
                                    <li>File dilindungi kata sandi atau memiliki pembatasan izin</li>
                                    <li>Ukuran file terlalu besar atau struktur file tidak standar</li>
                                </ul>
                                <p class="m-0">Jika tanda tangan gagal ditempatkan pada dokumen, sistem akan otomatis menerbitkan Lembar Pengesahan terpisah sebagai gantinya.</p>
                            </div>
                        </div>

                        {{-- File PDF --}}
                        <div>
                            <label for="file_pdf" class="block text-sm font-bold text-[#111111] mb-2">
                                File PDF <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="file_pdf" name="file_pdf" accept=".pdf" required
                                style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb; font-size: 14px; background: #ffffff; outline: none; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px rgba(230,33,41,0.2)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            <p class="text-xs text-gray-400 mt-2">Format: PDF, Ukuran maksimal: 5MB</p>
                            @error('file_pdf')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror

                            {{-- Mode info badge --}}
                            <div id="ttd-mode-info" style="display: none; margin-top: 16px; padding: 16px; border-radius: 12px; border: 1px solid #e5e7eb;">
                                <p class="text-sm font-medium" id="ttd-mode-text"></p>
                            </div>
                        </div>

                    </div>

                    {{-- Kolom Kanan: PDF Preview + TTD Marker --}}
                    <div>
                        <input type="hidden" name="ttd_coordinates" id="ttd_coordinates">

                        {{-- Placeholder saat belum ada jenis / mode append --}}
                        <div id="ttd-placeholder"
                             style="height: 100%; min-height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 16px; border: 2px dashed rgba(230,33,41,0.4); background: #f8faf9;">
                            <i data-lucide="file-search" class="w-12 h-12 text-[var(--color-primary)]/50 mb-3"></i>
                            <p class="text-sm font-semibold text-[var(--color-text)]/60">Pilih jenis surat terlebih dahulu</p>
                            <p class="text-xs text-gray-400 mt-1">Pratinjau TTD akan muncul jika mode stamp aktif</p>
                        </div>

                        {{-- Section TTD Marker (hanya mode stamp) --}}
                        <div id="ttd-marker-section" style="display: none; height: 100%;">
                            <div style="padding: 20px; background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column; gap: 16px;">

                                {{-- Header --}}
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h6 class="text-sm font-bold text-[#111111]">Posisi Tanda Tangan</h6>
                                        <p class="text-xs text-gray-400 mt-0.5">Klik pada pratinjau PDF untuk menempatkan TTD</p>
                                    </div>
                                    <div id="marker-status"
                                         class="text-xs font-bold text-amber-600 px-3 py-1 bg-amber-50 rounded-full border border-amber-100">
                                        Belum ditandai
                                    </div>
                                </div>

                                {{-- Approver buttons --}}
                                <div id="approver-buttons" class="flex flex-wrap gap-2"></div>

                                {{-- PDF Canvas area --}}
                                <div class="relative flex-1 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-inner" style="min-height:420px;">

                                    {{-- Upload hint (tampil sebelum PDF di-upload) --}}
                                    <div id="pdf-upload-hint"
                                         style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                                        <i data-lucide="upload-cloud" class="w-10 h-10 text-[var(--color-primary)] mb-2"></i>
                                        <p class="text-sm font-semibold text-[var(--color-text)]">Unggah PDF untuk melihat pratinjau</p>
                                        <p class="text-xs text-gray-400 mt-1">Lalu klik posisi tanda tangan</p>
                                    </div>

                                    {{-- PDF scroll container --}}
                                    <div id="pdf-container"
                                         style="display: none; position: relative; width: 100%; height: 100%; overflow: auto; text-align: center; padding: 16px; background: #e2e8f0;">
                                        <div class="relative inline-block shadow-xl">
                                            <canvas id="pdf-canvas" class="block cursor-crosshair"></canvas>
                                            <div id="marker-layer" class="absolute inset-0 pointer-events-none"></div>
                                        </div>
                                    </div>

                                    {{-- Page nav --}}
                                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full shadow border border-slate-200 z-20">
                                        <button type="button" id="prev-page"
                                                class="p-1 hover:bg-slate-100 rounded-full disabled:opacity-30">
                                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                        </button>
                                        <span class="text-xs font-bold text-slate-700 min-w-[70px] text-center">
                                            Hal <span id="current-page">1</span> / <span id="total-pages">1</span>
                                        </span>
                                        <button type="button" id="next-page"
                                                class="p-1 hover:bg-slate-100 rounded-full disabled:opacity-30">
                                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer buttons --}}
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('surat.index') }}"
                       style="padding: 10px 24px; border-radius: 12px; border: 1px solid #d1d5db; background: #ffffff; font-size: 14px; font-weight: 600; color: #374151; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;"
                       onmouseover="this.style.background='#F5F5F7'" onmouseout="this.style.background='#ffffff'">
                        Batal
                    </a>
                    <button type="submit" style="background-color: var(--color-text);"
                            class="inline-flex items-center gap-2 px-8 py-2.5 hover:opacity-90 text-white rounded-2xl text-sm font-bold shadow transition">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Simpan & Ajukan Surat
                    </button>
                </div>

            </form>
        </div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
window.suratDebug = { errors: [], logs: [] };
window.onerror = function(msg, url, line, col, error) {
    const err = `[Global Error] ${msg} at ${line}:${col}`;
    window.suratDebug.errors.push(err);
    console.error(err);
};

(function () {
    const log = (m) => { console.log('[SuratJS]', m); window.suratDebug.logs.push(m); };
    log('Script started');

    // ── PDF.js setup ──────────────────────────────────────────
    let pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
    
    const initPdfWorker = (lib) => {
        if (!lib) return;
        lib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        log('PDF.js worker set');
    };

    if (pdfjsLib) {
        initPdfWorker(pdfjsLib);
    } else {
        log('PDF.js not found yet, waiting...');
        window.addEventListener('load', () => {
            pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
            if (pdfjsLib) {
                initPdfWorker(pdfjsLib);
                log('PDF.js loaded on window.load');
            } else {
                log('PDF.js FAILED to load even on window.load');
            }
        });
    }

    // ── State ─────────────────────────────────────────────────
    let pdfDoc          = null;
    let pageNum         = 1;
    let currentMode     = null;
    let currentApprovers = [];
    let activeApproverIdx = 0;
    let coordinates     = {};
    let ttdImages       = {};
    let pendingPdfBytes = null;

    const $ = id => document.getElementById(id);
    const canvas      = () => $('pdf-canvas');
    const ctx         = () => canvas()?.getContext('2d');
    const markerLayer = () => $('marker-layer');

    // ── UI FEEDBACK ──────────────────────────────────────────
    function setPlaceholderMessage(title, sub, icon = 'file-search', isError = false) {
        const ph = $('ttd-placeholder');
        if (!ph) return;
        ph.style.display = 'flex';
        ph.innerHTML = `
            <div style="text-align:center; padding: 20px;">
                <p style="font-size: 14px; font-weight: 600; color: ${isError ? '#ef4444' : 'var(--color-text)'}; margin: 0;">${title}</p>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">${sub}</p>
                ${isError ? '<button type="button" onclick="window.location.reload()" style="margin-top:12px; padding:4px 12px; font-size:11px; background:var(--color-text); color:white; border-radius:99px; border:none; cursor:pointer;">Refresh Halaman</button>' : ''}
            </div>
        `;
    }

    // ── FETCH TTD MODE ────────────────────────────────────────
    async function updateTtdMode() {
        try {
            const select = $('jenis_surat');
            const selectedOption = select.options[select.selectedIndex];
            const jenis = selectedOption ? selectedOption.getAttribute('data-kode') : null;
            log('updateTtdMode called, jenis: ' + jenis);

            if (!jenis) {
                hideStamp();
                if ($('ttd-mode-info')) $('ttd-mode-info').style.display = 'none';
                setPlaceholderMessage('Pilih jenis surat terlebih dahulu', 'Pratinjau TTD akan muncul jika mode stamp aktif');
                return;
            }

            setPlaceholderMessage('Memproses...', 'Sedang memuat pengaturan dokumen');

            const url  = `/surat/ttd-mode?jenis_surat=${encodeURIComponent(jenis)}`;
            const resp = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            
            if (!resp.ok) throw new Error('Server returned ' + resp.status);
            const data = await resp.json();
            log('Mode data received: ' + data.mode);

            currentMode      = data.mode;
            currentApprovers = data.approvers || [];
            activeApproverIdx = 0;

            const infoDiv  = $('ttd-mode-info');
            const infoText = $('ttd-mode-text');
            if (infoDiv) infoDiv.style.display = 'block';

            if (data.mode === 'stamp') {
                log('Mode is STAMP');
                if ($('ttd-placeholder')) $('ttd-placeholder').style.display = 'none';
                showStamp();

                if (infoText) {
                    infoDiv.className = 'mt-4 p-4 rounded-2xl border bg-blue-50 border-blue-100';
                    infoText.innerHTML = `<strong class="text-blue-800">Mode Stamp:</strong> <span class="text-blue-700">Silakan unggah PDF dan tentukan posisi tanda tangan.</span>`;
                }

                preloadTtdImages();
                
                if (pendingPdfBytes) {
                    log('Processing pending PDF bytes');
                    await loadPDF(pendingPdfBytes);
                    pendingPdfBytes = null;
                } else {
                    if ($('pdf-upload-hint')) $('pdf-upload-hint').style.display = 'flex';
                    if ($('pdf-container')) $('pdf-container').style.display = 'none';
                }
                renderApproverButtons();
                checkReady();
            } else {
                log('Mode is APPEND');
                hideStamp();
                if ($('ttd-placeholder')) $('ttd-placeholder').style.display = 'none'; 
                if (infoText) {
                    infoDiv.className = 'mt-4 p-4 rounded-2xl border bg-emerald-50 border-emerald-100';
                    infoText.innerHTML = `<strong class="text-emerald-800">Mode Append:</strong> <span class="text-emerald-700">Tanda tangan akan ditambahkan di halaman baru di akhir dokumen.</span>`;
                }
            }
        } catch (err) {
            log('Error in updateTtdMode: ' + err.message);
            setPlaceholderMessage('Gagal Memuat Pengaturan', err.message, 'alert-circle', true);
        }
    }

    function showStamp() { const s = $('ttd-marker-section'); if(s) s.style.display = 'block'; }
    function hideStamp()  { const s = $('ttd-marker-section'); if(s) s.style.display = 'none'; }

    // ── LOAD PDF ──────────────────────────────────────────────
    async function loadPDF(bytes) {
        log('loadPDF called, bytes length: ' + bytes.length);
        showStamp();
        if ($('pdf-upload-hint')) $('pdf-upload-hint').style.display = 'none';
        if ($('pdf-container')) $('pdf-container').style.display = 'block';

        try {
            const lib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
            if (!lib) throw new Error('Library PDF.js belum siap. Silakan refresh halaman.');
            
            const loadingTask = lib.getDocument({ data: bytes });
            pdfDoc = await loadingTask.promise;
            
            log('PDF loaded, numPages: ' + pdfDoc.numPages);
            pageNum = 1;
            if ($('total-pages')) $('total-pages').textContent  = pdfDoc.numPages;
            if ($('current-page')) $('current-page').textContent = 1;
            await renderPage(1);
        } catch (e) {
            log('loadPDF Error: ' + e.message);
            alert('Gagal memproses file PDF: ' + e.message);
        }
    }

    async function renderPage(num) {
        const c = canvas();
        const x = ctx();
        if (!c || !x || !pdfDoc) return;

        try {
            const page     = await pdfDoc.getPage(num);
            const viewport = page.getViewport({ scale: 1.5 });
            c.width  = viewport.width;
            c.height = viewport.height;

            await page.render({ canvasContext: x, viewport: viewport }).promise;

            if ($('current-page')) $('current-page').textContent = num;
            if ($('prev-page')) $('prev-page').disabled = num <= 1;
            if ($('next-page')) $('next-page').disabled = num >= pdfDoc.numPages;
            renderMarkers();
        } catch (e) {
            log('renderPage Error: ' + e.message);
        }
    }

    // ── FILE INPUT ────────────────────────────────────────────
    const fileInput = $('file_pdf');
    if (fileInput) {
        fileInput.addEventListener('change', async function () {
            try {
                const file = this.files[0];
                if (!file || file.type !== 'application/pdf') return;

                log('File input changed: ' + file.name);
                const buf = await file.arrayBuffer();
                const bytes = new Uint8Array(buf);

                if (currentMode === 'stamp') {
                    await loadPDF(bytes);
                } else {
                    pendingPdfBytes = bytes;
                    if ($('jenis_surat') && $('jenis_surat').value) {
                        await updateTtdMode();
                    } else {
                        setPlaceholderMessage('Pilih jenis surat', 'Silakan tentukan jenis surat agar preview dapat dimuat');
                    }
                }
            } catch (err) {
                log('File input error: ' + err.message);
            }
        });
    }

    // ── PAGE NAV ──────────────────────────────────────────────
    const prevBtn = $('prev-page');
    if (prevBtn) prevBtn.onclick = () => { if (pageNum > 1) { pageNum--; renderPage(pageNum); } };
    
    const nextBtn = $('next-page');
    if (nextBtn) nextBtn.onclick = () => { if (pdfDoc && pageNum < pdfDoc.numPages) { pageNum++; renderPage(pageNum); } };

    // ── CLICK CANVAS → SET KOORDINAT ─────────────────────────
    document.addEventListener('click', function (e) {
        const c = canvas();
        if (!c || e.target !== c) return;
        if (!currentApprovers[activeApproverIdx]) return;

        const rect   = c.getBoundingClientRect();
        const scaleX = c.width  / rect.width;
        const scaleY = c.height / rect.height;
        const x = ((e.clientX - rect.left) * scaleX / c.width)  * 100;
        const y = ((e.clientY - rect.top)  * scaleY / c.height) * 100;

        const jabatan = currentApprovers[activeApproverIdx].jabatan;
        coordinates[jabatan] = { x, y, page: pageNum };

        if (activeApproverIdx < currentApprovers.length - 1) {
            activeApproverIdx++;
        }

        renderApproverButtons();
        renderMarkers();
        checkReady();
    });

    // ── PRELOAD TTD IMAGES ────────────────────────────────────
    function preloadTtdImages() {
        currentApprovers.forEach(app => {
            if (ttdImages[app.jabatan]) return;
            const img = new Image();
            img.src = `/surat/ttd-preview/${app.jabatan}?t=${Date.now()}`;
            img.onload  = () => { 
                ttdImages[app.jabatan] = img; 
                renderApproverButtons(); 
                renderMarkers(); 
            };
            img.onerror = () => { ttdImages[app.jabatan] = null; };
            ttdImages[app.jabatan] = img;
        });
    }

    // ── APPROVER BUTTONS ──────────────────────────────────────
    function renderApproverButtons() {
        const wrap = $('approver-buttons');
        if (!wrap) return;
        wrap.innerHTML = '';
        currentApprovers.forEach((app, idx) => {
            const done   = !!coordinates[app.jabatan];
            const active = idx === activeApproverIdx;
            const img    = ttdImages[app.jabatan];
            const hasImg = img && img.complete && img.naturalWidth > 0;

            const btn = document.createElement('button');
            btn.type  = 'button';
            btn.className = 'transition-all duration-150 flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-bold';
            
            if (active) btn.style.cssText = 'background:var(--color-text); color:#fff; border-color:var(--color-text);';
            else if (done) btn.style.cssText = 'background:#f0fdf4; color:#166534; border-color:#bbf7d0;';
            else btn.style.cssText = 'background:#fff; color:#64748b; border-color:#e2e8f0;';

            if (hasImg) {
                const thumb = document.createElement('img');
                thumb.src = img.src;
                thumb.className = 'h-4 w-auto object-contain';
                btn.appendChild(thumb);
            }
            btn.appendChild(document.createTextNode(app.label + (done ? ' ✓' : '')));
            btn.onclick = () => { activeApproverIdx = idx; renderApproverButtons(); renderMarkers(); };
            wrap.appendChild(btn);
        });
    }

    // ── RENDER MARKERS DI CANVAS ──────────────────────────────
    function renderMarkers() {
        const layer = markerLayer();
        if (!layer) return;
        layer.innerHTML = '';

        Object.entries(coordinates).forEach(([jabatan, coord]) => {
            if (coord.page !== pageNum) return;

            const label  = currentApprovers.find(a => a.jabatan === jabatan)?.label || jabatan;
            const active = currentApprovers[activeApproverIdx]?.jabatan === jabatan;
            const img    = ttdImages[jabatan];
            const hasImg = img && img.complete && img.naturalWidth > 0;

            const wrap = document.createElement('div');
            wrap.style.cssText = `position:absolute; left:${coord.x}%; top:${coord.y}%; transform:translate(-50%,-50%); z-index:${active ? 20 : 10}; pointer-events:none; display:flex; flex-direction:column; align-items:center; gap:3px;`;

            if (hasImg) {
                const box = document.createElement('div');
                box.style.cssText = `width:110px; height:54px; background:rgba(255,255,255,0.9); border:2px solid ${active ? 'var(--color-text)' : '#22c55e'}; border-radius:6px; box-shadow:0 3px 12px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;`;
                const preview = document.createElement('img');
                preview.src = img.src;
                preview.style.cssText = 'max-width:90%; max-height:90%; object-fit:contain;';
                box.appendChild(preview);

                const badge = document.createElement('div');
                badge.style.cssText = `position:absolute; top:-7px; right:-7px; width:16px; height:16px; background:${active ? 'var(--color-text)' : '#22c55e'}; border-radius:50%; border:2px solid white; display:flex; align-items:center; justify-content:center;`;
                badge.innerHTML = `<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>`;
                box.appendChild(badge);
                wrap.appendChild(box);
            } else {
                const dot = document.createElement('div');
                dot.style.cssText = `width:28px; height:28px; background:${active ? 'var(--color-text)' : '#22c55e'}; border-radius:50%; border:2px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center;`;
                dot.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;
                wrap.appendChild(dot);
            }

            const lbl = document.createElement('div');
            lbl.style.cssText = 'background:rgba(26,43,36,0.8); color:white; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; white-space:nowrap;';
            lbl.textContent = label;
            wrap.appendChild(lbl);
            layer.appendChild(wrap);
        });
    }

    function checkReady() {
        const all    = currentApprovers.every(a => coordinates[a.jabatan]);
        const status = $('marker-status');
        if (!status) return;

        if (all && currentApprovers.length > 0) {
            status.textContent = 'Siap ✓';
            status.className   = 'text-xs font-bold text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100';
            const coordInput = $('ttd_coordinates');
            if(coordInput) coordInput.value = JSON.stringify(coordinates);
        } else {
            const n = Object.keys(coordinates).length;
            status.textContent = currentApprovers.length > 0 ? `${n}/${currentApprovers.length}` : 'Belum ditandai';
            status.className   = 'text-xs font-bold text-amber-600 px-3 py-1 bg-amber-50 rounded-full border border-amber-100';
            const coordInput = $('ttd_coordinates');
            if(coordInput) coordInput.value = '';
        }
    }

    // ── INIT ──────────────────────────────────────────────────
    const init = async () => {
        log('Init function running');
        const select = $('jenis_surat');
        const fileIn = $('file_pdf');

        if (select) {
            select.addEventListener('change', updateTtdMode);
            select.addEventListener('input', updateTtdMode);
        }

        if (fileIn) {
            fileIn.addEventListener('change', async function() {
                const file = this.files[0];
                if (!file || file.type !== 'application/pdf') return;
                log('File changed: ' + file.name);
                const buf = await file.arrayBuffer();
                const bytes = new Uint8Array(buf);
                if (currentMode === 'stamp') await loadPDF(bytes);
                else pendingPdfBytes = bytes;
            });

            // Cek jika file sudah terpilih (misal after validation error/refresh)
            if (fileIn.files && fileIn.files[0]) {
                const file = fileIn.files[0];
                if (file.type === 'application/pdf') {
                    log('Pre-selected file found: ' + file.name);
                    const buf = await file.arrayBuffer();
                    pendingPdfBytes = new Uint8Array(buf);
                }
            }
        }

        if (select && select.value) {
            log('Initial value found: ' + select.value);
            await updateTtdMode();
        }
    };

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        init();
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }

})();

// ── Toggle komisi group berdasarkan jenis surat ──────────────────
function handleSuratTypeChange(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const orgTipe        = selectedOption ? selectedOption.getAttribute('data-organisasi-tipe') : null;
    const requiresDetail = selectedOption ? selectedOption.getAttribute('data-requires-kegiatan-detail') === '1' : false;

    // Toggle Komisi (khusus MPK)
    const komisiGroup  = document.getElementById('komisi-group');
    const komisiSelect = document.getElementById('komisi_id');
    if (komisiGroup) {
        if (orgTipe === 'mpk') {
            komisiGroup.style.display = 'block';
            if (komisiSelect) komisiSelect.required = true;
        } else {
            komisiGroup.style.display = 'none';
            if (komisiSelect) { komisiSelect.required = false; komisiSelect.value = ''; }
        }
    }

    // Toggle Section Detail Kegiatan
    const kegiatanSection = document.getElementById('kegiatan-detail-section');
    if (kegiatanSection) {
        if (requiresDetail) {
            kegiatanSection.style.display = 'block';
            // Aktifkan required pada field wajib
            ['nama_kegiatan', 'tanggal_mulai', 'lokasi'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.required = true;
            });
        } else {
            kegiatanSection.style.display = 'none';
            // Hapus required agar tidak menghalangi submit
            ['nama_kegiatan', 'tanggal_mulai', 'lokasi', 'tanggal_selesai', 'deskripsi_singkat'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.required = false;
            });
        }
    }
}

function handleOrganisasiChange(orgSelect) {
    const selectedOption = orgSelect.options[orgSelect.selectedIndex];
    const tipe = selectedOption ? selectedOption.getAttribute('data-tipe') : null;
    const jenisSelect = document.getElementById('jenis_surat');
    
    jenisSelect.value = "";
    handleSuratTypeChange(jenisSelect);

    if (!tipe) {
        jenisSelect.disabled = true;
        return;
    }

    jenisSelect.disabled = false;
    
    const options = jenisSelect.querySelectorAll('option');
    options.forEach(opt => {
        const optTipe = opt.getAttribute('data-organisasi-tipe');
        if (opt.value === "") {
            opt.style.display = "block";
        } else if (optTipe === "" || optTipe === null || optTipe === tipe) {
            opt.style.display = "block";
            opt.disabled = false;
        } else {
            opt.style.display = "none";
            opt.disabled = true;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const orgSelect   = document.getElementById('organisasi_id');
    const jenisSelect = document.getElementById('jenis_surat');
    
    if (orgSelect && orgSelect.value) {
        handleOrganisasiChange(orgSelect);
        
        const oldJenisSurat = "{{ old('surat_type_id') }}";
        if (oldJenisSurat && jenisSelect) {
            jenisSelect.value = oldJenisSurat;
            handleSuratTypeChange(jenisSelect);
        }
    }
});
</script>

{{-- ═══════════════════════════════════════════════════════════════
     REAL-TIME VALIDATION — cek duplikat & konflik via AJAX
     Dipanggil saat blur pada field relevan; debounced 400ms.
     Non-blocking: hanya tampilkan peringatan, tidak blokir submit.
     Validasi final (blocking) tetap di server via StoreSuratRequest
     dan DokumenValidationService di SuratController::store().
═══════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Debounce helper ────────────────────────────────────────────
    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // ── Ambil nilai field dengan aman ──────────────────────────────
    const val = id => (document.getElementById(id)?.value ?? '').trim();

    // ── Tampilkan / sembunyikan warning badge ──────────────────────
    function showWarn(id, detail) {
        const box    = document.getElementById(id);
        const detEl  = document.getElementById(id + '-detail');
        if (!box || !detEl) return;
        detEl.textContent = detail;
        box.style.display = 'flex';
    }

    function hideWarn(id) {
        const box = document.getElementById(id);
        if (box) box.style.display = 'none';
    }

    // Tombol ✕ di dalam badge
    window.dismissWarn = function (id) { hideWarn(id); };

    // ── Cek duplikat ───────────────────────────────────────────────
    async function cekDuplikat() {
        const namaKegiatan = val('nama_kegiatan');
        const tanggalMulai = val('tanggal_mulai');
        const organisasiId = val('organisasi_id');

        // Hanya cek jika semua field tersedia dan section terlihat
        if (!namaKegiatan || !tanggalMulai || !organisasiId) {
            hideWarn('warn-duplikat');
            return;
        }

        try {
            const url = new URL('/surat/cek-duplikat', window.location.origin);
            url.searchParams.set('nama_kegiatan', namaKegiatan);
            url.searchParams.set('tanggal_mulai',  tanggalMulai);
            url.searchParams.set('organisasi_id',  organisasiId);

            const res  = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (!res.ok) return; // silent fail — validasi server tetap jalan
            const data = await res.json();

            if (data.duplikat) {
                showWarn('warn-duplikat', data.rekomendasi);
            } else {
                hideWarn('warn-duplikat');
            }
        } catch (_) {
            // silent fail
        }
    }

    // ── Cek konflik jadwal ─────────────────────────────────────────
    async function cekKonflik() {
        const lokasi       = val('lokasi');
        const tanggalMulai = val('tanggal_mulai');
        const tanggalSelesai = val('tanggal_selesai') || null;

        if (!lokasi || !tanggalMulai) {
            hideWarn('warn-konflik');
            return;
        }

        try {
            const url = new URL('/surat/cek-konflik', window.location.origin);
            url.searchParams.set('lokasi',          lokasi);
            url.searchParams.set('tanggal_mulai',   tanggalMulai);
            if (tanggalSelesai) url.searchParams.set('tanggal_selesai', tanggalSelesai);

            const res  = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.konflik) {
                showWarn('warn-konflik', data.rekomendasi);
            } else {
                hideWarn('warn-konflik');
            }
        } catch (_) {
            // silent fail
        }
    }

    // ── Debounced versi dari kedua fungsi (400ms) ──────────────────
    const cekDuplikatDebounced = debounce(cekDuplikat, 400);
    const cekKonflikDebounced  = debounce(cekKonflik,  400);

    // ── Attach listeners setelah DOM siap ─────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        // Nama Kegiatan: trigger cek duplikat saat blur & saat tanggal berubah
        document.getElementById('nama_kegiatan')?.addEventListener('blur',  cekDuplikat);
        document.getElementById('nama_kegiatan')?.addEventListener('input', cekDuplikatDebounced);

        // Tanggal Mulai: trigger keduanya karena mempengaruhi duplikat & konflik
        document.getElementById('tanggal_mulai')?.addEventListener('change', () => {
            cekDuplikat();
            cekKonflik();
        });

        // Tanggal Selesai: hanya konflik
        document.getElementById('tanggal_selesai')?.addEventListener('change', cekKonflik);

        // Lokasi: trigger konflik saat blur & input
        document.getElementById('lokasi')?.addEventListener('blur',  cekKonflik);
        document.getElementById('lokasi')?.addEventListener('input', cekKonflikDebounced);

        // Organisasi: jika ganti organisasi, reset & re-cek duplikat
        document.getElementById('organisasi_id')?.addEventListener('change', () => {
            hideWarn('warn-duplikat');
            // Re-cek setelah DOM update
            setTimeout(cekDuplikat, 50);
        });
    });

})();
</script>
@endpush

