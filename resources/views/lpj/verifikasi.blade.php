@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Verifikasi Laporan Pertanggungjawaban (LPJ)</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Tinjau laporan pertanggungjawaban kegiatan organisasi dan berikan persetujuan atau revisi berkas.
            </p>
        </div>
    </div>

    {{-- List LPJ Submitted --}}
    <div class="space-y-8">
        @forelse($lpjs as $lpj)
            @php
                $surat = $lpj->surat;
                $detail = $surat->kegiatanDetail;
            @endphp
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6 md:p-8">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-6 border-b border-gray-50 pb-6 mb-6">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 mb-3 animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Menunggu Verifikasi
                        </span>
                        <h2 class="text-2xl font-sans font-bold text-[#111111]">{{ $detail->nama_kegiatan ?? $surat->perihal }}</h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Diajukan oleh PIC: <span class="font-bold text-gray-700">{{ $surat->picUser->name }}</span> &bull; 
                            Organisasi: <span class="font-semibold text-[#E62129]">{{ $surat->organisasi->nama ?? '-' }}</span>
                        </p>
                    </div>

                    {{-- Verification Action Buttons --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <button type="button" 
                                onclick="openRevisiModal('{{ route('lpj.verify', $surat->id) }}', '{{ $detail->nama_kegiatan ?? $surat->perihal }}')"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-2xl text-xs font-bold transition border border-rose-150">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Kembalikan / Revisi
                        </button>
                        <button type="button"
                                onclick="openVerifyModal('{{ route('lpj.verify', $surat->id) }}', '{{ $detail->nama_kegiatan ?? $surat->perihal }}')"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition shadow-sm shadow-emerald-100">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Sahkan & TTD LPJ
                        </button>
                    </div>
                </div>

                {{-- LPJ Content Details --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- Left side: Ringkasan --}}
                    <div class="space-y-6">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Ringkasan Kegiatan</span>
                            <div class="bg-gray-50 rounded-[28px] p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-line border border-gray-100">
                                {{ $lpj->ringkasan_kegiatan }}
                            </div>
                        </div>

                        {{-- Realisasi Anggaran --}}
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Realisasi Anggaran</span>
                            <div class="bg-white border border-gray-100 rounded-[28px] overflow-hidden">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                                            <th class="py-3 px-4">Nama Pengeluaran / Item</th>
                                            <th class="py-3 px-4 text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 text-gray-600">
                                        @php $totalAnggaran = 0; @endphp
                                        @forelse($lpj->realisasi_anggaran ?? [] as $item)
                                            @php $totalAnggaran += $item['jumlah']; @endphp
                                            <tr>
                                                <td class="py-3 px-4">{{ $item['item'] }}</td>
                                                <td class="py-3 px-4 text-right font-semibold text-gray-800">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="py-4 px-4 text-center text-gray-400">Tidak ada pengeluaran anggaran yang dicatat.</td>
                                            </tr>
                                        @endforelse
                                        @if(count($lpj->realisasi_anggaran ?? []) > 0)
                                            <tr class="bg-emerald-50/20 font-bold text-emerald-800 border-t border-gray-100">
                                                <td class="py-3 px-4">Total Pengeluaran</td>
                                                <td class="py-3 px-4 text-right text-base">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Right side: Lampiran --}}
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Berkas Lampiran Pendukung</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($lpj->lpjLampirans as $lampiran)
                                <div class="bg-gray-50 border border-gray-100 rounded-[28px] p-4 flex flex-col justify-between gap-3 text-xs">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-2xl bg-white border border-gray-150 text-gray-500 shrink-0">
                                            @if($lampiran->tipe === 'foto')
                                                <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                                            @elseif($lampiran->tipe === 'video')
                                                <i data-lucide="video" class="w-4 h-4 text-amber-500"></i>
                                            @elseif($lampiran->tipe === 'kwitansi')
                                                <i data-lucide="receipt" class="w-4 h-4 text-emerald-500"></i>
                                            @else
                                                <i data-lucide="file" class="w-4 h-4"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-700 capitalize">{{ $lampiran->tipe }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $lampiran->keterangan ?? 'Tanpa keterangan' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        @if($lampiran->tipe === 'foto')
                                            <a href="{{ asset('storage/' . $lampiran->file_path) }}" target="_blank"
                                               class="flex-1 text-center py-1.5 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg font-bold text-[10px] text-gray-600 transition">
                                                Pratinjau
                                            </a>
                                        @endif
                                        <a href="{{ asset('storage/' . $lampiran->file_path) }}" download
                                           class="flex-1 text-center py-1.5 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white rounded-lg font-bold text-[10px] transition">
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-12 text-center text-xs text-gray-400 bg-gray-50/50 border border-dashed border-gray-200 rounded-[28px]">
                                    <i data-lucide="file-warning" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                                    Tidak ada file lampiran terlampir.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-16 text-center">
                <div class="w-16 h-16 bg-[var(--color-bg-light)] rounded-[28px] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle" class="w-8 h-8 text-[#E62129]"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Semua LPJ Terverifikasi</h3>
                <p class="text-sm text-gray-500">Tidak ada Laporan Pertanggungjawaban (LPJ) baru yang menunggu verifikasi saat ini.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Verifikasi PIN Modal --}}
<div id="modalVerify" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[440px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        
        <div class="p-8 pb-4 text-center">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <h3 class="text-xl font-sans font-bold text-[#111111] mb-1">Verifikasi & Sahkan LPJ</h3>
            <p class="text-xs text-gray-500">
                <span id="verifyModalTitle" class="font-semibold text-[var(--color-text)]"></span>
            </p>
            <p class="text-xs text-gray-400 mt-1 px-4">Masukkan PIN Keamanan Anda untuk menyetujui, menandatangani secara digital, dan mengarsipkan LPJ ini.</p>
        </div>

        <form id="verifyModalForm" method="POST" class="p-8 pt-4">
            @csrf
            <input type="hidden" name="action" value="approve">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                    PIN Keamanan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="password" id="verifyPinInput" name="pin"
                           maxlength="6" required autocomplete="off"
                           class="hivi-input"
                           placeholder="••••••">
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <button type="submit"
                        class="w-full py-4 bg-emerald-600 text-white rounded-[28px] text-sm font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Sahkan & Bubuhkan TTD
                </button>
                <button type="button" onclick="closeVerifyModal()"
                        class="w-full py-3 bg-white text-gray-500 rounded-[28px] text-sm font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>

{{-- Revisi Modal --}}
<div id="modalRevisi" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[480px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        
        <div class="p-8 pb-4 text-center">
            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="x-circle" class="w-8 h-8 text-rose-500"></i>
            </div>
            <h3 class="text-xl font-sans font-bold text-[#111111] mb-1">Minta Revisi LPJ</h3>
            <p class="text-xs text-gray-500">
                <span id="revisiModalTitle" class="font-semibold text-[var(--color-text)]"></span>
            </p>
        </div>

        <form id="revisiModalForm" method="POST" class="p-8 pt-4">
            @csrf
            <input type="hidden" name="action" value="reject">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                    Catatan Revisi / Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="catatanRevisiInput" name="catatan_revisi" rows="4" required
                          placeholder="Jelaskan bagian laporan atau anggaran yang harus direvisi/dilengkapi oleh PIC..."
                          class="hivi-input"></textarea>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                <button type="submit"
                        class="w-full py-4 bg-rose-600 text-white rounded-[28px] text-sm font-bold shadow-lg shadow-rose-100 hover:bg-rose-700 transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Permintaan Revisi
                </button>
                <button type="button" onclick="closeRevisiModal()"
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
    // Verify (Approve) Modal
    function openVerifyModal(actionUrl, namaKegiatan) {
        document.getElementById('verifyModalTitle').textContent = namaKegiatan || 'Kegiatan';
        document.getElementById('verifyModalForm').action       = actionUrl;
        document.getElementById('verifyPinInput').value          = '';

        const modal = document.getElementById('modalVerify');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => document.getElementById('verifyPinInput').focus(), 150);
    }

    function closeVerifyModal() {
        const modal = document.getElementById('modalVerify');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Revisi Modal
    function openRevisiModal(actionUrl, namaKegiatan) {
        document.getElementById('revisiModalTitle').textContent = namaKegiatan || 'Kegiatan';
        document.getElementById('revisiModalForm').action       = actionUrl;
        document.getElementById('catatanRevisiInput').value      = '';

        const modal = document.getElementById('modalRevisi');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => document.getElementById('catatanRevisiInput').focus(), 150);
    }

    function closeRevisiModal() {
        const modal = document.getElementById('modalRevisi');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        const modalVerify = document.getElementById('modalVerify');
        const modalRevisi = document.getElementById('modalRevisi');
        if (e.target === modalVerify) closeVerifyModal();
        if (e.target === modalRevisi) closeRevisiModal();
    });
</script>
@endpush



