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
    .hv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
    }
    .hv-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(24px);
        border-radius: 20px;
        padding: 24px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .hv-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .hv-surat-nama {
        font-family: 'Poppins', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #111111;
        margin: 0;
    }
    .hv-surat-kode {
        background: var(--color-bg-light);
        color: var(--color-primary);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .hv-surat-desc {
        font-size: 13px;
        color: #6B7280;
        margin-bottom: 20px;
        line-height: 1.5;
        flex-grow: 1;
    }
    .hv-section-label {
        font-family: 'Poppins', sans-serif;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9CA3AF;
        margin-bottom: 8px;
    }
    .hv-approver-chain {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 20px;
    }
    .hv-approver-pill {
        background: #F3F4F6;
        color: #374151;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
    }
    .hv-chain-arrow {
        color: #D1D5DB;
    }
    .hv-stats-row {
        display: flex;
        gap: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .hv-stat-item {
        font-size: 12px;
        color: #4B5563;
    }
    .hv-stat-item b {
        color: #111111;
    }
    .hv-nomor-preview {
        background: #F5F5F7;
        border: 1px dashed #D1D5DB;
        border-radius: 8px;
        padding: 8px 12px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 12px;
        color: #4B5563;
        margin-bottom: 20px;
        text-align: center;
    }
    .hv-card-actions {
        display: flex;
        gap: 12px;
        margin-top: auto;
    }
    .hv-btn-edit {
        flex: 1;
        background: var(--color-primary);
        color: white !important;
        border: none;
        padding: 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        text-align: center;
        transition: all 0.2s;
    }
    .hv-btn-edit:hover {
        background: var(--color-primary-dark);
        color: white !important;
    }
    .hv-btn-delete {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #FCA5A5;
        color: #EF4444;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .hv-btn-delete:hover:not(:disabled) {
        background: #FEF2F2;
    }
    .hv-btn-delete:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #D1D5DB;
        color: #9CA3AF;
    }
    .hv-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .hv-btn-add {
        background: var(--color-primary);
        color: white !important;
        padding: 12px 24px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-bottom: 32px;
    }
    .hv-btn-add:hover {
        background: var(--color-primary-dark);
        color: white !important;
        transform: translateY(-2px);
    }
</style>

<div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#111111]">Kelola Tipe Dokumen</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">Konfigurasikan tipe dokumen dan alur kerja persetujuan untuk organisasi Anda.</p>
    </div>
    <a href="{{ route('surat-type.create') }}" class="hv-btn-add" style="margin-bottom: 0;">
        <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
        Tambah Tipe Dokumen
    </a>
</div>

<div style="margin-bottom: 28px; max-width: 320px;">
    <select class="hv-input" onchange="filterSuratType(this.value)" style="padding: 10px 16px; border-radius: 12px; font-size: 13px; font-family: 'Poppins', sans-serif; cursor: pointer; border: 1px solid rgba(0, 0, 0, 0.1); background-color: white;">
        <option value="all">Semua Organisasi</option>
        <option value="generic">Generik (Semua Organisasi)</option>
        @foreach($organisasis as $org)
            <option value="{{ $org->id }}">{{ $org->nama }}</option>
        @endforeach
    </select>
</div>

<div class="hv-grid">
    @foreach($suratTypes as $type)
    <div class="hv-card" data-org-id="{{ $type->organisasi_id ?? 'generic' }}">
        <div class="hv-card-header">
            <div>
                <h2 class="hv-surat-nama">{{ $type->nama }}</h2>
                <div class="mt-1 flex gap-2">
                    @if($type->organisasi)
                        @if($type->organisasi->tipe === 'osis')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase" style="background:var(--color-bg-light); color:#E62129;">{{ $type->organisasi->nama }}</span>
                        @elseif($type->organisasi->tipe === 'mpk')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase" style="background:#E0F2FE; color:#0369A1;">{{ $type->organisasi->nama }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase" style="background:#FEF3C7; color:#B45309;">{{ $type->organisasi->nama }}</span>
                        @endif
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase" style="background:#F1F5F9; color:#475569;">Generik</span>
                    @endif
                </div>
            </div>
            <span class="hv-surat-kode">{{ $type->kode }}</span>
        </div>
        
        <p class="hv-surat-desc">{{ $type->deskripsi ?: 'Tidak ada deskripsi yang tersedia.' }}</p>

        <div class="hv-section-label">Alur Kerja Persetujuan</div>
        <div class="hv-approver-chain">
            @forelse($type->approvers as $index => $approver)
                <span class="hv-approver-pill">{{ $approver->jabatan_label }}</span>
                @if(!$loop->last)
                    <i data-lucide="arrow-right" class="hv-chain-arrow" style="width: 14px; height: 14px;"></i>
                @endif
            @empty
                <span class="text-[12px] text-gray-400 italic">Belum dikonfigurasi</span>
            @endforelse
        </div>

        <div class="hv-stats-row">
            <div class="hv-stat-item">
                <b>{{ $type->surats_count }}</b> Dokumen
            </div>
            <div class="hv-stat-item">
                <b>{{ $type->surats()->where('status', 'approved_owner')->count() }}</b> Disetujui
            </div>
        </div>

        <div class="hv-card-actions">
            <a href="{{ route('surat-type.edit', $type->id) }}" class="hv-btn-edit">Ubah Tipe Dokumen</a>
            <form action="{{ route('surat-type.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus tipe dokumen ini? Ini juga akan menghapus semua dokumen yang terkait.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="hv-btn-delete" title="Delete">
                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
    function filterSuratType(orgId) {
        document.querySelectorAll('.hv-card').forEach(card => {
            const cardOrgId = card.getAttribute('data-org-id');
            if (orgId === 'all') {
                card.style.display = 'flex';
            } else if (orgId === 'generic') {
                if (cardOrgId === 'generic' || !cardOrgId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            } else {
                if (cardOrgId == orgId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }
</script>
@endpush

@endsection

