@extends('layouts.master')

@section('content')

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm shadow-sm">
        <p class="font-bold mb-1">Gagal memproses aksi:</p>
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- breadcrumb / header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-sans font-bold text-[#111111]">Detail Surat</h1>
            <p class="text-[12px] font-light text-[#6B7280] mt-1">Informasi lengkap dan status persetujuan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('surat.index') }}" class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
                Kembali
            </a>
            
            @if($surat->status === 'approved_owner' && ($surat->cover_pdf_path || $surat->hasFinalPdf()))
                @if($surat->final_pdf_path === 'ARCHIVED' || $surat->cover_pdf_path === 'ARCHIVED')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-500 rounded-2xl text-sm font-semibold shadow-sm cursor-not-allowed">
                        <i data-lucide="archive" class="w-4 h-4 inline-block"></i> Archived
                    </span>
                @else
                    <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'final']) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-2xl text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">
                        <i data-lucide="download" class="w-4 h-4 inline-block"></i> Unduh Surat
                    </a>
                @endif
            @elseif($surat->file_pdf)
                @if($surat->file_pdf === 'ARCHIVED')
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-gray-400 bg-gray-50 rounded-2xl text-sm font-semibold shadow-sm cursor-not-allowed">
                        <i data-lucide="archive" class="w-4 h-4 inline-block"></i> Archived
                    </span>
                @else
                    @can('download', $surat)
                    <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'original']) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 border border-[var(--color-text)] text-[var(--color-text)] bg-white hover:bg-gray-50 rounded-2xl text-sm font-semibold transition shadow-sm">
                        <i data-lucide="file-text" class="w-4 h-4 inline-block"></i> Lihat Dokumen
                    </a>
                    @endcan
                @endif
            @endif

            @if(Auth::id() === $surat->user_id)
                @if($surat->canBeEdited())
                <a href="{{ route('surat.edit', $surat->id) }}" class="px-5 py-2.5 bg-amber-500 text-white rounded-2xl text-sm font-semibold hover:bg-amber-600 transition shadow-sm">
                    <i data-lucide="edit" class="w-4 h-4 inline-block mr-1"></i> Ubah
                </a>
                @endif
                
                @if($surat->canBeDeleted())
                <form action="{{ route('surat.destroy', $surat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-rose-500 text-white rounded-2xl text-sm font-semibold hover:bg-rose-600 transition shadow-sm">
                        <i data-lucide="trash-2" class="w-4 h-4 inline-block mr-1"></i> Hapus
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── kolom kiri — detail card (2 col) ──────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100">
                <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4">
                    <div>
                        <h1 class="text-2xl font-sans font-bold text-[#111111]">{{ $surat->nomor_surat }}</h1>
                        <p class="text-xs font-poppins font-light text-[#6B7280] mt-1">{{ $surat->suratType ? $surat->suratType->nama : ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }}</p>
                    </div>
                    <div>
                        @if($surat->status === 'approved_owner')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Selesai</span>
                        @elseif($surat->status === 'rejected')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                        @elseif($surat->status === 'revised')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">Butuh Revisi</span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">Proses Persetujuan</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mb-1">Perihal</p>
                        <p class="font-medium text-gray-800">{{ $surat->perihal }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mb-1">Pembuat</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-[var(--color-primary)]/20 flex items-center justify-center font-semibold text-[var(--color-text)] text-xs">
                                {{ strtoupper(substr($surat->user->name ?? 'U',0,1)) }}
                            </div>
                            <p class="font-medium text-gray-800">{{ $surat->user->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mb-1">Tanggal Dibuat</p>
                        <p class="font-medium text-gray-800">{{ $surat->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold tracking-widest uppercase text-gray-400 mb-1">Lampiran</p>
                        @if($surat->file_pdf === 'ARCHIVED')
                            <p class="text-gray-400 italic flex items-center gap-1"><i data-lucide="archive" class="w-3 h-3"></i> File Archived</p>
                        @elseif($surat->file_pdf)
                            @can('download', $surat)
                                <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'original']) }}" class="inline-flex items-center gap-2 text-[var(--color-text)] hover:text-[var(--color-primary)] font-medium hover:underline">
                                    <i data-lucide="file-text" class="w-4 h-4"></i> Lihat Dokumen Asli
                                </a>
                            @else
                                <p class="text-gray-500 italic">File tersedia (akses terbatas)</p>
                            @endcan
                        @else
                            <p class="text-gray-400 italic">Tidak ada file lampiran</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── PDF Preview Card (Premium Interactive PDF.js Viewer with Overlaid Stamps) ── --}}
            @if($surat->file_pdf && $surat->file_pdf !== 'ARCHIVED')
            <div class="bg-white p-6 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100 mt-6">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-search" class="w-5 h-5 text-[var(--color-primary)]"></i>
                        <h3 class="text-base font-sans font-bold text-[#111111] m-0">Pratinjau Dokumen</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="show-prev-page" class="p-1 hover:bg-slate-100 rounded-full border border-slate-200 disabled:opacity-30">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <span class="text-xs font-bold text-slate-700 min-w-[70px] text-center">
                            Hal <span id="show-current-page">1</span> / <span id="show-total-pages">1</span>
                        </span>
                        <button type="button" id="show-next-page" class="p-1 hover:bg-slate-100 rounded-full border border-slate-200 disabled:opacity-30">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Preview Canvas area --}}
                <div class="relative bg-slate-100 border border-slate-200 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center p-4" style="min-height:380px;">
                    {{-- PDF container --}}
                    <div id="show-pdf-container" style="position: relative; display: inline-block; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <canvas id="show-pdf-canvas" class="block"></canvas>
                        <div id="show-marker-layer" class="absolute inset-0 pointer-events-none"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Proposal Format Check Card ── --}}
            @if($surat->proposalFormatCheck)
                @php
                    $check = $surat->proposalFormatCheck;
                    $detail = $check->detail;
                @endphp
                @if($check->skor_akhir < 70)
                <div class="p-6 bg-red-50 border border-red-200 rounded-3xl shadow-sm mb-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-red-100 text-red-600 rounded-2xl">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <h4 class="text-base font-bold text-red-900">Format Proposal Tidak Standar</h4>
                                <span class="px-3 py-1 bg-red-100 text-red-800 border border-red-200 rounded-full text-xs font-bold">
                                    Skor Akhir: {{ $check->skor_akhir }}/100
                                </span>
                            </div>
                            <p class="text-sm text-red-700 mt-1">
                                Dokumen ini tidak memenuhi format proposal resmi sekolah. Harap perhatikan beberapa catatan perbaikan berikut:
                            </p>

                            <div class="mt-4 space-y-4">
                                {{-- 1. Bagian wajib tidak ditemukan --}}
                                @php
                                    $missingWajib = [];
                                    foreach ($detail['sections'] ?? [] as $secKey => $secVal) {
                                        if (($secVal['is_wajib'] ?? false) && !($secVal['found'] ?? false)) {
                                            $missingWajib[] = $secKey;
                                        }
                                    }
                                @endphp
                                @if(count($missingWajib) > 0)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-red-800 mb-1">Bagian Wajib yang Hilang:</p>
                                    <ul class="list-disc pl-5 text-sm text-red-700 space-y-0.5">
                                        @foreach($missingWajib as $secKey)
                                            <li>Heading/Kata Kunci <strong>"{{ ucwords(str_replace('_', ' ', $secKey)) }}"</strong> tidak terdeteksi.</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                {{-- 2. Validasi terlalu_singkat --}}
                                @php
                                    $tooShort = [];
                                    foreach ($detail['sections'] ?? [] as $secKey => $secVal) {
                                        if ($secVal['terlalu_singkat'] ?? false) {
                                            $tooShort[] = [
                                                'key' => $secKey,
                                                'paragraphs' => $secVal['paragraf_count'] ?? 0,
                                                'avg_sentences' => $secVal['avg_kalimat'] ?? 0
                                            ];
                                        }
                                    }
                                @endphp
                                @if(count($tooShort) > 0)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-red-800 mb-1">Bagian Terlalu Singkat:</p>
                                    <ul class="list-disc pl-5 text-sm text-red-700 space-y-0.5">
                                        @foreach($tooShort as $item)
                                            <li>
                                                <strong>{{ ucwords(str_replace('_', ' ', $item['key'])) }}</strong>: 
                                                Hanya memiliki {{ $item['paragraphs'] }} paragraf (minimal 3) ATAU rata-rata {{ round($item['avg_sentences'], 1) }} kalimat per paragraf (minimal 4).
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                {{-- 3. Skor konten rendah --}}
                                @php
                                    $lowScores = [];
                                    foreach ($detail['sections'] ?? [] as $secKey => $secVal) {
                                        if (isset($secVal['skor_konten']) && $secVal['skor_konten'] !== null && $secVal['skor_konten'] < 50) {
                                            $lowScores[] = [
                                                'key' => $secKey,
                                                'score' => $secVal['skor_konten']
                                            ];
                                        }
                                    }
                                @endphp
                                @if(count($lowScores) > 0)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-red-800 mb-1">Kesesuaian Konten Rendah:</p>
                                    <ul class="list-disc pl-5 text-sm text-red-700 space-y-0.5">
                                        @foreach($lowScores as $item)
                                            <li>
                                                <strong>{{ ucwords(str_replace('_', ' ', $item['key'])) }}</strong>: 
                                                Kesesuaian dengan referensi sangat rendah (Skor: {{ $item['score'] }}/100).
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            {{-- catatan revisi --}}
            @if($surat->catatan_revisi)
            <div class="bg-orange-50 border border-orange-200 p-6 rounded-3xl shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                    <p class="text-sm font-bold uppercase text-orange-800 tracking-wider">Revision Notes</p>
                </div>
                <p class="text-orange-900 mt-2">{{ $surat->catatan_revisi }}</p>
            </div>
            @endif



            {{-- ── Notice: Pending Admin ── --}}
            @if($surat->status === 'pending_admin')
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-3xl shadow-sm mt-6">
                <div class="flex items-center gap-3 mb-2">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                    <h6 class="text-base font-bold text-blue-800">Menunggu Verifikasi Admin</h6>
                </div>
                <p class="text-sm text-blue-700">
                    Dokumen ini sedang diperiksa formatnya dan menunggu registrasi nomor surat resmi dari Sekretariat.
                </p>
            </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════════
                 SECTION: SURAT TURUNAN
                 Hanya muncul kalau surat sudah approved_owner
            ══════════════════════════════════════════════════════════════ --}}
            @if($surat->status === 'approved_owner')
            @php
                $suratTurunans = $surat->suratTurunans()
                    ->with(['template', 'signers.user'])
                    ->latest()
                    ->get();

                // Signer yang login & ada slot waiting miliknya
                $myPendingSigners = collect();
                foreach ($suratTurunans as $st) {
                    foreach ($st->signers as $sgn) {
                        if ((int)$sgn->user_id === (int)Auth::id() && $sgn->status === 'waiting') {
                            $myPendingSigners->push(['turunan' => $st, 'signer' => $sgn]);
                        }
                    }
                }
            @endphp

            <div class="bg-white rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-2xl bg-emerald-50 flex items-center justify-center">
                            <i data-lucide="file-plus-2" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-sans font-bold text-[#111111]">Surat Turunan</h2>
                            <p class="text-[11px] text-gray-400 mt-0.5">Dokumen turunan dari surat ini</p>
                        </div>
                    </div>
                    @can('view', $surat)
                    <a href="{{ route('surat.turunan.create', $surat->id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-[var(--color-primary)] text-white rounded-2xl text-xs font-bold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Generate
                    </a>
                    @endcan
                </div>

                {{-- Alert: giliran TTD user saat ini --}}
                @if($myPendingSigners->isNotEmpty())
                <div class="mx-6 mt-4 p-4 bg-amber-50 border border-amber-200 rounded-[28px] flex items-start gap-3">
                    <i data-lucide="pen-line" class="w-4 h-4 text-amber-600 mt-0.5 shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-amber-800 mb-1">Menunggu Tanda Tangan Anda</p>
                        <div class="flex flex-col gap-1.5">
                            @foreach($myPendingSigners as $item)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-amber-700 truncate">
                                    {{ $item['turunan']->template?->nama ?? '-' }}
                                    <span class="text-amber-400 mx-1">·</span>
                                    {{ $item['signer']->jabatanLabel }}
                                </span>
                                <button type="button"
                                    onclick="openTtdModal({{ $item['turunan']->id }}, {{ $item['signer']->id }}, '{{ addslashes($item['turunan']->template?->nama ?? '') }}')"
                                    class="shrink-0 inline-flex items-center gap-1 px-3 py-1 bg-amber-500 text-white rounded-lg text-[11px] font-bold hover:bg-amber-600 transition">
                                    <i data-lucide="pen" class="w-3 h-3"></i> TTD
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- List surat turunan --}}
                @if($suratTurunans->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                    <div class="w-12 h-12 rounded-[28px] bg-gray-50 flex items-center justify-center mb-3">
                        <i data-lucide="file-x" class="w-6 h-6 text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-400">Belum ada surat turunan</p>
                    <p class="text-xs text-gray-300 mt-1">Klik "Generate" untuk membuat surat turunan</p>
                </div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($suratTurunans as $st)
                    @php
                        $totalSigners  = $st->signers->count();
                        $signedCount   = $st->signers->where('status', 'signed')->count();
                        $waitingSigners = $st->signers->where('status', 'waiting');

                        // Label & warna status
                        [$stBg, $stText, $stBorder] = match($st->status) {
                            'ditandatangani' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200'],
                            'menunggu_ttd'   => ['bg-amber-50',   'text-amber-700',   'border-amber-200'],
                            default          => ['bg-gray-50',    'text-gray-500',    'border-gray-200'],
                        };
                    @endphp

                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3">

                        {{-- Icon + Info --}}
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-2xl {{ $st->status === 'ditandatangani' ? 'bg-emerald-50' : 'bg-gray-50' }} flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $st->status === 'ditandatangani' ? 'file-check-2' : 'file-clock' }}"
                                   class="w-4 h-4 {{ $st->status === 'ditandatangani' ? 'text-emerald-500' : 'text-gray-400' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-[#111111] truncate">
                                    {{ $st->template?->nama ?? '-' }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5 truncate">
                                    {{ $st->nomor_surat ?? 'Nomor belum ditetapkan' }}
                                </p>

                                {{-- Progress TTD --}}
                                @if($totalSigners > 0)
                                <div class="flex items-center gap-2 mt-1.5">
                                    <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden" style="max-width:80px;">
                                        <div class="h-full bg-emerald-400 rounded-full transition-all"
                                             style="width:{{ $totalSigners > 0 ? round(($signedCount/$totalSigners)*100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold {{ $signedCount === $totalSigners ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $signedCount }}/{{ $totalSigners }} TTD selesai
                                    </span>
                                </div>
                                @if($waitingSigners->isNotEmpty())
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Menunggu: {{ $waitingSigners->map(fn($s) => $s->jabatanLabel)->join(', ') }}
                                </p>
                                @endif
                                @endif
                            </div>
                        </div>

                        {{-- Kanan: badge status + actions --}}
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $stBg }} {{ $stText }} {{ $stBorder }}">
                                {{ $st->statusLabel }}
                            </span>

                            {{-- Tombol TTD untuk signer yang login & waiting --}}
                            @foreach($st->signers->where('status', 'waiting') as $sgn)
                                @if((int)$sgn->user_id === (int)Auth::id())
                                <button type="button"
                                    onclick="openTtdModal({{ $st->id }}, {{ $sgn->id }}, '{{ addslashes($st->template?->nama ?? '') }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 text-white rounded-lg text-[11px] font-bold hover:bg-amber-600 transition">
                                    <i data-lucide="pen" class="w-3 h-3"></i> Tanda Tangan
                                </button>
                                @endif
                            @endforeach

                            {{-- Download kalau sudah ditandatangani --}}
                            @if($st->status === 'ditandatangani' && $st->file_pdf_path)
                            <a href="{{ route('surat.turunan.download', [$surat->id, $st->id]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-[11px] font-bold hover:bg-emerald-700 transition">
                                <i data-lucide="download" class="w-3 h-3"></i> Unduh
                            </a>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
                @endif

            </div>
            @endif
            {{-- ── END SECTION SURAT TURUNAN ── --}}

            {{-- ── Action Bar: Approval (Premium Style) ── --}}
            @if($canApprove && $surat->status === 'submitted')
            <div class="bg-white border border-[var(--color-bg-light)] p-5 rounded-[20px] shadow-sm mt-4" style="background: linear-gradient(145deg, #ffffff 0%, #f9fbf9 100%);">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--color-primary)] flex items-center justify-center shadow-lg shadow-gray-200 shrink-0">
                        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-lg font-sans font-bold text-[#111111]">Menunggu Persetujuan Anda</h4>
                        <p class="text-[13px] text-gray-500 mt-0.5">Silakan tinjau rinciannya dan berikan tanda tangan digital Anda.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" 
                        onclick="openApproveModal()"
                        class="flex-1 px-5 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-bold hover:bg-[var(--color-primary-dark)] transition shadow-md shadow-gray-200 flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Sekarang
                    </button>
                    <button type="button"
                        onclick="openRejectModal()"
                        class="flex-1 px-5 py-2.5 bg-white text-red-500 border border-red-100 rounded-2xl text-sm font-bold hover:bg-red-50 transition flex items-center justify-center gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak
                    </button>
                </div>
            </div>
            @endif

        </div>

        {{-- ── kolom kanan — approval panel (1 col) ─────────── --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- ── LPJ & Pelaksanaan Actions (Premium Style) ── --}}
            @if($surat->status === 'approved_owner' && $surat->suratType?->requires_kegiatan_detail)
            <div class="bg-white border border-gray-100 p-6 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)]" style="background: linear-gradient(145deg, #ffffff 0%, #f7faf8 100%);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-sans font-bold text-[#111111] m-0">Laporan Pertanggungjawaban</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5">Status Pelaksanaan: <span class="font-bold text-emerald-600 uppercase">{{ str_replace('_', ' ', $surat->status_pelaksanaan) }}</span></p>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    @if(in_array($surat->status_pelaksanaan, ['belum_mulai', 'berjalan']))
                        @if((int)$surat->pic_user_id === (int)Auth::id())
                            <a href="{{ route('pelaksanaan.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-[var(--color-primary)] text-white rounded-full text-xs font-bold shadow-sm hover:opacity-90 transition">
                                <i data-lucide="play" class="w-4 h-4"></i> Kelola & Selesaikan Kegiatan
                            </a>
                        @else
                            <div class="p-3 bg-gray-50 border border-gray-150 rounded-xl text-center">
                                <p class="text-xs text-gray-500 font-medium">Kegiatan sedang berjalan oleh PIC.</p>
                            </div>
                        @endif
                    @elseif($surat->status_pelaksanaan === 'selesai')
                        @php
                            $lpj = $surat->lpj;
                        @endphp
                        
                        @if(!$lpj || in_array($lpj->status, ['draft', 'revisi']))
                            @if((int)$surat->pic_user_id === (int)Auth::id())
                                <a href="{{ route('lpj.create', $surat->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-full text-xs font-bold shadow-sm hover:bg-emerald-700 transition">
                                    <i data-lucide="file-plus" class="w-4 h-4"></i> Isi Laporan LPJ Sekarang
                                </a>
                                @if($lpj && $lpj->status === 'revisi')
                                    <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl mt-2">
                                        <p class="text-[11px] font-bold text-rose-800 uppercase">Perlu Revisi Pembina:</p>
                                        <p class="text-xs text-rose-700 mt-0.5">{{ $lpj->catatan_revisi }}</p>
                                    </div>
                                @endif
                            @else
                                <div class="p-3 bg-gray-50 border border-gray-150 rounded-xl text-center">
                                    <p class="text-xs text-gray-500 font-medium">Kegiatan selesai. Menunggu LPJ diisi oleh PIC.</p>
                                </div>
                            @endif
                        @elseif($lpj->status === 'submitted')
                            <a href="{{ route('lpj.show', $surat->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 text-white rounded-full text-xs font-bold shadow-sm hover:bg-indigo-700 transition">
                                <i data-lucide="eye" class="w-4 h-4"></i> Lihat LPJ (Menunggu Verifikasi)
                            </a>
                        @elseif($lpj->status === 'valid')
                            <a href="{{ route('lpj.show', $surat->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-teal-600 text-white rounded-full text-xs font-bold shadow-sm hover:bg-teal-700 transition">
                                <i data-lucide="file-check-2" class="w-4 h-4"></i> Lihat LPJ (Telah Disetujui)
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-white p-6 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-sans font-semibold text-[#111111]">Perjalanan Surat</h2>
                    <i data-lucide="map-pin" class="w-4 h-4 text-[var(--color-primary)]"></i>
                </div>

                @php
                    $approved   = $steps->where('status','approved')->count();
                    $total      = $steps->count();
                    $percentage = $total > 0 ? round(($approved / $total) * 100) : 0;
                    $waitingIdx = $steps->search(fn($s) => $s->status === 'waiting');
                @endphp

                {{-- Progress label --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        {{ $approved }} / {{ $total }} {{ $steps->contains('is_signer', true) ? 'disetujui & ditandatangani' : 'disetujui' }}
                    </span>
                    <span class="text-[13px] font-bold
                        @if($percentage === 100) text-green-600
                        @elseif($percentage > 0) text-[var(--color-primary)]
                        @else text-gray-400 @endif">
                        {{ $percentage }}%
                    </span>
                </div>

                {{-- Horizontal track bar --}}
                <div class="relative h-1.5 bg-gray-100 rounded-full mb-6 overflow-visible">
                    <div class="h-full rounded-full transition-all duration-700 ease-out
                        @if($percentage === 100) bg-green-500
                        @else bg-[var(--color-primary)] @endif"
                        style="width: {{ $percentage }}%"></div>
                    {{-- Pulse dot: posisi ujung progress --}}
                    @if($percentage === 100)
                        {{-- full: dot hijau statis di ujung kanan --}}
                        <div class="absolute top-1/2 -translate-y-1/2 right-0">
                            <span class="block w-3 h-3 rounded-full bg-green-500 shadow-sm"></span>
                        </div>
                    @elseif($percentage > 0)
                        {{-- sebagian: dot merah ping di ujung progress --}}
                        <div class="absolute top-1/2 -translate-y-1/2"
                             style="left: calc({{ $percentage }}% - 6px);">
                            <span class="relative block w-3 h-3">
                                <span class="absolute inline-flex w-full h-full rounded-full bg-[var(--color-primary)] opacity-50 animate-ping"></span>
                                <span class="relative block w-3 h-3 rounded-full bg-[var(--color-primary)] shadow-sm"></span>
                            </span>
                        </div>
                    @else
                        {{-- nol: dot hitam ping di posisi awal (kiri) —— surat belum bergerak --}}
                        <div class="absolute top-1/2 -translate-y-1/2" style="left: -3px;">
                            <span class="relative block w-3 h-3">
                                <span class="absolute inline-flex w-full h-full rounded-full bg-[#111111] opacity-40 animate-ping"></span>
                                <span class="relative block w-3 h-3 rounded-full bg-[#111111] shadow-sm"></span>
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Steps timeline --}}
                @php
                    // Cari approver pertama dari konfigurasi jenis surat
                    $firstApproverConfig = null;
                    if ($surat->suratType) {
                        $firstApproverConfig = $surat->suratType->approvers()
                            ->orderBy('urutan')
                            ->first();
                    }
                @endphp

                @forelse($steps as $index => $step)
                @php
                    $isApproved = $step->status === 'approved';
                    $isWaiting  = $step->status === 'waiting';
                    $isRejected = $step->status === 'rejected';
                    $isPending  = $step->status === 'pending';
                    $isLast     = $loop->last;
                @endphp

                <div class="flex gap-3 {{ $isLast ? '' : 'pb-1' }} relative">

                    {{-- Connector line --}}
                    @if(!$isLast)
                    <div class="absolute left-[13px] top-[28px] bottom-0 w-px
                        @if($isApproved) bg-[var(--color-primary)]
                        @else bg-gray-100 @endif
                        z-0"></div>
                    @endif

                    {{-- Circle node --}}
                    <div class="relative z-10 flex-shrink-0 mt-0.5">
                        @if($isApproved)
                        <div class="w-7 h-7 rounded-full bg-[var(--color-primary)] flex items-center justify-center shadow-sm shadow-red-100">
                            <i data-lucide="check" style="width:13px;height:13px;color:white;stroke-width:3;"></i>
                        </div>
                        @elseif($isWaiting)
                        <div class="w-7 h-7 rounded-full bg-[#111111] flex items-center justify-center relative">
                            <i data-lucide="navigation" style="width:12px;height:12px;color:white;"></i>
                            {{-- ping ring --}}
                            <span class="absolute inset-0 rounded-full bg-[#111111] opacity-30 animate-ping"></span>
                        </div>
                        @elseif($isRejected)
                        <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center">
                            <i data-lucide="x" style="width:13px;height:13px;color:#DC2626;stroke-width:3;"></i>
                        </div>
                        @else
                        <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-200">
                            <span class="text-[10px] font-bold text-gray-300">{{ $index + 1 }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pb-5 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[13px] font-semibold leading-tight
                                    @if($isApproved) text-[#111111]
                                    @elseif($isWaiting) text-[#111111]
                                    @else text-gray-400 @endif truncate">
                                    @php
                                        $label = $step->label ?? ucfirst(str_replace('_', ' ', $step->jabatan ?? 'Penyetuju'));
                                        if ($step->is_signer) {
                                            if (str_starts_with($label, 'Disetujui')) {
                                                $label = str_replace('Disetujui', 'Disetujui & Ditandatangani', $label);
                                            } elseif (str_starts_with($label, 'disetujui')) {
                                                $label = str_replace('disetujui', 'disetujui & ditandatangani', $label);
                                            } else {
                                                $label = 'Disetujui & Ditandatangani ' . $label;
                                            }
                                        }
                                    @endphp
                                    {{ $label }}
                                </p>
                                <p class="text-[11px] mt-0.5 leading-snug
                                    @if($isApproved) text-gray-400
                                    @elseif($isWaiting) text-[var(--color-primary)] font-medium
                                    @else text-gray-300 @endif">
                        @if($isApproved)
                            {{ $step->approver->name ?? '—' }}
                            · {{ optional($step->actioned_at)->format('d/m/y H:i') }}
                        @elseif($isWaiting)
                            @php
                                $namaApprover = null;
                                if ($step->assignedUser) {
                                    $namaApprover = $step->assignedUser->name;
                                } elseif ($step->approver) {
                                    $namaApprover = $step->approver->name;
                                }
                            @endphp
                            @if($namaApprover)
                                Menunggu <span class="font-semibold text-[#111111]">{{ $namaApprover }}</span>
                            @else
                                Menunggu persetujuan <span class="font-semibold text-[#111111]">{{ ucfirst(str_replace('_', ' ', $step->jabatan ?? 'Penyetuju')) }}</span>
                            @endif
                        @elseif($isRejected)
                            Ditolak
                        @else
                            Menunggu giliran
                        @endif
                                </p>
                            </div>

                            {{-- Status badge kecil --}}
                            @if($isApproved)
                            <span class="flex-shrink-0 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">✓</span>
                            @elseif($isWaiting)
                            <span class="flex-shrink-0 text-[10px] font-bold text-[#111111] bg-gray-100 px-2 py-0.5 rounded-full animate-pulse">Aktif</span>
                            @elseif($isRejected)
                            <span class="flex-shrink-0 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">✕</span>
                            @endif
                        </div>
                    </div>
                </div>

                @empty
                {{-- Steps belum dibuat — surat masih pending_admin --}}
                <div class="flex gap-3 relative">
                    {{-- Node berdenyut di step pertama --}}
                    <div class="relative z-10 flex-shrink-0 mt-0.5">
                        <div class="w-7 h-7 rounded-full bg-[#111111] flex items-center justify-center relative">
                            <i data-lucide="hourglass" style="width:12px;height:12px;color:white;"></i>
                            <span class="absolute inset-0 rounded-full bg-[#111111] opacity-30 animate-ping"></span>
                        </div>
                    </div>
                    <div class="flex-1 pb-2 min-w-0">
                        <p class="text-[13px] font-semibold text-[#111111] leading-tight">
                            @if($firstApproverConfig)
                                {{ $firstApproverConfig->label ?? ucfirst(str_replace('_', ' ', $firstApproverConfig->jabatan_label ?? 'Penyetuju Pertama')) }}
                            @else
                                Menunggu Verifikasi Admin
                            @endif
                        </p>
                        <p class="text-[11px] mt-0.5 leading-snug text-[var(--color-primary)] font-medium">
                            @if($surat->status === 'pending_admin')
                                Surat sedang diperiksa oleh
                                <span class="font-semibold text-[#111111]">Admin Sekretariat</span>
                            @else
                                Surat menunggu masuk ke alur persetujuan
                            @endif
                        </p>
                        <span class="inline-flex items-center gap-1 mt-1.5 text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                            Belum disetujui
                        </span>
                    </div>
                </div>
                @endforelse

            </div>

            {{-- download buttons --}}
            <div class="flex flex-col gap-3">
                {{-- Tombol download PDF asli (lampiran dari pembuat) --}}
                @if($surat->file_pdf === 'ARCHIVED')
                    <div class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-gray-100 text-gray-400 rounded-full text-[13px] font-poppins font-medium w-full cursor-not-allowed">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                        Dokumen Asli Diarsipkan
                    </div>
                @elseif($surat->file_pdf)
                    @can('download', $surat)
                    <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'original']) }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-[var(--color-primary)] text-white rounded-full text-[13px] font-poppins font-medium shadow-sm hover:bg-[var(--color-primary-dark)] transition w-full">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        Unduh Dokumen Asli
                    </a>
                    @endcan
                @endif
                
                {{-- Lembar Persetujuan (cover PDF yang dibuat ApprovalService) --}}
                @if($surat->status === 'approved_owner' && $surat->cover_pdf_path)
                @if($surat->cover_pdf_path === 'ARCHIVED')
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid #F0F4F2;">
                    <p style="font-size:11px;font-weight:500;color:#6B7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">
                        Lembar Persetujuan
                    </p>
                    <div style="display:inline-flex;align-items:center;gap:8px;background:#F3F4F6;color:#9CA3AF;border-radius:9999px;padding:10px 20px;font-family:'Poppins',sans-serif;font-size:13px;font-weight:500;cursor:not-allowed;">
                        <i data-lucide="archive" style="width:15px;height:15px;"></i>
                        Lembar Persetujuan Diarsipkan
                    </div>
                </div>
                @else
                @php
                    $coverExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($surat->cover_pdf_path);
                @endphp
                @if($coverExists)
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid #F0F4F2;">
                    <p style="font-size:11px;font-weight:500;color:#6B7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">
                        Lembar Persetujuan
                    </p>
                    <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'cover']) }}"
                       style="display:inline-flex;align-items:center;gap:8px;background:var(--color-text);color:white;border-radius:9999px;padding:10px 20px;font-family:'Poppins',sans-serif;font-size:13px;font-weight:500;text-decoration:none;">
                        <i data-lucide="file-check" style="width:15px;height:15px;"></i>
                        Unduh Lembar Persetujuan (PDF)
                    </a>
                </div>
                @else
                <div style="margin-top:12px;">
                    <p style="font-size:12px;color:#9CA3AF;">
                        <i data-lucide="alert-circle" style="width:12px;height:12px;display:inline;"></i>
                        File PDF tidak ditemukan di penyimpanan
                    </p>
                </div>
                @endif
                @endif
                @endif
                
                {{-- PDF Final (jika ada) --}}
                @if($surat->hasFinalPdf())
                    @if($surat->final_pdf_path === 'ARCHIVED')
                    <div class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-gray-100 text-gray-400 rounded-[28px] text-sm font-semibold shadow-sm cursor-not-allowed">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                        PDF Final Diarsipkan
                    </div>
                    @else
                    <a href="{{ route('surat.download', ['surat' => $surat->id, 'type' => 'final']) }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-emerald-600 text-white rounded-[28px] text-sm font-semibold shadow-sm hover:bg-emerald-700 border border-emerald-500 transition">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        Unduh PDF Akhir (Ditandatangani)
                    </a>
                    @endif
                @endif
                
                {{-- Fallback jika tidak ada file apapun --}}
                @if(!$surat->file_pdf && !$surat->cover_pdf_path && !$surat->hasFinalPdf())
                <p class="text-sm text-center text-gray-400 italic py-4">Tidak ada file yang tersedia</p>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('modals')
{{-- ── Modal: Approve Letter ── --}}
<div id="modalApprove" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[400px] rounded-[28px] shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="px-8 pt-8 pb-5">
            <div class="flex items-center gap-4 mb-1">
                <div class="w-11 h-11 rounded-2xl bg-[var(--color-bg-light)] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield-check" class="w-5 h-5 text-[var(--color-primary)]"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[#111111] leading-tight">Setujui Surat</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $surat->nomor_surat }}</p>
                </div>
                <button type="button" onclick="closeModals()" class="ml-auto text-gray-300 hover:text-gray-500 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Divider --}}
        <div class="h-px bg-gray-100 mx-8"></div>

        <form action="{{ route('surat.approve', $surat->id) }}" method="POST" class="px-8 pb-8 pt-5 space-y-4">
            @csrf

            {{-- Catatan --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    Catatan <span class="font-normal normal-case text-gray-300">(opsional)</span>
                </label>
                <input type="text" name="catatan"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 text-[#111111] rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] transition placeholder-gray-300"
                    placeholder="Tambahkan catatan...">
            </div>

            {{-- PIN --}}
            @if($waitingStep && $waitingStep->is_signer)
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    PIN Anda <span class="text-[var(--color-primary)]">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                    <input type="password" name="pin" id="approvalPin" maxlength="6" required
                        class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-100 text-[#111111] rounded-2xl text-sm font-bold tracking-[0.3em] text-center outline-none focus:ring-2 focus:ring-[var(--color-primary)]/20 focus:border-[var(--color-primary)] transition placeholder-gray-300"
                        placeholder="••••••">
                    <button type="button" onclick="toggleApprovalPin()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition">
                        <i data-lucide="eye" class="w-4 h-4" id="approvalPinEyeIcon"></i>
                    </button>
                </div>
                <p class="text-[10px] text-gray-300 mt-1.5 pl-1">PIN digunakan sebagai konfirmasi tanda tangan digital Anda</p>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModals()"
                    class="flex-1 py-3 border border-gray-200 text-gray-500 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-bold hover:bg-[var(--color-primary-dark)] transition shadow-sm shadow-red-200 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    @if($waitingStep && $waitingStep->is_signer)
                        Setujui dengan Tanda Tangan
                    @else
                        Setujui Sekarang
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Reject Letter ── --}}
<div id="modalReject" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[480px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-8 pt-10 text-center">
            <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="alert-triangle" class="w-10 h-10 text-rose-500"></i>
            </div>
            <h3 class="text-2xl font-sans font-bold text-[#111111] mb-2">Tolak Dokumen</h3>
            <p class="text-sm text-gray-500 px-6">Apakah Anda yakin ingin menolak dokumen ini? Anda harus memberikan alasan kepada pembuat dokumen.</p>
        </div>

        <form action="{{ route('surat.reject', $surat->id) }}" method="POST" class="p-8 pt-0">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan_revisi" rows="4" required
                    class="w-full bg-rose-50/30 border-none rounded-[28px] p-4 text-sm focus:ring-2 focus:ring-rose-100 transition resize-none"
                    placeholder="Jelaskan alasan dokumen ini ditolak..."></textarea>
            </div>

            <div class="mt-8 flex flex-col gap-3">
                <button type="submit" 
                    class="w-full py-4 bg-rose-500 text-white rounded-[28px] text-sm font-bold shadow-lg shadow-rose-100 hover:bg-rose-600 transition flex items-center justify-center gap-2">
                    <i data-lucide="x-circle" class="w-5 h-5"></i> Konfirmasi Penolakan
                </button>
                <button type="button" onclick="closeModals()"
                    class="w-full py-4 bg-white text-gray-500 rounded-[28px] text-sm font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('modals')
{{-- ── Modal: TTD Surat Turunan ── --}}
<div id="modalTtdTurunan" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[440px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">

        {{-- Header --}}
        <div class="p-8 pb-4 text-center">
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="pen-line" class="w-8 h-8 text-amber-500"></i>
            </div>
            <h3 class="text-xl font-sans font-bold text-[#111111] mb-1">Tanda Tangan Digital</h3>
            <p class="text-sm text-gray-500">
                <span id="ttdModalTitle" class="font-semibold text-[var(--color-text)]"></span>
            </p>
            <p class="text-xs text-gray-400 mt-1 px-4">Masukkan PIN Anda untuk mengkonfirmasi tanda tangan pada surat turunan ini.</p>
        </div>

        {{-- Form --}}
        <form id="ttdModalForm" method="POST" class="p-8 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                    PIN Keamanan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="password" id="ttdPinInput" name="pin"
                           maxlength="6" required autocomplete="off"
                           class="hivi-input"
                           placeholder="••••••">
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <button type="submit"
                    class="w-full py-4 bg-amber-500 text-white rounded-[28px] text-sm font-bold shadow-lg shadow-amber-100 hover:bg-amber-600 transition flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Konfirmasi Tanda Tangan
                </button>
                <button type="button" onclick="closeTtdModal()"
                    class="w-full py-3 bg-white text-gray-500 rounded-[28px] text-sm font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>
@endpush

@push('scripts')
<script>
    // ── Modal: TTD Surat Turunan ─────────────────────────────────
    // Data di-set oleh openTtdModal() sebelum modal dibuka
    let _ttdAction = '';

    function openTtdModal(suratTurunanId, signerId, namaTemplate) {
        // Susun action URL: surat/{surat}/turunan/{suratTurunan}/signer/{signer}/sign
        _ttdAction = `/surat/{{ $surat->id }}/turunan/${suratTurunanId}/signer/${signerId}/sign`;

        document.getElementById('ttdModalTitle').textContent  = namaTemplate || 'Surat Turunan';
        document.getElementById('ttdModalForm').action        = _ttdAction;
        document.getElementById('ttdPinInput').value          = '';

        const modal = document.getElementById('modalTtdTurunan');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Fokus ke input PIN
        setTimeout(() => document.getElementById('ttdPinInput').focus(), 150);
    }

    function closeTtdModal() {
        const modal = document.getElementById('modalTtdTurunan');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Tutup jika klik backdrop
    window.addEventListener('click', function(e) {
        if (e.target.id === 'modalTtdTurunan') {
            closeTtdModal();
        }
    });
    function openApproveModal() {
        const modal = document.getElementById('modalApprove');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function toggleApprovalPin() {
        const input = document.getElementById('approvalPin');
        const icon  = document.getElementById('approvalPinEyeIcon');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    function openRejectModal() {
        const modal = document.getElementById('modalReject');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeModals() {
        ['modalApprove', 'modalReject'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        });
        document.body.style.overflow = 'auto';
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        if (e.target.id === 'modalApprove' || e.target.id === 'modalReject') {
            closeModals();
        }
    });

    // ── PDF.js Viewer & Overlay logic ──
    (function() {
        const showCoords = @json($surat->ttd_coordinates ?? []);
        @php
            $mappedApprovals = $surat->approvals->map(function($a) {
                return [
                    'jabatan' => $a->jabatan,
                    'status' => $a->status,
                    'approver_name' => $a->approver?->name,
                    'ttd_path' => $a->ttd_snapshot ? asset('storage/' . $a->ttd_snapshot) : ($a->approver?->ttd_path ? asset('storage/' . $a->approver?->ttd_path) : null),
                ];
            });
        @endphp
        const showApprovals = @json($mappedApprovals);

        const canvas = document.getElementById('show-pdf-canvas');
        if (!canvas) return; // No PDF preview card rendered

        let pdfDoc = null;
        let pageNum = 1;
        const pdfUrl = "{{ route('surat.download', ['surat' => $surat->id, 'type' => 'original']) }}";

        const loadScript = (src) => {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        };

        const initViewer = async () => {
            try {
                if (!window.pdfjsLib) {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
                }
                const pdfjsLib = window.pdfjsLib;
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                document.getElementById('show-total-pages').textContent = pdfDoc.numPages;

                await renderPage(1);

                document.getElementById('show-prev-page').onclick = () => {
                    if (pageNum > 1) {
                        pageNum--;
                        renderPage(pageNum);
                    }
                };
                document.getElementById('show-next-page').onclick = () => {
                    if (pdfDoc && pageNum < pdfDoc.numPages) {
                        pageNum++;
                        renderPage(pageNum);
                    }
                };
            } catch (e) {
                console.error('Error in PDF viewer:', e);
            }
        };

        const renderPage = async (num) => {
            if (!pdfDoc) return;
            try {
                const page = await pdfDoc.getPage(num);
                const ctx = canvas.getContext('2d');
                
                // Scale target: standard fit (scaled down to 0.75 for a compact look)
                const containerWidth = canvas.parentElement.parentElement.clientWidth - 32;
                const scale = Math.min(0.75, (containerWidth / page.getViewport({ scale: 1 }).width) * 0.75);
                const viewport = page.getViewport({ scale: scale });

                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                document.getElementById('show-current-page').textContent = num;
                document.getElementById('show-prev-page').disabled = num <= 1;
                document.getElementById('show-next-page').disabled = num >= pdfDoc.numPages;

                renderOverlaidStamps();
            } catch (e) {
                console.error('Error rendering page:', e);
            }
        };

        const renderOverlaidStamps = () => {
            const layer = document.getElementById('show-marker-layer');
            if (!layer) return;
            layer.innerHTML = '';

            Object.entries(showCoords).forEach(([jabatan, coord]) => {
                if (coord.page !== pageNum) return;

                const approval = showApprovals.find(a => a.jabatan === jabatan);
                const isDone = approval && approval.status === 'approved';
                const ttdPath = approval ? approval.ttd_path : null;

                const wrap = document.createElement('div');
                wrap.style.cssText = `position:absolute; left:${coord.x}%; top:${coord.y}%; transform:translate(-50%,-50%); z-index:10; pointer-events:none; display:flex; flex-direction:column; align-items:center; gap:3px;`;

                if (isDone && ttdPath) {
                    const box = document.createElement('div');
                    box.style.cssText = `width:100px; height:48px; background:rgba(255,255,255,0.9); border:2px solid #22c55e; border-radius:6px; box-shadow:0 3px 10px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;`;
                    const img = document.createElement('img');
                    img.src = ttdPath;
                    img.style.cssText = 'max-width:90%; max-height:90%; object-fit:contain;';
                    box.appendChild(img);

                    const badge = document.createElement('div');
                    badge.style.cssText = `position:absolute; top:-6px; right:-6px; width:14px; height:14px; background:#22c55e; border-radius:50%; border:2px solid white; display:flex; align-items:center; justify-content:center;`;
                    badge.innerHTML = `<svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="4.5"><polyline points="20 6 9 17 4 12"/></svg>`;
                    box.appendChild(badge);
                    wrap.appendChild(box);
                } else {
                    const box = document.createElement('div');
                    const isRejected = approval && approval.status === 'rejected';
                    const borderColor = isRejected ? '#ef4444' : '#6366f1';
                    const bgColor = isRejected ? 'rgba(254,242,242,0.85)' : 'rgba(245,247,255,0.85)';
                    const textColor = isRejected ? '#b91c1c' : '#4f46e5';
                    const labelText = isRejected ? 'Ditolak' : 'Menunggu';

                    box.style.cssText = `width:100px; height:48px; background:${bgColor}; border:1.5px dashed ${borderColor}; border-radius:6px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 2px;`;
                    box.innerHTML = `
                        <span style="font-size:7px; font-weight:800; text-transform:uppercase; color:${textColor};">${labelText}</span>
                        <span style="font-size:8px; font-weight:bold; color:#1e293b; max-width:90%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;">${jabatan.replace(/_/g, ' ').toUpperCase()}</span>
                    `;
                    wrap.appendChild(box);
                }
                layer.appendChild(wrap);
            });
        };

        window.addEventListener('load', initViewer);
        if (document.readyState === 'complete') {
            initViewer();
        }
    })();
</script>
@endpush



