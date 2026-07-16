@extends('layouts.master')

@section('content')
<style>
  *, *::before, *::after { box-sizing: border-box; }

  /* Grid layout for top stats */
  .dashboard-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  @media (max-width: 768px) {
    .dashboard-stats {
      gap: 12px;
      margin-bottom: 24px;
    }
    
    .stat-card {
      padding: 16px !important;
      min-height: 110px !important;
      border-radius: 20px !important;
    }
    
    .stat-card .stat-number {
      font-size: 32px !important;
      margin-bottom: 6px !important;
    }
    
    .stat-card .stat-label {
      font-size: 9.5px !important;
      letter-spacing: 0.02em !important;
    }
  }

  /* Skeleton Loading */
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: shimmer 1.4s ease infinite;
    border-radius: 8px;
  }
  @keyframes shimmer {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
  }
  .real-content {
    opacity: 0;
    transition: opacity 200ms ease;
  }
  .real-content.loaded {
    opacity: 1;
  }
  .skeleton-wrapper {
    transition: opacity 200ms ease;
  }
</style>

<div class="w-full">
    {{-- Heading --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111] tracking-tight mb-1">Halo, {{ auth()->user()->name }}! </h1>
            <p class="text-sm text-gray-500 font-medium">Selamat datang kembali di SIMORA. Pantau pengajuan dan persetujuan surat Anda di sini.</p>
        </div>

    </div>

    {{-- 3 Stats Cards --}}
    <div class="dashboard-stats">
        <!-- Telah Diajukan -->
        <div class="bg-white p-6 rounded-[20px] shadow-sm flex flex-col justify-between min-h-[125px] stat-card">
            <div class="flex items-start justify-between">
                <span class="text-[42px] font-sans font-bold text-[#111111] leading-none stat-number">{{ $telahDiajukanCount }}</span>
                <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-500 shrink-0">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider stat-label mt-3 block">Telah diajukan</span>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white p-6 rounded-[20px] shadow-sm flex flex-col justify-between min-h-[125px] stat-card">
            <div class="flex items-start justify-between">
                <span class="text-[42px] font-sans font-bold text-[#111111] leading-none stat-number">{{ $sedangDiprosesCount }}</span>
                <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-500 shrink-0">
                    <i data-lucide="loader" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider stat-label mt-3 block">Sedang diproses</span>
        </div>

        <!-- Telah Disetujui -->
        <div class="bg-white p-6 rounded-[20px] shadow-sm flex flex-col justify-between min-h-[125px] stat-card">
            <div class="flex items-start justify-between">
                <span class="text-[42px] font-sans font-bold text-[#111111] leading-none stat-number">{{ $telahDisetujuiCount }}</span>
                <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-500 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider stat-label mt-3 block">Telah disetujui</span>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8 mt-8">
        <!-- LEFT COLUMN: Table & Search (8 cols on lg/xl, 12 on mobile) -->
        <div class="col-span-12 lg:col-span-8 flex flex-col">
            {{-- Section Title --}}
            <div class="mb-4">
                <h2 class="text-xl font-sans font-bold text-[#111111]">Daftar Surat</h2>
            </div>

            {{-- Search & Action Bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 mb-6">
                <form action="{{ route('home') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari surat..." 
                        class="px-5 py-2.5 bg-[#E5E7EB] text-[#111111] rounded-2xl text-sm font-medium border-0 outline-none w-full sm:w-[280px] focus:ring-2 focus:ring-red-400 placeholder-gray-500"
                    >
                    @if(request('search'))
                        <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-red-500 hover:underline">Reset</a>
                    @endif
                </form>
                
                @can('create', App\Models\Surat::class)
                <a href="{{ route('surat.create') }}" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-[#E62129] text-white rounded-2xl text-sm font-semibold hover:bg-[#C91A20] transition shadow-sm whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4"></i> Ajukan surat
                </a>
                @endcan
            </div>

            {{-- Table / Mail List Container --}}
            <div class="w-full mb-8">
                <!-- Skeleton Loading -->
                <div class="skeleton-wrapper w-full">
                    <div class="space-y-4">
                        @for($i=0; $i<3; $i++)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white rounded-[28px] shadow-sm border border-gray-100 px-6 py-5 gap-4">
                            <div class="flex items-center gap-4 w-full">
                                <div class="skeleton w-12 h-12 rounded-full flex-shrink-0"></div>
                                <div class="flex-grow space-y-2">
                                    <div class="skeleton h-4 w-1/3"></div>
                                    <div class="skeleton h-3 w-1/4"></div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Real Content Table -->
                <div class="real-content hidden w-full">
                    <div class="bg-[#E5E7EB]/40 p-6 rounded-[28px] border border-gray-200/60 shadow-[0_4px_20px_rgba(0,0,0,0.01)] overflow-x-auto">
                        <table class="w-full min-w-[900px] border-collapse text-left">
                            <thead>
                                <tr class="border-b border-gray-300 text-gray-700 text-xs font-semibold uppercase tracking-wider">
                                    <th class="pb-3 pr-4">Pengaju</th>
                                    <th class="pb-3 pr-4">Perihal</th>
                                    <th class="pb-3 pr-4">Jenis Surat</th>
                                    <th class="pb-3 pr-4">Organisasi</th>
                                    <th class="pb-3 pr-4">Nomor Surat</th>
                                    <th class="pb-3 pr-4">Tanggal</th>
                                    <th class="pb-3 pr-4 text-center">Status</th>
                                    <th class="pb-3 pl-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @inject('approvalService', 'App\Services\ApprovalService')
                                @forelse($surats as $surat)
                                    @php
                                        $isMyTurn = false;
                                        if ($surat->status === 'submitted') {
                                            $isMyTurn = $approvalService->canApprove('surat_' . $surat->jenis_surat, $surat->id, auth()->user());
                                        }
                                    @endphp
                                    <tr class="border-b border-gray-200/50 hover:bg-gray-200/40 text-sm transition last:border-0 {{ $isMyTurn ? 'bg-red-50/50' : '' }}">
                                        <!-- Pengaju -->
                                        <td class="py-4 pr-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-[#E62129]/10 text-[#E62129] font-bold flex items-center justify-center text-xs flex-shrink-0">
                                                    {{ strtoupper(substr($surat->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="font-semibold text-[#111111]">{{ $surat->user->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>

                                        <!-- Perihal -->
                                        <td class="py-4 pr-4 text-gray-800 font-medium">
                                            {{ $surat->perihal ?? '-' }}
                                        </td>

                                        <!-- Jenis Surat -->
                                        <td class="py-4 pr-4 text-gray-600">
                                            {{ $surat->suratType ? $surat->suratType->nama : ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }}
                                        </td>

                                        <!-- Organisasi -->
                                        <td class="py-4 pr-4 text-gray-600">
                                            {{ $surat->organisasi->nama ?? '-' }}
                                        </td>

                                        <!-- Nomor Surat -->
                                        <td class="py-4 pr-4 font-mono text-xs text-gray-500">
                                            {{ $surat->nomor_surat ?? '-' }}
                                        </td>

                                        <!-- Tanggal -->
                                        <td class="py-4 pr-4 text-gray-500 text-xs">
                                            {{ $surat->created_at->format('d M Y H:i') }}
                                        </td>

                                        <!-- Status -->
                                        <td class="py-4 pr-4 text-center">
                                            @if($surat->status === 'submitted')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Diajukan</span>
                                            @elseif($surat->status === 'approved_owner')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Disetujui</span>
                                            @elseif($surat->status === 'rejected')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Ditolak</span>
                                            @elseif($surat->status === 'revised')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Revisi</span>
                                            @elseif($surat->status === 'pending_admin')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Menunggu Admin</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ $surat->status }}</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-4 pl-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('surat.show', $surat->id) }}" class="p-1.5 text-gray-500 hover:text-[#E62129] hover:bg-white/80 rounded-lg transition" title="Lihat Detail">
                                                    <i data-lucide="eye" class="w-4.5 h-4.5"></i>
                                                </a>
                                                @if($surat->hasFinalPdf())
                                                    <a href="{{ route('surat.download', $surat->id) }}" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-white/80 rounded-lg transition" title="Download PDF">
                                                        <i data-lucide="download" class="w-4.5 h-4.5"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-gray-400 py-8 bg-white/50 rounded-2xl">Belum ada surat yang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $surats->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Secondary Info & Panduan (4 cols on lg/xl, 12 on mobile) -->
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
            {{-- Secondary Dashboard Panels --}}
            @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
                {{-- Admin: Log Aktivitas & Aksi Cepat --}}
                <div class="bg-white p-6 rounded-[28px] border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex flex-col gap-4">
                    <h3 class="font-bold text-base text-gray-800">Statistik Data</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100/50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Pengurus Aktif</span>
                                <span class="text-xl font-bold text-[#111111] mt-1 block">{{ $totalPengurus ?? 0 }}</span>
                            </div>
                            <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-500 shrink-0">
                                <i data-lucide="users" class="w-4.5 h-4.5"></i>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100/50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Menunggu Persetujuan</span>
                                <span class="text-xl font-bold text-[#111111] mt-1 block">{{ $suratMenungguCount ?? 0 }}</span>
                            </div>
                            <div class="w-9 h-9 bg-white border border-gray-100 rounded-full flex items-center justify-center text-gray-500 shrink-0">
                                <i data-lucide="clock" class="w-4.5 h-4.5"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas Terbaru -->
                <div class="bg-white p-6 rounded-[28px] border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-base text-gray-800">Aktivitas Terbaru</h3>
                        <a href="{{ route('activity.log') }}" class="text-xs text-red-500 hover:underline font-semibold">Lihat Semua</a>
                    </div>
                    @if(isset($recentActivities) && $recentActivities->count())
                        <div class="space-y-4 max-h-[220px] overflow-y-auto pr-1">
                            @foreach($recentActivities as $log)
                                <div class="flex items-start gap-3 text-sm">
                                    <div class="w-7 h-7 bg-red-50 text-red-600 rounded-full flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-semibold text-gray-800 text-xs">{{ $log->user?->name ?? 'System' }}</p>
                                        <p class="text-gray-500 text-[11px] mt-0.5 leading-snug">{{ $log->description }} &bull; <span class="opacity-70">{{ $log->created_at->diffForHumans() }}</span></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-xs text-gray-400 py-6">Belum ada aktivitas.</p>
                    @endif
                </div>
            @else
                {{-- User: Organisasi Saya --}}
                <div class="bg-white p-6 rounded-[28px] border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                    <h3 class="font-bold text-base text-gray-800 mb-4">Organisasi Saya</h3>
                    @if(isset($myOrganisasi) && $myOrganisasi->count())
                        <div class="space-y-3">
                            @foreach($myOrganisasi as $orgMember)
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-gray-50 border border-gray-100">
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800 leading-tight">{{ $orgMember->organisasi->nama ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">{{ $orgMember->organisasi->tipe ?? '-' }}</p>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wide">
                                        {{ $orgMember->jabatan }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-xs text-gray-400 py-6">Anda belum tergabung dalam organisasi manapun.</p>
                    @endif
                </div>

                <!-- LPJ Monitoring (If user has LPJs to track) -->
                <div class="bg-white p-6 rounded-[28px] border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                    <h3 class="font-bold text-base text-gray-800 mb-4">Monitoring LPJ</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100/50 text-center">
                            <span class="text-2xl font-bold text-amber-700 block leading-tight">{{ $lpjRevisiCount ?? 0 }}</span>
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block mt-1">Perlu Revisi</span>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100/50 text-center">
                            <span class="text-2xl font-bold text-blue-700 block leading-tight">{{ $lpjPendingCount ?? 0 }}</span>
                            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block mt-1">Submitted</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Panduan Singkat Alur SIMORA -->
            <div class="bg-white border border-gray-100 rounded-[28px] p-6 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-[var(--color-primary)]"></i>
                    Alur Pengajuan Surat
                </h3>
                <div class="relative pl-6 border-l-2 border-red-100 space-y-5 ml-3">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-[var(--color-primary)] border-4 border-white"></div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">1. Pembuatan Proposal</h4>
                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">BPH organisasi mengunggah draf proposal kegiatan atau surat resmi.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-[var(--color-primary)] border-4 border-white"></div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">2. Review Admin</h4>
                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">Admin memverifikasi berkas, memberi nomor surat, & memberikan disposisi awal.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-[var(--color-primary)] border-4 border-white"></div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">3. Persetujuan Bertahap</h4>
                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">Dokumen disetujui bertahap oleh BPH MPK, Pembina, hingga Kepala Sekolah.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="relative">
                        <div class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-[var(--color-primary)] border-4 border-white"></div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">4. Tanda Tangan Digital</h4>
                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">Surat ditandatangani digital & siap diunduh resmi dalam format PDF.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
