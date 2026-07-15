<aside class="simora-sidebar" role="navigation" aria-label="Sidebar">
  <div class="sidebar-inner">
    <a href="{{ url('/dashboard') }}" class="sidebar-logo">SIMORA</a>

    <nav class="sidebar-menu">
      <a href="{{ url('/dashboard') }}" class="menu-item {{ request()->is('/') || request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a>
      <a href="{{ url('/surat') }}" class="menu-item {{ request()->is('surat*') ? 'active' : '' }}">Ajukan surat</a>
      <a href="{{ url('/persetujuan') }}" class="menu-item {{ request()->is('persetujuan*') ? 'active' : '' }}">Persetujuan</a>
      <a href="{{ url('/daftar-surat') }}" class="menu-item {{ request()->is('daftar-surat*') ? 'active' : '' }}">Daftar surat</a>
    </nav>

    <div class="sidebar-logout">
      <a href="{{ url('/logout') }}" class="menu-item logout">Logout</a>
    </div>
  </div>
</aside>
