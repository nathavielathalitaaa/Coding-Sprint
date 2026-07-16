{{-- ═══════════════════════════════════════════════
     SIMORA — Sidebar navigasi utama
     ═══════════════════════════════════════════════ --}}
<div class="hv-sidebar" id="hv-sidebar">
    {{-- ── Logo Area ── --}}
    <div class="px-[30px] pb-8 pt-2">
        <a href="{{ route('home') }}" class="block">
            <img src="{{ asset('assets/images/SIMORA.png') }}" alt="Logo SIMORA" class="h-9 w-auto object-contain">
        </a>
    </div>

    {{-- ── top nav icons ── --}}
    <div class="hv-sidebar-nav">

        {{-- dashboard --}}
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i data-lucide="monitor"></i>
            <span>Dashboard</span>
        </a>

        {{-- ajukan surat --}}
        <a href="{{ route('surat.create') }}"
           class="{{ request()->routeIs('surat.create') ? 'active' : '' }}"
           title="Ajukan Surat Baru">
            <i data-lucide="plus-circle"></i>
            <span>Ajukan surat</span>
        </a>

        {{-- persetujuan --}}
        <a href="{{ route('surat.index', ['filter' => 'waiting']) }}"
           class="{{ request()->routeIs('surat.index') && request('filter') === 'waiting' ? 'active' : '' }}"
           title="Persetujuan Surat">
            <i data-lucide="check-square"></i>
            <span>Persetujuan</span>
        </a>

        {{-- daftar surat --}}
        <a href="{{ route('surat.index') }}"
           class="{{ request()->routeIs('surat.index') && request('filter') !== 'waiting' || request()->routeIs('surat.show') || request()->routeIs('surat.edit') ? 'active' : '' }}"
           title="Daftar Surat">
            <i data-lucide="file-text"></i>
            <span>Daftar surat</span>
        </a>

        {{-- Pelaksanaan & LPJ ── --}}
        <a href="{{ route('pelaksanaan.index') }}"
           class="{{ request()->routeIs('pelaksanaan.index') || request()->routeIs('pelaksanaan.disposisi') || request()->routeIs('lpj.create') ? 'active' : '' }}">
            <i data-lucide="play-circle"></i>
            <span>Pelaksanaan</span>
        </a>

        {{-- Verifikasi LPJ (Admin/Guru) ── --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin', 'guru']))
        <a href="{{ route('lpj.verifikasi.index') }}"
           class="{{ request()->routeIs('lpj.verifikasi.index') ? 'active' : '' }}">
            <i data-lucide="check-square"></i>
            <span>Verifikasi LPJ</span>
        </a>
        @endif
        {{-- Database Arsip LPJ ── --}}
        <a href="{{ route('arsip.index') }}"
           class="{{ request()->routeIs('arsip.index') || request()->routeIs('lpj.show') ? 'active' : '' }}">
            <i data-lucide="archive"></i>
            <span>Arsip LPJ</span>
        </a>

        {{-- profile --}}
        <a href="{{ route('profile.show') }}"
           class="{{ request()->routeIs('profile.show') ? 'active' : '' }}"
           title="Profil Saya">
            <i data-lucide="user-circle"></i>
            <span>Profil Saya</span>
        </a>

        {{-- inbox admin (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('surat.inbox_admin') }}"
           class="{{ request()->routeIs('surat.inbox_admin') ? 'active' : '' }}">
            <i data-lucide="inbox"></i>
            <span>Inbox Admin</span>
        </a>
        @endif

        {{-- kelola organisasi (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('organisasi.index') }}"
           class="{{ request()->routeIs('organisasi.*') || request()->routeIs('komisi.*') ? 'active' : '' }}">
            <i data-lucide="users"></i>
            <span>Organisasi</span>
        </a>
        @endif

        {{-- jenis surat (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('surat-type.index') }}"
           class="{{ request()->routeIs('surat-type.*') ? 'active' : '' }}">
            <i data-lucide="file-cog"></i>
            <span>Jenis Surat</span>
        </a>
        @endif

        {{-- system monitor (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('system/monitor') }}"
           class="{{ request()->routeIs('system/monitor') ? 'active' : '' }}">
            <i data-lucide="activity"></i>
            <span>Sistem</span>
        </a>
        @endif

        {{-- pengaturan (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('users.settings.document') }}"
           class="{{ request()->routeIs('users.settings.*') ? 'active' : '' }}">
            <i data-lucide="settings"></i>
            <span>Pengaturan</span>
        </a>
        @endif

    </div>

    {{-- ── logout at bottom ── --}}
    <div class="hv-sidebar-bottom">
        <a href="{{ route('logout') }}" class="hv-sidebar-logout">
            <i data-lucide="log-out"></i>
            <span>Keluar</span>
        </a>
    </div>

</div>
