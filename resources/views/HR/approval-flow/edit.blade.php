@extends('layouts.master')

@section('content')
        <div class="flex items-center justify-between py-6 print:hidden">
            <div>
                <h5 class="text-xl font-bold text-slate-900">Edit Flow: {{ $label }}</h5>
                <p class="text-sm text-slate-500 mt-1">Tambah, hapus, atau ubah urutan approver</p>
            </div>
            <a href="{{ route('hr.approval-flow.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[#4F6560] text-[#4F6560] bg-white/50 hover:bg-white rounded-xl text-sm font-bold transition shadow-sm backdrop-blur">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-sm border border-white/40 p-6 hover:-translate-y-1 hover:shadow-lg transition duration-200 max-w-4xl">
            <form action="{{ route('hr.approval-flow.update', $type) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-8">
                    <p class="text-sm text-slate-600 bg-amber-50 p-4 rounded-xl border border-amber-100 mb-6">
                        <i data-lucide="info" class="w-4 h-4 inline-block text-amber-500 mr-1"></i>
                        Perubahan flow hanya berlaku untuk surat <strong>baru</strong>. Gunakan fitur
                        <a href="{{ route('hr.approval-flow.reassign') }}" class="text-amber-600 font-bold hover:underline">Reassign Darurat</a>
                        untuk surat yang sedang berjalan.
                    </p>

                    <div id="steps-container" class="flex flex-col gap-4 mb-6">
                        @forelse($steps as $i => $step)
                        <div class="step-row flex items-center gap-4 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 transition hover:border-[#80BB9B]/50 hover:bg-[#80BB9B]/5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-[#80BB9B]/30 text-[#4F6560] step-number flex-shrink-0">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Jabatan</label>
                                    <select name="steps[{{ $i }}][jabatan]"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] text-sm bg-white shadow-sm"
                                        onchange="updateLabel(this)">
                                        @foreach($jabatanOptions as $val => $lbl)
                                        <option value="{{ $val }}"
                                            {{ $step->jabatan === $val ? 'selected' : '' }}
                                            data-label="{{ $lbl }}">
                                            {{ $lbl }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Label tampilan</label>
                                    <input type="text"
                                        name="steps[{{ $i }}][label]"
                                        value="{{ $step->label }}"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] text-sm shadow-sm"
                                        required>
                                </div>
                            </div>
                            <button type="button" onclick="removeStep(this)"
                                class="text-red-400 hover:text-red-600 flex-shrink-0 p-2 bg-red-50 rounded-lg hover:bg-red-100 transition shadow-sm" title="Hapus step ini">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                        @empty
                        <p class="text-sm text-slate-400 text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">Belum ada step. Tambahkan di bawah.</p>
                        @endforelse
                    </div>

                    <button type="button" onclick="addStep()"
                        class="inline-flex items-center gap-2 px-4 py-3 border-2 border-dashed border-[#80BB9B] text-[#4F6560] font-bold rounded-2xl w-full justify-center hover:bg-[#80BB9B]/10 transition-colors">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        Tambah Step Approver
                    </button>
                </div>

                {{-- pengaturan ttd --}}
                <div class="mt-8 pt-8 border-t border-gray-100">
                    <div class="flex items-center gap-2 mb-6">
                        <i data-lucide="pen-tool" class="w-5 h-5 text-[#80BB9B]"></i>
                        <h6 class="text-lg font-bold text-slate-800">Pengaturan TTD</h6>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-3">Mode TTD</label>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-100 transition">
                                <input type="radio" name="ttd_mode" value="append" {{ $ttd_mode === 'append' ? 'checked' : '' }} class="w-4 h-4 text-[#4F6560] focus:ring-[#80BB9B]">
                                <span class="text-sm font-medium text-slate-700">Append (Halaman terpisah)</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-100 transition">
                                <input type="radio" name="ttd_mode" value="stamp" {{ $ttd_mode === 'stamp' ? 'checked' : '' }} class="w-4 h-4 text-[#4F6560] focus:ring-[#80BB9B]">
                                <span class="text-sm font-medium text-slate-700">Stamp (Langsung di PDF)</span>
                            </label>
                        </div>
                    </div>

                    <div class="my-6 border-t border-slate-100"></div>

                    <div class="flex items-center justify-between mb-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="use_override" id="use_override" value="1" {{ $setting_overrides ? 'checked' : '' }} onchange="toggleOverride()" class="w-5 h-5 rounded text-[#4F6560] focus:ring-[#80BB9B]">
                                <span class="text-sm font-bold text-slate-800">Override Branding Khusus</span>
                            </label>
                            <p class="text-xs text-slate-500 mt-1 ml-8">Gunakan logo/warna berbeda untuk jenis surat ini</p>
                        </div>
                    </div>

                    <div id="override-fields" class="{{ $setting_overrides ? '' : 'hidden' }} space-y-5 p-6 bg-slate-50 rounded-2xl border border-slate-100 mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Logo Override</label>
                                <input type="file" name="override_logo" class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#80BB9B]/20 file:text-[#4F6560] hover:file:bg-[#80BB9B]/30 transition">
                                @if(isset($setting_overrides['logo_path']))
                                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1 font-medium">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i> Logo sudah terpasang
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Warna Aksen</label>
                                <div class="flex gap-3">
                                    <input type="color" name="override[accent_color]" value="{{ $setting_overrides['accent_color'] ?? '#04A54C' }}" class="h-10 w-12 rounded-lg border border-slate-200 cursor-pointer shadow-sm">
                                    <select name="override[font_family]" class="flex-1 px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] text-sm shadow-sm">
                                        <option value="Arial" {{ ($setting_overrides['font_family'] ?? '') === 'Arial' ? 'selected' : '' }}>Arial</option>
                                        <option value="Times New Roman" {{ ($setting_overrides['font_family'] ?? '') === 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                        <option value="Helvetica" {{ ($setting_overrides['font_family'] ?? '') === 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                        <option value="Georgia" {{ ($setting_overrides['font_family'] ?? '') === 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Perusahaan</label>
                            <input type="text" name="override[company_name]" value="{{ $setting_overrides['company_name'] ?? 'HR Sinergi Hotel & Villa' }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] text-sm shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer Text</label>
                            <textarea name="override[footer_text]" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] text-sm shadow-sm">{{ $setting_overrides['footer_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100">
                    <p class="text-sm font-bold text-red-700 mb-2 flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4"></i> Terdapat kesalahan:</p>
                    <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-sm text-red-600">{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                    <a href="{{ route('hr.approval-flow.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition shadow-sm">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Flow
                    </button>
                </div>
            </form>
        </div>

@push('scripts')
<script>
const jabatanOptions = @json($jabatanOptions);
let stepCount = {{ $steps->count() }};

function updateLabel(select) {
    const row = select.closest('.step-row');
    const labelInput = row.querySelector('input[type="text"]');
    const selectedOption = select.options[select.selectedIndex];
    labelInput.value = selectedOption.dataset.label;
}

function addStep() {
    const container = document.getElementById('steps-container');

    // Hapus pesan kosong jika ada
    const emptyMsg = container.querySelector('p');
    if (emptyMsg) emptyMsg.remove();

    const idx = stepCount++;
    const currentCount = container.querySelectorAll('.step-row').length + 1;

    let optionsHtml = Object.entries(jabatanOptions)
        .map(([val, lbl]) => `<option value="${val}" data-label="${lbl}">${lbl}</option>`)
        .join('');

    const firstKey = Object.keys(jabatanOptions)[0];
    const firstLabel = Object.values(jabatanOptions)[0];

    const div = document.createElement('div');
    div.className = 'step-row flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200';
    div.innerHTML = `
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold bg-[#80BB9B]/30 text-[#4F6560] step-number flex-shrink-0">
            ${currentCount}
        </div>
        <div class="flex-1 grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Jabatan</label>
                <select name="steps[${idx}][jabatan]"
                    class="w-full px-2 py-1.5 rounded border border-slate-200 text-sm bg-white"
                    onchange="updateLabel(this)">
                    ${optionsHtml}
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Label tampilan</label>
                <input type="text"
                    name="steps[${idx}][label]"
                    value="${firstLabel}"
                    class="w-full px-2 py-1.5 rounded border border-slate-200 text-sm"
                    required>
            </div>
        </div>
        <button type="button" onclick="removeStep(this)"
            class="text-red-400 hover:text-red-600 flex-shrink-0 p-1">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>`;

    container.appendChild(div);
    updateNumbers();

    if (window.lucide) lucide.createIcons();
}

function removeStep(btn) {
    btn.closest('.step-row').remove();
    updateNumbers();
}

function updateNumbers() {
    const rows = document.querySelectorAll('.step-row');
    rows.forEach((row, i) => {
        // Update nomor
        const numEl = row.querySelector('.step-number');
        if (numEl) numEl.textContent = i + 1;

        // Update name index
        row.querySelectorAll('select, input').forEach(el => {
            if (el.name) {
                el.name = el.name.replace(/steps\[\d+\]/, `steps[${i}]`);
            }
        });
    });
}

function toggleOverride() {
    const isChecked = document.getElementById('use_override').checked;
    const fields = document.getElementById('override-fields');
    if (isChecked) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>
@endpush

@endsection