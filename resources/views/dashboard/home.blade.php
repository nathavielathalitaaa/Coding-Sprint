@extends('layouts.master')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

:root {
    --bg-base:    #e8f2eb;
    --bg-card:    #f0f7f2;
    --bg-inner:   #ebf5ee;
    --sh-light:   rgba(255,255,255,0.92);
    --sh-dark:    rgba(148,188,163,0.42);
    --green-main: #1a9e5c;
    --green-mid:  #2db870;
    --green-lite: #6ee3a5;
    --text-dark:  #14321f;
    --text-body:  #3d6650;
    --text-muted: #7aaa8e;
    --amber:  #d97706; --blue: #2563eb;
    --rose:   #e11d48; --purple: #7c3aed;
    --teal:   #0d9488; --orange: #ea580c;
}

.sng * { font-family: 'Plus Jakarta Sans', sans-serif !important; box-sizing: border-box; }
.sng { background: var(--bg-base); min-height: 100vh; padding: 18px 22px 48px; }

/* === SOFT CARD — 3 lapis shadow === */
.sc {
    background: var(--bg-card);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.78);
    box-shadow:
        8px   8px  20px var(--sh-dark),
       -8px  -8px  20px var(--sh-light),
        0     0     1px rgba(255,255,255,0.85) inset;
    padding: 22px;
    transition: box-shadow .2s, transform .2s;
}


/* inset pressed look */
.si {
    background: var(--bg-inner);
    border-radius: 12px;
    box-shadow:
        inset 4px  4px 10px var(--sh-dark),
        inset -4px -4px 10px var(--sh-light);
    padding: 11px 14px;
}

/* icon bubble */
.sico {
    width:44px; height:44px; border-radius:14px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    box-shadow: 4px 4px 10px var(--sh-dark), -4px -4px 10px var(--sh-light);
}
.sico i { width:19px; height:19px; }

.ic-g  { background:linear-gradient(135deg,#bbf7d0,#6ee7b7); color:#065f46; }
.ic-b  { background:linear-gradient(135deg,#dbeafe,#93c5fd); color:#1d4ed8; }
.ic-a  { background:linear-gradient(135deg,#fef3c7,#fcd34d); color:#92400e; }
.ic-p  { background:linear-gradient(135deg,#ede9fe,#c4b5fd); color:#5b21b6; }
.ic-t  { background:linear-gradient(135deg,#ccfbf1,#5eead4); color:#0f766e; }
.ic-o  { background:linear-gradient(135deg,#ffedd5,#fdba74); color:#9a3412; }

/* section title with green left bar */
.stitle {
    font-size:11px; font-weight:700; letter-spacing:.07em;
    text-transform:uppercase; color:var(--text-body);
    display:flex; align-items:center; gap:8px; margin-bottom:16px;
}
.stitle::before {
    content:''; width:4px; height:16px; border-radius:99px; flex-shrink:0;
    background:linear-gradient(180deg,var(--green-main),var(--green-lite));
}

/* progress bar */
.sbar { height:5px; border-radius:99px; margin-top:10px; overflow:hidden;
    background:rgba(148,188,163,.25);
    box-shadow:inset 1px 1px 3px var(--sh-dark),inset -1px -1px 3px var(--sh-light);
}
.sbar-fill { height:100%; border-radius:99px; }
.sfill-g { background:linear-gradient(90deg,var(--green-main),var(--green-lite)); }
.sfill-a { background:linear-gradient(90deg,#d97706,#fcd34d); }
.sfill-p { background:linear-gradient(90deg,#7c3aed,#c4b5fd); }

/* badge pill */
.sbadge {
    display:inline-flex; align-items:center;
    padding:3px 10px; border-radius:99px;
    font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
    box-shadow:2px 2px 5px var(--sh-dark),-2px -2px 5px var(--sh-light);
}
.sb-g { background:#dcfce7; color:#14532d; }
.sb-b { background:#dbeafe; color:#1e3a8a; }
.sb-y { background:#fef9c3; color:#854d0e; }
.sb-o { background:#ffedd5; color:#7c2d12; }
.sb-p { background:#ede9fe; color:#4c1d95; }

/* list row */
.slrow {
    display:flex; align-items:center; gap:12px;
    padding:11px 14px; border-radius:14px;
    background:var(--bg-card);
    box-shadow:4px 4px 10px var(--sh-dark),-4px -4px 10px var(--sh-light),0 0 1px rgba(255,255,255,.7) inset;
    margin-bottom:9px; transition:transform .15s;
}
.slrow:last-child { margin-bottom:0; }

/* avatar */
.savatar {
    width:38px; height:38px; border-radius:12px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:13px;
    box-shadow:3px 3px 7px var(--sh-dark),-3px -3px 7px var(--sh-light);
}

/* greeting banner */
.sbanner {
    background:linear-gradient(135deg,#166534 0%,#15803d 45%,#16a34a 100%);
    border-radius:20px; padding:20px 26px; color:#fff; position:relative; overflow:hidden;
    box-shadow:6px 6px 20px rgba(22,101,52,.32),-3px -3px 14px rgba(255,255,255,.18);
    margin-bottom:22px;
}
.sbanner::before {
    content:''; position:absolute; top:-50px; right:-50px;
    width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,.07);
}
.sbanner::after {
    content:''; position:absolute; bottom:-30px; right:90px;
    width:110px; height:110px; border-radius:50%; background:rgba(255,255,255,.05);
}

/* grid helpers */
.sg4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
.sg2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
.sg1 { display:grid; grid-template-columns:1fr; gap:16px; }
@media(max-width:1024px){ .sg4 { grid-template-columns:repeat(2,1fr); } }
@media(max-width:640px) { .sg4,.sg2 { grid-template-columns:1fr; } .sng{ padding:12px 12px 40px; } }
</style>

{{-- Wrapper ikuti struktur template asli --}}
<div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
<div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
<div class="sng">

{{-- ── GREETING BANNER ─────────────────────────────────────── --}}
<div class="sbanner">
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <img src="{{ URL::to('assets/images/logo-sinergi.png') }}"
                 alt="Sinergi"
                 style="height:44px;filter:brightness(0) invert(1);opacity:.88;"
                 onerror="this.style.display='none'">
            <div>
                <p style="font-size:11px;opacity:.7;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin:0;">Selamat Datang Kembali</p>
                <h2 style="font-size:19px;font-weight:800;margin:2px 0 0;">{{ auth()->user()->name ?? 'Administrator' }}</h2>
                <p style="font-size:11.5px;opacity:.65;margin:3px 0 0;">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:10px 16px;backdrop-filter:blur(4px);">
                <p style="font-size:10px;opacity:.7;font-weight:600;letter-spacing:.05em;text-transform:uppercase;margin:0;">Sistem HRIS</p>
                <p style="font-size:13px;font-weight:700;margin:3px 0 0;">Sinergi Hotel & Villa</p>
                <p style="font-size:10px;opacity:.6;margin:2px 0 0;">Malang, Jawa Timur</p>
            </div>
        </div>
    </div>
</div>

{{-- ── BREADCRUMB ──────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;" class="print:hidden">
    <div>
        <h5 style="font-size:16px;font-weight:800;color:var(--text-dark);margin:0;">Dashboard HR</h5>
        <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Overview karyawan, absensi & penggajian</p>
    </div>
    <div style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:5px;">
        <span>Dashboard</span>
        <span style="opacity:.35;">/</span>
        <span style="color:var(--green-main);font-weight:700;">HR</span>
    </div>
</div>

{{-- ── CONDITIONAL CONTENT BY ROLE ──────────────────────── --}}

@role('admin')
{{-- ADMIN VIEW: Full HR Statistics --}}

{{-- ── ROW 1 — 4 STAT CARDS ────────────────────────────────── --}}
<div class="sg4" style="margin-bottom:16px;">

    {{-- Total Karyawan --}}
    <div class="sc">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
            <div class="sico ic-b"><i data-lucide="users"></i></div>
            <span class="sbadge sb-b">aktif</span>
        </div>
        <p style="font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Total Karyawan</p>
        <h3 style="font-size:28px;font-weight:800;color:var(--text-dark);line-height:1;margin:0;">{{ $totalKaryawan }}</h3>
        <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0;">karyawan status aktif</p>
        <div class="sbar"><div class="sbar-fill sfill-g" style="width:{{ $totalKaryawan > 0 ? min(100,round(($totalKaryawan/120)*100)) : 0 }}%;"></div></div>
    </div>

    {{-- Hadir Hari Ini --}}
    <div class="sc">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
            <div class="sico ic-g"><i data-lucide="check-circle"></i></div>
            <span class="sbadge sb-g">hari ini</span>
        </div>
        <p style="font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Hadir Hari Ini</p>
        <h3 style="font-size:28px;font-weight:800;color:var(--text-dark);line-height:1;margin:0;">{{ $hadirHariIni }}</h3>
        <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0;">dari {{ $totalKaryawan }} karyawan
            <strong style="color:var(--green-main);">({{ $totalKaryawan > 0 ? round(($hadirHariIni/$totalKaryawan)*100) : 0 }}%)</strong>
        </p>
        <div class="sbar"><div class="sbar-fill sfill-g" style="width:{{ $totalKaryawan > 0 ? round(($hadirHariIni/$totalKaryawan)*100) : 0 }}%;"></div></div>
    </div>

    {{-- Cuti Menunggu --}}
    <div class="sc">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
            <div class="sico ic-a"><i data-lucide="calendar-clock"></i></div>
            <span class="sbadge sb-y">pending</span>
        </div>
        <p style="font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Cuti Menunggu</p>
        <h3 style="font-size:28px;font-weight:800;color:var(--text-dark);line-height:1;margin:0;">{{ $cutiMenungguCount }}</h3>
        <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0;">menunggu persetujuan HR</p>
        <div class="sbar"><div class="sbar-fill sfill-a" style="width:{{ min(100,$cutiMenungguCount*8) }}%;"></div></div>
    </div>

    {{-- Total Departemen --}}
    <div class="sc">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
            <div class="sico ic-p"><i data-lucide="building-2"></i></div>
            <span class="sbadge sb-p">unit</span>
        </div>
        <p style="font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Departemen</p>
        <h3 style="font-size:28px;font-weight:800;color:var(--text-dark);line-height:1;margin:0;">{{ $totalDepartemen }}</h3>
        <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0;">unit departemen aktif</p>
        <div class="sbar"><div class="sbar-fill sfill-p" style="width:{{ min(100,$totalDepartemen*15) }}%;"></div></div>
    </div>

</div>{{-- end row 1 --}}

{{-- ── ROW 2 — PAYROLL 2 CARDS ─────────────────────────────── --}}
<div class="sg2" style="margin-bottom:16px;">

    {{-- Total Gaji Dibayar --}}
    <div class="sc" style="display:flex;align-items:center;gap:18px;">
        <div class="sico ic-t" style="width:54px;height:54px;border-radius:16px;">
            <i data-lucide="banknote" style="width:22px;height:22px;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Total Gaji Dibayar Bulan Ini</p>
            <h3 style="font-size:20px;font-weight:800;color:var(--text-dark);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                Rp{{ number_format($totalGajiBayar,0,',','.') }}
            </h3>
            <p style="font-size:11.5px;color:var(--text-muted);margin:3px 0 0;">
                Status <span style="color:var(--green-main);font-weight:700;">dibayar</span> · {{ date('m/Y') }}
            </p>
        </div>
        <div class="si" style="flex-shrink:0;text-align:center;padding:10px 16px;">
            <p style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.04em;">bulan ini</p>
            <p style="font-size:18px;font-weight:800;color:var(--green-main);margin:2px 0 0;">{{ date('M') }}</p>
        </div>
    </div>

    {{-- Total Jam Lembur --}}
    <div class="sc" style="display:flex;align-items:center;gap:18px;">
        <div class="sico ic-o" style="width:54px;height:54px;border-radius:16px;">
            <i data-lucide="timer" style="width:22px;height:22px;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <p style="font-size:10.5px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-muted);margin:0 0 4px;">Total Jam Lembur</p>
            <h3 style="font-size:20px;font-weight:800;color:var(--text-dark);margin:0;">
                {{ round($totalJamLembur,1) }}<span style="font-size:14px;font-weight:600;color:var(--text-muted);"> jam</span>
            </h3>
            <p style="font-size:11.5px;color:var(--text-muted);margin:3px 0 0;">30 hari terakhir · seluruh karyawan</p>
        </div>
        <div class="si" style="flex-shrink:0;text-align:center;padding:10px 16px;">
            <p style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.04em;">rata/hari</p>
            <p style="font-size:18px;font-weight:800;color:#c2410c;margin:2px 0 0;">{{ round($totalJamLembur/30,1) }}j</p>
        </div>
    </div>

</div>{{-- end row 2 --}}

{{-- ── ROW 3 — CHARTS ──────────────────────────────────────── --}}
<div class="sg2" style="margin-bottom:16px;">

    <div class="sc">
        <div class="stitle">Absensi 7 Hari Terakhir</div>
        <div style="position:relative;height:210px;">
            <canvas id="chartAbsensi"></canvas>
        </div>
    </div>

    <div class="sc">
        <div class="stitle">Jam Lembur 7 Hari Terakhir</div>
        <div style="position:relative;height:210px;">
            <canvas id="chartJamLembur"></canvas>
        </div>
    </div>

</div>{{-- end row 3 --}}

{{-- ── ROW 4 — LIST CUTI & LEMBUR ──────────────────────────── --}}
<div class="sg2">

    {{-- Pengajuan Cuti Terbaru --}}
    <div class="sc">
        <div class="stitle">5 Pengajuan Cuti Terbaru</div>
        @php
            $palBg = ['#d1fae5','#dbeafe','#ede9fe','#fef3c7','#ffe4e6'];
            $palFg = ['#065f46','#1d4ed8','#5b21b6','#92400e','#9f1239'];
        @endphp
        @forelse($cutiMenungguTerbaru as $cuti)
        @php $ci = $loop->index % 5; @endphp
        <div class="slrow">
            <div class="savatar" style="background:{{ $palBg[$ci] }};color:{{ $palFg[$ci] }};">
                {{ strtoupper(substr($cuti->employee_name ?? 'K',0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $cuti->employee_name }}</p>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0;">{{ $cuti->leave_type }} &middot; {{ $cuti->number_of_day }} hari</p>
                <p style="font-size:10.5px;color:var(--text-muted);margin:1px 0 0;">
                    {{ date('d M',strtotime($cuti->date_from)) }} – {{ date('d M Y',strtotime($cuti->date_to)) }}
                </p>
            </div>
            <span class="sbadge sb-y">{{ $cuti->status }}</span>
        </div>
        @empty
        <div class="si" style="text-align:center;padding:28px 16px;">
            <i data-lucide="calendar-check" style="width:28px;height:28px;color:var(--text-muted);margin:0 auto 8px;display:block;"></i>
            <p style="font-size:12px;color:var(--text-muted);margin:0;">Tidak ada pengajuan cuti menunggu</p>
        </div>
        @endforelse
    </div>

    {{-- Karyawan Lembur Hari Ini --}}
    <div class="sc">
        <div class="stitle">Karyawan Lembur Hari Ini</div>
        @php
            $palBg2 = ['#ffedd5','#ccfbf1','#fce7f3','#e0f2fe','#f0fdf4'];
            $palFg2 = ['#9a3412','#0f766e','#9d174d','#0369a1','#166534'];
        @endphp
        @forelse($karyawanLemburHariIni as $lembur)
        @php $ci2 = $loop->index % 5; @endphp
        <div class="slrow">
            <div class="savatar" style="background:{{ $palBg2[$ci2] }};color:{{ $palFg2[$ci2] }};">
                {{ strtoupper(substr($lembur['nama'] ?? 'K',0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $lembur['nama'] }}</p>
                <p style="font-size:11px;color:var(--text-muted);margin:2px 0 0;">{{ $lembur['departemen'] ?? '-' }}</p>
                <p style="font-size:10.5px;color:var(--text-muted);margin:1px 0 0;">
                    {{ $lembur['jam_masuk'] }} – {{ $lembur['jam_keluar'] }}
                </p>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <p style="font-size:15px;font-weight:800;color:#c2410c;margin:0;">+{{ $lembur['jam_lembur'] }}j</p>
                <span class="sbadge sb-o">lembur</span>
            </div>
        </div>
        @empty
        <div class="si" style="text-align:center;padding:28px 16px;">
            <i data-lucide="clock" style="width:28px;height:28px;color:var(--text-muted);margin:0 auto 8px;display:block;"></i>
            <p style="font-size:12px;color:var(--text-muted);margin:0;">Tidak ada karyawan lembur hari ini</p>
        </div>
        @endforelse
    </div>

</div>{{-- end row 4 --}}

@endrole{{-- END ADMIN VIEW --}}

{{-- ── SUPERVISOR VIEW ────────────────────────────────────── --}}
@role('supervisor')

<div style="margin-bottom:16px;">
    {{-- Surat Menunggu Approval --}}
    <div class="sc">
        <div class="stitle">Surat Menunggu Approval</div>
        <p style="font-size:14px;font-weight:800;color:var(--text-dark);margin:0 0 16px;">
            {{ $suratMenungguApproval ?? 0 }}<span style="font-size:12px;color:var(--text-muted);font-weight:600;"> surat</span>
        </p>
        <a href="{{ route('surat.index') }}" class="sc" style="display:block;text-align:center;padding:14px;text-decoration:none;background:var(--bg-inner);border-radius:12px;color:var(--green-main);font-size:13px;font-weight:700;">
            Lihat Surat Pending
        </a>
    </div>
</div>

@endrole{{-- END SUPERVISOR VIEW --}}

{{-- ── STAFF VIEW ─────────────────────────────────────────── --}}
@role('staff')

<div class="sg1" style="margin-bottom:16px;">
    {{-- Quick Stats --}}
    <div style="display:flex;gap:12px;margin-bottom:16px;">
        <div class="si" style="flex:1;text-align:center;">
            <p style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;font-weight:700;margin:0;">Total Surat</p>
            <p style="font-size:18px;font-weight:800;color:var(--text-dark);margin:4px 0 0;">{{ count($suratStaff ?? []) }}</p>
        </div>
        <div class="si" style="flex:1;text-align:center;">
            <p style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;font-weight:700;margin:0;">Menunggu Approval</p>
            <p style="font-size:18px;font-weight:800;color:var(--orange-main);margin:4px 0 0;">{{ $suratStaffPendingCount ?? 0 }}</p>
        </div>
        <div class="si" style="flex:1;text-align:center;">
            <p style="font-size:10.5px;color:var(--text-muted);text-transform:uppercase;font-weight:700;margin:0;">Perlu Revisi</p>
            <p style="font-size:18px;font-weight:800;color:var(--red-main);margin:4px 0 0;">{{ $suratStaffRevisiCount ?? 0 }}</p>
        </div>
    </div>

    {{-- Surat List --}}
    <div class="sc">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div class="stitle" style="margin:0;">Surat Saya</div>
            <a href="{{ route('surat.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--green-main);color:#fff;border-radius:8px;text-decoration:none;font-size:11.5px;font-weight:700;transition:opacity 0.2s;">
                + Buat Surat
            </a>
        </div>

        @forelse($suratStaff ?? [] as $surat)
            <div class="slrow" style="margin-bottom:8px;">
                <div class="savatar" style="background:linear-gradient(135deg,#dbeafe,#93c5fd);color:#1d4ed8;">
                    <i data-lucide="file-text" style="width:16px;height:16px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:12px;font-weight:700;color:var(--text-dark);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $surat->nomor_surat }}</p>
                    <p style="font-size:10px;color:var(--text-muted);margin:1px 0 0;">{{ substr($surat->perihal, 0, 40) }}{{ strlen($surat->perihal) > 40 ? '...' : '' }}</p>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    @if($surat->status === 'submitted')
                        <span class="sbadge sb-b" style="font-size:9.5px;">diajukan</span>
                    @elseif($surat->status === 'approved_supervisor')
                        <span class="sbadge sb-y" style="font-size:9.5px;">approval owner</span>
                    @elseif($surat->status === 'approved_owner')
                        <span class="sbadge sb-g" style="font-size:9.5px;">✓ disetujui</span>
                    @elseif($surat->status === 'revised')
                        <span class="sbadge sb-o" style="font-size:9.5px;">⚠ revisi</span>
                    @elseif($surat->status === 'rejected')
                        <span class="sbadge sb-p" style="font-size:9.5px;">ditolak</span>
                    @endif
                    <a href="{{ route('surat.show', $surat->id) }}" style="display:inline-block;margin-left:6px;padding:4px 8px;background:var(--bg-inner);border-radius:6px;text-decoration:none;color:var(--green-main);font-size:10px;font-weight:700;">
                        Lihat
                    </a>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:24px 16px;">
                <i data-lucide="inbox" style="width:32px;height:32px;color:var(--text-muted);margin:0 auto 8px;display:block;"></i>
                <p style="font-size:13px;color:var(--text-muted);margin:0;">Belum ada surat</p>
                <a href="{{ route('surat.create') }}" style="display:inline-block;margin-top:12px;padding:8px 16px;background:var(--green-main);color:#fff;border-radius:8px;text-decoration:none;font-size:11px;font-weight:700;">
                    Buat Surat Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>

@endrole{{-- END STAFF VIEW --}}
</div>{{-- end container-fluid --}}
</div>{{-- end wrapper --}}

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const font = { family: 'Plus Jakarta Sans' };
    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { ...font, size:11, weight:'600' },
                    usePointStyle:true, pointStyleWidth:8,
                    color:'#3d6650', padding:16 }
            },
            tooltip: {
                backgroundColor:'#f0f7f2', titleColor:'#14321f', bodyColor:'#3d6650',
                borderColor:'rgba(148,188,163,.4)', borderWidth:1,
                titleFont:{...font,weight:'700',size:12}, bodyFont:{...font,size:11},
                padding:10, cornerRadius:10
            }
        },
        scales: {
            x: { grid:{display:false}, ticks:{font:{...font,size:10,weight:'600'},color:'#7aaa8e'} },
            y: { beginAtZero:true, grid:{color:'rgba(148,188,163,.18)'},
                 ticks:{font:{...font,size:10},color:'#7aaa8e'} }
        }
    };

    // CHART ABSENSI — line
    const ctxA = document.getElementById('chartAbsensi');
    if(ctxA){
        new Chart(ctxA, {
            type:'line',
            data:{
                labels: @json($chartAbsensi['labels']),
                datasets:[
                    { label:'Hadir',
                      data: @json($chartAbsensi['datasets'][0]['data']),
                      borderColor:'#1a9e5c', backgroundColor:'rgba(26,158,92,.1)',
                      tension:.4, fill:true, pointRadius:4, pointBackgroundColor:'#1a9e5c', borderWidth:2.5 },
                    { label:'Izin',
                      data: @json($chartAbsensi['datasets'][1]['data']),
                      borderColor:'#d97706', backgroundColor:'rgba(217,119,6,.06)',
                      tension:.4, fill:false, pointRadius:3, pointBackgroundColor:'#d97706', borderWidth:2 },
                    { label:'Sakit',
                      data: @json($chartAbsensi['datasets'][2]['data']),
                      borderColor:'#2563eb', backgroundColor:'rgba(37,99,235,.06)',
                      tension:.4, fill:false, pointRadius:3, pointBackgroundColor:'#2563eb', borderWidth:2 },
                    { label:'Alpha',
                      data: @json($chartAbsensi['datasets'][3]['data']),
                      borderColor:'#e11d48', backgroundColor:'rgba(225,29,72,.06)',
                      tension:.4, fill:false, pointRadius:3, pointBackgroundColor:'#e11d48', borderWidth:2 },
                ]
            },
            options: baseOpts
        });
    }

    // CHART JAM LEMBUR — bar
    const ctxL = document.getElementById('chartJamLembur');
    if(ctxL){
        new Chart(ctxL, {
            type:'bar',
            data:{
                labels: @json($chartJamLembur['labels']),
                datasets:[{
                    label:'Jam Lembur',
                    data: @json($chartJamLembur['datasets'][0]['data']),
                    backgroundColor:'rgba(26,158,92,.72)',
                    borderColor:'#1a9e5c', borderWidth:1.5,
                    borderRadius:8, borderSkipped:false,
                }]
            },
            options:{
                ...baseOpts,
                scales:{
                    ...baseOpts.scales,
                    y:{ ...baseOpts.scales.y,
                        title:{ display:true, text:'jam',
                            font:{...font,size:10}, color:'#7aaa8e' } }
                }
            }
        });
    }
})();
</script>

@endsection