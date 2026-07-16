@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Pelaksanaan Kegiatan Saya</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Pantau pelaksanaan, perbarui progress, dan tandai kegiatan selesai untuk memulai penyusunan LPJ.
            </p>
        </div>
    </div>

    {{-- Grid List Pelaksanaan PIC --}}
    <div class="grid grid-cols-1 gap-8">
        @forelse($surats as $surat)
            @php
                $detail = $surat->kegiatanDetail;
                $latestUpdate = $surat->progressUpdates->first();
                $currentProgress = $latestUpdate ? $latestUpdate->persentase : 0;
            @endphp
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6 md:p-8 transition duration-200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- Column 1: Info Kegiatan & Progress --}}
                    <div class="space-y-6">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[var(--color-bg-light)] text-[var(--color-primary)] mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-primary)]"></span>
                                {{ ucfirst($surat->status_pelaksanaan) }}
                            </span>
                            <h2 class="text-2xl font-sans font-bold text-[#111111]">{{ $detail->nama_kegiatan ?? $surat->perihal }}</h2>
                            <p class="text-xs text-gray-500 mt-1">
                                Organisasi: <span class="font-semibold text-gray-700">{{ $surat->organisasi->nama ?? '-' }}</span>
                            </p>
                        </div>

                        {{-- Timeline --}}
                        <div class="bg-gray-50 rounded-[28px] p-4 border border-gray-100 space-y-2.5 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                <span>
                                    {{ \Carbon\Carbon::parse($detail->tanggal_mulai)->translatedFormat('d M Y') }}
                                    @if($detail->tanggal_selesai)
                                        s/d {{ \Carbon\Carbon::parse($detail->tanggal_selesai)->translatedFormat('d M Y') }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                <span class="font-medium">{{ $detail->lokasi }}</span>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold">
                                <span class="text-gray-400 uppercase tracking-widest">Progress Pelaksanaan</span>
                                <span class="text-[var(--color-primary)]">{{ $currentProgress }}%</span>
                            </div>
                            <div class="w-full bg-gray-150 h-3.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: {{ $currentProgress }}%; background: var(--color-primary);"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Column 2: Update Progress Form & Selesai --}}
                    <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100 space-y-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 mb-4">Perbarui Progress Kegiatan</h4>
                            <form action="{{ route('pelaksanaan.progress', $surat->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Persentase (0-100)</label>
                                    <div class="flex items-center gap-3">
                                        <input type="range" name="persentase" min="0" max="100" value="{{ $currentProgress }}"
                                               id="slider-{{ $surat->id }}"
                                               class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-[var(--color-primary)]"
                                               oninput="document.getElementById('badge-{{ $surat->id }}').textContent = this.value + '%'">
                                        <span id="badge-{{ $surat->id }}" class="bg-white border border-gray-200 px-3 py-1.5 rounded-2xl text-xs font-bold text-gray-700 shrink-0 shadow-sm">
                                            {{ $currentProgress }}%
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Catatan Progress</label>
                                    <textarea name="catatan" rows="2" placeholder="Tulis perkembangan pelaksanaan kegiatan..." required
                                              class="w-full bg-white border border-gray-250 rounded-2xl py-3 px-4 text-xs focus:ring-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] transition outline-none resize-none"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-xs font-bold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
                                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Progress
                                </button>
                            </form>
                        </div>

                        {{-- Mark Selesai Action --}}
                        <div class="pt-4 border-t border-gray-150">
                            <button type="button"
                                    onclick="openSelesaiModal('{{ route('pelaksanaan.selesai', $surat->id) }}', '{{ addslashes($detail->nama_kegiatan ?? $surat->perihal) }}')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-2xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Tandai Kegiatan Selesai
                            </button>
                        </div>
                    </div>

                    {{-- Column 3: Histori Progress (Timeline) --}}
                    <div class="lg:border-l lg:border-gray-100 lg:pl-8 space-y-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Histori Perkembangan</span>
                        <div class="space-y-4 overflow-y-auto max-h-[300px] pr-2">
                            @forelse($surat->progressUpdates as $upd)
                                <div class="relative pl-6 border-l-2 border-gray-100 pb-4 last:pb-0">
                                    {{-- Bullet icon --}}
                                    <div class="absolute -left-[7px] top-1 w-3 h-3 bg-gray-200 border-2 border-white rounded-full {{ $upd->persentase === 100 ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                    <div class="text-xs">
                                        <div class="flex justify-between items-center gap-2">
                                            <span class="font-bold text-gray-700">Progress: {{ $upd->persentase }}%</span>
                                            <span class="text-[10px] text-gray-400">{{ $upd->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-500 mt-1 leading-relaxed">{{ $upd->catatan }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-gray-400">
                                    <i data-lucide="activity" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                                    Belum ada perkembangan yang dilaporkan.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-16 text-center">
                <div class="w-16 h-16 bg-[var(--color-bg-light)] rounded-[28px] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8 text-[var(--color-primary)]"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Tidak Ada Kegiatan Aktif</h3>
                <p class="text-sm text-gray-500">Anda tidak terdaftar sebagai PIC untuk kegiatan aktif apapun saat ini.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Selesai Modal --}}
<div id="modalSelesai" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[480px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        
        <div class="p-8 pb-4 text-center">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <h3 class="text-xl font-sans font-bold text-[#111111] mb-1">Tandai Kegiatan Selesai</h3>
            <p class="text-xs text-gray-500">
                <span id="selesaiModalTitle" class="font-semibold text-[var(--color-text)]"></span>
            </p>
            <p class="text-xs text-gray-400 mt-2 px-4">
                Dengan menandai kegiatan selesai, sistem akan mengunci alur progress dan membuat draf Laporan Pertanggungjawaban (LPJ).
            </p>
        </div>

        <form id="selesaiModalForm" method="POST" class="p-8 pt-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                    Catatan Penutup Kegiatan <span class="text-red-500">*</span>
                </label>
                <textarea id="catatanPenutupInput" name="catatan_penutup" rows="4" required
                          placeholder="Berikan ringkasan penutup hasil pelaksanaan kegiatan, kendala, atau kesimpulan..."
                          class="hivi-input"></textarea>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <button type="submit"
                        class="w-full py-4 bg-emerald-600 text-white rounded-[28px] text-sm font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Selesaikan Kegiatan
                </button>
                <button type="button" onclick="closeSelesaiModal()"
                        class="w-full py-3 bg-white text-gray-500 rounded-[28px] text-sm font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSelesaiModal(actionUrl, namaKegiatan) {
        document.getElementById('selesaiModalTitle').textContent = namaKegiatan || 'Kegiatan';
        document.getElementById('selesaiModalForm').action       = actionUrl;
        document.getElementById('catatanPenutupInput').value      = '';

        const modal = document.getElementById('modalSelesai');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => document.getElementById('catatanPenutupInput').focus(), 150);
    }

    function closeSelesaiModal() {
        const modal = document.getElementById('modalSelesai');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalSelesai');
        if (e.target === modal) {
            closeSelesaiModal();
        }
    });
</script>
@endpush



