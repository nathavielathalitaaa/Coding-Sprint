<!DOCTYPE html>
<html lang="id" class="light scroll-smooth group" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <meta charset="utf-8">
    <title>SIMORA | Sistem Surat OSIS - BPH OSIS SKOMDA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="SIMORA - Sistem Persuratan OSIS" name="description">
    <meta content="BPH OSIS SKOMDA" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- app favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ URL::to('assets/images/logo-tab.svg') }}">
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <!-- layout config js -->
    <script src="{{ URL::to('assets/js/layout.js') }}"></script>
    <!-- SIMORA SMK Telkom Sidoarjo css -->
    <link rel="stylesheet" href="{{ URL::to('assets/css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['Poppins', 'sans-serif'],
          poppins: ['Poppins', 'sans-serif'],
        }
      }
    }
  }
</script>
    
    <!-- hivi design system fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
<!-- ============================================
     hivi design system styles
     ============================================ -->
<style>
  /* =============================================
     CSS VARIABLES (Design Tokens)
     ============================================= */
  :root {
    --color-primary: #E62129;
    --color-primary-dark: #C91A20;
    --color-text: #111111;
    --color-text-muted: #6B7280;
    --color-surface: #FFFFFF;
    --color-border: #E5E7EB;
    --color-bg-light: #FFF1F2;
    --radius-card: 28px;
    --radius-pill: 9999px;
    --radius-input: 9999px;
  }

  /* =============================================
     RESET & BASE
  ============================================= */
  * {
    box-sizing: border-box;
  }
  
  html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
  }

  /* =============================================
     FONT & BACKGROUND
  ============================================= */
  body {
    font-family: 'Poppins', sans-serif;
    background: #F5F5F7 !important;
    min-height: 100vh;
    color: var(--color-text);
    overflow-x: hidden;
  }

  h1, h2, h3, h4, h5, h6, .serif { 
    font-family: 'Poppins', sans-serif; 
  }

  /* =============================================
     HIDE OLD TEMPLATE SIDEBAR
  ============================================= */
  .app-menu,
  .app-menu-overlay,
  #sidebar-overlay,
  footer {
    display: none !important;
  }

  /* =============================================
     TOPBAR
  ============================================= */
  #page-topbar {
    background-color: transparent !important;
    border-bottom: none !important;
    box-shadow: none !important;
    height: 56px !important;
    padding: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1001 !important;
    top: 0 !important;
    position: fixed !important;
    width: 100% !important;
  }
  
  #page-topbar .layout-width {
    width: 100% !important;
    padding-left: 20px !important;
  }

  #page-topbar .layout-width > div {
    background-color: transparent !important;
    border-bottom: none !important;
    box-shadow: none !important;
    height: 56px !important;
    padding: 0 !important;
  }

  /* =============================================
     PAGE CONTAINER (CLEAN LAYOUT)
  ============================================= */
  .hivi-page-wrapper {
    margin-left: 240px !important;
    padding: 24px;
    padding-top: 64px;
    min-height: 100vh;
    display: block;
  }

  .hivi-page-wrapper > * {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Responsive: Tablet */
  @media (max-width: 1024px) {
    .hivi-page-wrapper {
      margin-left: 100px;
      padding: 24px;
      padding-top: 80px;
    }
  }

  /* Responsive: Mobile */
  @media (max-width: 768px) {
    .hivi-page-wrapper {
      margin-left: 0;
      padding: 20px;
      padding-top: 76px;
    }
  }

  /* =============================================
     HIVI DESIGN SYSTEM UTILITIES
  ============================================= */
  .hivi-card {
      background: var(--color-surface);
      border-radius: var(--radius-card);
      padding: 32px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.06);
      border: none;
  }

  .hivi-btn-primary {
      background: var(--color-primary);
      color: white !important;
      border-radius: var(--radius-pill);
      padding: 14px 24px;
      font-size: 15px;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
  }
  .hivi-btn-primary:hover { background: var(--color-primary-dark); }
  .hivi-btn-primary:active { transform: scale(0.99); }
  
  .hivi-btn-secondary {
      background: transparent;
      color: var(--color-primary) !important;
      border-radius: var(--radius-pill);
      padding: 12px 28px;
      border: 1px solid var(--color-primary);
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
  }
  .hivi-btn-secondary:hover { background: var(--color-bg-light); }
  
  .hivi-btn-outline {
      background: transparent;
      color: var(--color-text-muted) !important;
      border-radius: var(--radius-pill);
      padding: 8px 16px;
      border: 1px solid var(--color-border);
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
  }
  .hivi-btn-outline:hover { background: var(--color-bg-light); }

  .hivi-badge {
      border-radius: var(--radius-pill);
      padding: 4px 12px;
      font-size: 12px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
  }
  .hivi-badge-green  { background: var(--color-bg-light); color: #E62129; }
  .hivi-badge-amber  { background: #fef3c7; color: #92400e; }
  .hivi-badge-red    { background: #fee2e2; color: #991b1b; }
  .hivi-badge-blue   { background: #dbeafe; color: #1e40af; }
  .hivi-badge-gray   { background: #f3f4f6; color: #374151; }
  
  .hivi-input {
      background: #E5E7EB;
      border-radius: var(--radius-input);
      padding: 14px 20px;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 400;
      width: 100%;
      outline: none;
      border: none;
      transition: box-shadow 0.2s;
      color: var(--color-text);
  }
  .hivi-input:focus { 
      box-shadow: 0 0 0 2px var(--color-primary);
      outline: none;
  }
  .hivi-input::placeholder { color: var(--color-text-muted); }
  
  .hivi-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .hivi-table thead th {
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 500;
      color: #6B7280;
      padding: 10px 16px;
      background: transparent;
      border-bottom: 1px solid #F5F5F7;
      text-align: left;
  }
  .hivi-table tbody tr {
      transition: background 0.15s;
      height: 52px;
  }
  .hivi-table tbody tr:hover { 
      background: rgba(255, 241, 242, 0.6); 
  }
  .hivi-table tbody td {
      padding: 0 16px;
      font-size: 14px;
      color: #111111;
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      border-bottom: 1px solid #F3F4F6;
  }
  .hivi-table tbody tr:last-child td {
      border-bottom: none;
  }

  .hivi-section-title {
      font-family: 'Poppins', sans-serif;
      font-size: 20px;
      font-weight: 600;
      color: #111111;
      margin-bottom: 16px;
  }

  /* =============================================
     STAT CARDS (FLEX LAYOUT)
  ============================================= */
  .hv-stat {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 200px;
  }

  .hv-stat-bottom {
      display: flex;
      align-items: flex-end;
      justify-content: flex-start;
      margin-top: auto;
      gap: 12px;
  }

  .hv-stat-icon {
      width: 32px;
      height: 32px;
      color: #9CA3AF;
      flex-shrink: 0;
  }

/* =============================================
     FULL HEIGHT SIDEBAR
  ============================================= */
.hv-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 240px;
    background: #E62129;
    display: flex;
    flex-direction: column;
    padding: 24px 0;
    z-index: 999999;
}

/* Nav icon group */
.hv-sidebar-nav {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 0 20px 0 0; /* Flush to the left */
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

/* Sliding Background Pill */
.hv-sidebar-slider {
    position: absolute;
    left: 0;
    right: 20px;
    height: 44px; /* default */
    background: #FFFFFF;
    border-radius: 0 9999px 9999px 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), height 0.25s;
    z-index: 0;
    pointer-events: none;
    opacity: 0; /* hidden until js sets it */
}
.hv-sidebar-nav::-webkit-scrollbar { display: none; }

/* Bottom section (logout) */
.hv-sidebar-bottom {
    padding: 0 20px 0 0;
    margin-top: auto;
}

/* ── Menu Items ── */
.hv-sidebar a {
    position: relative;
    z-index: 1;
    width: 100%;
    border-radius: 0 30px 30px 0;
    display: flex;
    align-items: center;
    padding: 12px 20px 12px 30px;
    color: rgba(255,255,255,0.9);
    background: transparent !important;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    gap: 12px;
}
.hv-sidebar a i,
.hv-sidebar a svg {
    width: 20px;
    height: 20px;
    stroke-width: 2;
    transition: inherit;
    flex-shrink: 0;
}

/* Hover & Active text colors */
.hv-sidebar a:hover,
.hv-sidebar a.active {
    color: #000000 !important;
}

/* Logout icon - same styling as other nav items */
.hv-sidebar-logout {
    color: rgba(255,255,255,0.9) !important;
    background: transparent !important;
}
.hv-sidebar-logout:hover,
.hv-sidebar-logout.active {
    color: #000000 !important;
    background: transparent !important;
}



/* ── Content offset ── */
.hv-main {
    margin-left: 240px;
    padding: 24px;
}

/* ── Responsive: Mobile & Tablet (Offcanvas) ── */
@media (max-width: 1024px) {
    .hv-sidebar {
        top: 0;
        bottom: 0;
        left: 0;
        transform: translateY(0) translateX(-100%);
        height: 100vh;
        width: 80px;
        border-radius: 0;
        padding: 80px 0 20px 0;
        background: #ffffff;
        box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hv-sidebar.open {
        transform: translateY(0) translateX(0);
    }
    .hv-sidebar-nav {
        max-height: calc(100vh - 180px);
    }
    .hv-sidebar-bottom {
        margin-top: auto;
    }
    .hv-main, .hivi-page-wrapper {
        margin-left: 0;
        padding-bottom: 32px;
    }
    .hivi-page-wrapper {
      padding-top: 76px;
      padding-left: 16px;
      padding-right: 16px;
    }
}

/* =============================================
     FORCE OVERRIDE LEGACY LAYOUT WRAPPERS
  ============================================= */
#layout-wrapper,
.layout-wrapper,
.main-content,
.page-content,
.vertical-menu,
[data-simplebar] {
    margin-left: 0 !important;
    padding-left: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
}
.vertical-menu {
    display: none !important;
}
.hv-sidebar i {
    pointer-events: none; 
}

/* Suppress any tooltips on sidebar */
.hv-sidebar a {
    position: relative;
    z-index: 1;
}
.hv-sidebar a::after {
    display: none !important;
}
.hv-sidebar [data-tooltip] {
    /* Prevent tippy.js tooltips if present */
}
.hv-sidebar [data-tippy-root] {
    display: none !important;
}

/* Completely disable tooltips on sidebar elements */
.hv-sidebar a[title],
.hv-sidebar a[data-tooltip],
.hv-sidebar a[data-tippy-content] {
    pointer-events: auto !important;
}
.hv-sidebar a[title]::before,
.hv-sidebar a[title]::after {
    display: none !important;
    content: none !important;
}
</style>

<style>
  .invalid-feedback {
    color: red;
  }
  .is-invalid {
    border-color: red;
  }
  .choices {
    position: relative;
    overflow: hidden;
    margin-bottom: 0px !important;
    font-size: 16px;
  }
  
  /* ── Skeleton Loading ── */
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
    background-size: 400% 100%;
    animation: shimmer 1.4s ease infinite;
    border-radius: 8px;
  }
  @keyframes shimmer {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
  }
  .real-content {
    opacity: 0;
    transition: opacity 200ms ease;
  }
  .real-content.loaded {
    opacity: 1;
  }
  .skeleton-wrapper {
    transition: opacity 200ms ease;
  }
</style>

</head>
<body class="text-base bg-body-bg text-body font-poppins dark:text-zink-100 dark:bg-zink-800 group-data-[skin=bordered]:bg-body-bordered group-data-[skin=bordered]:dark:bg-zink-700">

  <!-- floating sidebar (outside all containers) -->
  @include('sidebar.sidebar')

  <!-- page wrapper -->
  <div class="hivi-page-wrapper">
    
    <!-- topbar header -->
    <header id="page-topbar">
      <div class="layout-width">
        <div class="flex items-center px-4 mx-auto bg-topbar border-b-2 border-topbar group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:border-topbar-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:border-topbar-brand shadow-md h-header shadow-slate-200/50 group-data-[navbar=bordered]:rounded-md group-data-[navbar=bordered]:group-[.is-sticky]/topbar:rounded-t-none group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:border-zink-700 dark:shadow-none group-data-[topbar=dark]:group-[.is-sticky]/topbar:dark:shadow-zink-500 group-data-[topbar=dark]:group-[.is-sticky]/topbar:dark:shadow-md group-data-[navbar=bordered]:shadow-none group-data-[layout=horizontal]:group-data-[navbar=bordered]:rounded-b-none group-data-[layout=horizontal]:shadow-none group-data-[layout=horizontal]:dark:group-[.is-sticky]/topbar:shadow-none">
          
          <div class="flex items-center w-full group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl navbar-header group-data-[layout=horizontal]:ltr:xl:pr-3 group-data-[layout=horizontal]:rtl:xl:pl-3">
            
            <!-- hamburger (mobile only) -->
            <button type="button" id="mobile-menu-btn" class="flex items-center justify-center p-2 mr-3 text-slate-500 rounded-lg lg:hidden hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200">
              <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <!-- logo (horizontal only) -->
            <div class="items-center justify-center hidden px-5 text-center h-header group-data-[layout=horizontal]:md:flex group-data-[layout=horizontal]:ltr::pl-0 group-data-[layout=horizontal]:rtl:pr-0">
              <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span style="font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 700; color: #111111;">SIMORA</span>
              </a>
            </div>



            <!-- right side: icons + notifications + profile -->
            <div class="flex gap-3 ms-auto">
            </div>

            </div>
          </div>
        </div>
    </header>

    <!-- page content -->
    <div class="max-w-7xl px-6">
        @auth
        @unless(auth()->user()->hasRole('staff'))
            @php
                $user = auth()->user();
                $activeSuratIds = \App\Models\Surat::where('status', 'submitted')->pluck('id');
                $myWaitingGlobal = \App\Models\DocumentApproval::where('status', 'waiting')
                    ->where('document_type', 'LIKE', 'surat_%')
                    ->whereIn('document_id', $activeSuratIds)
                    ->where(function($q) use ($user) {
                        $q->where('assigned_user_id', $user->id)
                          ->orWhere(function($sq) use ($user) {
                              $jabatans = $user->organisasiMembers()->pluck('jabatan')->filter()->unique();
                              $sq->whereNull('assigned_user_id');
                              if ($jabatans->isNotEmpty()) {
                                  $sq->whereIn('jabatan', $jabatans);
                              } else {
                                  $sq->where('jabatan', '___NONE___');
                              }
                          });
                    })
                    ->count();
            @endphp
            @if($myWaitingGlobal > 0)
            <div class="mb-4 flex items-center justify-between gap-3 px-4 py-3 rounded-2xl shadow-sm border border-red-200"
                 style="background:rgba(254,242,242,0.9); backdrop-filter: blur(4px);">
                <div class="flex items-center gap-3">
                    <i data-lucide="bell-ring" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
                    <p class="text-sm text-red-800 font-medium">
                        Anda memiliki <strong>{{ $myWaitingGlobal }} surat</strong> yang menunggu persetujuan Anda.
                    </p>
                </div>
                @unless(request()->routeIs('surat.index'))
                <a href="{{ route('surat.index') }}" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition flex-shrink-0">
                    Tinjau Sekarang
                </a>
                @endunless
            </div>
            @endif
        @endunless
        @endauth

        @yield('content')
      </div>
    </div>

  </div>
  <!-- end page wrapper -->

  <!-- scripts -->
  <script src="{{ URL::to('assets/libs/lucide/umd/lucide.js') }}"></script>
  <script src="{{ URL::to('assets/js/layout.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- mobile sidebar overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 z-[999998] hidden bg-slate-900/50 backdrop-blur-sm transition-opacity opacity-0"></div>

  <!-- initialize icons & scripts -->
  <script>
    lucide.createIcons();
    document.addEventListener('DOMContentLoaded', function() {
      // Completely prevent tooltips on sidebar by disabling tippy.js for these elements
      const sidebarLinks = document.querySelectorAll('.hv-sidebar a');
      sidebarLinks.forEach(link => {
        // Remove all tooltip-related attributes
        link.removeAttribute('title');
        link.removeAttribute('data-tooltip');
        link.removeAttribute('data-tippy-content');
        link.removeAttribute('data-tooltip-content');
        link.removeAttribute('data-tooltip-trigger');
        
        // Destroy any existing tippy instances
        if (link._tippy) {
          link._tippy.destroy();
        }
        
        // Prevent future tooltip initialization
        link.setAttribute('data-tippy-ignore', 'true');
      });

      // Mobile Menu
      const btn = document.getElementById('mobile-menu-btn');
      const sidebar = document.getElementById('hv-sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      
      if(btn && sidebar && overlay) {
        function toggleMenu() {
          sidebar.classList.toggle('open');
          if (sidebar.classList.contains('open')) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
          } else {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
          }
        }
        btn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
      }

      // Skeleton Loading Toggle
      setTimeout(() => {
        document.querySelectorAll('.skeleton-wrapper').forEach(el => {
          el.style.opacity = '0';
          setTimeout(() => el.style.display = 'none', 200);
        });
        document.querySelectorAll('.real-content').forEach(el => {
          el.classList.remove('hidden');
          // trigger reflow
          void el.offsetWidth;
          el.classList.add('loaded');
        });
      }, 600); // 600ms artificial delay for perceived performance

      // Sidebar Sliding Animation
      const nav = document.querySelector('.hv-sidebar-nav');
      if (nav) {
        const slider = document.createElement('div');
        slider.className = 'hv-sidebar-slider';
        nav.appendChild(slider);
        const links = nav.querySelectorAll('a:not(.hv-sidebar-logout)');
        let activeLink = nav.querySelector('a.active');
        
        function moveSlider(target) {
            if (!target) {
                slider.style.opacity = '0';
                return;
            }
            slider.style.opacity = '1';
            slider.style.transform = `translateY(${target.offsetTop}px)`;
            slider.style.height = `${target.offsetHeight}px`;
        }
        
        if (activeLink) {
            setTimeout(() => {
                slider.style.transition = 'none';
                moveSlider(activeLink);
                setTimeout(() => {
                    slider.style.transition = 'transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), height 0.25s';
                }, 50);
            }, 50);
        }
        
        links.forEach(link => {
            link.addEventListener('mouseenter', () => {
                moveSlider(link);
            });
        });
        nav.addEventListener('mouseleave', () => {
            moveSlider(activeLink);
        });
      }
    });
  </script>

  @stack('modals')
  @stack('scripts')
</body>
</html>

