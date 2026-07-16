@extends('layouts.master')

@section('content')
@php
    $slotLabels = [
        'ketua_pelaksana' => 'Ketua Pelaksana / Ketua Organisasi',
        'pembina' => 'Pembina Organisasi',
        'kepala_sekolah' => 'Kepala Sekolah'
    ];
@endphp

<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#1A2B24]">Buat Surat Turunan</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Pilih jenis surat turunan dan tentukan pihak yang menandatanganinya.
            </p>
        </div>
        <a href="{{ route('surat.show', $surat->id) }}"
           class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
            Kembali
        </a>
    </div>

    {{-- Induk Letter Info --}}
    <div class="bg-gray-50 rounded-[28px] p-6 border border-gray-100 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-[var(--color-bg-light)] rounded-2xl flex items-center justify-center shrink-0">
                <i data-lucide="file-text" class="w-6 h-6 text-[#2E7D5E]"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Surat Induk</p>
                <h4 class="text-base font-bold text-gray-800">{{ $surat->perihal }}</h4>
                <p class="text-xs text-gray-500 mt-1">
                    No. Surat: {{ $surat->nomor_surat ?? '(Belum ada nomor)' }} &bull; Pengaju: {{ $surat->user->name }}
                </p>
            </div>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                Disetujui Penuh
            </span>
        </div>
    </div>

    {{-- Error / Validation Alerts --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border-l-4 border-red-500 text-red-700">
            <p class="text-sm font-semibold mb-1">Terjadi Kesalahan:</p>
            <ul class="text-xs list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Form --}}
    <form action="{{ route('surat.turunan.store', $surat->id) }}" method="POST">
        @csrf

        {{-- Check if signers are available --}}
        @if(empty($availableSigners))
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-rose-50 rounded-[28px] flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="user-x" class="w-8 h-8 text-rose-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Penandatangan Tidak Tersedia</h3>
                <p class="text-sm text-gray-500">Tidak ada penandatangan yang bisa dipilih untuk surat ini.</p>
                <div class="mt-6">
                    <a href="{{ route('surat.show', $surat->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-250 text-gray-600 rounded-2xl text-sm font-semibold transition">
                        Kembali Ke Surat Induk
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-6">
                @forelse($templates as $tpl)
                    <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6 transition duration-200">
                        {{-- Template Checkbox Header --}}
                        <div class="flex items-start gap-4">
                            <div class="pt-1">
                                <input type="checkbox" name="templates[]" value="{{ $tpl->kode }}"
                                       id="tpl-checkbox-{{ $tpl->kode }}"
                                       class="w-5 h-5 text-[#2E7D5E] border-gray-300 rounded focus:ring-[#2E7D5E] transition cursor-pointer"
                                       onchange="toggleSignersContainer('{{ $tpl->kode }}')">
                            </div>
                            <div class="flex-1 cursor-pointer select-none" onclick="toggleCheckbox('tpl-checkbox-{{ $tpl->kode }}')">
                                <h3 class="text-lg font-sans font-bold text-[#1A2B24]">{{ $tpl->nama }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $tpl->deskripsi ?? 'Buat surat turunan menggunakan template ini.' }}</p>
                                <div class="mt-2">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-[var(--color-bg-light)] text-[#2E7D5E]">
                                        {{ $tpl->kode }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Signers Selection Container (Toggled via JS) --}}
                        <div id="signers-container-{{ $tpl->kode }}" class="hidden mt-6 pt-6 border-t border-gray-50">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Pihak Penandatangan (Signers)</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($availableSigners as $slot => $user)
                                    <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-2xl border border-gray-100 hover:bg-gray-100/50 transition cursor-pointer"
                                         onclick="toggleSubCheckbox('signer-{{ $tpl->kode }}-{{ $slot }}')">
                                        <input type="checkbox" name="signers[{{ $tpl->kode }}][]" value="{{ $slot }}"
                                               id="signer-{{ $tpl->kode }}-{{ $slot }}"
                                               class="w-4 h-4 text-[#2E7D5E] border-gray-300 rounded focus:ring-[#2E7D5E] transition cursor-pointer"
                                               onclick="event.stopPropagation()">
                                        <div class="flex-1 leading-none">
                                            <p class="text-sm font-semibold text-gray-700">{{ $user->name }}</p>
                                            <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-1 inline-block">
                                                {{ $slotLabels[$slot] ?? ucfirst(str_replace('_', ' ', $slot)) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-[28px] flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="file-warning" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Template Tidak Aktif</h3>
                        <p class="text-sm text-gray-500">Tidak ada template surat turunan aktif yang tersedia saat ini.</p>
                    </div>
                @endforelse

                @if($templates->isNotEmpty())
                    <div class="flex items-center justify-end pt-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-bold hover:bg-[var(--color-primary-dark)] transition shadow-md">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                            Buat Surat Turunan
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Toggle sub-checkbox container based on template checkbox state
    function toggleSignersContainer(kode) {
        const checkbox = document.getElementById(`tpl-checkbox-${kode}`);
        const container = document.getElementById(`signers-container-${kode}`);
        
        if (checkbox.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            // Uncheck all signer checkboxes for this template when template is unchecked
            const signers = container.querySelectorAll('input[type="checkbox"]');
            signers.forEach(s => s.checked = false);
        }
    }

    // Toggle main template checkbox when card is clicked
    function toggleCheckbox(id) {
        const cb = document.getElementById(id);
        cb.checked = !cb.checked;
        // Trigger the onchange handler
        const event = new Event('change');
        cb.dispatchEvent(event);
    }

    // Toggle signer checkbox when row is clicked
    function toggleSubCheckbox(id) {
        const cb = document.getElementById(id);
        cb.checked = !cb.checked;
    }
</script>
@endpush


