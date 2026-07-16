@extends('layouts.master')

@section('title', 'Kelola Organisasi — SIMORA')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#111111]">
            <i data-lucide="users" class="inline-block size-7 mr-2 text-[var(--color-text)] align-text-bottom"></i>
            Kelola Organisasi
        </h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">Manajemen OSIS, MPK, dan Sub Organ SMK Telkom Sidoarjo</p>
    </div>
    <div>
        <a href="{{ route('organisasi.create') }}" class="hivi-btn-primary">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            Tambah Sub Organ
        </a>
    </div>
</div>

<div class="org-grid">

    {{-- ══════════════════════════════════════
         OSIS
    ══════════════════════════════════════ --}}
    @if($osis)
    <div class="org-card">
        <div class="org-card-header">
            <div class="flex justify-between items-center mb-4">
                <div class="org-badge org-badge--osis">OSIS</div>
                <div class="org-stat">
                    <i data-lucide="users" style="width:14px;height:14px;"></i>
                    {{ $osis->members->count() }} anggota
                </div>
            </div>
            <h2 class="org-card-title">{{ $osis->nama }}</h2>
            <p class="org-card-desc">{{ $osis->deskripsi }}</p>
        </div>
        <div class="org-card-body">
            @forelse($osis->members->take(5) as $member)
            <div class="org-member-row">
                <div class="org-member-avatar">{{ substr($member->user->name ?? 'U', 0, 1) }}</div>
                <div class="org-member-info">
                    <span class="org-member-name">{{ $member->user->name ?? '-' }}</span>
                    <span class="org-member-jabatan jabatan-{{ $member->jabatan }}">{{ ucfirst(str_replace('_', ' ', $member->jabatan)) }}</span>
                </div>
            </div>
            @empty
            <p class="org-empty">Belum ada anggota.</p>
            @endforelse
        </div>
        <div class="org-card-footer">
            <a href="{{ route('organisasi.show', $osis->id) }}" class="hivi-btn-secondary w-full text-center" style="display: flex;">
                Kelola Anggota →
            </a>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         MPK
    ══════════════════════════════════════ --}}
    @if($mpk)
    <div class="org-card">
        <div class="org-card-header">
            <div class="flex justify-between items-center mb-4">
                <div class="org-badge org-badge--mpk">MPK</div>
                <div class="flex gap-3">
                    <div class="org-stat">
                        <i data-lucide="users" style="width:14px;height:14px;"></i>
                        {{ $mpk->members->count() }} anggota
                    </div>
                    <div class="org-stat">
                        <i data-lucide="layers" style="width:14px;height:14px;"></i>
                        {{ $mpk->komisis->count() }} komisi
                    </div>
                </div>
            </div>
            <h2 class="org-card-title">{{ $mpk->nama }}</h2>
            <p class="org-card-desc">{{ $mpk->deskripsi }}</p>
        </div>
        <div class="org-card-body">
            @forelse($mpk->members->take(4) as $member)
            <div class="org-member-row">
                <div class="org-member-avatar">{{ substr($member->user->name ?? 'U', 0, 1) }}</div>
                <div class="org-member-info">
                    <span class="org-member-name">{{ $member->user->name ?? '-' }}</span>
                    <span class="org-member-jabatan jabatan-{{ $member->jabatan }}">{{ ucfirst(str_replace('_', ' ', $member->jabatan)) }}</span>
                </div>
            </div>
            @empty
            <p class="org-empty">Belum ada anggota.</p>
            @endforelse

            @if($mpk->komisis->count() > 0)
            <div class="org-komisi-section">
                <p class="org-komisi-label"><i data-lucide="layers" style="width:12px;height:12px;"></i> Komisi:</p>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($mpk->komisis as $komisi)
                    <span class="tag-komisi">{{ $komisi->nama }} ({{ $komisi->members->count() }})</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="org-card-footer">
            <a href="{{ route('organisasi.show', $mpk->id) }}" class="hivi-btn-secondary w-full text-center" style="display: flex;">
                Kelola Anggota & Komisi →
            </a>
        </div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════
     Sub Organ List
══════════════════════════════════════ --}}
<div class="mb-4 mt-12">
    <h2 class="text-xl font-sans font-bold text-[#111111]">Sub Organisasi</h2>
</div>

<div class="sub-organ-grid">
    @forelse($subOrgans as $sub)
    <div class="sub-organ-card">
        <div class="sub-organ-header">
            <div class="flex justify-between items-center mb-4">
                <div class="org-badge org-badge--sub">Sub Organ</div>
                <div class="org-stat">
                    <i data-lucide="users" style="width:12px;height:12px;"></i>
                    {{ $sub->members->count() }} anggota
                </div>
            </div>
            <h3 class="sub-organ-title">{{ $sub->nama }}</h3>
            <p class="sub-organ-desc">{{ $sub->deskripsi ?? '-' }}</p>
        </div>
        <div class="sub-organ-footer mt-4">
            <a href="{{ route('organisasi.show', $sub->id) }}" class="hivi-btn-outline w-full text-center" style="display: flex;">
                Kelola →
            </a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i data-lucide="inbox" style="width:40px;height:40px;opacity:.4; margin: 0 auto 12px;"></i>
        <p>Belum ada Sub Organ. <a href="{{ route('organisasi.create') }}" class="text-[var(--color-text)] font-semibold underline">Tambah sekarang</a>.</p>
    </div>
    @endforelse
</div>

<style>
.org-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:24px; width: 100%; }

.org-card {
    background: var(--color-surface);
    border-radius: var(--radius-card);
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.org-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
}
.org-card-header { padding:24px 24px 16px; }
.org-card-title { font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 700; color: var(--color-text); margin: 0 0 8px; }
.org-card-desc { color:var(--color-text-muted); font-size:13px; line-height: 1.5; margin:0; }
.org-stat { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--color-text-muted); font-weight: 400; }
.org-card-body { padding:0 24px 20px; flex-grow: 1; }
.org-card-footer { padding:16px 24px 24px; border-top:1px solid var(--color-border); }

.org-badge { display:inline-block; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:600; letter-spacing:.05em; text-transform: uppercase; }
.org-badge--osis { background:var(--color-bg-light); color:var(--color-primary); }
.org-badge--mpk  { background:#E0F2FE; color:#0369A1; }
.org-badge--sub  { background:#FEF3C7; color:#B45309; }

.org-member-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--color-border); }
.org-member-row:last-child { border-bottom:none; }
.org-member-avatar { width:32px; height:32px; border-radius:50%; background:var(--color-bg-light); color:var(--color-text); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; }
.org-member-info { display:flex; flex-direction:column; }
.org-member-name { font-size:13px; font-weight:500; color: var(--color-text); }
.org-member-jabatan { font-size:11px; font-weight: 500; }
.jabatan-bph { color:#B45309; }
.jabatan-ketua { color:#059669; }
.jabatan-pembina { color:#0369A1; }
.jabatan-pengawas { color:#B91C1C; }

.org-komisi-section { margin-top:16px; padding-top:16px; border-top:1px dashed rgba(0,0,0,0.08); }
.org-komisi-label { font-size:12px; font-weight: 600; color:#4B5563; margin:0 0 8px; display:flex; align-items:center; gap:6px; }
.tag-komisi { display:inline-block; background:rgba(3,105,161,0.08); color:#0369A1; border-radius:6px; padding:2px 8px; font-size:12px; font-weight: 500; }

.sub-organ-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px, 1fr)); gap:20px; width: 100%; }
.sub-organ-card {
    background: var(--color-surface);
    border-radius: var(--radius-card);
    padding: 20px;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.sub-organ-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
}
.sub-organ-title { font-family: 'Poppins', sans-serif; font-size:16px; font-weight:700; color: var(--color-text); margin: 0 0 4px; }
.sub-organ-desc { font-size:13px; color:var(--color-text-muted); margin:0; line-height: 1.5; }
.sub-organ-footer { border-top:1px solid var(--color-border); padding-top:12px; margin-top: auto; }

.empty-state { grid-column:1/-1; text-align:center; padding:48px; background: var(--color-bg-light); border-radius: var(--radius-card); border: 1px dashed var(--color-border); color:var(--color-text-muted); font-size: 14px; }
.org-empty { color:var(--color-text-muted); font-size:13px; padding:8px 0; font-style: italic; }
</style>
@endsection

