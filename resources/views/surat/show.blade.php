@extends('layouts.master')

@section('content')

    {{-- breadcrumb / header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-playfair font-semibold text-[#1A2B24]">Detail Surat</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap dan status persetujuan surat</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('surat.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white/50 hover:bg-white text-sm font-medium text-gray-600 transition shadow-sm backdrop-blur">
                Kembali
            </a>
            @if(Auth::id() === $surat->user_id)
                @if($surat->canBeEdited())
                <a href="{{ route('surat.edit', $surat->id) }}" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition shadow-sm">
                    <i data-lucide="edit" class="w-4 h-4 inline-block mr-1"></i> Edit
                </a>
                @endif
                
                @if($surat->canBeDeleted())
                <form action="{{ route('surat.destroy', $surat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-rose-500 text-white rounded-xl text-sm font-semibold hover:bg-rose-600 transition shadow-sm">
                        <i data-lucide="trash-2" class="w-4 h-4 inline-block mr-1"></i> Hapus
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── kolom kiri — detail card (2 col) ──────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white/80 backdrop-blur p-8 rounded-3xl shadow-sm border border-white/40">
                <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $surat->nomor_surat }}</h1>
                        <p class="text-gray-500 mt-1">{{ ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }}</p>
                    </div>
                    <div>
                        @if($surat->status === 'approved_owner')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">Selesai</span>
                        @elseif($surat->status === 'rejected')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                        @elseif($surat->status === 'revised')
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">Perlu Revisi</span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">Proses Approval</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-1.5">Perihal</p>
                        <p class="font-medium text-gray-800">{{ $surat->perihal }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-1.5">Pembuat</p>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#80BB9B]/20 flex items-center justify-center font-semibold text-[#4F6560] text-xs">
                                {{ strtoupper(substr($surat->user->name ?? 'U',0,1)) }}
                            </div>
                            <p class="font-medium text-gray-800">{{ $surat->user->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-1.5">Tanggal Dibuat</p>
                        <p class="font-medium text-gray-800">{{ $surat->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-1.5">File Lampiran</p>
                        @if($surat->file_pdf)
                            @can('download', $surat)
                                <a href="{{ route('surat.download', $surat->id) }}" class="inline-flex items-center gap-2 text-[#4F6560] hover:text-[#3d504c] font-medium hover:underline">
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

            {{-- catatan revisi --}}
            @if($surat->catatan_revisi)
            <div class="bg-orange-50/80 backdrop-blur p-6 rounded-3xl shadow-sm border border-orange-200">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                    <p class="text-sm font-bold uppercase text-orange-800 tracking-wider">Catatan Revisi</p>
                </div>
                <p class="text-orange-900 mt-2">{{ $surat->catatan_revisi }}</p>
            </div>
            @endif

            {{-- ── form revisi — hanya untuk staff pemilik ── --}}
            @if($surat->status === 'revised' && Auth::id() === $surat->user_id)
            <div class="bg-amber-50/80 backdrop-blur p-6 rounded-3xl shadow-sm border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="edit-3" class="w-5 h-5 text-amber-600"></i>
                        <h6 class="text-base font-bold text-amber-800">Surat Perlu Direvisi</h6>
                    </div>
                    <p class="text-sm text-amber-700">
                        Surat Anda ditolak. Silakan perbaiki sesuai catatan, lalu upload ulang.
                    </p>
                </div>
                <a href="{{ route('surat.edit', $surat->id) }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-amber-500 text-white rounded-xl text-sm font-bold shadow-sm hover:bg-amber-600 transition shrink-0">
                    <i data-lucide="upload" class="w-4 h-4"></i> Upload Revisi
                </a>
            </div>
            @endif

            {{-- ── form approve — hanya tampil jika giliran jabatan user ── --}}
            @if($canApprove && $surat->status === 'submitted')
            <div class="bg-emerald-50/80 backdrop-blur p-8 rounded-3xl shadow-sm border border-emerald-200">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-emerald-100">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <div>
                        <h6 class="text-base font-bold text-emerald-900">Giliran Anda</h6>
                        <p class="text-sm text-emerald-700">{{ $waitingStep->label ?? 'Approval' }}</p>
                    </div>
                </div>

                <form action="{{ route('surat.approve', $surat->id) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-2">Catatan (opsional)</label>
                            <textarea name="catatan" rows="2" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 bg-white" placeholder="Tambahkan catatan jika ada..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-emerald-800 mb-2">
                                PIN Anda <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="pin" maxlength="6" class="w-full px-4 py-3 rounded-xl border border-emerald-200 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 bg-white" placeholder="Masukkan PIN 6 digit" required>
                            <p class="text-xs text-emerald-600/70 mt-2">PIN digunakan sebagai konfirmasi tanda tangan digital.</p>
                        </div>
                    </div>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow transition">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Setujui Surat
                    </button>
                </form>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-emerald-200/50"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-emerald-50 px-4 text-xs text-emerald-600 font-medium">ATAU</span>
                    </div>
                </div>

                <div x-data="{ open: false }" class="mt-2 text-center sm:text-left">
                    <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-6 py-3 border border-red-200 text-red-600 bg-white hover:bg-red-50 rounded-xl text-sm font-bold transition">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Surat
                    </button>

                    <div x-show="open" x-transition class="mt-6 p-6 bg-white rounded-2xl border border-red-100 text-left">
                        <form action="{{ route('surat.reject', $surat->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-red-700 mb-2">
                                    Alasan Penolakan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="catatan_revisi" rows="3" class="w-full px-4 py-3 rounded-xl border border-red-200 text-sm focus:border-red-400 focus:ring-1 focus:ring-red-100 bg-red-50/30" placeholder="Tuliskan alasan penolakan secara jelas..." required></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition" onclick="return confirm('Yakin ingin menolak surat ini?')">
                                Konfirmasi Penolakan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ── kolom kanan — approval panel (1 col) ─────────── --}}
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-[#4F6560] text-white p-8 rounded-3xl shadow-md border border-[#3d504c]">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-lg font-playfair font-semibold tracking-wide">Status & Riwayat</h2>
                    <i data-lucide="history" class="w-5 h-5 text-[#80BB9B]"></i>
                </div>

                @php
                    $approved = $steps->where('status','approved')->count();
                    $total    = $steps->count();
                    $percentage = $total > 0 ? round(($approved/$total)*100) : 0;
                @endphp

                <div class="mb-8">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm font-medium text-white/80">Progress</span>
                        <span class="text-2xl font-bold">{{ $percentage }}%</span>
                    </div>
                    <div class="h-2.5 bg-black/20 rounded-full overflow-hidden">
                        <div class="h-full bg-[#80BB9B] rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="space-y-6">
                    @forelse($steps as $step)
                        @php
                            $isApproved = $step->status === 'approved';
                            $isWaiting  = $step->status === 'waiting';
                            $isRejected = $step->status === 'rejected';
                        @endphp
                        
                        <div class="flex gap-4 relative">
                            @if(!$loop->last)
                            <div class="absolute top-8 left-4 w-px h-full bg-white/20 -ml-px"></div>
                            @endif

                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10 border-2 
                                {{ $isApproved ? 'bg-[#80BB9B] border-[#80BB9B]' : '' }}
                                {{ $isWaiting  ? 'bg-[#4F6560] border-[#80BB9B]' : '' }}
                                {{ $isRejected ? 'bg-red-500 border-red-500' : '' }}
                                {{ (!$isApproved && !$isWaiting && !$isRejected) ? 'bg-[#4F6560] border-white/20' : '' }}">
                                
                                @if($isApproved)
                                    <i data-lucide="check" class="w-4 h-4 text-[#1A2B24]"></i>
                                @elseif($isWaiting)
                                    <i data-lucide="clock" class="w-4 h-4 text-[#80BB9B] animate-pulse"></i>
                                @elseif($isRejected)
                                    <i data-lucide="x" class="w-4 h-4 text-white"></i>
                                @else
                                    <span class="text-xs font-bold text-white/40">{{ $step->step_order }}</span>
                                @endif
                            </div>

                            <div class="pb-2">
                                <p class="text-sm font-medium {{ $isWaiting ? 'text-[#80BB9B]' : 'text-white' }}">{{ $step->label }}</p>
                                
                                @if($step->approver)
                                    <p class="text-xs text-white/70 mt-0.5">{{ $step->approver->name }}</p>
                                @else
                                    <p class="text-xs text-white/50 mt-0.5 italic">Menunggu approver</p>
                                @endif

                                @if($step->actioned_at)
                                    <p class="text-[11px] text-white/40 mt-1">
                                        {{ $step->actioned_at->format('d M Y, H:i') }}
                                    </p>
                                @endif

                                @if($step->catatan && ($isApproved || $isRejected))
                                    <div class="mt-2 p-3 rounded-xl text-xs {{ $isRejected ? 'bg-red-500/20 text-red-100 border border-red-500/30' : 'bg-black/10 text-white/90 border border-white/10' }}">
                                        "{{ $step->catatan }}"
                                    </div>
                                @endif

                                @if($isApproved && $step->approver && $step->approver->profile)
                                    @php
                                        $signature = $step->approver->profile->signature_path ?? $step->approver->profile->ttd_path;
                                    @endphp
                                    @if($signature)
                                        <div class="mt-2 bg-white rounded-lg p-2 inline-block shadow-inner">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($signature) }}" class="h-8 w-auto object-contain" alt="TTD">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-sm text-white/50 italic">Belum ada data riwayat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- download buttons --}}
            <div class="flex flex-col gap-3">
                @if($surat->status === 'approved_owner' && $surat->cover_pdf_path)
                <a href="{{ \Illuminate\Support\Facades\Storage::url($surat->cover_pdf_path) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-white/80 backdrop-blur text-[#4F6560] rounded-2xl text-sm font-semibold shadow-sm hover:bg-white border border-white/40 transition">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                    Lembar Persetujuan (PDF)
                </a>
                @endif

                @if($surat->hasFinalPdf())
                <a href="{{ asset('storage/' . $surat->final_pdf_path) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-5 py-4 bg-emerald-600 text-white rounded-2xl text-sm font-semibold shadow-sm hover:bg-emerald-700 border border-emerald-500 transition">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Download PDF Final
                </a>
                @endif
            </div>
        </div>

    </div>

@endsection