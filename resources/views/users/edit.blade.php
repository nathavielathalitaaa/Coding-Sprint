@extends('layouts.master')
@section('content')
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-sans font-semibold text-[var(--color-text)]">Ubah Data Anggota/Staf</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi lengkap untuk {{ $user->name }}</p>
            </div>
            <a href="{{ route('hr/employee/list') }}" class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white/50 hover:bg-white text-sm font-medium text-gray-600 transition shadow-sm backdrop-blur">
                <i data-lucide="arrow-left" class="w-4 h-4 inline-block mr-1"></i> Kembali
            </a>
        </div>

        <div style="background:rgba(255,255,255,0.8);backdrop-filter:blur(24px);border-radius:24px;padding:32px;border:1px solid rgba(255,255,255,0.4);box-shadow:0 1px 2px 0 rgba(0,0,0,0.05);">
            <form action="{{ route('hr/employee/update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="old_photo" value="{{ $user->avatar }}">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- left column: photo & basic account -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40 text-center">
                            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-text)]/60 mb-4">Foto Profil</p>
                            <div class="relative inline-block">
                                <img id="edit-photo-preview" src="{{ $user->avatar ? URL::to('assets/images/user/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=FFF1F2&color=111111&size=200' }}"
                                     class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-sm transition hover:scale-105 duration-300">
                                <label for="photo-input" class="absolute bottom-0 right-0 w-10 h-10 bg-[var(--color-primary)] rounded-full flex items-center justify-center cursor-pointer hover:bg-[var(--color-primary-dark)] shadow-lg border-2 border-white transition">
                                    <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                                </label>
                                <input id="photo-input" name="photo" type="file" class="hidden" accept="image/*" onchange="previewPhoto(this)">
                            </div>
                            <p class="text-xs text-[var(--color-text)]/60 mt-3">Klik kamera untuk mengubah foto</p>
                        </div>

                        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-text)]/60 mb-4">Status Akun</p>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">ID Staf</label>
                                    <input type="text" value="{{ $user->user_id }}" class="hivi-input" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Peran</label>
                                    <select name="role_name" class="hivi-input">
                                        @foreach($roleName as $role)
                                            <option value="{{ $role->role_type }}" {{ $user->role_name == $role->role_type ? 'selected' : '' }}>{{ $role->role_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Status Keaktifan</label>
                                    <select name="status" class="hivi-input">
                                        @foreach($statusUser as $status)
                                            <option value="{{ $status->type_name }}" {{ $user->status == $status->type_name ? 'selected' : '' }}>{{ $status->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- right column: full details -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-text)]/60 mb-4 border-b pb-2">Informasi Pekerjaan / Jabatan</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="hivi-input" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Email Kerja</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="hivi-input" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nomor Telepon</label>
                                    <input type="tel" name="phone_number" value="{{ $user->phone_number }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Bidang</label>
                                    <input type="text" name="department" value="{{ $user->department }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Jabatan</label>
                                    <select name="position" class="hivi-input">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach($position as $pos)
                                            <option value="{{ $pos->position }}" {{ $user->position == $pos->position ? 'selected' : '' }}>{{ $pos->position }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Peran Persetujuan</label>
                                    <select name="jabatan" class="hivi-input">
                                        <option value="">-- Tidak Ada --</option>
                                        <option value="hod" {{ $user->profile?->jabatan == 'hod' ? 'selected' : '' }}>Head of Department</option>
                                        <option value="hr" {{ $user->profile?->jabatan == 'hr' ? 'selected' : '' }}>Human Resources</option>
                                        <option value="purchasing" {{ $user->profile?->jabatan == 'purchasing' ? 'selected' : '' }}>Purchasing</option>
                                        <option value="owner_rep" {{ $user->profile?->jabatan == 'owner_rep' ? 'selected' : '' }}>Owner Representative</option>
                                        <option value="direktur" {{ $user->profile?->jabatan == 'direktur' ? 'selected' : '' }}>Director</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Tanggal Bergabung</label>
                                    <input type="date" name="join_date" value="{{ $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') : '' }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Pendidikan Terakhir</label>
                                    <select name="pendidikan_terakhir" class="hivi-input">
                                        <option value="">-- Pilih --</option>
                                        @foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $edu)
                                            <option value="{{ $edu }}" {{ $user->profile?->pendidikan_terakhir == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                         <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-text)]/60 mb-4 border-b pb-2">Data Kependudukan (KTP)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nomor Induk Kependudukan (NIK)</label>
                                    <input type="text" name="nik" value="{{ $user->profile?->nik }}" class="hivi-input" maxlength="16">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nomor Kartu Keluarga (KK)</label>
                                    <input type="text" name="no_kk" value="{{ $user->profile?->no_kk }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Nomor NPWP</label>
                                    <input type="text" name="npwp" value="{{ $user->profile?->npwp }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Status Pernikahan</label>
                                    <select name="status_pernikahan" class="hivi-input">
                                        <option value="belum_menikah" {{ $user->profile?->status_pernikahan == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="menikah" {{ $user->profile?->status_pernikahan == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="cerai" {{ $user->profile?->status_pernikahan == 'cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                         <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                            <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-text)]/60 mb-4 border-b pb-2">Alamat Lengkap</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Alamat</label>
                                    <textarea name="alamat" rows="2" class="hivi-input">{{ $user->profile?->alamat }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Kota</label>
                                    <input type="text" name="kota" value="{{ $user->profile?->kota }}" class="hivi-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Provinsi</label>
                                    <input type="text" name="provinsi" value="{{ $user->profile?->provinsi }}" class="hivi-input">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8">
                            <button type="submit" class="px-8 py-3 bg-[var(--color-primary)] text-white rounded-2xl text-md font-bold hover:bg-[var(--color-primary-dark)] shadow-sm transition">
                                <i data-lucide="save" class="w-5 h-5 inline mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('edit-photo-preview').src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection


