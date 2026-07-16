@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#1A2B24]">Database Arsip LPJ</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Temukan seluruh laporan pertanggungjawaban kegiatan organisasi yang telah disahkan dan diarsipkan.
            </p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6 mb-8">
        <form action="{{ route('arsip.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            {{-- Search Input --}}
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kata Kunci Pencarian</label>
                    @if(isset($isSemanticActive) && $isSemanticActive)
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md border border-purple-100">
                            <i data-lucide="sparkles" class="w-3 h-3"></i> Pencarian Pintar (AI) aktif
                        </span>
                    @endif
                </div>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kegiatan, isi laporan, jenis surat..."
                           class="hivi-input">
                </div>
            </div>

            {{-- Organisasi Filter --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Organisasi</label>
                <select name="organisasi_id"
                        class="hivi-input">
                    <option value="">Semua Organisasi</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ request('organisasi_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun Filter & Action Buttons --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tahun Arsip</label>
                <div class="flex gap-2">
                    <select name="tahun"
                            class="hivi-input">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="px-4 py-2.5 bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] rounded-2xl text-xs font-bold transition shrink-0 shadow-sm">
                        Cari
                    </button>
                    @if(request()->anyFilled(['q', 'organisasi_id', 'tahun']))
                        <a href="{{ route('arsip.index') }}"
                           class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-2xl text-xs font-bold transition shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Grid List Arsip --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($lpjs as $lpj)
            @php
                $surat = $lpj->surat;
                $detail = $surat->kegiatanDetail;
            @endphp
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] flex flex-col justify-between transition hover:shadow-md">
                
                {{-- Content --}}
                <div class="p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                            <i data-lucide="archive" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-green-50 text-green-700 border border-green-250">
                                Valid & Sah
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 block mt-1.5 uppercase">No. Induk: {{ $surat->nomor_surat ?? '-' }}</span>
                        </div>
                    </div>

                    <h3 class="text-base font-sans font-bold text-[#1A2B24] mb-1.5 leading-snug">{{ $detail->nama_kegiatan ?? $surat->perihal }}</h3>
                    <p class="text-[11px] text-gray-500 mb-4">
                        Organisasi: <span class="font-semibold text-gray-700">{{ $surat->organisasi->nama ?? '-' }}</span> &bull; 
                        PIC: <span class="font-medium text-gray-700">{{ $surat->picUser->name }}</span>
                    </p>

                    {{-- Summary snippet --}}
                    <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-3 text-xs text-gray-600 leading-relaxed line-clamp-3">
                        {{ $lpj->ringkasan_kegiatan }}
                    </div>
                </div>

                {{-- Footer info & actions --}}
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 rounded-b-[24px] flex items-center justify-between gap-4 text-xs">
                    <div class="text-gray-400 text-[10px]">
                        @if($lpj->archived_at)
                            Arsip: {{ $lpj->archived_at->translatedFormat('d M Y') }}
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('lpj.show', $surat->id) }}"
                           class="inline-flex items-center gap-1 px-4 py-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white rounded-lg font-bold transition text-[11px]">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat LPJ
                        </a>
                        @if($surat->hasFinalPdf())
                            <a href="{{ route('surat.download', $surat->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 rounded-lg font-bold transition text-[11px]">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i> Proposal
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-16 text-center">
                <div class="w-16 h-16 bg-[var(--color-primary)]/10 rounded-[28px] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="folder-search" class="w-8 h-8 text-[var(--color-text)]"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Arsip Tidak Ditemukan</h3>
                <p class="text-sm text-gray-500">Tidak ada Laporan Pertanggungjawaban yang sesuai dengan pencarian/filter Anda.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection



