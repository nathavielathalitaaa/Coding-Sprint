@extends('layouts.master')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#1A2B24]">Ubah Template</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">
            <span class="font-medium text-[var(--color-text)]">{{ $template->nama }}</span>
            <span class="mx-1.5 text-gray-300">·</span>
            <code class="text-[11px] bg-gray-100 px-1.5 py-0.5 rounded font-mono">{{ $template->kode }}</code>
        </p>
    </div>
    <a href="{{ route('surat-turunan-template.index') }}"
       class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
        Kembali
    </a>
</div>

@if($errors->any())
<div class="mb-6 p-4 rounded-2xl" style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;">
    <p class="text-sm font-semibold text-red-800 mb-2">Terjadi Kesalahan:</p>
    <ul class="text-sm text-red-700 list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Kolom Kiri: Form edit (2/3 lebar) ─────────────────────────── --}}
    <div class="xl:col-span-2">
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.05)] p-6">

            <form action="{{ route('surat-turunan-template.update', $template->id) }}" method="POST" id="templateForm">
                @csrf
                @method('PUT')

                {{-- Nama Template --}}
                <div class="mb-5">
                    <label for="nama" class="block text-sm font-bold text-[#1A2B24] mb-2">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama"
                        value="{{ old('nama', $template->nama) }}"
                        placeholder="cth. Surat Undangan"
                        style="width:100%;padding:11px 16px;border-radius:12px;border:1px solid #e5e7eb;font-size:14px;background:#fff;outline:none;transition:all 0.2s;"
                        onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 2px rgba(128,187,155,0.2)'"
                        onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                        required>
                    @error('nama')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status Aktif --}}
                <div class="mb-5">
                    <label class="flex items-center gap-3 cursor-pointer w-fit">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded accent-[var(--color-text)] cursor-pointer">
                        <span class="text-sm font-medium text-[#1A2B24]">Template aktif</span>
                        <span class="text-xs text-gray-400">(nonaktif = tidak muncul di form generate)</span>
                    </label>
                </div>

                {{-- Konten Template --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label for="konten_template" class="block text-sm font-bold text-[#1A2B24]">
                            Konten Template <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            {{-- Tombol preview --}}
                            <button type="button" onclick="togglePreview()"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-medium text-gray-600 transition">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span id="previewBtnText">Pratinjau</span>
                            </button>
                            {{-- Counter karakter --}}
                            <span id="charCount" class="text-[11px] text-gray-400">
                                {{ mb_strlen($template->konten_template) }} karakter
                            </span>
                        </div>
                    </div>

                    {{-- Editor --}}
                    <div id="editorWrap" class="relative">
                        <textarea id="konten_template" name="konten_template" rows="22"
                            placeholder="Tulis isi surat di sini. Gunakan {{token}} untuk data dinamis."
                            oninput="updateCharCount(this)"
                            style="width:100%;padding:14px 16px;border-radius:12px;border:1px solid #e5e7eb;font-size:13px;font-family:'Courier New',monospace;line-height:1.7;background:#fafafa;outline:none;resize:vertical;transition:all 0.2s;"
                            onfocus="this.style.borderColor='var(--color-primary)';this.style.boxShadow='0 0 0 2px rgba(128,187,155,0.2)'"
                            onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                            required>{{ old('konten_template', $template->konten_template) }}</textarea>
                    </div>

                    {{-- Preview panel (hidden by default) --}}
                    <div id="previewPanel" style="display:none;"
                         class="mt-3 p-5 rounded-2xl border border-gray-200 bg-white">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Pratinjau (placeholder belum diganti)</p>
                        <div id="previewContent"
                             class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap font-mono"></div>
                    </div>

                    @error('konten_template')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('surat-turunan-template.index') }}"
                       style="padding:10px 24px;border-radius:12px;border:1px solid #d1d5db;background:#fff;font-size:14px;font-weight:600;color:#374151;text-decoration:none;display:inline-flex;align-items:center;transition:all 0.2s;"
                       onmouseover="this.style.background='#F5F5F7'" onmouseout="this.style.background='#fff'">
                        Batal
                    </a>
                    <button type="submit"
                            style="background-color:var(--color-text);"
                            class="inline-flex items-center gap-2 px-8 py-2.5 hover:opacity-90 text-white rounded-2xl text-sm font-bold shadow transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Kolom Kanan: Panduan placeholder (1/3 lebar) ──────────────── --}}
    <div class="xl:col-span-1 space-y-5">

        {{-- Daftar Token --}}
        <div class="bg-white rounded-[20px] border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.05)] p-5">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="braces" class="w-4 h-4 text-[var(--color-text)]"></i>
                <h3 class="text-sm font-bold text-[#1A2B24]">Placeholder Tersedia</h3>
            </div>
            <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                Klik token untuk menyalinnya ke clipboard, lalu tempel di posisi yang diinginkan di editor.
            </p>

            <div class="space-y-2">
                @foreach($placeholders as $ph)
                <div class="group flex items-start gap-2 p-2.5 rounded-2xl hover:bg-gray-50 transition cursor-pointer"
                     onclick="copyToken('{{ $ph['token'] }}', this)"
                     title="Klik untuk salin">
                    <code class="shrink-0 text-[11px] font-bold font-mono bg-[var(--color-bg-light)] text-[#2E7D5E] px-2 py-1 rounded-lg group-hover:bg-[#d1ede0] transition select-none">
                        {{ $ph['token'] }}
                    </code>
                    <span class="text-[11px] text-gray-500 leading-relaxed pt-0.5">
                        {{ $ph['keterangan'] }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Toast copied --}}
            <div id="copyToast"
                 style="display:none;"
                 class="mt-3 text-center text-xs font-semibold text-emerald-600 bg-emerald-50 py-2 rounded-lg border border-emerald-100">
                ✓ Disalin ke clipboard
            </div>
        </div>

        {{-- Tips penggunaan --}}
        <div class="bg-amber-50 border border-amber-100 rounded-[20px] p-5">
            <div class="flex items-center gap-2 mb-3">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                <h3 class="text-sm font-bold text-amber-800">Tips</h3>
            </div>
            <ul class="space-y-2 text-xs text-amber-700 leading-relaxed list-disc list-inside">
                <li>Baris kosong akan dipertahankan persis di hasil akhir surat.</li>
                <li><code class="bg-amber-100 px-1 rounded font-mono">{{'{{'}}tanggal_selesai{{'}}'}}</code> otomatis kosong jika kegiatan satu hari.</li>
                <li>Blok garis bawah <code class="bg-amber-100 px-1 rounded font-mono">_____</code> di area TTD akan digantikan gambar tanda tangan asli saat PDF digenerate.</li>
                <li>Perubahan hanya berlaku untuk surat turunan yang di-generate <strong>setelah</strong> disimpan.</li>
            </ul>
        </div>

        {{-- Info metadata --}}
        <div class="bg-gray-50 rounded-[20px] border border-gray-100 p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Informasi</p>
            <div class="space-y-1.5 text-xs text-gray-500">
                <div class="flex justify-between">
                    <span>Kode</span>
                    <code class="font-mono font-semibold text-[var(--color-text)]">{{ $template->kode }}</code>
                </div>
                <div class="flex justify-between">
                    <span>Dibuat</span>
                    <span>{{ $template->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Terakhir diperbarui</span>
                    <span>{{ $template->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Update counter karakter ──────────────────────────────────────────────
function updateCharCount(el) {
    const counter = document.getElementById('charCount');
    if (counter) counter.textContent = el.value.length + ' karakter';
}

// ── Toggle preview panel ─────────────────────────────────────────────────
let previewOpen = false;
function togglePreview() {
    previewOpen = !previewOpen;
    const panel   = document.getElementById('previewPanel');
    const content = document.getElementById('previewContent');
    const btnText = document.getElementById('previewBtnText');
    const textarea = document.getElementById('konten_template');

    if (previewOpen) {
        content.textContent = textarea.value;
        panel.style.display = 'block';
        btnText.textContent = 'Sembunyikan';
    } else {
        panel.style.display = 'none';
        btnText.textContent = 'Pratinjau';
    }
}

// Perbarui preview secara real-time jika sedang terbuka
document.getElementById('konten_template')?.addEventListener('input', function() {
    if (previewOpen) {
        document.getElementById('previewContent').textContent = this.value;
    }
});

// ── Salin token ke clipboard ─────────────────────────────────────────────
function copyToken(token, el) {
    navigator.clipboard.writeText(token).then(() => {
        const toast = document.getElementById('copyToast');
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 1800);

        // Insert token ke posisi kursor di textarea
        const ta = document.getElementById('konten_template');
        if (ta) {
            const start = ta.selectionStart;
            const end   = ta.selectionEnd;
            const val   = ta.value;
            ta.value = val.substring(0, start) + token + val.substring(end);
            ta.selectionStart = ta.selectionEnd = start + token.length;
            ta.focus();
            updateCharCount(ta);
            if (previewOpen) {
                document.getElementById('previewContent').textContent = ta.value;
            }
        }
    }).catch(() => {
        // Fallback: select konten token
        const range = document.createRange();
        range.selectNode(el.querySelector('code'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    });
}
</script>
@endpush

