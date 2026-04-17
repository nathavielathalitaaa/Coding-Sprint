<div class="sng" style="padding: 24px 16px 32px; min-height: 100vh; border-radius: 0 24px 24px 0;">

    {{-- Logo & Brand --}}
    <div class="flex items-center gap-3 mb-10 px-2">
        <img src="{{ URL::to('assets/images/logo-sinergi.png') }}" 
             alt="Sinergi Hotel & Villa" 
             style="height: 42px; filter: brightness(0) invert(0.25) sepia(1) saturate(4) hue-rotate(140deg);">
        <div class="leading-none">
            <p class="text-[10px] font-medium tracking-widest text-var(--text-muted) uppercase">HR System</p>
            <p class="font-bold text-base text-var(--text-dark) -mt-0.5">Sinergi Hotel & Villa</p>
        </div>
    </div>

    <ul class="space-y-1">

        {{-- MENU UTAMA --}}
        {{-- Dashboard / Beranda --}}
        <li>
            <a href="{{ route('home') }}" 
               class="slrow flex items-center gap-3 px-4 py-[14px] {{ request()->routeIs('home') ? 'bg-[var(--bg-inner)] shadow-inner' : '' }}">
                <div class="sico ic-b">
                    <i data-lucide="monitor-dot" class="w-5 h-5"></i>
                </div>
                <span class="font-semibold text-[15px] text-var(--text-dark)">Dasbor</span>
            </a>
        </li>

        {{-- Manajemen SDM --}}
        <li>
            <a href="#!" onclick="toggleDropdown(this)" 
               class="slrow flex items-center gap-3 px-4 py-[14px] dropdown-button {{ request()->is('hr/*') ? 'bg-[var(--bg-inner)] shadow-inner' : '' }}">
                <div class="sico ic-g">
                    <i data-lucide="circuit-board" class="w-5 h-5"></i>
                </div>
                <span class="font-semibold text-[15px] text-var(--text-dark)">Manajemen SDM</span>
                <i data-lucide="chevron-down" class="ml-auto w-4 h-4 transition-transform dropdown-icon"></i>
            </a>

            {{-- Submenu --}}
            <div class="dropdown-content ml-5 mt-1 space-y-0.5 hidden">
                <a href="{{ route('hr/employee/list') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/employee/list') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Daftar Karyawan
                </a>
                <a href="{{ route('hr/holidays/page') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/holidays/page') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Hari Libur
                </a>
                <a href="{{ route('hr/department/page') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/department/page') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Departemen
                </a>
                <a href="{{ route('hr/absensi/page') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/absensi/page') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Absensi
                </a>
                <a href="{{ route('hr/shift/page') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/shift/page') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Shift Karyawan
                </a>
                <a href="{{ route('hr/penggajian/page') }}" 
                   class="flex items-center gap-3 px-5 py-3 rounded-2xl text-sm transition-all hover:slrow {{ request()->routeIs('hr/penggajian/page') ? 'text-var(--green-main) font-semibold' : 'text-var(--text-body)' }}">
                    <span class="w-2 h-2 rounded-full bg-var(--green-lite)"></span>
                    Penggajian
                </a>

                {{-- Kelola Cuti --}}
                <div class="pt-2">
                    <div class="px-5 text-[10px] font-bold tracking-widest text-var(--text-muted) mb-1">KELOLA CUTI</div>
                    <a href="{{ route('hr/leave/employee/page') }}" class="block px-5 py-2.5 text-sm text-var(--text-body) hover:text-var(--green-main) transition-colors">Atas Nama Karyawan</a>
                    <a href="{{ route('hr/leave/hr/page') }}" class="block px-5 py-2.5 text-sm text-var(--text-body) hover:text-var(--green-main) transition-colors">Atas Nama SDM</a>
                </div>
            </div>
        </li>

        {{-- HALAMAN --}}
        <li class="stitle px-3 mt-8 mb-3">
            HALAMAN
        </li>

        <li>
            <a href="#" 
               class="slrow flex items-center gap-3 px-4 py-[14px]">
                <div class="sico ic-p">
                    <i data-lucide="codesandbox" class="w-5 h-5"></i>
                </div>
                <span class="font-semibold text-[15px] text-var(--text-dark)">Akun & Pengaturan</span>
            </a>
        </li>

    </ul>

    {{-- Footer Sidebar --}}
    <div class="mt-auto pt-12 px-3">
        <div class="si text-center text-[10px] text-var(--text-muted)">
            © {{ date('Y') }} Sinergi Hotel & Villa Malang<br>
            <span class="text-var(--green-main)">HRIS System</span>
        </div>
    </div>
</div>

{{-- Script untuk toggle dropdown sidebar --}}
<script>
function toggleDropdown(el) {
    const content = el.nextElementSibling;
    const icon = el.querySelector('.dropdown-icon');
    
    if (content) {
        content.classList.toggle('hidden');
        if (icon) icon.classList.toggle('rotate-180');
    }
}

// Auto open submenu jika di halaman HR
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/hr/')) {
        const hrDropdown = document.querySelector('.dropdown-button');
        if (hrDropdown) {
            const content = hrDropdown.nextElementSibling;
            if (content) content.classList.remove('hidden');
        }
    }
});
</script>