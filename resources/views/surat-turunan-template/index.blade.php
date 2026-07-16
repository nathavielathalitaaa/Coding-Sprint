@extends('layouts.master')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#111111]">Template Surat Turunan</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">
            Kelola teks default untuk setiap jenis surat turunan yang bisa digenerate dari surat induk.
        </p>
    </div>
</div>

{{-- Info banner placeholder --}}
<div class="mb-6 flex items-start gap-3 p-4 rounded-[28px] border border-blue-100 bg-blue-50/60">
    <i data-lucide="info" class="w-4 h-4 text-blue-500 mt-0.5 shrink-0"></i>
    <div class="text-xs text-blue-700 leading-relaxed">
        <span class="font-semibold">Placeholder token</span> — gunakan
        <code class="bg-blue-100 px-1 py-0.5 rounded font-mono text-[11px]">{{'{{'}}token{{'}}'}}</code>
        di dalam teks. Saat generate, token otomatis diganti dengan data kegiatan surat induk.
        Daftar lengkap token tersedia di halaman edit masing-masing template.
    </div>
</div>

{{-- Grid template cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
    @foreach($templates as $tpl)
    @php
        $preview = mb_substr(strip_tags($tpl->konten_template), 0, 160);
        if (mb_strlen($tpl->konten_template) > 160) $preview .= '…';
    @endphp
    <div class="bg-white rounded-[20px] border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.05)] flex flex-col transition hover:shadow-md">

        {{-- Card header --}}
        <div class="flex items-start justify-between p-6 pb-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[var(--color-bg-light)] flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#E62129]"></i>
                </div>
                <div>
                    <h2 class="text-base font-sans font-bold text-[#111111]">{{ $tpl->nama }}</h2>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-[var(--color-bg-light)] text-[#E62129]">
                        {{ $tpl->kode }}
                    </span>
                </div>
            </div>

            {{-- Toggle aktif/nonaktif --}}
            <button type="button"
                onclick="toggleTemplate({{ $tpl->id }}, this)"
                data-active="{{ $tpl->is_active ? '1' : '0' }}"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border transition
                       {{ $tpl->is_active
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                            : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $tpl->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                {{ $tpl->is_active ? 'Aktif' : 'Nonaktif' }}
            </button>
        </div>

        {{-- Preview konten --}}
        <div class="px-6 pb-4 flex-1">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-widest mb-2">Preview Isi</p>
            <div class="bg-gray-50 rounded-2xl p-3 font-mono text-[11px] text-gray-500 leading-relaxed whitespace-pre-wrap break-words"
                 style="max-height:120px; overflow:hidden;">{{ $preview }}</div>
        </div>

        {{-- Footer actions --}}
        <div class="px-6 pb-5 pt-2 flex items-center gap-3 border-t border-gray-50">
            <a href="{{ route('surat-turunan-template.edit', $tpl->id) }}"
               class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[var(--color-primary)] text-white rounded-full text-[13px] font-medium hover:bg-[var(--color-primary-dark)] transition">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                Ubah Template
            </a>
            <span class="text-[11px] text-gray-300">
                Diperbarui {{ $tpl->updated_at->diffForHumans() }}
            </span>
        </div>

    </div>
    @endforeach
</div>

@endsection

@push('scripts')
<script>
async function toggleTemplate(id, btn) {
    try {
        const res = await fetch(`/admin/surat-turunan-template/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (!data.success) throw new Error('Gagal');

        const isActive = data.is_active;
        btn.setAttribute('data-active', isActive ? '1' : '0');

        // Update label teks
        btn.innerHTML = `
            <span class="w-1.5 h-1.5 rounded-full ${isActive ? 'bg-emerald-500' : 'bg-gray-300'}"></span>
            ${isActive ? 'Aktif' : 'Nonaktif'}
        `;

        // Update warna pill
        btn.className = btn.className
            .replace(/bg-\S+ text-\S+ border-\S+ hover:bg-\S+/g, '')
            .trim();
        if (isActive) {
            btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'hover:bg-emerald-100');
            btn.classList.remove('bg-gray-50', 'text-gray-400', 'border-gray-200', 'hover:bg-gray-100');
        } else {
            btn.classList.add('bg-gray-50', 'text-gray-400', 'border-gray-200', 'hover:bg-gray-100');
            btn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'hover:bg-emerald-100');
        }
    } catch (e) {
        alert('Gagal mengubah status template. Silakan coba lagi.');
    }
}
</script>
@endpush

