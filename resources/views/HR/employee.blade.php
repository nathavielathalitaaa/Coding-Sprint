@extends('layouts.master')
@section('content')
<style>
/* ── Employee List Page Styles ── */
.emp-page { font-family: 'Poppins', sans-serif; }
.emp-container { background: rgba(255,255,255,0.80); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-radius: 24px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.4); }
.emp-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 600; color: #1A2B24; }
.emp-subtitle { font-size: 13px; color: #6B7280; margin-top: 2px; }
.emp-search { display: flex; align-items: center; background: #fff; border-radius: 999px; padding: 8px 16px; border: 1px solid rgba(229, 231, 235, 0.5); width: 260px; }
.emp-search input { border: none; outline: none; background: transparent; font-size: 13px; width: 100%; margin-left: 8px; font-family: 'Poppins', sans-serif; color: #1A2B24; }
.emp-search input::placeholder { color: #9CA3AF; }
.emp-btn-export { display: inline-flex; align-items: center; gap: 6px; background: #fff; padding: 8px 18px; border-radius: 12px; border: 1px solid #E5E7EB; font-size: 13px; font-weight: 500; color: #4F6560; cursor: pointer; transition: all 0.2s; font-family: 'Poppins', sans-serif; }
.emp-btn-export:hover { background: #f0f7f3; }
.emp-btn-add { display: inline-flex; align-items: center; gap: 6px; background: #4F6560; color: #fff; padding: 8px 20px; border-radius: 12px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s; font-family: 'Poppins', sans-serif; text-decoration: none; }
.emp-btn-add:hover { background: #3d504c; color: #fff; }

/* Table container */
.emp-table-wrap { background: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.4); border-radius: 16px; padding: 20px; margin-top: 20px; }
.emp-table-head { display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr 1fr; padding: 0 20px 12px; font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
.emp-table-head > div:last-child { text-align: right; }

/* Rows */
.emp-row { display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr 1fr; align-items: center; padding: 14px 20px; border-radius: 999px; transition: all 0.2s ease; margin-bottom: 4px; cursor: default; }
.emp-row:hover { background: rgba(128,187,155,0.12); }
.emp-row-active { background: #80BB9B !important; box-shadow: 0 4px 16px rgba(128,187,155,0.35); }
.emp-row-active, .emp-row-active .emp-name, .emp-row-active .emp-email, .emp-row-active .emp-col, .emp-row-active .emp-action-link { color: #fff !important; }
.emp-row-active .emp-badge { background: rgba(255,255,255,0.25) !important; color: #fff !important; }
.emp-row-active .emp-avatar { background: rgba(255,255,255,0.3) !important; color: #fff !important; }
.emp-row-active .emp-action-btn { background: rgba(255,255,255,0.2) !important; color: #fff !important; }
.emp-row-active .emp-action-btn:hover { background: rgba(255,255,255,0.35) !important; }

/* Avatar */
.emp-avatar { width: 40px; height: 40px; border-radius: 50%; background: #E8F5EE; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #4F6560; flex-shrink: 0; overflow: hidden; }
.emp-avatar img { width: 100%; height: 100%; object-fit: cover; }
.emp-name { font-size: 14px; font-weight: 500; color: #1A2B24; }
.emp-email { font-size: 11px; color: #9CA3AF; }
.emp-col { font-size: 13px; color: #4B5563; }
.emp-badge { display: inline-block; padding: 4px 14px; border-radius: 999px; font-size: 11px; font-weight: 500; }
.emp-badge-green { background: #E8F5EE; color: #2E7D5E; }
.emp-badge-red { background: #FEE2E2; color: #991B1B; }
.emp-badge-gray { background: #F3F4F6; color: #6B7280; }
.emp-badge-blue { background: #DBEAFE; color: #1E40AF; }

/* Action buttons */
.emp-action-btn { width: 32px; height: 32px; border-radius: 8px; background: #F3F4F6; display: inline-flex; align-items: center; justify-content: center; color: #6B7280; border: none; cursor: pointer; transition: all 0.2s; }
.emp-action-btn:hover { background: #E5E7EB; color: #1A2B24; }
.emp-action-btn.danger:hover { background: #FEE2E2; color: #DC2626; }
.emp-action-link { font-size: 13px; color: #4F6560; text-decoration: none; font-weight: 500; }
.emp-action-link:hover { text-decoration: underline; }

/* Empty state */
.emp-empty { text-align: center; padding: 48px 20px; color: #9CA3AF; }
.emp-empty i { margin-bottom: 12px; }

/* Hidden data row for JS compat */
.emp-data-row { display: none; }

/* Responsive */
@media (max-width: 1024px) {
    .emp-table-head { display: none; }
    .emp-row { grid-template-columns: 1fr; gap: 8px; border-radius: 16px; padding: 16px; }
}
@media (max-width: 768px) {
    .emp-page { padding: 16px; }
    .emp-container { padding: 16px; border-radius: 20px; }
}
</style>

<div class="emp-page">
    <div class="emp-container">

        {{-- header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <div>
                <h1 class="emp-title">Staff</h1>
                <p class="emp-subtitle">Kelola data karyawan perusahaan</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="emp-search">
                    <i data-lucide="search" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;"></i>
                    <input type="text" id="empSearchInput" placeholder="Cari karyawan..." onkeyup="filterEmployees()">
                </div>
                <button class="emp-btn-export" onclick="window.print()">
                    <i data-lucide="upload" style="width:16px;height:16px;"></i>
                    Export
                </button>
                <button class="emp-btn-add" onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')">
                    <i data-lucide="plus" style="width:16px;height:16px;"></i>
                    Tambah Karyawan
                </button>
            </div>
        </div>

        {{-- card table --}}
        <div class="emp-table-wrap">

            {{-- column headers --}}
            <div class="emp-table-head">
                <div>Nama Karyawan</div>
                <div>Departemen</div>
                <div>Status</div>
                <div>Tgl Bergabung</div>
                <div>Aksi</div>
            </div>

            {{-- data rows --}}
            <div id="empRowContainer">
            @forelse($employeeList as $key => $employee)
                @php
                    $fullName = $employee->name ?? '';
                    $parts = explode(' ', $fullName);
                    $initials = collect($parts)->map(fn($p) => strtoupper(substr($p,0,1)))->implode('');
                    $isActive = $loop->first;
                @endphp

                {{-- hidden tr for js compatibility (editemployee / deleterecord use closest('tr') + dataset) --}}
                <table style="display:none;"><tbody>
                <tr class="emp-data-row"
                    data-id="{{ $employee->id }}"
                    data-photo="{{ $employee->avatar }}"
                    data-location="{{ $employee->location }}"
                    data-join-date="{{ $employee->join_date }}"
                    data-status="{{ $employee->status }}"
                    data-email="{{ $employee->email }}"
                    data-phone="{{ $employee->phone_number }}"
                    data-experience="{{ $employee->experience }}"
                    data-role="{{ $employee->role_name }}"
                    data-department="{{ $employee->department }}"
                    data-designation="{{ $employee->designation }}"
                    data-position="{{ $employee->position }}"
                    data-nik="{{ $employee->profile?->nik }}"
                    data-no-kk="{{ $employee->profile?->no_kk }}"
                    data-npwp="{{ $employee->profile?->npwp }}"
                    data-bpjs-kesehatan="{{ $employee->profile?->bpjs_kesehatan }}"
                    data-bpjs-ketenagakerjaan="{{ $employee->profile?->bpjs_ketenagakerjaan }}"
                    data-jabatan="{{ $employee->profile?->jabatan }}"
                    data-pendidikan="{{ $employee->profile?->pendidikan_terakhir }}"
                    data-status-pernikahan="{{ $employee->profile?->status_pernikahan }}"
                    data-jumlah-anak="{{ $employee->profile?->jumlah_anak }}"
                    data-alamat="{{ $employee->profile?->alamat }}"
                    data-kota="{{ $employee->profile?->kota }}"
                    data-provinsi="{{ $employee->profile?->provinsi }}"
                    data-kode-pos="{{ $employee->profile?->kode_pos }}">
                    <td class="user_id">{{ $employee->user_id }}</td>
                    <td class="name">{{ $employee->name }}</td>
                </tr>
                </tbody></table>

                {{-- visual card row --}}
                <div class="emp-row {{ $isActive ? 'emp-row-active' : '' }} emp-searchable"
                     data-name="{{ strtolower($employee->name) }}"
                     data-dept="{{ strtolower($employee->department ?? '') }}"
                     data-emp-id="{{ $employee->id }}">

                    {{-- name + avatar --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="emp-avatar">
                            @if(!empty($employee->avatar))
                                <img src="{{ URL::to('assets/images/user/'.$employee->avatar) }}" alt="">
                            @else
                                {{ substr($initials, 0, 2) }}
                            @endif
                        </div>
                        <div>
                            <p class="emp-name">{{ $employee->name }}</p>
                            <p class="emp-email">{{ $employee->email }}</p>
                        </div>
                    </div>

                    {{-- department --}}
                    <div class="emp-col">
                        {{ $employee->department ?? '-' }}
                        @if($employee->profile?->jabatan)
                            <br><span class="emp-badge emp-badge-blue" style="margin-top:4px;">{{ ucfirst(str_replace('_',' ', $employee->profile?->jabatan)) }}</span>
                        @endif
                    </div>

                    {{-- status --}}
                    <div>
                        @if(strtolower($employee->status) == 'aktif' || strtolower($employee->status) == 'active')
                            <span class="emp-badge emp-badge-green">Aktif</span>
                        @elseif(strtolower($employee->status) == 'disable' || strtolower($employee->status) == 'inactive')
                            <span class="emp-badge emp-badge-red">Nonaktif</span>
                        @else
                            <span class="emp-badge emp-badge-gray">{{ $employee->status }}</span>
                        @endif
                    </div>

                    {{-- join date --}}
                    <div class="emp-col">
                        {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}
                    </div>

                    {{-- actions --}}
                    <div style="text-align:right; display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                        <a href="{{ $employee->user_id ? url('page/account/'.$employee->user_id) : '#' }}"
                           @if(!$employee->user_id) onclick="alert('ID karyawan belum ter-generate, coba refresh halaman')" @endif
                           class="emp-action-btn" title="Lihat Profil">
                            <i data-lucide="eye" style="width:15px;height:15px;"></i>
                        </a>
                        <button type="button" data-id="{{ $employee->id }}"
                                class="editEmployee emp-action-btn" title="Edit">
                            <i data-lucide="pencil" style="width:15px;height:15px;"></i>
                        </button>
                        <button type="button" data-id="{{ $employee->id }}"
                                class="deleteRecord emp-action-btn danger" title="Hapus">
                            <i data-lucide="trash-2" style="width:15px;height:15px;"></i>
                        </button>
                    </div>
                </div>

            @empty
                <div class="emp-empty">
                    <i data-lucide="users" style="width:40px;height:40px;display:block;margin:0 auto 12px;color:#D1D5DB;"></i>
                    <p style="font-weight:500;color:#6B7280;margin-bottom:4px;">Belum ada karyawan</p>
                    <p style="font-size:13px;margin-bottom:16px;">Mulai dengan menambahkan data karyawan baru</p>
                    <button class="emp-btn-add" onclick="document.getElementById('addEmployeeModal').classList.remove('hidden')">
                        <i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Karyawan
                    </button>
                </div>
            @endforelse
            </div>
        </div>

    </div>
</div>

<script>
// Client-side search filter
function filterEmployees() {
    var q = document.getElementById('empSearchInput').value.toLowerCase();
    document.querySelectorAll('.emp-searchable').forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        var dept = row.getAttribute('data-dept') || '';
        row.style.display = (name.includes(q) || dept.includes(q)) ? '' : 'none';
    });
}
</script>

<div id="addEmployeeModal" class="fixed inset-0 z-[1000] hidden">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('addEmployeeModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-start justify-center p-4 overflow-y-auto">
        <div class="relative bg-white dark:bg-zink-700 rounded-2xl shadow-2xl w-full max-w-2xl my-8">
            <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-zink-500">
                <div>
                    <h5 class="text-lg font-bold text-slate-900 dark:text-zink-50">Tambah Karyawan Baru</h5>
                    <p class="text-xs text-slate-500 mt-0.5">Lengkapi data karyawan</p>
                </div>
                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden')" class="p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                </button>
            </div>
            <form action="{{ route('hr/employee/save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-6">

                    <div class="flex justify-center">
                        <div class="relative">
                            <img id="addPhotoPreview" src="{{ URL::to('assets/images/profile.png') }}" class="w-24 h-24 rounded-full object-cover border-4 border-slate-100">
                            <label for="addPhoto" class="absolute bottom-0 right-0 w-8 h-8 bg-custom-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-custom-600">
                                <i data-lucide="camera" class="w-4 h-4 text-white"></i>
                            </label>
                            <input type="file" id="addPhoto" name="profile_image" class="hidden" accept="image/*" onchange="previewPhoto(this, 'addPhotoPreview')">
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Informasi Dasar</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Nama lengkap" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="email@sinergi.com" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</label>
                                <input type="tel" name="phone_number" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                                <select name="role_name" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roleName as $value)
                                    <option value="{{ $value->role_type }}">{{ $value->role_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                                <select name="status" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Pilih Status --</option>
                                    @foreach($statusUser as $value)
                                    <option value="{{ $value->type_name }}">{{ $value->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
                                <input type="text" name="department" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Nama departemen">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Posisi</label>
                                <select name="position" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach($position as $value)
                                    <option value="{{ $value->position }}">{{ $value->position }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan Approval</label>
                                <select name="jabatan" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Tidak ada --</option>
                                    <option value="hod">Head of Department</option>
                                    <option value="hr">Human Resources</option>
                                    <option value="purchasing">Purchasing</option>
                                    <option value="owner_rep">Owner Representative</option>
                                    <option value="direktur">Direktur</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Bergabung</label>
                                <input type="date" name="tgl_bergabung" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Kontrak Akhir</label>
                                <input type="date" name="tgl_kontrak_akhir" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA/SMK">SMA/SMK</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Data Kependudukan</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">NIK</label>
                                <input type="text" name="nik" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="16 digit NIK">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">No. KK</label>
                                <input type="text" name="no_kk" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="No. Kartu Keluarga">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">NPWP</label>
                                <input type="text" name="npwp" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="No. NPWP">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">BPJS Kesehatan</label>
                                <input type="text" name="bpjs_kesehatan" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="No. BPJS Kesehatan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">BPJS Ketenagakerjaan</label>
                                <input type="text" name="bpjs_ketenagakerjaan" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="No. BPJS Ketenagakerjaan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status Pernikahan</label>
                                <select name="status_pernikahan" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                                    <option value="">-- Pilih --</option>
                                    <option value="belum_menikah">Belum Menikah</option>
                                    <option value="menikah">Menikah</option>
                                    <option value="cerai">Cerai</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Anak</label>
                                <input type="number" name="jumlah_anak" min="0" value="0" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Alamat</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Jl. ..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kota</label>
                                <input type="text" name="kota" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Kota">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Provinsi</label>
                                <input type="text" name="provinsi" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Provinsi">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="00000">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Akun</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Min. 8 karakter" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" class="hivi-input border-slate-200 focus:outline-none focus:border-custom-500 w-full" placeholder="Ulangi password" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200">
                    <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-custom-500 text-white rounded-lg text-sm font-bold hover:bg-custom-600">
                        <i data-lucide="save" class="w-4 h-4 inline mr-1"></i> Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    {{-- edit employee modal — struktur baru scrollable --}}
    <div id="editEmployeeModal" class="fixed inset-0 z-[1000] hidden">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('editEmployeeModal').classList.add('hidden')"></div>
        <div class="fixed inset-0 flex items-start justify-center p-4 overflow-y-auto">
            <div class="relative bg-white dark:bg-zink-700 rounded-2xl shadow-2xl w-full max-w-2xl my-8">

                <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-zink-500">
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-zink-50">Edit Data Karyawan</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi karyawan</p>
                    </div>
                    <button type="button" onclick="document.getElementById('editEmployeeModal').classList.add('hidden')"
                        class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-zink-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                    </button>
                </div>

                <form class="create-form" id="create-form" action="{{ route('hr/employee/update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="e_id">
                    <input type="hidden" name="old_photo" id="old_photo">

                    <div class="p-6 space-y-6">

                        {{-- foto --}}
                        <div class="flex justify-center">
                            <div class="relative">
                                <img id="edit-photo-preview" src="{{ URL::to('assets/images/user.png') }}"
                                    class="w-24 h-24 rounded-full object-cover border-4 border-slate-100 edit-user-profile-image">
                                <label for="edit-profile-img-file-input"
                                    class="absolute bottom-0 right-0 w-8 h-8 bg-custom-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-custom-600 transition-colors">
                                    <i data-lucide="camera" class="w-4 h-4 text-white"></i>
                                </label>
                                <input id="edit-profile-img-file-input" name="photo" type="file"
                                    class="hidden edit-profile-img-file-input" accept="image/*">
                            </div>
                        </div>

                        {{-- informasi dasar --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Informasi Dasar</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Employee ID</label>
                                    <input type="text" id="e_employee_id" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none dark:text-zink-100 dark:bg-zink-700 w-full bg-slate-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="e_name" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Email</label>
                                    <input type="email" name="email" id="e_email" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">No. Telepon</label>
                                    <input type="tel" name="phone_number" id="e_phone_number" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Role</label>
                                    <select name="role_name" id="e_role_name" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach($roleName as $key => $value)
                                        <option value="{{ $value->role_type }}">{{ $value->role_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Status</label>
                                    <select name="status" id="e_status" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Pilih Status --</option>
                                        @foreach($statusUser as $key => $value)
                                        <option value="{{ $value->type_name }}">{{ $value->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Departemen</label>
                                    <input type="text" name="department" id="e_department" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Posisi</label>
                                    <select name="position" id="e_position" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Pilih Posisi --</option>
                                        @foreach($position as $key => $value)
                                        <option value="{{ $value->position }}">{{ $value->position }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Jabatan Approval</label>
                                    <select name="jabatan" id="e_jabatan" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Tidak ada --</option>
                                        <option value="hod">Head of Department</option>
                                        <option value="hr">Human Resources</option>
                                        <option value="purchasing">Purchasing</option>
                                        <option value="owner_rep">Owner Representative</option>
                                        <option value="direktur">Direktur</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Tanggal Bergabung</label>
                                    <input type="date" name="join_date" id="e_join_date" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Tanggal Kontrak Akhir</label>
                                    <input type="date" name="tgl_kontrak_akhir" id="e_tgl_kontrak_akhir" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Pendidikan Terakhir</label>
                                    <select name="pendidikan_terakhir" id="e_pendidikan_terakhir" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Pilih --</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA/SMK">SMA/SMK</option>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- data kependudukan --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Data Kependudukan</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">NIK</label>
                                    <input type="text" name="nik" id="e_nik" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full" placeholder="16 digit NIK">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">No. KK</label>
                                    <input type="text" name="no_kk" id="e_no_kk" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">NPWP</label>
                                    <input type="text" name="npwp" id="e_npwp" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">BPJS Kesehatan</label>
                                    <input type="text" name="bpjs_kesehatan" id="e_bpjs_kesehatan" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">BPJS Ketenagakerjaan</label>
                                    <input type="text" name="bpjs_ketenagakerjaan" id="e_bpjs_ketenagakerjaan" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Status Pernikahan</label>
                                    <select name="status_pernikahan" id="e_status_pernikahan" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                        <option value="">-- Pilih --</option>
                                        <option value="belum_menikah">Belum Menikah</option>
                                        <option value="menikah">Menikah</option>
                                        <option value="cerai">Cerai</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Jumlah Anak</label>
                                    <input type="number" name="jumlah_anak" id="e_jumlah_anak" min="0" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                            </div>
                        </div>

                        {{-- alamat --}}
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Alamat</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Alamat Lengkap</label>
                                    <textarea name="alamat" id="e_alamat" rows="2" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full" placeholder="Jl. ..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Kota</label>
                                    <input type="text" name="kota" id="e_kota" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Provinsi</label>
                                    <input type="text" name="provinsi" id="e_provinsi" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1">Kode Pos</label>
                                    <input type="text" name="kode_pos" id="e_kode_pos" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-zink-500">
                        <button type="button" onclick="document.getElementById('editEmployeeModal').classList.add('hidden')"
                            class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-custom-500 text-white rounded-lg text-sm font-bold hover:bg-custom-600 transition-colors">
                            <i data-lucide="save" class="w-4 h-4 inline mr-1"></i> Update Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" modal-center="" class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto px-6 py-8">
                <div class="float-right">
                    <button data-modal-close="deleteModal" class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <img src="data:image/png;base64,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" alt="" class="block h-12 mx-auto">
                <form action="{{ route('hr/employee/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_delete" id="e_idDelete">
                    <input type="hidden" name="del_photo" id="del_photo">
                    <div class="mt-5 text-center">
                        <h5 class="mb-1">Apakah Anda yakin?</h5>
                        <p class="text-slate-500 dark:text-zink-200">Data karyawan ini akan dihapus secara permanen.</p>
                        <div class="flex justify-center gap-2 mt-6">
                            <button type="reset" data-modal-close="deleteModal" class="bg-white text-slate-500 btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-slate-500/10 dark:focus:bg-slate-500/10 dark:active:bg-slate-500/10">Batal</button>
                            <button type="submit" class="text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20">Ya, Hapus!</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.editEmployee', function () {
            var empId = $(this).data('id');
            // Find hidden TR that holds all data-* attributes
            var row = document.querySelector('tr.emp-data-row[data-id="' + empId + '"]');
            if (!row) return;
            
            // Ambil data dari data-* attribute di <tr>
            var data = row.dataset;
            
            // Handle photo
            var photo = data.photo || '';
            if (photo && photo !== 'profile.png') {
                $('#edit-photo-preview').attr('src', '/assets/images/user/' + photo);
            } else {
                $('#edit-photo-preview').attr('src', '/assets/images/profile.png');
            }
            $('#old_photo').val(photo);

            // Assign values
            $('#e_id').val(data.id || '');
            $('#e_employee_id').val(row.querySelector('.user_id')?.textContent.trim() || '');
            $('#e_name').val(row.querySelector('.name')?.textContent.trim() || '');
            $('#e_email').val(data.email || '');
            $('#e_position').val(data.position || '');
            $('#e_phone_number').val(data.phone || '');
            $('#e_location').val(data.location || '');
            $('#e_join_date').val(data.joinDate || '');
            $('#e_experience').val(data.experience || '');
            $('#e_designation').val(data.designation || '');
            
            // Select fields
            $('#e_department').val(data.department || '').trigger('change');
            $('#e_role_name').val(data.role || '').trigger('change');
            $('#e_status').val(data.status || '').trigger('change');
            
            // Profile fields
            $('#e_nik').val(data.nik || '');
            $('#e_no_kk').val(data.noKk || '');
            $('#e_npwp').val(data.npwp || '');
            $('#e_bpjs_kesehatan').val(data.bpjsKesehatan || '');
            $('#e_bpjs_ketenagakerjaan').val(data.bpjsKetenagakerjaan || '');
            $('#e_jabatan').val(data.jabatan || '').trigger('change');
            $('#e_pendidikan_terakhir').val(data.pendidikan || '').trigger('change');
            $('#e_status_pernikahan').val(data.statusPernikahan || '').trigger('change');
            $('#e_jumlah_anak').val(data.jumlahAnak || 0);
            $('#e_alamat').val(data.alamat || '');
            $('#e_kota').val(data.kota || '');
            $('#e_provinsi').val(data.provinsi || '');
            $('#e_kode_pos').val(data.kodePos || '');

            // Buka modal
            document.getElementById('editEmployeeModal').classList.remove('hidden');
        });

        $(document).on('click', '.deleteRecord', function () {
            var empId = $(this).data('id');
            var row = document.querySelector('tr.emp-data-row[data-id="' + empId + '"]');
            if (!row) return;
            $('#e_idDelete').val(row.dataset.id || '');
            $('#del_photo').val(row.dataset.photo || '');
            document.getElementById('deleteModal').classList.remove('hidden');
        });
    </script>

    <script>
        //for add profile
        if (document.querySelector("#profile-img-file-input")) {
            document.querySelector("#profile-img-file-input").addEventListener("change", function () {
                var preview = document.querySelector(".user-profile-image");
                var file = document.querySelector(".profile-img-file-input").files[0];
                var reader = new FileReader();
                reader.addEventListener(
                    "load",
                    function () {
                        preview.src = reader.result;
                    },
                    false
                );
                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        }
        //for edit profile
        if (document.querySelector("#edit-profile-img-file-input")) {
            document.querySelector("#edit-profile-img-file-input").addEventListener("change", function () {
                var preview = document.querySelector(".edit-user-profile-image");
                var file = document.querySelector(".edit-profile-img-file-input").files[0];
                var reader = new FileReader();
                reader.addEventListener(
                    "load",
                    function () {
                        preview.src = reader.result;
                    },
                    false
                );
                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        }

        function previewPhoto(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById(previewId).src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }
        // Tutup modal kalau klik backdrop
        document.getElementById('addEmployeeModal').addEventListener('click', function(e) {
            if (e.target === this || e.target === this.firstElementChild) {
                this.classList.add('hidden');
            }
        });
    </script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('alternativePagination')) {
        new DataTable('#alternativePagination', {
            pagingType: 'full_numbers',
            columnDefs: [
                { orderable: false, targets: [0, 7] }
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
                emptyTable: 'Tidak ada data'
            }
        });
    }
});
</script>
@endpush
@endsection
