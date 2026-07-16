@extends('layouts.master')

@section('title', 'Detail Organisasi — ' . $organisasi->nama . ' — SIMORA')

@section('content')
<div class="content-header">
    <div class="content-header-left">
        <a href="{{ route('organisasi.index') }}" class="breadcrumb-back">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kelola Organisasi
        </a>
        <h1 class="page-title" style="margin-top:.5rem;">
            <span class="org-badge org-badge--{{ $organisasi->tipe }}">{{ $organisasi->tipe_label }}</span>
            {{ $organisasi->nama }}
        </h1>
        @if($organisasi->deskripsi)
        <p class="page-subtitle">{{ $organisasi->deskripsi }}</p>
        @endif
    </div>
</div>

<div class="show-grid">

    {{-- ══════════════════════════════════
         Daftar Anggota
    ══════════════════════════════════ --}}
    <div class="show-card">
        <div class="show-card-header">
            <h2 class="show-card-title">
                <i data-lucide="users" style="width:18px;height:18px;"></i>
                Daftar Anggota ({{ $organisasi->members->count() }})
            </h2>
        </div>
        <div class="show-card-body">
            @if($organisasi->members->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organisasi->members as $member)
                    <tr>
                        <td>
                            <div class="member-cell">
                                <div class="member-avatar">{{ substr($member->user->name ?? 'U', 0, 1) }}</div>
                                <span>{{ $member->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $member->user->email ?? '-' }}</td>
                        <td><span class="badge-jabatan jabatan-{{ $member->jabatan }}">{{ $member->jabatan_label }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('organisasi.members.remove', [$organisasi->id, $member->id]) }}"
                                  onsubmit="return confirm('Copot {{ $member->user->name ?? 'anggota' }} dari organisasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Copot</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state-sm">
                <i data-lucide="user-x" style="width:32px;height:32px;opacity:.4;"></i>
                <p>Belum ada anggota di organisasi ini.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════
         Form Tambah Anggota
    ══════════════════════════════════ --}}
    <div class="show-card show-card--aside">
        <div class="show-card-header">
            <h2 class="show-card-title">
                <i data-lucide="user-plus" style="width:18px;height:18px;"></i>
                Tambah Anggota
            </h2>
        </div>
        <div class="show-card-body">
            <form method="POST" action="{{ route('organisasi.members.add', $organisasi->id) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Pilih User</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih User --</option>
                        @foreach($availableUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <select name="jabatan" class="form-select" required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatanOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full">
                    <i data-lucide="plus" style="width:16px;height:16px;"></i>
                    Tambah Anggota
                </button>
            </form>
        </div>

        {{-- ══════════════ Kelola Komisi (khusus MPK) ══════════════ --}}
        @if($organisasi->tipe === 'mpk')
        <div class="show-card-header" style="margin-top:1.5rem; border-top:1px solid rgba(255,255,255,.08); padding-top:1.25rem;">
            <h2 class="show-card-title">
                <i data-lucide="layers" style="width:18px;height:18px;"></i>
                Kelola Komisi
            </h2>
        </div>
        <div class="show-card-body">
            {{-- List existing Komisi --}}
            @foreach($organisasi->komisis as $komisi)
            <div class="komisi-item">
                <div class="komisi-header">
                    <span class="komisi-name">{{ $komisi->nama }}</span>
                    <span class="komisi-count">{{ $komisi->members->count() }} anggota</span>
                </div>
                <div class="komisi-members">
                    @foreach($komisi->members as $km)
                    <div class="komisi-member-row">
                        <span class="komisi-member-name">{{ $km->user->name ?? '-' }}</span>
                        <form method="POST" action="{{ route('komisi.members.remove', [$komisi->id, $km->id]) }}"
                              onsubmit="return confirm('Copot dari komisi?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-link-danger">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                {{-- Form tambah anggota komisi --}}
                <form method="POST" action="{{ route('komisi.members.add', $komisi->id) }}" class="komisi-add-form">
                    @csrf
                    <select name="user_id" class="form-select form-select-sm" required>
                        <option value="">Tambah anggota...</option>
                        @foreach($availableUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                        @foreach($organisasi->members as $m)
                        <option value="{{ $m->user_id }}">{{ $m->user->name }} (anggota)</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline btn-xs">+</button>
                </form>
            </div>
            @endforeach

            {{-- Form buat komisi baru --}}
            <div class="new-komisi-form">
                <p class="form-label" style="margin-bottom:.75rem;">Buat Komisi Baru</p>
                <form method="POST" action="{{ route('organisasi.komisi.store', $organisasi->id) }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="nama" class="form-input" placeholder="Nama komisi..." required>
                    </div>
                    <div class="form-group">
                        <textarea name="deskripsi" class="form-input" placeholder="Deskripsi (opsional)" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm btn-full">
                        <i data-lucide="plus" style="width:14px;height:14px;"></i>
                        Buat Komisi
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>

</div>

<style>
/* ── Breadcrumb ── */
.breadcrumb-back {
    color: var(--color-text-muted);
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: color .2s;
}
.breadcrumb-back:hover { color: var(--color-text); }

/* ── Page Header ── */
.content-header { margin-bottom: 2rem; }
.page-title {
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin: 8px 0 4px;
}
.page-subtitle { color: var(--color-text-muted); font-size: 13px; margin: 0; }

/* ── Layout Grid ── */
.show-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    align-items: start;
    max-width: 1400px;
}
@media(max-width:768px){ .show-grid { grid-template-columns: 1fr; } }

/* ── Cards ── */
.show-card {
    background: var(--color-surface);
    border-radius: var(--radius-card);
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    overflow: hidden;
}
.show-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--color-border);
}
.show-card-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-text);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.show-card-body { padding: 20px 24px; }

/* ── Table ── */
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th {
    text-align: left;
    padding: 8px 12px;
    color: var(--color-text-muted);
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom: 1px solid var(--color-border);
}
.data-table td { padding: 10px 12px; border-bottom: 1px solid var(--color-border); color: var(--color-text); }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: var(--color-bg-light); }

/* ── Member Cell ── */
.member-cell { display: flex; align-items: center; gap: 10px; }
.member-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--color-bg-light);
    color: var(--color-text);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}
.text-muted { color: var(--color-text-muted); }

/* ── Org Badges ── */
.org-badge { display:inline-block; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; }
.org-badge--osis     { background: var(--color-bg-light); color: var(--color-primary); }
.org-badge--mpk      { background: #E0F2FE; color: #0369A1; }
.org-badge--sub_organ{ background: #FEF3C7; color: #B45309; }

/* ── Jabatan Badges ── */
.badge-jabatan { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
.jabatan-bph        { background: #FEF3C7; color: #B45309; }
.jabatan-ketua      { background: var(--color-bg-light); color: #059669; }
.jabatan-pembina    { background: #E0F2FE; color: #0369A1; }
.jabatan-pengawas   { background: #FCE7F3; color: #9D174D; }
.jabatan-anggota    { background: #F3F4F6; color: #374151; }
.jabatan-sekretaris { background: #EDE9FE; color: #5B21B6; }
.jabatan-komisi     { background: #FEF9C3; color: #854D0E; }

/* ── Form elements ── */
.form-group { margin-bottom: 16px; }
.form-label { font-size: 12px; font-weight: 600; color: var(--color-text-muted); display: block; margin-bottom: 6px; }
.form-select, .form-input {
    width: 100%;
    background: var(--color-bg-light);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-input);
    padding: 10px 14px;
    color: var(--color-text);
    font-size: 13px;
    transition: border-color .2s, box-shadow .2s;
    font-family: 'Poppins', sans-serif;
}
.form-select:focus, .form-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(230,33,41,0.12);
}
.form-select-sm { padding: 6px 10px; font-size: 12px; }
.btn-full { width: 100%; justify-content: center; }

/* ── Copot button ── */
.btn.btn-danger.btn-xs {
    background: rgba(239,68,68,0.08);
    color: #DC2626;
    border: 1px solid rgba(239,68,68,0.2);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.btn.btn-danger.btn-xs:hover {
    background: #DC2626;
    color: #fff;
}

/* ── Empty state ── */
.empty-state-sm {
    text-align: center;
    padding: 40px;
    color: #9CA3AF;
}

/* ── Komisi (MPK sidebar) ── */
.komisi-item {
    background: rgba(3,105,161,0.04);
    border: 1px solid rgba(3,105,161,0.12);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
}
.komisi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.komisi-name { font-weight: 600; font-size: 13px; color: var(--color-text); }
.komisi-count { font-size: 11px; color: var(--color-text-muted); background: var(--color-bg-light); padding: 2px 8px; border-radius: 999px; }
.komisi-members { margin-bottom: 10px; }
.komisi-member-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid var(--color-border); }
.komisi-member-row:last-child { border-bottom: none; }
.komisi-member-name { font-size: 12px; color: var(--color-text); }
.btn-link-danger { background: none; border: none; color: #DC2626; cursor: pointer; font-size: 13px; padding: 2px 4px; opacity: .7; transition: opacity .2s; }
.btn-link-danger:hover { opacity: 1; }
.komisi-add-form { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
.new-komisi-form { margin-top: 20px; padding-top: 20px; border-top: 1px dashed var(--color-border); }
</style>
@endsection

