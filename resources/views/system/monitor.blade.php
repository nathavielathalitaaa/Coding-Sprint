@extends('layouts.master')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#111111]">Monitor Sistem</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">
            Pantau kesehatan server, penyimpanan, log aktivitas, dan statistik dokumen secara real-time.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Sistem Normal
        </span>
        <span class="text-xs text-gray-400">Dipindai: {{ now()->format('d M Y, H:i') }}</span>
    </div>
</div>

{{-- ── BARIS 1: Stat Cards Utama ─────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Surat --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Surat</span>
        </div>
        <p class="text-3xl font-sans font-bold text-[#111111]">{{ number_format($totalSurat) }}</p>
        <p class="text-xs text-gray-400 mt-1">+{{ $suratBulanIni }} bulan ini</p>
    </div>

    {{-- Surat Disetujui --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Disetujui</span>
        </div>
        <p class="text-3xl font-sans font-bold text-[#111111]">{{ number_format($suratApproved) }}</p>
        @if($totalSurat > 0)
        <p class="text-xs text-gray-400 mt-1">{{ round(($suratApproved/$totalSurat)*100, 1) }}% dari total</p>
        @else
        <p class="text-xs text-gray-400 mt-1">—</p>
        @endif
    </div>

    {{-- Menunggu Proses --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Menunggu</span>
        </div>
        <p class="text-3xl font-sans font-bold text-[#111111]">{{ number_format($suratPending) }}</p>
        <p class="text-xs text-gray-400 mt-1">Pending + Submitted</p>
    </div>

    {{-- Log Hari Ini --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                <i data-lucide="activity" class="w-4 h-4 text-purple-600"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Log Hari Ini</span>
        </div>
        <p class="text-3xl font-sans font-bold text-[#111111]">{{ number_format($logsToday) }}</p>
        <p class="text-xs text-gray-400 mt-1">+{{ $logsThisMonth }} bulan ini</p>
    </div>

</div>

{{-- ── BARIS 2: Storage + Database + Log ───────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Penyimpanan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="hard-drive" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Penyimpanan Dokumen</h2>
        </div>

        <div class="space-y-3">
            {{-- Surat --}}
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-2">
                    <i data-lucide="folder" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-xs text-gray-600">/surat</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-[#111111]">{{ $storageSize }}</p>
                    <p class="text-[10px] text-gray-400">{{ $totalFiles }} file</p>
                </div>
            </div>
            {{-- Surat Turunan --}}
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-2">
                    <i data-lucide="folder" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-xs text-gray-600">/surat-turunan</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-[#111111]">{{ $storageTurunanSize }}</p>
                    <p class="text-[10px] text-gray-400">{{ $totalTurunanFiles }} file</p>
                </div>
            </div>
            {{-- Arsip --}}
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-2">
                    <i data-lucide="archive" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-xs text-gray-600">/archives</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-[#111111]">{{ $archiveSize }}</p>
                    <p class="text-[10px] text-gray-400">{{ $totalArchives }} file</p>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('system/monitor/archive-manager') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4F6560] hover:text-[#3d504c] transition">
                <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                Kelola Arsip Dokumen
            </a>
        </div>
    </div>

    {{-- Database --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="database" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Database</h2>
        </div>

        <div class="mb-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Ukuran Database</p>
            <p class="text-3xl font-sans font-bold text-[#111111]">{{ $dbSize }}</p>
        </div>

        <div class="space-y-2">
            @php
                $tables = [
                    ['nama' => 'surats',                  'model' => \App\Models\Surat::class],
                    ['nama' => 'surat_kegiatan_details',  'model' => \App\Models\SuratKegiatanDetail::class],
                    ['nama' => 'surat_turunans',          'model' => \App\Models\SuratTurunan::class],
                    ['nama' => 'document_approvals',      'model' => \App\Models\DocumentApproval::class],
                    ['nama' => 'activity_logs',           'model' => \App\Models\ActivityLog::class],
                    ['nama' => 'users',                   'model' => \App\Models\User::class],
                    ['nama' => 'organisasis',             'model' => \App\Models\Organisasi::class],
                ];
            @endphp
            @foreach($tables as $tbl)
            @php
                try {
                    $count = $tbl['model']::count();
                } catch (\Exception $e) {
                    $count = '?';
                }
            @endphp
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-mono">{{ $tbl['nama'] }}</span>
                <span class="font-semibold text-gray-700">{{ is_numeric($count) ? number_format($count) : $count }} baris</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Log Aktivitas Top Actions --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="bar-chart-2" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Aksi Terbanyak</h2>
        </div>

        <div class="space-y-2">
            @php $maxLog = $topActions->max('total') ?: 1; @endphp
            @forelse($topActions as $action)
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-600 font-medium capitalize">{{ str_replace('_', ' ', $action->action) }}</span>
                    <span class="font-bold text-[#111111]">{{ number_format($action->total) }}</span>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[rgba(230,33,41,0.2)] rounded-full"
                         style="width: {{ round(($action->total / $maxLog) * 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic text-center py-4">Belum ada log aktivitas</p>
            @endforelse
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Total semua aksi</span>
            <span class="text-sm font-bold text-[#111111]">{{ number_format($logCount) }}</span>
        </div>
    </div>

</div>

{{-- ── BARIS 3: Surat per Bulan + Info Server ───────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Grafik surat per bulan --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <i data-lucide="trending-up" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Pengajuan Surat per Bulan (6 Bulan Terakhir)</h2>
        </div>

        @php $maxSurat = $suratPerBulan->max('total') ?: 1; @endphp
        <div class="flex items-end gap-3 h-32">
            @foreach($suratPerBulan as $item)
            @php $pct = round(($item['total'] / $maxSurat) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1.5">
                <span class="text-[10px] font-bold text-gray-500">{{ $item['total'] }}</span>
                <div class="w-full bg-gray-100 rounded-t-lg overflow-hidden" style="height: 80px;">
                    <div class="w-full bg-[rgba(230,33,41,0.2)] rounded-t-lg transition-all"
                         style="height: {{ $pct }}%; margin-top: {{ 100 - $pct }}%;"></div>
                </div>
                <span class="text-[9px] text-gray-400 text-center leading-tight">{{ $item['bulan'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Info Server --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="server" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Info Lingkungan</h2>
        </div>

        <div class="space-y-3">
            @foreach([
                ['icon' => 'code-2',      'label' => 'PHP',          'value' => $phpVersion],
                ['icon' => 'layers',      'label' => 'Laravel',      'value' => $laravelVersion],
                ['icon' => 'globe',       'label' => 'Server',       'value' => $serverInfo],
                ['icon' => 'cpu',         'label' => 'Memori (PHP)', 'value' => $memoryUsage],
                ['icon' => 'timer',       'label' => 'Uptime',       'value' => $uptime],
                ['icon' => 'calendar',    'label' => 'Waktu Server', 'value' => now()->format('d M Y, H:i')],
            ] as $info)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $info['icon'] }}" class="w-3.5 h-3.5 text-gray-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-gray-400 font-medium">{{ $info['label'] }}</p>
                    <p class="text-xs font-semibold text-[#111111] truncate">{{ $info['value'] ?: 'N/A' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── BARIS 4: Log Aktivitas Terbaru ──────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <i data-lucide="scroll-text" class="w-4 h-4 text-[#4F6560]"></i>
            <h2 class="text-sm font-bold text-[#111111]">Log Aktivitas Terbaru</h2>
        </div>
        <a href="{{ route('activity.log') }}"
           class="text-xs font-semibold text-[#4F6560] hover:underline flex items-center gap-1">
            Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    <div class="divide-y divide-gray-50">
        @forelse($recentLogs as $log)
        <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition">
            {{-- Avatar --}}
            <div class="w-8 h-8 rounded-full bg-[var(--color-bg-light)] flex items-center justify-center shrink-0 text-[#E62129] text-xs font-bold">
                {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
            </div>

            {{-- Detail --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-[#111111]">{{ $log->user?->name ?? 'System' }}</span>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 uppercase tracking-wide">
                        {{ str_replace('_', ' ', $log->action) }}
                    </span>
                </div>
                @if($log->description)
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $log->description }}</p>
                @endif
            </div>

            {{-- Waktu + IP --}}
            <div class="shrink-0 text-right">
                <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                @if($log->ip_address)
                <p class="text-[10px] text-gray-300 font-mono">{{ $log->ip_address }}</p>
                @endif
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center">
            <i data-lucide="inbox" class="w-8 h-8 mx-auto text-gray-200 mb-2"></i>
            <p class="text-sm text-gray-400">Belum ada log aktivitas yang tercatat.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
