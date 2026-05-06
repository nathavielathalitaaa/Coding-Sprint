<!DOCTYPE html>
<html lang="en" class="light scroll-smooth group" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>HR | Sinergi Hotel & Vila - HR Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Minimal Admin & Dashboard Template" name="description">
    <meta content="Sinergi Hotel & Vila" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- app favicon -->
    <link rel="shortcut icon" href="{{ URL::to('assets/images/favicon.ico') }}">
    <!-- layout config js -->
    <script src="{{ URL::to('assets/js/layout.js') }}"></script>
    <!-- sinergi hotel & vila css -->
    <link rel="stylesheet" href="{{ URL::to('assets/css/starcode2.css') }}">
    
    <!-- hivi design system fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
<!-- ============================================
     hivi design system styles
     ============================================ -->
<style>
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
    background: linear-gradient(135deg, #F6F6F6 0%, #E3EFE8 40%, #80BB9B 100%);
    background-attachment: fixed;
    min-height: 100vh;
    color: #1A2B24;
    overflow-x: hidden;
  }

  h1, h2, h3, h4, h5, h6, .serif { 
    font-family: 'Playfair Display', serif; 
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

  /* Search Bar - Pill Shape */
  #topbar-search {
    background: rgba(255, 255, 255, 0.4) !important;
    backdrop-filter: blur(6px) !important;
    border: 1px solid rgba(232, 237, 237, 0.4) !important;
    border-radius: 999px !important;
    padding: 8px 16px 8px 36px !important;
    box-shadow: none !important;
    transition: all 0.2s ease;
    color: #1A2B24 !important;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    width: 280px;
  }
  #topbar-search:focus {
    border-color: #80BB9B !important;
    outline: none !important;
    background: rgba(255, 255, 255, 0.6) !important;
  }
  #topbar-search::placeholder {
    color: #6B7280 !important;
    font-weight: 300;
  }

  /* =============================================
     PAGE CONTAINER (CLEAN LAYOUT)
  ============================================= */
  .page {
    margin-left: 110px;
    padding: 30px;
    padding-top: 86px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .page > * {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Responsive: Tablet */
  @media (max-width: 1024px) {
    .page {
      margin-left: 100px;
      padding: 24px;
      padding-top: 80px;
    }
  }

  /* Responsive: Mobile */
  @media (max-width: 768px) {
    .page {
      margin-left: 0;
      padding: 20px;
      padding-top: 76px;
    }
  }

  /* =============================================
     HIVI DESIGN SYSTEM UTILITIES
  ============================================= */
  .hivi-card {
      background: rgba(246, 246, 246, 0.7);
      backdrop-filter: blur(6px);
      border-radius: 28px;
      padding: 28px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.04);
      border: none;
  }

  .hivi-btn-primary {
      background: #4F6560;
      color: white !important;
      border-radius: 9999px;
      padding: 10px 24px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: background 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
  }
  .hivi-btn-primary:hover { background: #3d504c; }
  
  .hivi-btn-secondary {
      background: transparent;
      color: #4F6560 !important;
      border-radius: 9999px;
      padding: 10px 24px;
      border: 1px solid #4F6560;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
  }
  .hivi-btn-secondary:hover { background: #F6F6F6; }
  
  .hivi-btn-outline {
      background: transparent;
      color: #6B7280 !important;
      border-radius: 9999px;
      padding: 8px 16px;
      border: 1px solid #E5E7EB;
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
  }
  .hivi-btn-outline:hover { background: #F6F6F6; }

  .hivi-badge {
      border-radius: 9999px;
      padding: 4px 12px;
      font-size: 12px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
  }
  .hivi-badge-green  { background: #E8F5EE; color: #2E7D5E; }
  .hivi-badge-amber  { background: #fef3c7; color: #92400e; }
  .hivi-badge-red    { background: #fee2e2; color: #991b1b; }
  .hivi-badge-blue   { background: #dbeafe; color: #1e40af; }
  .hivi-badge-gray   { background: #f3f4f6; color: #374151; }
  
  .hivi-input {
      background: rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(6px);
      border-radius: 9999px;
      padding: 10px 20px;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 300;
      width: 100%;
      outline: none;
      border: 1px solid rgba(229, 231, 235, 0.5);
      transition: border-color 0.2s;
      color: #1A2B24;
  }
  .hivi-input:focus { 
      border-color: #80BB9B; 
      outline: none;
      background: rgba(255, 255, 255, 0.7);
  }
  .hivi-input::placeholder { color: #6B7280; }
  
  .hivi-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .hivi-table thead th {
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 500;
      color: #6B7280;
      padding: 10px 16px;
      background: transparent;
      border-bottom: 1px solid #E8EDED;
      text-align: left;
  }
  .hivi-table tbody tr {
      transition: background 0.15s;
      height: 52px;
  }
  .hivi-table tbody tr:hover { 
      background: rgba(240, 247, 243, 0.6); 
  }
  .hivi-table tbody td {
      padding: 0 16px;
      font-size: 14px;
      color: #1A2B24;
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      border-bottom: 1px solid #F3F4F6;
  }
  .hivi-table tbody tr:last-child td {
      border-bottom: none;
  }

  .hivi-section-title {
      font-family: 'Playfair Display', serif;
      font-size: 20px;
      font-weight: 600;
      color: #1A2B24;
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

</style>

<!-- default template styles (keep for compatibility) -->
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
</style>

</head>
<body class="text-base bg-body-bg text-body font-public dark:text-zink-100 dark:bg-zink-800 group-data-[skin=bordered]:bg-body-bordered group-data-[skin=bordered]:dark:bg-zink-700">

  <!-- floating sidebar (outside all containers) -->
  @include('sidebar.sidebar')

  <!-- page wrapper -->
  <div class="page">
    
    <!-- topbar header -->
    <header id="page-topbar">
      <div class="layout-width">
        <div class="flex items-center px-4 mx-auto bg-topbar border-b-2 border-topbar group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:border-topbar-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:border-topbar-brand shadow-md h-header shadow-slate-200/50 group-data-[navbar=bordered]:rounded-md group-data-[navbar=bordered]:group-[.is-sticky]/topbar:rounded-t-none group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:border-zink-700 dark:shadow-none group-data-[topbar=dark]:group-[.is-sticky]/topbar:dark:shadow-zink-500 group-data-[topbar=dark]:group-[.is-sticky]/topbar:dark:shadow-md group-data-[navbar=bordered]:shadow-none group-data-[layout=horizontal]:group-data-[navbar=bordered]:rounded-b-none group-data-[layout=horizontal]:shadow-none group-data-[layout=horizontal]:dark:group-[.is-sticky]/topbar:shadow-none">
          
          <div class="flex items-center w-full group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl navbar-header group-data-[layout=horizontal]:ltr:xl:pr-3 group-data-[layout=horizontal]:rtl:xl:pl-3">
            
            <!-- logo (horizontal only) -->
            <div class="items-center justify-center hidden px-5 text-center h-header group-data-[layout=horizontal]:md:flex group-data-[layout=horizontal]:ltr::pl-0 group-data-[layout=horizontal]:rtl:pr-0">
              <a href="{{ route('home') }}">
                <img src="{{ URL::to('assets/images/logo-dark.png') }}" alt="" class="h-6 mx-auto">
              </a>
            </div>

            <!-- search bar -->
            <div class="relative hidden ltr:ml-3 rtl:mr-3 lg:block group-data-[layout=horizontal]:hidden group-data-[layout=horizontal]:lg:block">
              <input type="text" id="topbar-search" class="py-2 pr-4 text-sm text-topbar-item bg-topbar border border-topbar-border rounded pl-8 placeholder:text-slate-400 form-control focus-visible:outline-0 min-w-[300px] focus:border-blue-400 group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:border-topbar-border-dark group-data-[topbar=dark]:placeholder:text-slate-500 group-data-[topbar=dark]:text-topbar-item-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:border-topbar-border-brand group-data-[topbar=brand]:placeholder:text-blue-300 group-data-[topbar=brand]:text-topbar-item-brand group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:border-zink-500 group-data-[topbar=dark]:dark:text-zink-100" placeholder="Cari karyawan, menu, departemen" autocomplete="off">
              <i data-lucide="search" class="inline-block size-4 absolute left-2.5 top-2.5 text-topbar-item fill-slate-100 group-data-[topbar=dark]:fill-topbar-item-bg-hover-dark group-data-[topbar=dark]:text-topbar-item-dark group-data-[topbar=brand]:fill-topbar-item-bg-hover-brand group-data-[topbar=brand]:text-topbar-item-brand group-data-[topbar=dark]:dark:text-zink-200 group-data-[topbar=dark]:dark:fill-zink-600"></i>
              <div id="search-results" style="display:none; position:absolute; top:42px; left:0; width:340px; background:#fff; border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.12); z-index:9999; max-height:320px; overflow-y:auto;" class="dark:bg-zink-700"></div>
            </div>

            <!-- right side: icons + notifications + profile -->
            <div class="flex gap-3 ms-auto">
              
              <!-- theme toggle -->
              <div class="relative flex items-center h-header">
                <button type="button" class="inline-flex relative justify-center items-center p-0 text-topbar-item transition-all w-[37.5px] h-[37.5px] duration-200 ease-linear bg-topbar rounded-md btn hover:bg-topbar-item-bg-hover hover:text-topbar-item-hover group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:hover:bg-topbar-item-bg-hover-dark group-data-[topbar=dark]:hover:text-topbar-item-hover-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:hover:bg-topbar-item-bg-hover-brand group-data-[topbar=brand]:hover:text-topbar-item-hover-brand group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:hover:bg-zink-600 group-data-[topbar=brand]:text-topbar-item-brand group-data-[topbar=dark]:dark:hover:text-zink-50 group-data-[topbar=dark]:dark:text-zink-200 group-data-[topbar=dark]:text-topbar-item-dark" id="light-dark-mode">
                  <i data-lucide="sun" class="inline-block w-5 h-5 stroke-1 fill-slate-100 group-data-[topbar=dark]:fill-topbar-item-bg-hover-dark group-data-[topbar=brand]:fill-topbar-item-bg-hover-brand"></i>
                </button>
              </div>

              <!-- notifications -->
              @php
                $myNotifs = [];
                $unreadCount = 0;
                if (auth()->check()) {
                    $myNotifs = \App\Models\Notification::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }
              @endphp
              <div class="relative flex items-center dropdown h-header">
                <button type="button" class="inline-flex justify-center relative items-center p-0 text-topbar-item transition-all w-[37.5px] h-[37.5px] duration-200 ease-linear bg-topbar rounded-md dropdown-toggle btn hover:bg-topbar-item-bg-hover hover:text-topbar-item-hover group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:hover:bg-topbar-item-bg-hover-dark group-data-[topbar=dark]:hover:text-topbar-item-hover-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:hover:bg-topbar-item-bg-hover-brand group-data-[topbar=brand]:hover:text-topbar-item-hover-brand group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:hover:bg-zink-600 group-data-[topbar=brand]:text-topbar-item-brand group-data-[topbar=dark]:dark:hover:text-zink-50 group-data-[topbar=dark]:dark:text-zink-200 group-data-[topbar=dark]:text-topbar-item-dark" id="notificationDropdown" data-bs-toggle="dropdown">
                  <i data-lucide="bell-ring" class="inline-block w-5 h-5 stroke-1 fill-slate-100 group-data-[topbar=dark]:fill-topbar-item-bg-hover-dark group-data-[topbar=brand]:fill-topbar-item-bg-hover-brand"></i>
                  @if($unreadCount > 0)
                  <span class="absolute top-0 right-0 flex w-1.5 h-1.5">
                    <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-sky-400"></span>
                    <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                  </span>
                  @endif
                </button>
                <div class="absolute z-50 hidden ltr:text-left rtl:text-right bg-white rounded-md shadow-md !top-4 dropdown-menu min-w-[20rem] lg:min-w-[26rem] dark:bg-zink-600" aria-labelledby="notificationDropdown">
                  <div class="p-4 border-b border-slate-100 dark:border-zink-500">
                    <h6 class="mb-0 text-16 flex items-center gap-2">Notifikasi <span class="inline-flex items-center justify-center w-5 h-5 text-[11px] font-medium border rounded-full text-white bg-orange-500 border-orange-500">{{ $unreadCount }}</span></h6>
                  </div>
                  <div data-simplebar="" class="max-h-[350px]">
                    <div class="flex flex-col" id="notification-list">
                      @forelse($myNotifs as $notif)
                      <a href="{{ $notif->url ?? '#' }}" class="flex gap-3 p-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 dark:hover:bg-zink-500 {{ !$notif->is_read ? 'bg-sky-50/30' : '' }}">
                        <div class="flex items-center justify-center w-10 h-10 rounded-md shrink-0" style="background:#e0f2fe;">
                          <i data-lucide="{{ str_contains($notif->title, 'Tolak') ? 'alert-circle' : 'file-check' }}" class="w-5 h-5" style="color:#0284c7;"></i>
                        </div>
                        <div class="grow">
                          <h6 class="mb-1 text-sm font-bold {{ !$notif->is_read ? 'text-custom-500' : '' }}">{{ $notif->title }}</h6>
                          <p class="mb-1 text-xs text-slate-500 dark:text-zink-300 leading-relaxed">
                            {{ $notif->message }}
                          </p>
                          <p class="mb-0 text-[10px] text-slate-400 dark:text-zink-400">
                            <i data-lucide="clock" class="inline-block w-3 h-3 mr-1"></i>
                            {{ $notif->created_at->diffForHumans() }}
                          </p>
                        </div>
                        @if(!$notif->is_read)
                        <div class="flex items-center self-start gap-2 text-xs text-slate-500 shrink-0 dark:text-zink-300">
                          <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                        </div>
                        @endif
                      </a>
                      @empty
                      <div class="p-6 text-center">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                        <p class="text-sm text-slate-500 dark:text-zink-300">Tidak ada notifikasi baru</p>
                      </div>
                      @endforelse
                    </div>
                  </div>
                  <div class="flex items-center gap-2 p-4 border-t border-slate-200 dark:border-zink-500">
                    <div class="grow">
                      <p class="text-xs text-slate-400">Menampilkan 10 notifikasi terbaru</p>
                    </div>
                    <div class="shrink-0">
                      <a href="{{ route('surat.index') }}" type="button" class="px-3 py-1.5 text-xs text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:bg-custom-600 focus:bg-custom-600 active:bg-custom-600">
                        Lihat Semua <i data-lucide="move-right" class="inline-block w-3.5 h-3.5 ml-1"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- profile dropdown -->
              <div class="relative flex items-center dropdown h-header">
                <button type="button" class="inline-block p-0 transition-all duration-200 ease-linear bg-topbar rounded-full text-topbar-item dropdown-toggle btn hover:bg-topbar-item-bg-hover hover:text-topbar-item-hover group-data-[topbar=dark]:text-topbar-item-dark group-data-[topbar=dark]:bg-topbar-dark group-data-[topbar=dark]:hover:bg-topbar-item-bg-hover-dark group-data-[topbar=dark]:hover:text-topbar-item-hover-dark group-data-[topbar=brand]:bg-topbar-brand group-data-[topbar=brand]:hover:bg-topbar-item-bg-hover-brand group-data-[topbar=brand]:hover:text-topbar-item-hover-brand group-data-[topbar=dark]:dark:bg-zink-700 group-data-[topbar=dark]:dark:hover:bg-zink-600 group-data-[topbar=brand]:text-topbar-item-brand group-data-[topbar=dark]:dark:hover:text-zink-50 group-data-[topbar=dark]:dark:text-zink-200" id="dropdownMenuButton" data-bs-toggle="dropdown">
                  <div class="bg-pink-100 rounded-full">
                    @if(auth()->user()->avatar)
                      <img src="{{ URL::to('assets/images/user/' . auth()->user()->avatar) }}" alt="" class="w-[37.5px] h-[37.5px] rounded-full object-cover">
                    @else  
                      <div class="flex items-center justify-center font-medium rounded-full size-10 shrink-0 bg-slate-200 text-slate-800 dark:text-zink-50 dark:bg-zink-600">
                        @php
                          $fullName = auth()->user()->name;
                          $parts = explode(' ', $fullName);
                          $initials = '';
                          foreach ($parts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                          }
                        @endphp
                        {{ $initials }}
                      </div>
                    @endif
                  </div>
                </button>
                <div class="absolute z-50 hidden p-4 ltr:text-left rtl:text-right bg-white rounded-md shadow-md !top-4 dropdown-menu min-w-[14rem] dark:bg-zink-600" aria-labelledby="dropdownMenuButton">
                  <a href="#!" class="flex gap-3 mb-3">
                    <div class="relative inline-block shrink-0">
                      <div class="rounded bg-slate-100 dark:bg-zink-500">
                        @if(auth()->user()->avatar)
                          <img src="{{ URL::to('assets/images/user/' . auth()->user()->avatar) }}" alt="" class="w-[37.5px] h-[37.5px] rounded-full object-cover">
                        @else  
                          <div class="flex items-center justify-center font-medium rounded-full size-10 shrink-0 bg-slate-200 text-slate-800 dark:text-zink-50 dark:bg-zink-600">
                            @php
                              $fullName = auth()->user()->name;
                              $parts = explode(' ', $fullName);
                              $initials = '';
                              foreach ($parts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                              }
                            @endphp
                            {{ $initials }}
                          </div>
                        @endif
                      </div>
                      <span class="-top-1 ltr:-right-1 rtl:-left-1 absolute w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full dark:border-zink-600"></span>
                    </div>
                    <div>
                      <h6 class="mb-1 text-15">{{ Session::get('name') }}</h6>
                      <p class="text-slate-500 dark:text-zink-300">{{ Session::get('position') }}</p>
                    </div>
                  </a>
                  <ul>
                    <li>
                      <a class="block ltr:pr-4 rtl:pl-4 py-1.5 text-base font-medium transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:text-custom-500 focus:text-custom-500 dark:text-zink-200 dark:hover:text-custom-500 dark:focus:text-custom-500" href="{{ url('page/account/'.Session::get('user_id')) }}">
                        <i data-lucide="user-2" class="inline-block size-4 ltr:mr-2 rtl:ml-2"></i> profil saya
                      </a>
                    </li>
                    <li>
                      <a class="block ltr:pr-4 rtl:pl-4 py-1.5 text-base font-medium transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:text-custom-500 focus:text-custom-500 dark:text-zink-200 dark:hover:text-custom-500 dark:focus:text-custom-500" href="apps-mailbox.html">
                        <i data-lucide="mail" class="inline-block size-4 ltr:mr-2 rtl:ml-2"></i> kotak masuk 
                        <span class="inline-flex items-center justify-center w-5 h-5 ltr:ml-2 rtl:mr-2 text-[11px] font-medium border rounded-full text-white bg-red-500 border-red-500">15</span>
                      </a>
                    </li>
                    <li class="pt-2 mt-2 border-t border-slate-200 dark:border-zink-500">
                      <a class="block ltr:pr-4 rtl:pl-4 py-1.5 text-base font-medium transition-all duration-200 ease-linear text-slate-600 dropdown-item hover:text-custom-500 focus:text-custom-500 dark:text-zink-200 dark:hover:text-custom-500 dark:focus:text-custom-500" href="{{ route('logout') }}">
                        <i data-lucide="log-out" class="inline-block size-4 ltr:mr-2 rtl:ml-2"></i> keluar
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- page content -->
    @yield('content')

  </div>
  <!-- end page wrapper -->

  <!-- scripts -->
  <script src="{{ URL::to('assets/js/lucide.js') }}"></script>
  <script src="{{ URL::to('assets/js/layout.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- initialize icons -->
  <script>
    lucide.createIcons();
  </script>

</body>
</html>
