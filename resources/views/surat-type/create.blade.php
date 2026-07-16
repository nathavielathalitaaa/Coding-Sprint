@extends('layouts.master')

@section('content')
<style>
    .hv-page-title {
        font-family: 'Poppins', sans-serif;
        font-size: 32px;
        color: #111111;
        margin-bottom: 4px;
    }
    .hv-page-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 300;
        color: #6B7280;
        margin-bottom: 32px;
    }
    .hv-form-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 32px;
        align-items: start;
    }
    .hv-section-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(24px);
        border-radius: 20px;
        padding: 32px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
    }
    .hv-section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #111111;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .hv-section-title i {
        color: var(--color-primary);
    }
    .hv-label {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6B7280;
        margin-bottom: 8px;
        display: block;
    }
    .hv-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        background: #F5F5F7;
        font-size: 14px;
        transition: all 0.2s;
    }
    .hv-input:focus {
        outline: none;
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(230, 33, 41, 0.12);
    }
    .hv-textarea {
        min-height: 100px;
        resize: vertical;
    }
    .hv-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .hv-form-group {
        margin-bottom: 20px;
    }

    /* Format Builder */
    .hv-component-pool {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
        padding: 16px;
        background: #F3F4F6;
        border-radius: 12px;
    }
    .hv-pill {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }
    .hv-pill-tool {
        background: white;
        border: 1px solid #E5E7EB;
        color: #4B5563;
    }
    .hv-pill-tool:hover {
        border-color: var(--color-text);
        color: var(--color-text);
    }
    .hv-active-format {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        min-height: 60px;
        padding: 16px;
        background: white;
        border: 2px dashed #D1D5DB;
        border-radius: 12px;
    }
    .hv-format-item {
        background: var(--color-bg-light);
        color: var(--color-primary);
        border: 1px solid rgba(230, 33, 41, 0.2);
        padding: 6px 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .hv-format-item-remove {
        color: #991B1B;
        cursor: pointer;
        opacity: 0.6;
    }
    .hv-format-item-remove:hover {
        opacity: 1;
    }
    .hv-format-separator {
        color: #9CA3AF;
        font-weight: bold;
    }
    .hv-format-input-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 8px;
    }

    /* Approval Builder */
    .hv-step-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .hv-step-item {
        display: grid;
        grid-template-columns: 40px 1.2fr 1.2fr 1fr 0.8fr 180px 0.8fr 40px;
        gap: 12px;
        align-items: center;
        padding: 16px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
    }
    .hv-step-number {
        width: 24px;
        height: 24px;
        background: var(--color-primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }
    .hv-btn-add-step {
        width: 100%;
        padding: 12px;
        background: transparent;
        border: 1px dashed var(--color-primary);
        color: var(--color-primary);
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 16px;
        transition: all 0.2s;
    }
    .hv-btn-add-step:hover {
        background: var(--color-bg-light);
    }

    /* Preview Card */
    .hv-preview-card {
        position: sticky;
        top: 32px;
    }
    .hv-card-preview {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .hv-preview-title {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9CA3AF;
        margin-bottom: 16px;
        text-align: center;
    }

    /* Toggle Switch */
    .hv-toggle {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .hv-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .hv-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #D1D5DB;
        transition: .4s;
        border-radius: 24px;
    }
    .hv-toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .hv-toggle-slider {
        background-color: var(--color-primary);
    }
    input:checked + .hv-toggle-slider:before {
        transform: translateX(20px);
    }

    .hv-btn-submit {
        background: var(--color-primary);
        color: white;
        padding: 16px 32px;
        border-radius: 999px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        margin-top: 16px;
    }
    .hv-btn-submit:hover {
        background: var(--color-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(230, 33, 41, 0.2);
    }

    /* Card Index Style for Preview */
    .hv-p-card { border-radius: 20px; padding: 24px; background: white; border: 1px solid #f0f0f0; }
    .hv-p-nama { font-family: 'Poppins', sans-serif; font-size: 18px; font-weight: 700; color: #111111; margin-bottom: 4px; }
    .hv-p-kode { background: var(--color-bg-light); color: var(--color-primary); padding: 2px 10px; border-radius: 999px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
    .hv-p-desc { font-size: 12px; color: #6B7280; margin: 12px 0; }
    .hv-p-label { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: #9CA3AF; margin-bottom: 6px; }
    .hv-p-chain { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-bottom: 16px; }
    .hv-p-pill { background: #F3F4F6; color: #374151; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 500; }
    .hv-p-nomor { background: #F5F5F7; border: 1px dashed #D1D5DB; border-radius: 6px; padding: 6px; font-family: monospace; font-size: 11px; color: #4B5563; text-align: center; }
</style>

<div class="mb-8">
    <h1 class="text-3xl font-sans font-bold text-[#111111]">{{ isset($suratType) ? 'Ubah Tipe Dokumen' : 'Tambah Tipe Dokumen' }}</h1>
    <p class="text-[13px] font-light text-[#6B7280] mt-1">Tentukan format nomor dan alur kerja persetujuan untuk dokumen Anda.</p>
</div>

@if($errors->any())
<div style="background: #FEE2E2; border: 1px solid #EF4444; color: #B91C1C; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
    <p style="font-weight: 600; margin-bottom: 8px;">Tunggu, ada yang kurang atau salah:</p>
    <ul style="font-size: 13px; list-style-type: disc; margin-left: 20px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ isset($suratType) ? route('surat-type.update', $suratType->id) : route('surat-type.store') }}" method="POST" id="suratTypeForm">
    @csrf
    @if(isset($suratType)) @method('PUT') @endif

    <div class="hv-form-grid">
        <div class="hv-form-left">
            
            {{-- Informasi Dasar --}}
            <div class="hv-section-card">
                <h2 class="hv-section-title">
                    <i data-lucide="info" style="width: 20px; height: 20px;"></i>
                    Informasi Tipe Dokumen
                </h2>
                
                <div class="hv-form-row">
                    <div class="hv-form-group">
                        <label class="hv-label">Nama Dokumen</label>
                        <input type="text" name="nama" id="input_nama" class="hv-input" placeholder="Contoh: Surat Izin Kerja" value="{{ $suratType->nama ?? '' }}" required>
                    </div>
                    <div class="hv-form-group">
                        <label class="hv-label">Kode / Slug</label>
                        <input type="text" name="kode" id="input_kode" class="hv-input" placeholder="Contoh: izin" value="{{ $suratType->kode ?? '' }}" required>
                    </div>
                </div>

                <div class="hv-form-group">
                    <label class="hv-label">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="input_deskripsi" class="hv-input hv-textarea" placeholder="Jelaskan tujuan dokumen ini...">{{ $suratType->deskripsi ?? '' }}</textarea>
                <div class="hv-form-row">
                    <div class="hv-form-group">
                        <label class="hv-label">Berlaku untuk Organisasi</label>
                        <select name="organisasi_tipe" class="hv-input">
                            <option value="" {{ (!isset($suratType) || is_null($suratType->organisasi_tipe)) ? 'selected' : '' }}>Semua Organisasi (Generic)</option>
                            <option value="osis" {{ (isset($suratType) && $suratType->organisasi_tipe == 'osis') ? 'selected' : '' }}>OSIS</option>
                            <option value="mpk" {{ (isset($suratType) && $suratType->organisasi_tipe == 'mpk') ? 'selected' : '' }}>MPK</option>
                            <option value="sub_organ" {{ (isset($suratType) && $suratType->organisasi_tipe == 'sub_organ') ? 'selected' : '' }}>Sub Organ</option>
                        </select>
                    </div>
                    <div class="hv-form-group">
                        <!-- empty for layout spacing -->
                    </div>
                </div>

                <div class="hv-form-row">
                    <div class="hv-form-group">
                        <label class="hv-label">Reset Penghitung Nomor</label>
                        <select name="nomor_reset" class="hv-input">
                            <option value="yearly" {{ (isset($suratType) && $suratType->nomor_reset == 'yearly') ? 'selected' : '' }}>Setiap Tahun</option>
                            <option value="monthly" {{ (isset($suratType) && $suratType->nomor_reset == 'monthly') ? 'selected' : '' }}>Setiap Bulan</option>
                            <option value="never" {{ (isset($suratType) && $suratType->nomor_reset == 'never') ? 'selected' : '' }}>Tidak Pernah</option>
                        </select>
                    </div>
                    <div class="hv-form-group">
                        <label class="hv-label">Status Aktif</label>
                        <div style="display: flex; align-items: center; gap: 12px; height: 46px;">
                            <label class="hv-toggle">
                                <input type="checkbox" name="is_active" value="1" {{ (!isset($suratType) || $suratType->is_active) ? 'checked' : '' }}>
                                <span class="hv-toggle-slider"></span>
                            </label>
                            <span style="font-size: 14px; color: #4B5563;">Aktif & Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Format Nomor Surat --}}
            <div class="hv-section-card">
                <h2 class="hv-section-title">
                    <i data-lucide="hash" style="width: 20px; height: 20px;"></i>
                    Format Nomor Dokumen
                </h2>
                
                <p class="hv-label" style="color: #9CA3AF; margin-bottom: 16px;">Klik komponen di bawah ini untuk menambahkannya ke format:</p>
                
                <div class="hv-component-pool">
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('NOMOR_URUT')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [NOMOR_URUT]
                    </div>
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('KODE_SURAT')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [KODE_SURAT]
                    </div>
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('LEMBAGA')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [LEMBAGA]
                    </div>
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('BULAN_ROMAWI')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [BULAN_ROMAWI]
                    </div>
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('TAHUN')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [TAHUN]
                    </div>
                    <div class="hv-pill hv-pill-tool" onclick="addFormatItem('CUSTOM')">
                        <i data-lucide="plus" style="width: 12px; height: 12px;"></i> [CUSTOM]
                    </div>
                </div>

                <div class="hv-active-format" id="format_container">
                    {{-- items will be injected here --}}
                </div>
                
                <div id="format_inputs_container" style="margin-top: 16px;">
                    {{-- extra inputs for LEMBAGA/CUSTOM will appear here --}}
                </div>
            </div>

            {{-- Alur Approval --}}
            <div class="hv-section-card">
                <h2 class="hv-section-title">
                    <i data-lucide="git-branch" style="width: 20px; height: 20px;"></i>
                    Alur Kerja Persetujuan
                </h2>
                
                <div class="hv-step-list" id="approver_container">
                    {{-- steps will be injected here --}}
                </div>
                
                <button type="button" class="hv-btn-add-step" onclick="addApproverStep()">
                    <i data-lucide="plus" style="display: inline; vertical-align: middle; width: 14px; height: 14px;"></i>
                    Tambah Langkah Persetujuan
                </button>
            </div>

            <button type="submit" class="hv-btn-submit">
                {{ isset($suratType) ? 'Perbarui Tipe Dokumen' : 'Simpan Tipe Dokumen' }}
            </button>
        </div>

        <div class="hv-form-right">
            <div class="hv-preview-card">
                <p class="hv-preview-title">Pratinjau Langsung</p>
                <div class="hv-card-preview">
                    <div class="hv-p-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <h2 class="hv-p-nama" id="p_nama">Nama Dokumen</h2>
                            <span class="hv-p-kode" id="p_kode">KODE</span>
                        </div>
                        <p class="hv-p-desc" id="p_desc">Deskripsi dokumen akan muncul di sini saat Anda mengetik...</p>
                        
                        <div class="hv-p-label">Alur Kerja Persetujuan</div>
                        <div class="hv-p-chain" id="p_chain">
                            {{-- preview steps --}}
                        </div>
                        
                        <div class="hv-p-label">Contoh Nomor</div>
                        <div class="hv-p-nomor" id="p_nomor">001/KODE/V/2026</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const formatContainer = document.getElementById('format_container');
    const approverContainer = document.getElementById('approver_container');
    const formatInputsContainer = document.getElementById('format_inputs_container');

    const users = @json($approverUsers);
    const targetModeOptions = @json(\App\Models\SuratTypeApprover::targetModeOptions());
    let formatItems = @json($suratType->nomor_format ?? []);
    let approverSteps = @json($suratType->approvers ?? []);

    if (approverSteps.length === 0) {
        approverSteps = [
            { user_id: '', target_mode: 'submitter', jabatan_label: 'HOD', label: 'Requested by', metode_ttd: 'stamp', is_required: true, is_signer: true },
            { user_id: '', target_mode: 'submitter', jabatan_label: 'Owner Rep', label: 'Checked by', metode_ttd: 'stamp', is_required: true, is_signer: true },
            { user_id: '', target_mode: 'submitter', jabatan_label: 'Direktur', label: 'Approved by', metode_ttd: 'stamp', is_required: true, is_signer: true }
        ];
    }

    // Sortable for Format
    new Sortable(formatContainer, {
        animation: 150,
        onEnd: function() {
            rebuildFormatItemsFromUI();
            updatePreview();
        }
    });

    // Sortable for Approvers
    new Sortable(approverContainer, {
        animation: 150,
        handle: '.hv-step-number',
        onEnd: function() {
            reindexApprovers();
            updatePreview();
        }
    });

    function addFormatItem(type) {
        formatItems.push({ type: type, value: type === 'LEMBAGA' ? 'HRD' : '' });
        renderFormat();
        updatePreview();
    }

    function removeFormatItem(index) {
        formatItems.splice(index, 1);
        renderFormat();
        updatePreview();
    }

    function renderFormat() {
        formatContainer.innerHTML = '';
        // formatInputsContainer is no longer used for inputs, just a clear indicator
        formatInputsContainer.innerHTML = '';

        formatItems.forEach((item, index) => {
            const el = document.createElement('div');
            el.className = 'hv-format-item';
            el.style.flexDirection = 'column';
            el.style.alignItems = 'flex-start';
            el.style.gap = '4';
            
            let extraInput = '';
            if (item.type === 'LEMBAGA' || item.type === 'CUSTOM') {
                extraInput = `
                    <div style="margin-top: 4px;">
                        <input type="text" name="nomor_format[${index}][value]" class="hv-input" 
                               style="padding: 2px 6px; font-size: 10px; width: 60px; height: auto;" 
                               value="${item.value || ''}" 
                               oninput="updateFormatValue(${index}, this.value)"
                               onclick="event.stopPropagation()">
                    </div>
                `;
            } else {
                extraInput = `<input type="hidden" name="nomor_format[${index}][value]" value="">`;
            }

            el.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; width: 100%;">
                    <span>${item.type}</span>
                    <input type="hidden" name="nomor_format[${index}][type]" value="${item.type}">
                    <i data-lucide="x" class="hv-format-item-remove" style="margin-left: auto;" onclick="removeFormatItem(${index})"></i>
                </div>
                ${extraInput}
            `;
            formatContainer.appendChild(el);

            if (index < formatItems.length - 1) {
                const sep = document.createElement('span');
                sep.className = 'hv-format-separator';
                sep.innerText = '/';
                formatContainer.appendChild(sep);
            }
        });
        lucide.createIcons();
    }

    function updateFormatValue(index, val) {
        formatItems[index].value = val;
        updatePreview();
    }

    function addApproverStep() {
        approverSteps.push({ user_id: '', target_mode: 'submitter', jabatan_label: '', label: '', metode_ttd: 'stamp', is_required: true, is_signer: true });
        renderApprovers();
        updatePreview();
    }

    function removeApproverStep(index) {
        if (approverSteps.length > 1) {
            approverSteps.splice(index, 1);
            renderApprovers();
            updatePreview();
        }
    }

    function renderApprovers() {
        approverContainer.innerHTML = '';
        approverSteps.forEach((step, index) => {
            const el = document.createElement('div');
            el.className = 'hv-step-item';
            
            // Build user options
            let userOptions = '<option value="">-- Select Approver --</option>';
            users.forEach(u => {
                const selected = (step.user_id == u.id) ? 'selected' : '';
                userOptions += `<option value="${u.id}" ${selected}>${u.name} [${u.role.toUpperCase()}] — ${u.jabatan}</option>`;
            });

            el.innerHTML = `
                <div class="hv-step-number">${index + 1}</div>
                
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">Person / User</label>
                    <select name="approvers[${index}][user_id]" class="hv-input" style="padding: 8px; font-size: 12px;" onchange="updateApproverData(${index}, 'user_id', this.value)">
                        ${userOptions}
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">Target Approver</label>
                    <select name="approvers[${index}][target_mode]" class="hv-input" style="padding: 8px; font-size: 12px;" onchange="updateApproverData(${index}, 'target_mode', this.value)">
                        ${Object.entries(targetModeOptions).map(([key, val]) => `
                            <option value="${key}" ${step.target_mode === key ? 'selected' : ''}>${val}</option>
                        `).join('')}
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">PDF Label (Job Title)</label>
                    <input type="text" name="approvers[${index}][jabatan_label]" class="hv-input" style="padding: 8px; font-size: 12px;" placeholder="Example: Manager" value="${step.jabatan_label || ''}" oninput="updateApproverData(${index}, 'jabatan_label', this.value)">
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">Need Signature?</label>
                    <label class="hv-toggle" style="transform: scale(0.7);">
                        <input type="checkbox" name="approvers[${index}][is_signer]" value="1" ${step.is_signer !== false ? 'checked' : ''} onchange="updateApproverData(${index}, 'is_signer', this.checked)">
                        <span class="hv-toggle-slider"></span>
                    </label>
                </div>
                
                <div style="display: ${step.is_signer !== false ? 'flex' : 'none'}; flex-direction: column; align-items: flex-start; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">Method</label>
                    <select name="approvers[${index}][metode_ttd]" class="hv-input" style="padding: 4px 8px; font-size: 11px; height: auto;" onchange="updateApproverData(${index}, 'metode_ttd', this.value)">
                        <option value="stamp" ${step.metode_ttd === 'stamp' ? 'selected' : ''}>Stamp (place at coordinates)</option>
                        <option value="append" ${step.metode_ttd === 'append' ? 'selected' : ''}>Append (new endorsement page)</option>
                    </select>
                    <p style="font-family: 'Poppins', sans-serif; font-size: 11px; color: #6B7280; margin-top: 2px; line-height: 1.2;">Stamp: Signature is placed at the selected point in the document.<br>Append: Signature is issued as a separate Endorsement Sheet.</p>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                    <label class="hv-label" style="margin:0; font-size:9px;">Required</label>
                    <label class="hv-toggle" style="transform: scale(0.7);">
                        <input type="checkbox" name="approvers[${index}][is_required]" value="1" ${step.is_required ? 'checked' : ''} onchange="updateApproverData(${index}, 'is_required', this.checked)">
                        <span class="hv-toggle-slider"></span>
                    </label>
                </div>
                <button type="button" class="hv-format-item-remove" style="background:none; border:none; margin-top: 15px;" onclick="removeApproverStep(${index})">
                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                </button>
            `;
            approverContainer.appendChild(el);
        });
        lucide.createIcons();
    }

    function updateApproverData(index, key, val) {
        approverSteps[index][key] = val;
        if (key === 'is_signer') {
            renderApprovers(); // re-render to toggle method visibility
        }
        updatePreview();
    }

    function reindexApprovers() {
        const newSteps = [];
        approverContainer.querySelectorAll('.hv-step-item').forEach((item, index) => {
            newSteps.push({
                user_id: item.querySelector('select[name*="[user_id]"]').value,
                target_mode: item.querySelector('select[name*="[target_mode]"]').value,
                jabatan_label: item.querySelector('input[name*="[jabatan_label]"]').value,
                metode_ttd: item.querySelector('select[name*="[metode_ttd]"]') ? item.querySelector('select[name*="[metode_ttd]"]').value : null,
                is_required: item.querySelector('input[name*="[is_required]"]').checked,
                is_signer: item.querySelector('input[name*="[is_signer]"]').checked
            });
        });
        approverSteps = newSteps;
        renderApprovers();
    }

    function rebuildFormatItemsFromUI() {
        const updatedItems = [];
        const formatElements = formatContainer.querySelectorAll('.hv-format-item');
        
        formatElements.forEach(el => {
            const typeInput = el.querySelector('input[name*="[type]"]');
            const valInput = el.querySelector('input[name*="[value]"]');
            if (typeInput && valInput) {
                updatedItems.push({ type: typeInput.value, value: valInput.value });
            }
        });
        
        formatItems = updatedItems;
        renderFormat();
    }

    function updatePreview() {
        document.getElementById('p_nama').innerText = document.getElementById('input_nama').value || 'Document Name';
        document.getElementById('p_kode').innerText = (document.getElementById('input_kode').value || 'CODE').toUpperCase();
        document.getElementById('p_desc').innerText = document.getElementById('input_deskripsi').value || 'Document description will appear here...';

        // Chain preview
        const pChain = document.getElementById('p_chain');
        pChain.innerHTML = '';
        approverSteps.forEach((step, idx) => {
            if (step.jabatan_label || step.user_id) {
                const s = document.createElement('span');
                s.className = 'hv-p-pill';
                
                let displayText = step.jabatan_label;
                if (!displayText && step.user_id) {
                    const user = users.find(u => u.id == step.user_id);
                    displayText = user ? user.name : 'Unknown';
                }
                
                s.innerHTML = `
                    <div style="display:flex; flex-direction:column; align-items:center;">
                        <span>${displayText || 'Penyetuju'}</span>
                        <span style="font-size:8px; font-weight:normal; background: ${step.is_signer !== false ? '#E0F2FE' : '#F3F4F6'}; color: ${step.is_signer !== false ? '#0284C7' : '#6B7280'}; padding: 1px 4px; border-radius: 4px; margin-top: 2px;">
                            ${step.is_signer !== false ? 'Setujui + TTD' : 'Hanya Setujui'}
                        </span>
                    </div>
                `;
                pChain.appendChild(s);
                if (idx < approverSteps.length - 1) {
                    const arrow = document.createElement('i');
                    arrow.setAttribute('data-lucide', 'chevron-right');
                    arrow.style.width = '10px';
                    arrow.style.height = '10px';
                    arrow.style.color = '#D1D5DB';
                    pChain.appendChild(arrow);
                }
            }
        });

        // Nomor preview
        const now = new Date();
        const romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][now.getMonth()];
        const previewParts = formatItems.map(item => {
            switch(item.type) {
                case 'NOMOR_URUT': return '001';
                case 'KODE_SURAT': return (document.getElementById('input_kode').value || 'KODE').toUpperCase();
                case 'LEMBAGA': return item.value || 'HRD';
                case 'BULAN_ROMAWI': return romawi;
                case 'TAHUN': return now.getFullYear();
                case 'CUSTOM': return item.value || '';
                default: return '';
            }
        }).filter(p => p !== '');
        document.getElementById('p_nomor').innerText = previewParts.join('/');
        lucide.createIcons();
    }

    // Init
    document.getElementById('input_nama').addEventListener('input', updatePreview);
    document.getElementById('input_kode').addEventListener('input', function(e) {
        this.value = this.value.toLowerCase().replace(/\s+/g, '-');
        updatePreview();
    });
    document.getElementById('input_deskripsi').addEventListener('input', updatePreview);

    renderFormat();
    renderApprovers();
    updatePreview();
</script>
@endpush

