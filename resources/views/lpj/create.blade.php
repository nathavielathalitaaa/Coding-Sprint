@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Isi Laporan Pertanggungjawaban (LPJ)</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Lengkapi pertanggungjawaban kegiatan berupa ringkasan, realisasi anggaran, dan berkas lampiran pendukung.
            </p>
        </div>
        <a href="{{ route('pelaksanaan.index') }}"
           class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
            Kembali
        </a>
    </div>

    {{-- Alert Revisi --}}
    @if($lpj->status === 'revisi' && $lpj->catatan_revisi)
        <div class="mb-6 p-5 rounded-[28px] bg-amber-50 border border-amber-200 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
            <div>
                <h5 class="text-xs font-bold uppercase tracking-wider text-amber-800">Catatan Revisi Pembina / Admin</h5>
                <p class="text-xs text-amber-700 mt-1 leading-relaxed">{{ $lpj->catatan_revisi }}</p>
            </div>
        </div>
    @endif

    {{-- Kegiatan Info Banner --}}
    <div class="bg-gray-50 rounded-[28px] p-6 border border-gray-100 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-[var(--color-bg-light)] rounded-2xl flex items-center justify-center shrink-0">
                <i data-lucide="award" class="w-6 h-6 text-[#E62129]"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kegiatan</p>
                <h4 class="text-base font-bold text-gray-800">{{ $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal }}</h4>
                <p class="text-xs text-gray-500 mt-1">
                    Organisasi: {{ $surat->organisasi->nama ?? '-' }} &bull; Lokasi: {{ $surat->kegiatanDetail->lokasi }}
                </p>
            </div>
        </div>
    </div>

    {{-- Main Form --}}
    <form action="{{ route('lpj.store', $surat->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- Section 1: Ringkasan Kegiatan --}}
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="file-text" class="w-5 h-5 text-[#E62129]"></i>
                <h3 class="text-lg font-sans font-bold text-[#111111]">Ringkasan Kegiatan</h3>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ringkasan / Laporan Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="ringkasan_kegiatan" rows="6" required
                          placeholder="Jelaskan jalannya acara, kesuksesan, kendala yang dihadapi, serta kesimpulan penutup..."
                          class="hivi-input">{{ old('ringkasan_kegiatan', $lpj->ringkasan_kegiatan) }}</textarea>
            </div>
        </div>

        {{-- Section 2: Realisasi Anggaran --}}
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="dollar-sign" class="w-5 h-5 text-[#E62129]"></i>
                    <h3 class="text-lg font-sans font-bold text-[#111111]">Realisasi Anggaran</h3>
                </div>
                <button type="button" onclick="addAnggaranRow()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[var(--color-bg-light)] text-[#E62129] hover:bg-[#d5ecd1] rounded-2xl text-xs font-bold transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Baris
                </button>
            </div>

            <div class="space-y-3" id="anggaran-rows">
                @php
                    $anggaranList = old('realisasi_anggaran', $lpj->realisasi_anggaran ?? []);
                @endphp
                @forelse($anggaranList as $index => $row)
                    <div class="flex items-center gap-3 anggaran-row" id="anggaran-row-{{ $index }}">
                        <div class="flex-1">
                            <input type="text" name="realisasi_anggaran[{{ $index }}][item]" value="{{ $row['item'] }}" placeholder="Nama Item / Pengeluaran" required
                                   class="hivi-input">
                        </div>
                        <div class="w-1/3">
                            <input type="number" name="realisasi_anggaran[{{ $index }}][jumlah]" value="{{ $row['jumlah'] }}" placeholder="Jumlah (Rp)" required min="0"
                                   class="hivi-input">
                        </div>
                        <button type="button" onclick="removeAnggaranRow({{ $index }})"
                                class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-2xl transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                @empty
                    {{-- Default empty row --}}
                    <div class="flex items-center gap-3 anggaran-row" id="anggaran-row-0">
                        <div class="flex-1">
                            <input type="text" name="realisasi_anggaran[0][item]" placeholder="Nama Item / Pengeluaran"
                                   class="hivi-input">
                        </div>
                        <div class="w-1/3">
                            <input type="number" name="realisasi_anggaran[0][jumlah]" placeholder="Jumlah (Rp)" min="0"
                                   class="hivi-input">
                        </div>
                        <button type="button" onclick="removeAnggaranRow(0)"
                                class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-2xl transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Section 3: Lampiran Multiple File --}}
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="paperclip" class="w-5 h-5 text-[#E62129]"></i>
                    <h3 class="text-lg font-sans font-bold text-[#111111]">Lampiran Berkas</h3>
                </div>
                <button type="button" onclick="addLampiranRow()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[var(--color-bg-light)] text-[#E62129] hover:bg-[#d5ecd1] rounded-2xl text-xs font-bold transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Lampiran
                </button>
            </div>

            <div class="space-y-4" id="lampiran-rows">
                {{-- Default empty row --}}
                <div class="flex flex-col md:flex-row md:items-center gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-[28px] lampiran-row" id="lampiran-row-0">
                    <div class="w-full md:w-1/3">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pilih File</label>
                        <input type="file" name="lampirans[0][file]" required
                               class="w-full bg-white border border-gray-200 rounded-2xl py-1.5 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none">
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipe</label>
                        <select name="lampirans[0][tipe]" required
                                class="w-full bg-white border border-gray-200 rounded-2xl py-2 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none">
                            <option value="foto">Foto Dokumentasi</option>
                            <option value="video">Video Dokumentasi</option>
                            <option value="kwitansi">Kwitansi / Nota</option>
                            <option value="lainnya">Lain-lain</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                        <input type="text" name="lampirans[0][keterangan]" placeholder="Deskripsi singkat lampiran"
                               class="w-full bg-white border border-gray-200 rounded-2xl py-2.5 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none transition">
                    </div>
                    <div class="md:pt-5">
                        <button type="button" onclick="removeLampiranRow(0)"
                                class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-2xl transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('pelaksanaan.index') }}"
               class="px-6 py-3.5 bg-gray-150 hover:bg-gray-200 text-gray-600 rounded-2xl text-xs font-bold transition">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3.5 bg-[#E62129] text-white rounded-2xl text-xs font-bold hover:bg-[#235f47] transition shadow-md">
                <i data-lucide="send" class="w-4 h-4"></i> Ajukan Laporan Pertanggungjawaban (LPJ)
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    let anggaranIndex = {{ count($anggaranList) > 0 ? count($anggaranList) : 1 }};
    let lampiranIndex = 1;

    // Add repeatable anggaran row
    function addAnggaranRow() {
        const container = document.getElementById('anggaran-rows');
        const html = `
            <div class="flex items-center gap-3 anggaran-row animate-in fade-in duration-200" id="anggaran-row-${anggaranIndex}">
                <div class="flex-1">
                    <input type="text" name="realisasi_anggaran[${anggaranIndex}][item]" placeholder="Nama Item / Pengeluaran" required
                           class="hivi-input">
                </div>
                <div class="w-1/3">
                    <input type="number" name="realisasi_anggaran[${anggaranIndex}][jumlah]" placeholder="Jumlah (Rp)" required min="0"
                           class="hivi-input">
                </div>
                <button type="button" onclick="removeAnggaranRow(${anggaranIndex})"
                        class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-2xl transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        anggaranIndex++;
        
        // Re-init lucide icons for newly added HTML
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeAnggaranRow(index) {
        const row = document.getElementById(`anggaran-row-${index}`);
        if (row) {
            row.remove();
        }
    }

    // Add repeatable lampiran row
    function addLampiranRow() {
        const container = document.getElementById('lampiran-rows');
        const html = `
            <div class="flex flex-col md:flex-row md:items-center gap-3 p-4 bg-gray-50/50 border border-gray-100 rounded-[28px] lampiran-row animate-in fade-in duration-200" id="lampiran-row-${lampiranIndex}">
                <div class="w-full md:w-1/3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pilih File</label>
                    <input type="file" name="lampirans[${lampiranIndex}][file]" required
                           class="w-full bg-white border border-gray-200 rounded-2xl py-1.5 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none">
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipe</label>
                    <select name="lampirans[${lampiranIndex}][tipe]" required
                            class="w-full bg-white border border-gray-200 rounded-2xl py-2 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none">
                        <option value="foto">Foto Dokumentasi</option>
                        <option value="video">Video Dokumentasi</option>
                        <option value="kwitansi">Kwitansi / Nota</option>
                        <option value="lainnya">Lain-lain</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Keterangan</label>
                    <input type="text" name="lampirans[${lampiranIndex}][keterangan]" placeholder="Deskripsi singkat lampiran"
                           class="w-full bg-white border border-gray-200 rounded-2xl py-2.5 px-3 text-xs focus:ring-1 focus:ring-[#E62129] outline-none transition">
                </div>
                <div class="md:pt-5">
                    <button type="button" onclick="removeLampiranRow(${lampiranIndex})"
                            class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-2xl transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        lampiranIndex++;

        // Re-init lucide icons for newly added HTML
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeLampiranRow(index) {
        const row = document.getElementById(`lampiran-row-${index}`);
        if (row) {
            row.remove();
        }
    }
</script>
@endpush


