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

        {{-- ajukan surat (bukan admin) --}}
        @can('create', App\Models\Surat::class)
        <a href="{{ route('surat.create') }}"
           class="{{ request()->routeIs('surat.create') ? 'active' : '' }}"
           title="Ajukan Surat Baru">
            <i data-lucide="plus-circle"></i>
            <span>Ajukan surat</span>
        </a>
        @endcan

        {{-- persetujuan --}}
        @unless(auth()->user()->hasRole('staff'))
        @php
            $waitingCount = 0;
            if(auth()->check()) {
                $authUser = auth()->user();
                $activeSuratIds = \App\Models\Surat::where('status', 'submitted')->pluck('id');
                $waitingCount = \App\Models\DocumentApproval::where('status', 'waiting')
                    ->where('document_type', 'LIKE', 'surat_%')
                    ->whereIn('document_id', $activeSuratIds)
                    ->where(function($q) use ($authUser) {
                        $q->where('assigned_user_id', $authUser->id)
                          ->orWhere(function($sq) use ($authUser) {
                              $jabatans = $authUser->organisasiMembers()->pluck('jabatan')->filter()->unique();
                              $sq->whereNull('assigned_user_id');
                              if ($jabatans->isNotEmpty()) {
                                  $sq->whereIn('jabatan', $jabatans);
                              } else {
                                  $sq->where('jabatan', '___NONE___');
                              }
                          });
                    })
                    ->count();
            }
        @endphp
        <a href="{{ route('surat.index', ['filter' => 'waiting']) }}"
           id="nav-persetujuan"
           class="{{ request()->routeIs('surat.index') && request('filter') === 'waiting' ? 'active' : '' }}"
           onclick="dismissWaitingBadge()"
           title="Persetujuan Surat">
            <i data-lucide="check-square"></i>
            <span>Persetujuan</span>
            @if($waitingCount > 0)
            <span id="waiting-badge"
                  style="margin-left:auto;min-width:20px;height:20px;border-radius:999px;background:#fff;color:#E62129;font-size:11px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;line-height:1;">
                {{ $waitingCount }}
            </span>
            @endif
        </a>
        @endunless

        {{-- daftar surat --}}
        <a href="{{ route('surat.index') }}"
           class="{{ request()->routeIs('surat.index') || request()->routeIs('surat.show') || request()->routeIs('surat.edit') ? 'active' : '' }}"
           title="Daftar Surat">
            <i data-lucide="file-text"></i>
            <span>Daftar surat</span>
            @if($waitingCount > 0)
            <span id="waiting-badge"
                  style="margin-left:auto;min-width:20px;height:20px;border-radius:999px;background:#fff;color:#E62129;font-size:11px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;line-height:1;">
                {{ $waitingCount }}
            </span>
            @endif
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

        {{-- inbox admin (admin only) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
        <a href="{{ route('surat.inbox_admin') }}"
           class="{{ request()->routeIs('surat.inbox_admin') ? 'active' : '' }}">
            <i data-lucide="inbox"></i>
            <span>Inbox Admin</span>
        </a>
        @endif

        {{-- kelola organisasi (admin, super-admin, and BPH OSIS/MPK) --}}
        @php
            $canManageOrg = auth()->check() && (
                auth()->user()->hasAnyRole(['admin', 'super-admin']) || 
                \App\Models\OrganisasiMember::where('user_id', auth()->id())
                    ->whereIn('jabatan', ['bph', 'ketua'])
                    ->whereHas('organisasi', function($q) {
                        $q->whereIn('tipe', ['osis', 'mpk']);
                    })
                    ->exists()
            );
        @endphp
        @if($canManageOrg)
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
