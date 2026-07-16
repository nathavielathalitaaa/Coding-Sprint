@extends('layouts.master')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap');

  .profile-page * { font-family: 'Poppins', sans-serif; }

  .bento-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr 1fr;
    gap: 24px;
    align-items: start;
  }
  @media (max-width: 1023px) {
    .bento-grid { 
      grid-template-columns: 1fr; 
    }
    /* Move Center Column (Photo) to top on mobile */
    .bento-grid > div:nth-child(1) { order: 2; } /* Left Column */
    .bento-grid > div:nth-child(2) { order: 1; } /* Center Column (Photo) */
    .bento-grid > div:nth-child(3) { order: 3; } /* Right Column */
  }

  .bento-card {
    background: rgba(255,255,255,0.82);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.45);
    border-radius: 20px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    padding: 24px;
  }
  @media (max-width: 639px) {
    .bento-card {
      padding: 16px;
      border-radius: 16px;
    }
  }

  .bento-card-placeholder {
    background: rgba(255,255,255,0.4);
    border: 2px dashed rgba(230,33,41,0.25);
    border-radius: 20px;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 16px;
  }

  .field-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #9CA3AF;
    margin-bottom: 3px;
  }

  .field-value {
    font-size: 13.5px;
    font-weight: 500;
    color: #1F2937;
  }

  .soft-input {
    width: 100%;
    background: #F4F5F7;
    border: 1px solid #E9EAEC;
    border-radius: 10px;
    padding: 9px 13px;
    font-size: 13.5px;
    color: #1F2937;
    outline: none;
    transition: border-color 0.2s;
  }
  .soft-input:focus { border-color: var(--color-primary); }

  .edit-pill {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 14px;
    border-radius: 999px;
    background: var(--color-bg-light);
    color: var(--color-primary);
    border: 1px solid rgba(230,33,41,0.2);
    cursor: pointer;
    transition: all 0.2s;
  }
  .edit-pill:hover { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }

  .profile-hero {
    position: relative;
    width: 100%;
    height: 320px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
  }

  .profile-hero img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  @media (max-width: 639px) {
    .profile-hero {
      height: 280px;
    }
  }

  .profile-hero-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-bg-light) 0%, #FECACA 100%);
    font-family: 'Poppins', sans-serif;
    font-size: 5rem;
    color: var(--color-primary);
    font-weight: 700;
  }

  .profile-hero-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.72) 0%, transparent 100%);
    padding: 32px 24px 24px;
  }

  .profile-hero-upload {
    position: absolute;
    top: 14px; left: 14px;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.88);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.2s;
    z-index: 10;
  }
  .profile-hero-upload:hover { background: #fff; transform: scale(1.08); }

  .profile-hero-delete {
    position: absolute;
    top: 14px; left: 58px;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.88);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transition: all 0.2s;
    z-index: 10;
    color: #EF4444;
  }
  .profile-hero-delete:hover { background: #fff; transform: scale(1.08); }

  .ttd-preview-box {
    background: #fff;
    border: 2px dashed #E5E7EB;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
  }

  .sng-box-danger {
    background: #fee2e2;
    border-left: 4px solid #ef4444;
    border-radius: 10px;
    padding: 10px 14px;
    color: #991b1b;
    font-size: 13px;
  }

  .sng-box-info {
    background: #F6F6F6;
    border-left: 4px solid var(--color-primary);
    border-radius: 10px;
    padding: 10px 14px;
    color: var(--color-text);
    font-size: 13px;
  }

  [x-cloak] { display: none !important; }

  .pw-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }
  .pw-wrap .soft-input {
    padding-right: 38px;
  }
  .pw-eye {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    color: #9CA3AF;
    padding: 0;
    display: flex;
    align-items: center;
    transition: color 0.15s;
  }
  .pw-eye:hover { color: var(--color-text); }
</style>

@section('content')
<div class="profile-page">



  {{-- page header --}}
  <div class="flex items-center justify-between mb-6">
    <h2 style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:700;color:#111111;">
      Profil Akun {{ $user->id === auth()->id() ? 'Saya' : $user->name }}
    </h2>
    @if(auth()->user()->hasRole('hr') && isset($user) && $user->id !== auth()->id())
    <a href="{{ route('hr/employee/edit', $user->id) }}" class="hivi-btn-primary">
      <i data-lucide="edit" class="w-4 h-4"></i> Edit Employee Data
    </a>
    @endif
  </div>

  <div class="skeleton-wrapper w-full">
    <div class="bento-grid">
      {{-- LEFT SKELETON --}}
      <div class="flex flex-col gap-6">
        <div class="bento-card">
          <div class="skeleton h-6 w-1/2 mb-4"></div>
          <div class="space-y-4 mt-2">
            <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-2/3"></div></div>
            <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-1/2"></div></div>
          </div>
        </div>
        @unless($user->hasRole('staff'))
        <div class="bento-card">
          <div class="skeleton h-6 w-1/2 mb-4"></div>
          <div class="skeleton h-32 w-full rounded-2xl"></div>
        </div>
        @endunless
      </div>
      {{-- CENTER SKELETON --}}
      <div class="flex flex-col gap-6">
        <div class="bento-card !p-0 overflow-hidden">
          <div class="skeleton w-full h-[280px] sm:h-[320px]"></div>
        </div>
        @unless($user->hasRole('staff'))
        <div class="bento-card">
          <div class="skeleton h-6 w-1/3 mb-4"></div>
          <div class="space-y-4">
             <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-full"></div></div>
             <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-3/4"></div></div>
          </div>
        </div>
        @else
        <div class="bento-card">
          <div class="skeleton h-6 w-1/3 mb-4"></div>
          <div class="space-y-4">
             <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-full"></div></div>
          </div>
        </div>
        @endunless
      </div>
      {{-- RIGHT SKELETON --}}
      <div class="flex flex-col gap-6">
        <div class="bento-card">
          <div class="skeleton h-6 w-1/2 mb-4"></div>
          <div class="space-y-4">
            <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-2/3"></div></div>
            <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-2/3"></div></div>
            <div><div class="skeleton h-3 w-1/4 mb-2"></div><div class="skeleton h-4 w-1/2"></div></div>
          </div>
        </div>
        <div class="bento-card">
          <div class="skeleton h-6 w-1/2 mb-4"></div>
          <div class="space-y-4">
            <div class="skeleton h-10 w-full rounded-2xl"></div>
            <div class="skeleton h-10 w-full rounded-2xl"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- REAL CONTENT --}}
  <div class="real-content hidden w-full">
    <div class="bento-grid">

    {{-- ===== LEFT COLUMN ===== --}}
    <div class="flex flex-col gap-6">

      {{-- Account Information Card --}}
      <div class="bento-card">
        <div class="flex items-center justify-between mb-4">
          <span class="card-title" style="margin-bottom:0;">Informasi Akun</span>
          <button type="button" class="edit-pill" onclick="document.getElementById('account-form-wrap').classList.toggle('hidden')">Ubah</button>
        </div>

        {{-- read-only view --}}
        <div id="account-read-view" class="space-y-4">
          <div>
            <p class="field-label">Nama Lengkap</p>
            <p class="field-value">{{ $user->name }}</p>
          </div>
          <div>
            <p class="field-label">Nomor Telepon</p>
            <p class="field-value">{{ $user->phone_number ?? '—' }}</p>
          </div>
        </div>

        {{-- editable form (toggle) --}}
        <div id="account-form-wrap" class="hidden mt-4">
          <form action="{{ route('profile.update', $user->id) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="field-label block mb-1">Nama Lengkap</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="soft-input" placeholder="Nama lengkap">
              @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
              <label class="field-label block mb-1">Nomor Telepon</label>
              <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="soft-input" placeholder="08xxxxxxxxxx">
              @error('phone_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
              <label class="field-label block mb-1">Lokasi</label>
              <input type="text" name="location" value="{{ old('location', $user->location) }}" class="soft-input" placeholder="Lokasi">
              @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="hivi-btn-primary w-full">
              <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
            </button>
          </form>
        </div>
      </div>

      {{-- Digital Signature Card --}}
      @unless($user->hasRole('staff'))      <div class="bento-card" x-data="{ showUpload: {{ ($user->ttd_path) ? 'false' : 'true' }} }">
        <p class="card-title">Tanda Tangan Digital</p>
 
        @if($user->ttd_path)
          <div class="ttd-preview-box mb-4">
            <img src="{{ route('ttd.preview.user', $user->id) }}?v={{ time() }}"
                 alt="Signature"
                 style="max-height:90px;width:auto;object-fit:contain;display:block;margin:0 auto;"
                 onerror="this.parentElement.innerHTML='<p class=\'text-xs text-gray-400\'>Preview failed</p>'">
          </div>
          <div class="flex items-center gap-3 mb-3">
            <button @click="showUpload = !showUpload" type="button" class="hivi-btn-secondary text-xs px-3 py-1.5">
              <i data-lucide="refresh-cw" class="w-3 h-3"></i>
              <span x-text="showUpload ? 'Batal' : 'Ganti'"></span>
            </button>
            <form action="{{ route('profile.signature.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tanda tangan ini?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:#EF4444;display:flex;align-items:center;gap:4px;font-size:12px;">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
            </form>
          </div>
        @else
          <div class="sng-box-danger mb-4 flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4"></i> Belum ada tanda tangan
          </div>
        @endif
        <div x-show="showUpload" x-transition>
          <form action="{{ route('profile.signature.upload', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="ttd-preview-box cursor-pointer hover:bg-gray-50 transition-colors mb-3">
              <input type="file" name="signature" id="signature" accept="image/png,image/jpeg,image/jpg" required class="hidden" onchange="previewSignature(event)">
              <label for="signature" class="cursor-pointer block">
                <i data-lucide="upload-cloud" class="w-7 h-7 mx-auto mb-1 text-gray-400"></i>
                <p class="text-xs font-medium text-gray-600">Klik untuk mengunggah</p>
              </label>
            </div>
            <div id="signature-preview-container" class="hidden mb-3">
              <div class="ttd-preview-box">
                <img id="signature-preview-img" src="" alt="Preview" style="max-height:80px;width:auto;object-fit:contain;display:block;margin:0 auto;">
              </div>
            </div>
            <button type="submit" class="hivi-btn-primary w-full text-xs">
              <i data-lucide="upload" class="w-3.5 h-3.5"></i> Unggah Tanda Tangan
            </button>
          </form>
        </div>
 
        <p class="text-center mt-3" style="font-size:10.5px;color:#9CA3AF;">Format yang didukung: PNG, JPG. Ukuran maks: 2MB</p>
      </div>
      @endunless

    </div>
    {{-- END LEFT --}}

    {{-- ===== CENTER COLUMN ===== --}}
    <div class="flex flex-col gap-6">

      {{-- Profile Hero Card --}}
      <div class="profile-hero">
        @php
          $fullName = $user->name ?? 'User';
          $parts = explode(' ', trim($fullName));
          $initials = '';
          foreach ($parts as $part) { if (!empty($part)) $initials .= strtoupper(substr($part, 0, 1)); }
          if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
        @endphp

        @if($user->avatar)
          <img id="avatar-preview" src="{{ URL::to('assets/images/user/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;display:block;position:absolute;top:0;left:0;">
        @else
          <div id="avatar-initials" class="profile-hero-initials">{{ $initials }}</div>
          <img id="avatar-preview" src="" alt="{{ $user->name }}" style="display:none;width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;">
        @endif

        {{-- upload button --}}
        <label for="photo-upload" class="profile-hero-upload" title="Ubah foto">
          <i data-lucide="camera" style="width:16px;height:16px;color:var(--color-text);"></i>
        </label>
        <input type="file" id="photo-upload" class="hidden" accept="image/*" onchange="uploadPhoto(event)">

        {{-- delete photo button --}}
        <button type="button" id="delete-photo-btn" onclick="deletePhoto()" class="profile-hero-delete" title="Hapus foto" style="{{ $user->avatar ? '' : 'display:none;' }}">
          <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
        </button>

        {{-- overlay --}}
        <div class="profile-hero-overlay">
          <p style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:700;color:#fff;line-height:1.2;margin-bottom:4px;">
            {{ $user->name }}
          </p>
          @if($user->role_name)
            <p style="font-size:13px;color:rgba(255,255,255,0.78);font-weight:400;">
              {{ $user->role_name }}@if($user->position) · {{ $user->position }}@endif
            </p>
          @endif
        </div>
      </div>

      {{-- Security & PIN Card (non-staff: full card with PIN, Email, Password) --}}
      @unless($user->hasRole('staff'))
      <div class="bento-card" x-data="{ showEmailForm: false, showPasswordForm: false, showPinForm: false }">
        <p class="card-title">Keamanan & PIN</p>

        {{-- PIN --}}
        <div class="mb-5">
          <div class="flex items-center justify-between mb-3">
            <div>
              <p class="field-label" style="margin-bottom:2px;">PIN Persetujuan</p>
              @if($user->pin)
                <span class="text-xs text-green-600 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> PIN telah disetel</span>
              @else
                <span class="text-xs text-amber-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> Belum ada PIN</span>
              @endif
            </div>
            <button @click="showPinForm = !showPinForm" type="button" class="edit-pill">
              <span x-text="showPinForm ? 'Tutup' : 'Atur PIN'"></span>
            </button>
          </div>
          <form action="{{ route('profile.pin') }}" method="POST" x-show="showPinForm" x-transition class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
            @csrf
            @if($user->pin)
              <div class="mb-3">
                <label class="field-label block mb-1">PIN Saat Ini</label>
                <input type="password" name="current_pin" inputmode="numeric" maxlength="6" class="soft-input tracking-widest text-center" placeholder="••••••">
                @error('current_pin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
              </div>
            @endif
            <div class="mb-3">
              <label class="field-label block mb-1">PIN Baru (6 digit)</label>
              <input type="password" name="pin" inputmode="numeric" maxlength="6" required class="soft-input tracking-widest text-center" placeholder="••••••">
              @error('pin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
              <label class="field-label block mb-1">Konfirmasi PIN</label>
              <input type="password" name="pin_confirmation" inputmode="numeric" maxlength="6" required class="soft-input tracking-widest text-center" placeholder="••••••">
            </div>
            <button type="submit" class="hivi-btn-primary w-full">Simpan PIN</button>
          </form>
        </div>

        {{-- Email --}}
        <div class="border-t border-gray-100 pt-4 mb-4">
          <div class="flex items-center justify-between mb-3">
            <p class="field-label" style="margin-bottom:0;">Ubah Email</p>
            <button @click="showEmailForm = !showEmailForm" type="button" class="edit-pill">
              <span x-text="showEmailForm ? 'Tutup' : 'Ubah'"></span>
            </button>
          </div>
          <form action="{{ route('profile.email') }}" method="POST" x-show="showEmailForm" x-transition class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
            @csrf
            <div class="mb-3">
              <label class="field-label block mb-1">Email Baru</label>
              <input type="email" name="email" required class="soft-input" placeholder="name@email.com">
              @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
              <label class="field-label block mb-1">Kata Sandi Saat Ini</label>
              <input type="password" name="password" required class="soft-input" placeholder="••••••••">
              @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="hivi-btn-primary w-full">Simpan Email</button>
          </form>
        </div>

        {{-- Password --}}
        <div class="border-t border-gray-100 pt-4">
          <div class="flex items-center justify-between mb-3">
            <p class="field-label" style="margin-bottom:0;">Ubah Kata Sandi</p>
            <button @click="showPasswordForm = !showPasswordForm" type="button" class="edit-pill">
              <span x-text="showPasswordForm ? 'Tutup' : 'Ubah'"></span>
            </button>
          </div>
          <form action="{{ route('profile.password') }}" method="POST" x-show="showPasswordForm" x-transition class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
            @csrf
            <div class="mb-3">
              <label class="field-label block mb-1">Kata Sandi Saat Ini</label>
              <div class="pw-wrap">
                <input type="password" name="current_password" required class="soft-input" placeholder="••••••••">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
              @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
              <label class="field-label block mb-1">Kata Sandi Baru</label>
              <div class="pw-wrap">
                <input type="password" name="new_password" required class="soft-input" placeholder="Min. 8 karakter">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
              @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
              <label class="field-label block mb-1">Konfirmasi Kata Sandi Baru</label>
              <div class="pw-wrap">
                <input type="password" name="new_password_confirmation" required class="soft-input" placeholder="Ulangi kata sandi baru">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
            </div>
            <button type="submit" class="hivi-btn-primary w-full">Simpan Kata Sandi</button>
          </form>
        </div>
      </div>
      @endunless

      {{-- Change Password Card (staff only) --}}
      @if($user->hasRole('staff'))
      <div class="bento-card" x-data="{ showPasswordForm: {{ $errors->has('current_password') || $errors->has('new_password') ? 'true' : ($user->must_change_password ? 'true' : 'false') }} }">
        <p class="card-title">Keamanan</p>

        @if($user->must_change_password)
          <div class="sng-box-danger mb-4 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Anda diwajibkan mengganti password sebelum melanjutkan.</span>
          </div>
        @endif

        {{-- Password --}}
        <div>
          <div class="flex items-center justify-between mb-3">
            <p class="field-label" style="margin-bottom:0;">Ubah Kata Sandi</p>
            <button @click="showPasswordForm = !showPasswordForm" type="button" class="edit-pill">
              <span x-text="showPasswordForm ? 'Tutup' : 'Ubah'"></span>
            </button>
          </div>
          <form action="{{ route('profile.password') }}" method="POST" x-show="showPasswordForm" x-transition class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
            @csrf
            <div class="mb-3">
              <label class="field-label block mb-1">Kata Sandi Saat Ini</label>
              <div class="pw-wrap">
                <input type="password" name="current_password" required class="soft-input" placeholder="••••••••">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
              @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
              <label class="field-label block mb-1">Kata Sandi Baru</label>
              <div class="pw-wrap">
                <input type="password" name="new_password" required class="soft-input" placeholder="Min. 8 karakter">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
              @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
              <label class="field-label block mb-1">Konfirmasi Kata Sandi Baru</label>
              <div class="pw-wrap">
                <input type="password" name="new_password_confirmation" required class="soft-input" placeholder="Ulangi kata sandi baru">
                <button type="button" class="pw-eye" onclick="togglePw(this)" tabindex="-1"><i data-lucide="eye" class="w-4 h-4"></i></button>
              </div>
            </div>
            <button type="submit" class="hivi-btn-primary w-full">Simpan Kata Sandi</button>
          </form>
        </div>
      </div>
      @endif

    </div>
    {{-- END CENTER --}}

    {{-- ===== RIGHT COLUMN ===== --}}
    <div class="flex flex-col gap-6">

      {{-- Organization Memberships Card --}}
      <div class="bento-card">
        <p class="card-title">Keanggotaan Organisasi</p>
        @if($user->organisasiMembers && $user->organisasiMembers->count() > 0)
          <div class="space-y-4">
            @foreach($user->organisasiMembers as $member)
              <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                <div>
                  <p class="font-bold text-gray-800">{{ $member->organisasi->nama ?? 'Unknown' }}</p>
                  <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">{{ str_replace('_', ' ', $member->jabatan) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[var(--color-primary)]/20 flex items-center justify-center">
                  <i data-lucide="users" class="w-5 h-5 text-[var(--color-text)]"></i>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-6 text-gray-500 text-sm">
            Anda belum menjadi anggota organisasi apa pun.
          </div>
        @endif
      </div>
    </div>
    {{-- END RIGHT --}}

  </div>
  {{-- END BENTO GRID --}}

  </div>
  {{-- END REAL CONTENT --}}

</div>

<script>
  function previewSignature(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
      alert('Max file size is 2MB!');
      event.target.value = '';
      document.getElementById('signature-preview-container').classList.add('hidden');
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('signature-preview-img').src = e.target.result;
      document.getElementById('signature-preview-container').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
  }

  async function uploadPhoto(event) {
    const file = event.target.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', '{{ csrf_token() }}');
    try {
      const response = await fetch('{{ route("profile.photo") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const result = await response.json();
      if (result.success) {
        const preview = document.getElementById('avatar-preview');
        const initials = document.getElementById('avatar-initials');
        const deleteBtn = document.getElementById('delete-photo-btn');
        preview.src = result.url;
        preview.style.display = 'block';
        if (initials) initials.style.display = 'none';
        if (deleteBtn) deleteBtn.style.display = 'flex';
        Swal.fire({ icon: 'success', title: 'Success', text: 'Profile photo updated', timer: 1500, showConfirmButton: false });
      } else {
        Swal.fire({ icon: 'error', title: 'Failed', text: result.message || 'Upload failed' });
      }
    } catch (error) {
      console.error('Upload error:', error);
      Swal.fire({ icon: 'error', title: 'Error', text: 'System error occurred' });
    }
  }

  function togglePw(btn) {
    const input = btn.closest('.pw-wrap').querySelector('input');
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      input.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
  }

  async function deletePhoto() {
    if (!confirm('Are you sure you want to delete your profile photo?')) return;
    try {
      const response = await fetch('{{ route("profile.photo.delete") }}', {
        method: 'DELETE',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });
      const result = await response.json();
      if (result.success) {
        const preview = document.getElementById('avatar-preview');
        const initials = document.getElementById('avatar-initials');
        const deleteBtn = document.getElementById('delete-photo-btn');
        if (preview) {
            preview.src = '';
            preview.style.display = 'none';
        }
        if (initials) initials.style.display = 'flex';
        if (deleteBtn) deleteBtn.style.display = 'none';
        Swal.fire({ icon: 'success', title: 'Success', text: 'Profile photo deleted', timer: 1500, showConfirmButton: false });
      }
    } catch (error) {
      console.error('Delete error:', error);
      Swal.fire({ icon: 'error', title: 'Error', text: 'System error occurred' });
    }
  }
</script>
</div>{{-- /profile-page --}}
@endsection
