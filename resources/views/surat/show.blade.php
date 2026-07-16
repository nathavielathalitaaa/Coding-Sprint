@extends('layouts.master')

@section('content')

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

            {{-- ── Preview posisi TTD (hanya mode stamp & ada koordinat) ── --}}
            @php
                $hasTtdCoords = $surat->ttd_coordinates && count($surat->ttd_coordinates) > 0;
                $docType = 'surat_' . $surat->jenis_surat;
                
                if ($surat->suratType) {
                    $isStampMode = true; // SuratType default to stamp
                } else {
                    $firstStep = \App\Models\ApprovalStep::where('document_type', $docType)->first();
                    $isStampMode = $firstStep?->ttd_mode === 'stamp';
                }
            @endphp
            @if($hasTtdCoords && $isStampMode)
            <div class="bg-white p-5 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[var(--color-primary)]"></i>
                    <h6 class="font-sans text-base font-semibold text-[#111111]">Posisi Tanda Tangan</h6>
                    <span class="ml-auto text-[11px] font-poppins px-3 py-0.5 rounded-full border border-[var(--color-text)] text-[var(--color-text)]">Mode Stamp</span>
                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    {{-- minimap A4 --}}
                    <div style="position:relative;border:1px solid #E5E7EB;border-radius:8px;overflow:hidden;background:#F5F5F7;width:90px;height:127px;flex-shrink:0;">
                        {{-- A4 paper grid lines --}}
                        <div style="position:absolute;inset:0;display:grid;grid-template-rows:repeat(10,1fr);opacity:0.15;">
                            @for($l=0;$l<10;$l++)
                            <div style="border-bottom:1px solid #9CA3AF;"></div>
                            @endfor
                        </div>
                        {{-- Document icon --}}
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;">
                            <i data-lucide="file-text" style="width:24px;height:24px;color:#E5E7EB;"></i>
                        </div>
                        {{-- TTD position markers --}}
                        @foreach($surat->ttd_coordinates as $jabatan => $coord)
                        @php
                            $colors = ['hod'=>'var(--color-text)','hr'=>'var(--color-primary)','purchasing'=>'#f59e0b','owner_rep'=>'#3b82f6','direktur'=>'#8b5cf6','supervisor'=>'#06b6d4'];
                            $c = $colors[$jabatan] ?? 'var(--color-text)';
                        @endphp
                        <div style="position:absolute;left:{{ $coord['x'] }}%;top:{{ $coord['y'] }}%;transform:translate(-50%,-50%);z-index:10;">
                            <div style="background:var(--color-bg-light);color:var(--color-primary);font-size:5px;font-weight:700;padding:2px 6px;border-radius:999px;white-space:nowrap;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                {{ strtoupper($jabatan ?? 'Approver') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Legend --}}
                    <div class="flex-1">
                        <div class="space-y-1.5">
                            @foreach($surat->ttd_coordinates as $jabatan => $coord)
                            @php $c = $colors[$jabatan] ?? 'var(--color-text)'; @endphp
                            <div class="flex items-center gap-2">
                                <div style="width:8px;height:8px;border-radius:50%;background:var(--color-primary);flex-shrink:0;"></div>
                                <span class="text-xs font-poppins px-2 py-0.5 bg-[var(--color-bg-light)] text-[var(--color-primary)] rounded-full">{{ strtoupper($jabatan ?? 'Approver') }}</span>
                                <span class="text-[10px] text-gray-400 ml-auto">X: {{ number_format($coord['x'],1) }}% Y: {{ number_format($coord['y'],1) }}% (Hal {{ $coord['page'] ?? 1 }})</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
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
            <div class="bg-white border border-[var(--color-bg-light)] p-5 rounded-[20px] shadow-sm mt-4 flex flex-col md:flex-row items-center justify-between gap-4" style="background: linear-gradient(145deg, #ffffff 0%, #f9fbf9 100%);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--color-primary)] flex items-center justify-center shadow-lg shadow-gray-200">
                        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-sans font-bold text-[#111111]">Menunggu Persetujuan Anda</h4>
                        <p class="text-[13px] text-gray-500 mt-0.5">Silakan tinjau rinciannya dan berikan tanda tangan digital Anda.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button type="button" 
                        onclick="openApproveModal()"
                        class="flex-1 md:flex-none px-6 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-bold hover:bg-[var(--color-primary-dark)] transition shadow-md shadow-gray-200 flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Sekarang
                    </button>
                    <button type="button"
                        onclick="openRejectModal()"
                        class="flex-1 md:flex-none px-6 py-2.5 bg-white text-red-500 border border-red-100 rounded-2xl text-sm font-bold hover:bg-red-50 transition flex items-center justify-center gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak
                    </button>
                </div>
            </div>
            @endif

        </div>

        {{-- ── kolom kanan — approval panel (1 col) ─────────── --}}
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white p-6 rounded-[20px] shadow-[0_2px_12px_rgba(0,0,0,0.05)] border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-sans font-semibold text-[#111111]">Status & Riwayat</h2>
                    <i data-lucide="history" class="w-5 h-5 text-[var(--color-primary)]"></i>
                </div>

                @php
                    $approved = $steps->where('status','approved')->count();
                    $total    = $steps->count();
                    $percentage = $total > 0 ? round(($approved/$total)*100) : 0;
                @endphp

                <div class="mb-8">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[12px] font-poppins text-[#6B7280]">Progres</span>
                        <span class="text-[32px] font-sans font-bold text-[#111111]">{{ $percentage }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[var(--color-primary)] rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="flex flex-col">
                    @forelse($steps as $index => $step)
                        @php
                            $isApproved = $step->status === 'approved';
                            $isWaiting  = $step->status === 'waiting';
                            $isRejected = $step->status === 'rejected';
                            
                            $circleBg = '#F3F4F6';
                            $circleColor = '#9CA3AF';
                            if ($isApproved) {
                                $circleBg = 'var(--color-primary)';
                                $circleColor = 'white';
                            } elseif ($isWaiting) {
                                $circleBg = 'var(--color-text)';
                                $circleColor = 'white';
                            }
                        @endphp
                        
                        <div class="flex items-center gap-3 py-[10px]" style="{{ !$loop->last ? 'border-bottom: 0.5px solid #F3F4F6;' : '' }}">
                            <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center flex-shrink-0 text-[12px] font-bold" 
                                 style="background: {{ $circleBg }}; color: {{ $circleColor }};">
                                {{ $index + 1 }}
                            </div>

                            <div class="flex-1">
                                <p class="text-[13px] font-poppins font-medium text-[#111111] leading-tight">
                                    {{ $step->jabatan ?? $step->label ?? 'Penyetuju' }}
                                </p>
                                <p class="text-[11px] font-poppins font-light text-[#6B7280]">
                                    @if($isApproved)
                                        {{ $step->approver->name ?? 'User' }} · {{ $step->actioned_at->format('d/m/y H:i') }}
                                    @elseif($isWaiting)
                                        Giliran {{ $step->assignedUser->name ?? ($step->jabatan ?? 'Penyetuju') }}
                                    @else
                                        Menunggu
                                    @endif
                                </p>
                            </div>
                            
                            @if($isApproved)
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-[var(--color-primary)]"></i>
                            @elseif($isWaiting)
                                <i data-lucide="clock" class="w-4 h-4 text-[var(--color-text)] animate-pulse"></i>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-400 italic">Belum ada data riwayat.</p>
                        </div>
                    @endforelse
                </div>
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
<div id="modalApprove" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[420px] rounded-[32px] p-8 shadow-2xl animate-in fade-in zoom-in duration-200 relative">
        <button type="button" onclick="closeModals()" class="absolute right-6 top-6 text-gray-400 hover:text-gray-600 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div class="text-center mb-8">
            <h3 class="text-2xl font-sans font-bold text-[#111111]">Security Pin</h3>
        </div>

        <form action="{{ route('surat.approve', $surat->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2 pl-1">Notes</label>
                <input type="text" name="catatan" class="w-full px-5 py-3.5 bg-[#E5E7EB] text-[#111111] rounded-full text-sm font-medium border-0 outline-none focus:ring-2 focus:ring-red-400 placeholder-gray-500" placeholder="Tambahkan catatan...">
            </div>

            @if($waitingStep && $waitingStep->is_signer)
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2 pl-1">Password</label>
                <input type="password" name="pin" maxlength="6" required class="w-full px-5 py-3.5 bg-[#E5E7EB] text-[#111111] rounded-full text-sm font-medium border-0 outline-none focus:ring-2 focus:ring-red-400 placeholder-gray-500" placeholder="••••••">
            </div>
            @endif

            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-[#E62129] text-white rounded-full text-sm font-bold shadow-md hover:bg-[#C91A20] transition flex items-center justify-center">
                    Konfirmasi
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
    }

    function openRejectModal() {
        const modal = document.getElementById('modalReject');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
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
</script>
@endpush



