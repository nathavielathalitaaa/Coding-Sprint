{{-- ═══════════════════════════════════════════════
     floating pill sidebar – placed directly under <body>
     class: .hv-sidebar  (styled in master.blade.php)
     ═══════════════════════════════════════════════ --}}
<div class="hv-sidebar" id="hv-sidebar">

    {{-- ── top nav icons ── --}}
    <div class="hv-sidebar-nav">

        {{-- profile --}}
        <a href="{{ route('profile.show') }}"
           class="{{ request()->routeIs('profile.show') ? 'active' : '' }}"
           title="Profil">
            <i data-lucide="user-circle"></i>
        </a>

        {{-- dashboard --}}
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'active' : '' }}"
           title="Dashboard">
            <i data-lucide="monitor"></i>
        </a>

        {{-- karyawan (hr only) --}}
        @if(auth()->user()->hasRole('hr'))
        <a href="{{ route('hr/employee/list') }}"
           class="{{ request()->routeIs('hr/employee/list') ? 'active' : '' }}"
           title="Karyawan">
            <i data-lucide="layout-grid"></i>
        </a>
        @endif

        {{-- absensi (hr only) --}}
        @if(auth()->user()->hasRole('hr'))
        <a href="{{ route('hr/absensi/page') }}"
           class="{{ request()->routeIs('hr/absensi/page') ? 'active' : '' }}"
           title="Absensi">
            <i data-lucide="calendar-range"></i>
        </a>
        @endif

        {{-- surat --}}
        <a href="{{ route('surat.index') }}"
           class="{{ request()->routeIs('surat.*') ? 'active' : '' }}"
           title="Surat">
            <i data-lucide="mail"></i>
        </a>

        {{-- cuti hr --}}
        @if(auth()->user()->hasRole('hr'))
        <a href="{{ route('hr/leave/hr/page') }}"
           class="{{ request()->routeIs('hr/leave/hr/page') ? 'active' : '' }}"
           title="Cuti">
            <i data-lucide="calendar-check"></i>
        </a>

        {{-- penggajian --}}
        <a href="{{ route('hr/penggajian/page') }}"
           class="{{ request()->routeIs('hr/penggajian/page') ? 'active' : '' }}"
           title="Penggajian">
            <i data-lucide="trending-up"></i>
        </a>

        {{-- flow approval --}}
        <a href="{{ route('hr.approval-flow.index') }}"
           class="{{ request()->routeIs('hr.approval-flow.*') ? 'active' : '' }}"
           title="Flow Approval">
            <i data-lucide="file-text"></i>
        </a>
        @endif

        {{-- cuti staff --}}
        @if(auth()->user()->hasRole('staff'))
        <a href="{{ route('hr/leave/employee/page') }}"
           class="{{ request()->routeIs('hr/leave/employee/page') ? 'active' : '' }}"
           title="Cuti Saya">
            <i data-lucide="calendar-check"></i>
        </a>
        @endif

    </div>

    {{-- ── logout at bottom ── --}}
    <div class="hv-sidebar-bottom">
        <a href="{{ route('logout') }}" class="hv-sidebar-logout" title="Keluar">
            <i data-lucide="log-out"></i>
        </a>
    </div>

</div>