@extends('layouts.master')

@section('title', 'Kelola Anggota Organisasi — SIMORA')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-sans font-bold text-[#111111] flex items-center gap-2">
            <i data-lucide="users" class="size-7 text-[var(--color-text)] align-text-bottom"></i>
            Kelola Anggota & Organisasi
        </h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">Kelola keanggotaan dan struktur organisasi Anda di SIMORA</p>
    </div>
</div>

{{-- Organization Selector (Only shown if user has more than 1 organization to manage, e.g. Admin) --}}
@if($organisasis->count() > 1)
<div class="mb-6 p-4 bg-white border border-gray-150 rounded-[20px] shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-4">
    <label for="org_selector" class="text-xs font-bold text-gray-500 uppercase tracking-widest">Pilih Organisasi yang Dikelola:</label>
    <select id="org_selector" class="form-select max-w-xs" onchange="window.location.href = '?org_id=' + this.value" style="background:#ffffff;">
        @foreach($organisasis as $org)
            <option value="{{ $org->id }}" {{ $org->id === $organisasi->id ? 'selected' : '' }}>
                {{ $org->nama }} ({{ strtoupper($org->tipe) }})
            </option>
        @endforeach
    </select>
</div>
@endif

<div class="show-grid">

    {{-- ══════════════════════════════════
         Daftar Anggota
    ══════════════════════════════════ --}}
    <div class="show-card">
        <div class="show-card-header flex justify-between items-center bg-white">
            <h2 class="show-card-title flex items-center gap-2">
                <i data-lucide="users" style="width:18px;height:18px;color:var(--color-primary);"></i>
                Anggota {{ $organisasi->nama }} ({{ $organisasi->members->count() }})
            </h2>
            <span class="org-badge org-badge--{{ $organisasi->tipe }}">{{ strtoupper($organisasi->tipe) }}</span>
        </div>
        <div class="show-card-body">
            @if($organisasi->members->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organisasi->members as $member)
                    <tr>
                        <td>
                            <div class="member-cell">
                                <div class="member-avatar">{{ substr($member->user->name ?? 'U', 0, 1) }}</div>
                                <span class="font-medium text-[#111111]">{{ $member->user->name ?? '-' }}</span>
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
                <i data-lucide="user-x" style="width:32px;height:32px;opacity:.4;margin:0 auto 12px;"></i>
                <p>Belum ada anggota di organisasi ini.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════
         Form Tambah Anggota
    ══════════════════════════════════ --}}
    <div class="show-card show-card--aside">
        <div class="show-card-header bg-white">
            <h2 class="show-card-title flex items-center gap-2">
                <i data-lucide="user-plus" style="width:18px;height:18px;color:var(--color-primary);"></i>
                Buat Akun & Anggota Baru
            </h2>
        </div>
        <div class="show-card-body">
            <div class="form-group">
                <label class="form-label">Metode Pembuatan</label>
                <select id="action_selector" class="form-select" onchange="switchAction(this.value)">
                    <option value="existing">Buat Akun Baru (Input Manual)</option>
                    <option value="excel">Buat Akun Baru (Unggah Excel)</option>
                    @if($organisasi->tipe === 'mpk' || $organisasi->tipe === 'osis')
                    <option value="manage_komisi">Kelola {{ $organisasi->tipe === 'osis' ? 'Divisi' : 'Komisi' }}</option>
                    @endif
                </select>
            </div>

            <!-- FORM 1: Buat Akun Baru Manual -->
            <div id="form_existing">
                <form method="POST" action="{{ route('organisasi.members.add', $organisasi->id) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-input" placeholder="Masukkan nama lengkap..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="Masukkan email..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role_name" class="form-select" required>
                            <option value="anggota">Anggota (Siswa)</option>
                            <option value="guru">Guru / Pembina</option>
                            <option value="admin">Admin</option>
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
                    @if($organisasi->komisis->count() > 0)
                    <div class="form-group">
                        <label class="form-label">Pilih {{ $organisasi->tipe === 'osis' ? 'Divisi' : 'Komisi' }} (Opsional)</label>
                        <select name="komisi_id" class="form-select">
                            <option value="">-- Tanpa {{ $organisasi->tipe === 'osis' ? 'Divisi' : 'Komisi' }} --</option>
                            @foreach($organisasi->komisis as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="hivi-btn-primary btn-full w-full justify-center" style="background:var(--color-primary); color:white;">
                        <i data-lucide="plus" style="width:16px;height:16px;"></i>
                        Tambah Anggota
                    </button>
                </form>
            </div>

            <!-- FORM 2: Buat Akun Semarak (Upload Excel) -->
            <div id="form_excel" style="display:none;">
                <form method="POST" action="{{ route('organisasi.import-excel', $organisasi->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Unduh Format Excel</label>
                        <a href="{{ route('organisasi.template-excel') }}" class="btn btn-outline btn-sm btn-full text-center" style="display: flex; gap: 8px; justify-content: center; align-items: center; border: 1px solid var(--color-border); border-radius: 9999px; text-decoration: none; color: var(--color-text); padding: 8px 12px; font-size: 13px;">
                            <i data-lucide="download" style="width:14px;height:14px;"></i>
                            Unduh Template Excel
                        </a>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unggah File Excel</label>
                        <input type="file" name="file" class="form-input" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted" style="font-size:10.5px; display:block; margin-top:4px;">Gunakan template di atas. Kolom ID & password tidak perlu diisi. Password default: password</small>
                    </div>
                    <button type="submit" class="hivi-btn-primary btn-full w-full justify-center" style="background:var(--color-primary); color:white;">
                        <i data-lucide="upload" style="width:16px;height:16px;"></i>
                        Proses Import Akun
                    </button>
                </form>
            </div>

            <!-- FORM 3: Kelola Komisi/Divisi (MPK / OSIS) -->
            @if($organisasi->tipe === 'mpk' || $organisasi->tipe === 'osis')
            @php
                $term      = $organisasi->tipe === 'osis' ? 'Divisi' : 'Komisi';
                $termDesc  = $organisasi->tipe === 'osis'
                    ? 'Divisi adalah pengelompokan bidang kerja dalam OSIS, misalnya Divisi Humas, Divisi Seni, etc.'
                    : 'Komisi adalah unit kerja dalam MPK yang menangani fungsi pengawasan tertentu.';
                $termColor = $organisasi->tipe === 'osis' ? 'term--osis' : 'term--mpk';
            @endphp
            <div id="form_manage_komisi" style="display:none;">

                {{-- Header --}}
                <div class="term-section-header {{ $termColor }}">
                    <div class="term-section-icon">
                        <i data-lucide="layers" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <p class="term-section-title">Kelola {{ $term }}</p>
                        <p class="term-section-desc">{{ $termDesc }}</p>
                    </div>
                </div>

                {{-- List existing --}}
                @if($organisasi->komisis->count() > 0)
                <div class="term-list">
                    @foreach($organisasi->komisis as $komisi)
                    <div class="term-item {{ $termColor }}">
                        <div class="term-item-header">
                            <div class="term-item-title-wrap">
                                <span class="term-item-dot {{ $termColor }}"></span>
                                <span class="term-item-name">{{ $komisi->nama }}</span>
                            </div>
                            <span class="term-item-count">{{ $komisi->members->count() }} anggota</span>
                        </div>

                        @if($komisi->deskripsi)
                        <p class="term-item-desc">{{ $komisi->deskripsi }}</p>
                        @endif

                        {{-- Members list --}}
                        @if($komisi->members->count() > 0)
                        <div class="term-members">
                            @foreach($komisi->members as $km)
                            <div class="term-member-row">
                                <div class="term-member-avatar">{{ strtoupper(substr($km->user->name ?? 'U', 0, 1)) }}</div>
                                <span class="term-member-name">{{ $km->user->name ?? '-' }}</span>
                                <form method="POST" action="{{ route('komisi.members.remove', [$komisi->id, $km->id]) }}"
                                      onsubmit="return confirm('Copot dari {{ strtolower($term) }}?')" style="margin-left:auto;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="term-remove-btn" title="Copot">✕</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="term-empty-members">Belum ada anggota di {{ strtolower($term) }} ini.</p>
                        @endif

                        {{-- Tambah anggota ke komisi/divisi --}}
                        <form method="POST" action="{{ route('komisi.members.add', $komisi->id) }}" class="term-add-form">
                            @csrf
                            <select name="user_id" class="form-select form-select-sm" required style="flex:1;">
                                <option value="">Tambah anggota ke {{ strtolower($term) }}...</option>
                                @foreach($organisasi->members as $m)
                                <option value="{{ $m->user_id }}">{{ $m->user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="term-add-btn {{ $termColor }}">+</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="term-none-yet">Belum ada {{ strtolower($term) }}. Buat yang pertama di bawah.</p>
                @endif

                {{-- Form buat baru --}}
                <div class="term-new-form {{ $termColor }}">
                    <p class="term-new-title">
                        <i data-lucide="plus-circle" style="width:14px;height:14px;"></i>
                        Buat {{ $term }} Baru
                    </p>
                    <p class="term-new-hint">Tambahkan {{ strtolower($term) }} baru untuk mengelompokkan anggota berdasarkan bidang atau fungsi.</p>
                    <form method="POST" action="{{ route('organisasi.komisi.store', $organisasi->id) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Nama {{ $term }} <span style="color:#E62129">*</span></label>
                            <input type="text" name="nama" class="form-input" placeholder="cth: {{ $organisasi->tipe === 'osis' ? 'Divisi Humas, Divisi Seni...' : 'Komisi A, Komisi Kedisiplinan...' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deskripsi {{ $term }}</label>
                            <textarea name="deskripsi" class="form-input" rows="2" placeholder="Masukkan deskripsi tugas singkat..." style="resize:none;"></textarea>
                        </div>
                        <button type="submit" class="hivi-btn-primary btn-full w-full justify-center {{ $termColor }}">
                            <i data-lucide="plus" style="width:16px;height:16px;"></i>
                            Simpan {{ $term }}
                        </button>
                    </form>
                </div>

            </div>
            @endif

        </div>
    </div>

</div>

<style>
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

/* ══════════════════════════════════════
   TERM SECTION (Divisi / Komisi)
══════════════════════════════════════ */
.term-section-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    margin: 16px 0 20px;
    border-top: 1px dashed var(--color-border);
    padding-top: 20px;
}
.term-section-header.term--osis { background: #FFF1F2; border: 1px solid rgba(230,33,41,0.12); }
.term-section-header.term--mpk  { background: #EFF6FF; border: 1px solid rgba(3,105,161,0.14); }

.term-section-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.term--osis .term-section-icon { background: rgba(230,33,41,0.1); color: #E62129; }
.term--mpk  .term-section-icon { background: rgba(3,105,161,0.1); color: #0369A1; }

.term-section-title { font-size: 13px; font-weight: 700; color: var(--color-text); margin: 0; }
.term-section-desc { font-size: 11px; color: var(--color-text-muted); margin: 2px 0 0; line-height: 1.4; }

/* Term List */
.term-list { display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px; }
.term-item { border-radius: 14px; padding: 14px; border: 1px solid var(--color-border); }
.term-item.term--osis { background: #FCFDFD; }
.term-item.term--mpk  { background: #FCFDFD; }

.term-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.term-item-title-wrap { display: flex; align-items: center; gap: 8px; }
.term-item-dot { width: 8px; height: 8px; border-radius: 50%; }
.term-item-dot.term--osis { background: #E62129; }
.term-item-dot.term--mpk  { background: #0369A1; }

.term-item-name { font-size: 13px; font-weight: 700; color: var(--color-text); }
.term-item-count { font-size: 10.5px; font-weight: 600; color: var(--color-text-muted); background: var(--color-bg-light); padding: 2px 8px; border-radius: 99px; }
.term-item-desc { font-size: 11.5px; color: var(--color-text-muted); margin: 0 0 10px 16px; line-height: 1.4; }

/* Term Members */
.term-members {
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: var(--color-bg-light);
    border-radius: 10px;
    padding: 10px;
    margin: 8px 0 12px 16px;
}
.term-member-row { display: flex; align-items: center; gap: 8px; font-size: 12px; }
.term-member-avatar { width: 22px; height: 22px; border-radius: 50%; background: #ffffff; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: var(--color-text); }
.term-member-name { font-weight: 500; color: var(--color-text); }
.term-remove-btn { background: none; border: none; color: #EF4444; font-size: 13px; font-weight: bold; cursor: pointer; padding: 2px 6px; border-radius: 4px; transition: background .2s; }
.term-remove-btn:hover { background: rgba(239,68,68,0.1); }
.term-empty-members { font-size: 11px; color: var(--color-text-muted); font-style: italic; margin: 4px 0 12px 16px; }

/* Term Add Member form */
.term-add-form { display: flex; gap: 8px; margin-left: 16px; }
.term-add-btn {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: none;
    color: white;
    font-size: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity .2s;
}
.term-add-btn.term--osis { background: #E62129; }
.term-add-btn.term--mpk  { background: #0369A1; }
.term-add-btn:hover { opacity: .9; }

/* Term New form */
.term-new-form { border-radius: 14px; padding: 14px; border: 1px dashed var(--color-border); }
.term-new-form.term--osis { background: rgba(230,33,41,0.02); }
.term-new-form.term--mpk  { background: rgba(3,105,161,0.02); }

.term-new-title { font-size: 12px; font-weight: 700; color: var(--color-text); margin: 0 0 4px; display: flex; align-items: center; gap: 6px; }
.term-new-hint { font-size: 11px; color: var(--color-text-muted); margin: 0 0 12px; }

.term-new-form button.term--osis { background: #E62129; color: white; }
.term-new-form button.term--mpk  { background: #0369A1; color: white; }
.term-none-yet { font-size: 11.5px; color: var(--color-text-muted); font-style: italic; margin-bottom: 16px; }
</style>

@endsection

@push('scripts')
<script>
    function switchAction(val) {
        // Form IDs
        const forms = ['form_existing', 'form_excel', 'form_manage_komisi'];
        forms.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = (id === 'form_' + val) ? 'block' : 'none';
        });
    }
</script>
@endpush
