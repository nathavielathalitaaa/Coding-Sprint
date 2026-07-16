@extends('layouts.master')

@section('content')
@php
    $slotLabels = [
        'ketua_pelaksana' => 'Ketua Pelaksana / Ketua Organisasi',
        'pembina' => 'Pembina Organisasi',
        'kepala_sekolah' => 'Kepala Sekolah'
    ];
@endphp

<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Surat Turunan</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Kelola, tanda tangani, dan unduh dokumen turunan dari surat induk.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('surat.show', $surat->id) }}"
               class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
                Detail Surat Induk
            </a>
            <a href="{{ route('surat.turunan.create', $surat->id) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Generate Surat Turunan Baru
            </a>
        </div>
    </div>

    {{-- Induk Letter Info Banner --}}
    <div class="bg-gray-50 rounded-[28px] p-5 border border-gray-100 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-[var(--color-bg-light)] rounded-2xl flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-5 h-5 text-[#E62129]"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Surat Induk</p>
                <h4 class="text-sm font-bold text-gray-800">{{ $surat->perihal }}</h4>
                <p class="text-xs text-gray-500 mt-0.5">
                    No. Surat: {{ $surat->nomor_surat ?? '(Belum ada nomor)' }} &bull; Pengaju: {{ $surat->user->name }}
                </p>
            </div>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Disetujui Penuh
            </span>
        </div>
    </div>

    {{-- Grid List Surat Turunan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($suratTurunans as $st)
            @php
                $signedCount = $st->signers->where('status', 'signed')->count();
                $totalCount = $st->signers->count();
                $isSigned = $st->status === 'ditandatangani';
            @endphp
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] flex flex-col transition hover:shadow-md">
                
                {{-- Card Header --}}
                <div class="p-6 pb-4 border-b border-gray-50 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gray-50 border border-gray-150 flex items-center justify-center shrink-0">
                            <i data-lucide="file-check-2" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-sans font-bold text-[#111111]">{{ $st->template->nama ?? 'Surat Turunan' }}</h3>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600">
                                {{ $st->nomor_surat ?? '(Belum ada nomor)' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @if($st->status === 'ditandatangani')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Ditandatangani
                            </span>
                        @elseif($st->status === 'menunggu_ttd')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu TTD
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200">
                                <i data-lucide="info" class="w-3.5 h-3.5"></i> Draft
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Signer Progress & Details --}}
                <div class="px-6 py-4 flex-1">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Progress Persetujuan</span>
                        <span class="text-xs font-bold text-[#E62129] bg-[var(--color-bg-light)] px-2 py-0.5 rounded">
                            {{ $signedCount }}/{{ $totalCount }} Signer
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($st->signers as $signer)
                            <div class="flex items-center justify-between p-3 rounded-2xl border border-gray-50 bg-gray-50/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center font-bold text-xs text-gray-500 shrink-0">
                                        {{ strtoupper(substr($signer->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">{{ $signer->user->name ?? 'Unknown' }}</p>
                                        <span class="text-[9px] font-bold text-gray-400 tracking-wider uppercase">
                                            {{ $slotLabels[$signer->jabatan_slot] ?? ucfirst(str_replace('_', ' ', $signer->jabatan_slot)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($signer->status === 'signed')
                                        <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-600">
                                            <i data-lucide="check-circle-2" class="w-4 h-4"></i> Ditandatangani
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 text-xs font-bold text-gray-400">
                                            <i data-lucide="circle-dashed" class="w-4 h-4"></i> Menunggu
                                        </span>

                                        {{-- Tanda Tangan Button --}}
                                        @if(auth()->id() === (int)$signer->user_id)
                                            <button type="button"
                                                    onclick="openTtdModal('{{ route('surat.turunan.sign', [$surat->id, $st->id, $signer->id]) }}', '{{ $st->template->nama }}')"
                                                    class="ml-2 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1 shadow-sm shadow-amber-100">
                                                <i data-lucide="pen-line" class="w-3.5 h-3.5"></i> Tanda Tangan
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Card Footer Actions --}}
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 rounded-b-[24px] flex items-center justify-between gap-4">
                    <span class="text-[10px] text-gray-400">
                        Dibuat {{ $st->created_at->diffForHumans() }}
                    </span>

                    @if($isSigned)
                        <a href="{{ route('surat.turunan.download', [$surat->id, $st->id]) }}"
                           class="inline-flex items-center gap-1 px-4 py-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white rounded-lg text-xs font-bold transition shadow-sm">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh PDF
                        </a>
                    @else
                        <button type="button" disabled
                                class="inline-flex items-center gap-1 px-4 py-2 bg-gray-200 text-gray-400 rounded-lg text-xs font-bold cursor-not-allowed">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Belum Selesai TTD
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-16 text-center">
                <div class="w-16 h-16 bg-[var(--color-primary)]/10 rounded-[28px] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="copy" class="w-8 h-8 text-[var(--color-primary)]"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Surat Turunan</h3>
                <p class="text-sm text-gray-500 mb-6">Dokumen turunan belum digenerate dari surat induk ini.</p>
                <a href="{{ route('surat.turunan.create', $surat->id) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Generate Surat Turunan
                </a>
            </div>
        @endforelse
    </div>
</div>

{{-- PIN Modal for signing --}}
<div id="modalTtdTurunan" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white w-full max-w-[440px] rounded-[32px] overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        
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
@endsection

@push('scripts')
<script>
    function openTtdModal(actionUrl, namaTemplate) {
        document.getElementById('ttdModalTitle').textContent  = namaTemplate || 'Surat Turunan';
        document.getElementById('ttdModalForm').action        = actionUrl;
        document.getElementById('ttdPinInput').value          = '';

        const modal = document.getElementById('modalTtdTurunan');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (window.lucide) {
            window.lucide.createIcons();
        }

        setTimeout(() => document.getElementById('ttdPinInput').focus(), 150);
    }

    function closeTtdModal() {
        const modal = document.getElementById('modalTtdTurunan');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalTtdTurunan');
        if (e.target === modal) {
            closeTtdModal();
        }
    });
</script>
@endpush



