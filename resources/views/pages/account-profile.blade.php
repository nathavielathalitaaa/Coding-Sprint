@extends('layouts.master')

<style>
  /* Specific styling for profile page */
  .avatar-upload-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #4F6560;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s;
  }
  .avatar-upload-overlay:hover {
    transform: scale(1.1);
    background: #3d504c;
  }

  .ttd-preview-box {
    background: #FFFFFF;
    border: 2px dashed #E5E7EB;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
  }

  .sng-box-danger {
    background: #fee2e2;
    border-left: 4px solid #ef4444;
    border-radius: 12px;
    padding: 12px 16px;
    color: #991b1b;
  }

  .sng-box-info {
    background: #F6F6F6;
    border-left: 4px solid #80BB9B;
    border-radius: 12px;
    padding: 12px 16px;
    color: #4F6560;
  }

  [x-cloak] {
    display: none !important;
  }
</style>

@section('content')
<div class="mb-6">
    <nav class="text-sm" aria-label="breadcrumb">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('home') }}" style="color: #80BB9B; font-weight: 600;" class="hover:opacity-80">Beranda</a></li>
            <li style="color: #6B7280;">/</li>
            <li style="color: #2C2C2A; font-weight: 600;">Profil Saya</li>
        </ol>
    </nav>
</div>

<div class="flex items-center justify-between mb-8">
    <h2 class="hivi-section-title mb-0">Profil Akun {{ $user->id === auth()->id() ? 'Saya' : $user->name }}</h2>
    @if(auth()->user()->hasRole('hr') && isset($user) && $user->id !== auth()->id())
    <a href="{{ route('hr/employee/edit', $user->id) }}" class="hivi-btn-primary">
        <i data-lucide="edit" class="w-4 h-4"></i> Edit Data Karyawan
    </a>
    @endif
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- left column: user info card -->
    <div class="lg:col-span-1">
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40">
            @php
                $fullName = $user->name ?? 'User';
                $parts = explode(' ', trim($fullName));
                $initials = '';
                foreach ($parts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                }
                if (strlen($initials) > 2) {
                    $initials = substr($initials, 0, 2);
                }
            @endphp
            <div class="flex justify-center mb-6">
                <div class="relative">
                    @if($user->avatar)
                        <div class="w-24 h-24 rounded-full border-4 border-white shadow-sm overflow-hidden flex items-center justify-center bg-gray-50">
                            <img id="avatar-preview" src="{{ URL::to('assets/images/user/'.$user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div id="avatar-initials" class="w-24 h-24 rounded-full border-4 border-white shadow-sm bg-gray-100 flex items-center justify-center">
                            <span class="text-3xl font-semibold text-gray-500" style="font-family: 'Playfair Display', serif;">{{ $initials }}</span>
                        </div>
                        <img id="avatar-preview" src="" alt="{{ $user->name }}" class="w-24 h-24 rounded-full border-4 border-white shadow-sm object-cover hidden">
                    @endif
                    
                    <label for="photo-upload" class="avatar-upload-overlay">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </label>
                    <input type="file" id="photo-upload" class="hidden" accept="image/*" onchange="uploadPhoto(event)">
                </div>
            </div>

            <h3 class="text-xl font-semibold text-center mb-2" style="font-family: 'Playfair Display', serif;">
                {{ $user->name }}
            </h3>

            @if($user->role_name)
                <div class="flex justify-center mb-4">
                    <span class="hivi-badge hivi-badge-green">{{ $user->role_name }}</span>
                </div>
            @endif

            @if($user->position)
                <p class="text-center text-sm font-medium mb-4 text-gray-500">
                    {{ $user->position }}
                </p>
            @endif

            <hr class="border-gray-200 my-6">

            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-900 break-all">{{ $user->email }}</p>
                    </div>
                </div>

                @if($user->phone_number)
                    <div class="flex items-start gap-3">
                        <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                        <div>
                            <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400">Telepon</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->phone_number }}</p>
                        </div>
                    </div>
                @endif

                @if($user->location)
                    <div class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gray-400"></i>
                        <div>
                            <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400">Lokasi</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->location }}</p>
                        </div>
                    </div>
                @endif

                @if($user->join_date)
                    <div class="flex items-start gap-3">
                        <i data-lucide="calendar" class="w-5 h-5 text-gray-400"></i>
                        <div>
                            <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400">Tanggal Bergabung</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->join_date->format('d M Y') }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                    <div>
                        <p class="text-[11px] font-semibold tracking-wider uppercase text-gray-400">Status</p>
                        <span class="hivi-badge hivi-badge-green mt-1">
                            {{ ucfirst($user->status ?? 'aktif') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- right column: forms -->
    <div class="lg:col-span-2" x-data="{ showEmailForm: false, showPasswordForm: false, showPinForm: false }">
        
        <!-- informasi dasar -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
            <h4 class="text-lg font-semibold mb-6 flex items-center gap-2" style="font-family: 'Playfair Display', serif;">
                Informasi Akun
            </h4>
            
            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium mb-2 text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="hivi-input"
                        placeholder="Masukkan nama lengkap">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label for="phone_number" class="block text-sm font-medium mb-2 text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="hivi-input"
                        placeholder="Contoh: 08123456789">
                    @error('phone_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium mb-2 text-gray-700">Lokasi</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="hivi-input"
                        placeholder="Masukkan lokasi">
                    @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="hivi-btn-primary w-full">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- tanda tangan digital -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6" x-data="{ showUpload: {{ ($profile->signature_path || $profile->ttd_path) ? 'false' : 'true' }} }">
            <div class="flex items-center gap-2 mb-4">
                <h4 class="text-lg font-semibold m-0" style="font-family: 'Playfair Display', serif;">Tanda Tangan Digital</h4>
            </div>
            <p class="text-sm text-gray-500 mb-6">TTD ini akan digunakan untuk persetujuan dokumen</p>

            @if($profile->signature_path || $profile->ttd_path)
                <div class="mb-6">
                    <div class="ttd-preview-box">
                        <img src="{{ Storage::url($profile->signature_path ?? $profile->ttd_path) }}?v={{ time() }}" alt="Signature"
                             style="max-height: 100px; width: auto; object-fit: contain; display: block; margin: 0 auto;"
                             onerror="this.parentElement.innerHTML='<p class=\'text-xs text-gray-400\'>Gagal memuat preview</p>'">
                    </div>
                </div>

                <div class="mb-4 flex items-center gap-4">
                    <button @click="showUpload = !showUpload" type="button" class="hivi-btn-secondary text-sm px-4 py-2">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> <span x-text="showUpload ? 'Batal' : 'Ganti TTD'"></span>
                    </button>
                    
                    <form action="{{ route('profile.signature.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="hivi-btn-outline text-red-600 border-red-200 hover:bg-red-50 text-sm px-4 py-2">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </button>
                    </form>
                </div>
            @else
                <div class="sng-box-danger mb-6 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Belum ada tanda tangan
                </div>
            @endif

            <div x-show="showUpload" x-transition>
                <div class="sng-box-info mb-5">
                    <div class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold mb-1">Ketentuan File Tanda Tangan</p>
                            <ul class="text-xs space-y-1 list-disc pl-4 text-gray-600">
                                <li>Format yang didukung: <strong>PNG, JPG, JPEG</strong></li>
                                <li>Disarankan: <strong>PNG dengan background TRANSPARAN</strong></li>
                                <li>Ukuran file maksimal: <strong>2MB</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.signature.upload', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <div class="ttd-preview-box cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="file" name="signature" id="signature" accept="image/png, image/jpeg, image/jpg" required
                                   class="hidden" onchange="previewSignature(event)">
                            <label for="signature" class="cursor-pointer block">
                                <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                <p class="text-sm font-medium text-gray-700">Klik untuk memilih file</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG (Max 2MB)</p>
                            </label>
                        </div>
                        @error('signature') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div id="signature-preview-container" class="hidden mb-5">
                        <p class="text-xs font-semibold uppercase tracking-widest mb-2 text-gray-500">Preview:</p>
                        <div class="ttd-preview-box">
                            <img id="signature-preview-img" src="" alt="Preview Signature"
                                 style="max-height: 100px; width: auto; object-fit: contain; display: block; margin: 0 auto;">
                        </div>
                    </div>

                    <button type="submit" class="hivi-btn-primary w-full">
                        <i data-lucide="upload" class="w-4 h-4"></i> Upload TTD
                    </button>
                </form>
            </div>
        </div>

        <!-- data kepegawaian -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
            <h4 class="text-lg font-semibold mb-6" style="font-family: 'Playfair Display', serif;">Data Kepegawaian</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Jabatan Approval</p>
                    <p class="text-sm text-gray-900 font-medium">
                        @if($profile->jabatan)
                            <span class="hivi-badge hivi-badge-blue">{{ ucfirst(str_replace('_',' ',$profile->jabatan)) }}</span>
                        @else — @endif
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Pendidikan Terakhir</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->pendidikan_terakhir ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Tanggal Bergabung</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $profile->tgl_bergabung ? \Carbon\Carbon::parse($profile->tgl_bergabung)->format('d M Y') : ($user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('d M Y') : '—') }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Tanggal Kontrak Akhir</p>
                    <p class="text-sm text-gray-900 font-medium">
                        {{ $profile->tgl_kontrak_akhir ? \Carbon\Carbon::parse($profile->tgl_kontrak_akhir)->format('d M Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Status Pernikahan</p>
                    <p class="text-sm text-gray-900 font-medium">{{ ucfirst(str_replace('_',' ', $profile->status_pernikahan ?? '—')) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Jumlah Anak</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->jumlah_anak ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- data kependudukan -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
            <h4 class="text-lg font-semibold mb-6" style="font-family: 'Playfair Display', serif;">Data Kependudukan</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">NIK</p>
                    <p class="text-sm text-gray-900 font-mono">{{ $profile->nik ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">No. KK</p>
                    <p class="text-sm text-gray-900 font-mono">{{ $profile->no_kk ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">NPWP</p>
                    <p class="text-sm text-gray-900 font-mono">{{ $profile->npwp ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">BPJS Kesehatan</p>
                    <p class="text-sm text-gray-900 font-mono">{{ $profile->bpjs_kesehatan ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">BPJS Ketenagakerjaan</p>
                    <p class="text-sm text-gray-900 font-mono">{{ $profile->bpjs_ketenagakerjaan ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- alamat -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
            <h4 class="text-lg font-semibold mb-6" style="font-family: 'Playfair Display', serif;">Alamat</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Alamat Lengkap</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->alamat ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Kota</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->kota ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Provinsi</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->provinsi ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Kode Pos</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $profile->kode_pos ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- keamanan & pin -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
            <h4 class="text-lg font-semibold mb-6" style="font-family: 'Playfair Display', serif;">Keamanan & PIN</h4>
            
            <!-- pin setup -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-1">
                            <i data-lucide="key-round" class="w-4 h-4 text-gray-400"></i> PIN Approval
                        </h5>
                        @if($profile->pin)
                            <span class="text-xs text-green-600 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> PIN sudah diatur</span>
                        @else
                            <span class="text-xs text-amber-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> Belum ada PIN</span>
                        @endif
                    </div>
                    <button @click="showPinForm = !showPinForm" type="button" class="hivi-btn-outline text-xs">
                        <span x-text="showPinForm ? 'Tutup ▲' : 'Ubah PIN ▼'"></span>
                    </button>
                </div>

                <form action="{{ route('profile.pin') }}" method="POST" x-show="showPinForm" x-transition class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    @csrf
                    @if($profile->pin)
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2 text-gray-700">PIN Lama</label>
                            <input type="password" name="current_pin" inputmode="numeric" maxlength="6" class="hivi-input tracking-widest text-center" placeholder="••••••">
                            @error('current_pin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 text-gray-700">PIN Baru (6 digit angka)</label>
                        <input type="password" name="pin" inputmode="numeric" maxlength="6" required class="hivi-input tracking-widest text-center" placeholder="••••••">
                        @error('pin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Konfirmasi PIN Baru</label>
                        <input type="password" name="pin_confirmation" inputmode="numeric" maxlength="6" required class="hivi-input tracking-widest text-center" placeholder="••••••">
                    </div>
                    <button type="submit" class="hivi-btn-primary w-full">Simpan PIN</button>
                </form>
            </div>

            <!-- email & password -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                    <div>
                        <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-1">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i> Ubah Email
                        </h5>
                    </div>
                    <button @click="showEmailForm = !showEmailForm" type="button" class="hivi-btn-outline text-xs">
                        <span x-text="showEmailForm ? 'Tutup ▲' : 'Ubah Email ▼'"></span>
                    </button>
                </div>

                <form action="{{ route('profile.email') }}" method="POST" x-show="showEmailForm" x-transition class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Email Baru</label>
                        <input type="email" name="email" required class="hivi-input" placeholder="nama@email.com">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Konfirmasi Password Saat Ini</label>
                        <input type="password" name="password" required class="hivi-input" placeholder="••••••••">
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="hivi-btn-primary w-full">Simpan Email</button>
                </form>

                <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                    <div>
                        <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-1">
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i> Ubah Kata Sandi
                        </h5>
                    </div>
                    <button @click="showPasswordForm = !showPasswordForm" type="button" class="hivi-btn-outline text-xs">
                        <span x-text="showPasswordForm ? 'Tutup ▲' : 'Ubah Kata Sandi ▼'"></span>
                    </button>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" x-show="showPasswordForm" x-transition class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Password Saat Ini</label>
                        <input type="password" name="current_password" required class="hivi-input" placeholder="••••••••">
                        @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Password Baru</label>
                        <input type="password" name="new_password" required class="hivi-input" placeholder="Minimal 8 karakter">
                        @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" required class="hivi-input" placeholder="Ulangi password baru">
                    </div>
                    <button type="submit" class="hivi-btn-primary w-full">Simpan Password Baru</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function previewSignature(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                const preview = document.getElementById('avatar-preview');
                const initials = document.getElementById('avatar-initials');
                
                preview.src = result.url;
                preview.classList.remove('hidden');
                if (initials) initials.classList.add('hidden');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Foto profil berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || 'Gagal upload foto'
                });
            }
        } catch (error) {
            console.error('Upload error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Terjadi kesalahan sistem'
            });
        }
    }
</script>
@endsection
