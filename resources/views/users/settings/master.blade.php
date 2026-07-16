@extends('layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-8">
        <h1 class="serif text-[32px] text-[#111111] font-semibold">Pengaturan Master Data</h1>
        <p class="font-poppins font-light text-[13px] text-[#6B7280]">Kelola data referensi yang digunakan di seluruh sistem</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Card 1: Jabatan --}}
        <div class="hivi-card">
            <div class="mb-4">
                <h2 class="serif text-[18px] font-bold text-[#111111]">Jabatan</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Digunakan dalam formulir anggota/staf</p>
            </div>

            <form action="{{ route('users.settings.position.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <input type="text" name="position" class="hivi-input flex-1" placeholder="Nama jabatan baru..." required>
                <button type="submit" class="hivi-btn-primary whitespace-nowrap text-[13px] px-5">+ Tambah</button>
            </form>

            <div class="space-y-3">
                @forelse($positions as $item)
                    <div class="flex items-center justify-between group" id="pos-row-{{ $item->id }}">
                        {{-- Display Mode --}}
                        <div class="display-mode flex items-center justify-between w-full">
                            <span class="hivi-badge hivi-badge-green font-medium text-[11px]">{{ $item->position }}</span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="toggleEdit('pos', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[var(--color-text)]">
                                    <i data-lucide="pencil" class="size-4"></i>
                                </button>
                                <button onclick="toggleDelete('pos', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[#E57373]">
                                    <i data-lucide="trash" class="size-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Edit Mode --}}
                        <div class="edit-mode hidden w-full flex items-center gap-2">
                            <input type="text" id="pos-input-{{ $item->id }}" value="{{ $item->position }}" class="hivi-input flex-1 text-[13px] py-1">
                            <button onclick="saveEdit('pos', {{ $item->id }}, '{{ route('users.settings.position.update', $item->id) }}')" class="p-1 text-[#E62129]">
                                <i data-lucide="check" class="size-4"></i>
                            </button>
                            <button onclick="toggleEdit('pos', {{ $item->id }}, false)" class="p-1 text-[#6B7280]">
                                <i data-lucide="x" class="size-4"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation Mode --}}
                        <div class="delete-mode hidden w-full flex items-center justify-between bg-[#FEE2E2] px-3 py-1 rounded-lg">
                            <span class="text-[#991B1B] text-[11px] font-medium">Apakah Anda yakin?</span>
                            <div class="flex gap-2">
                                <form action="{{ route('users.settings.position.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#991B1B] text-[11px] font-bold">Ya</button>
                                </form>
                                <button onclick="toggleDelete('pos', {{ $item->id }}, false)" class="text-[#6B7280] text-[11px]">Tidak</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8">
                        <i data-lucide="inbox" class="size-8 text-[#D1D5DB] mb-2"></i>
                        <p class="font-poppins text-[12px] text-[#9CA3AF]">Belum ada jabatan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 2: Status Karyawan --}}
        <div class="hivi-card">
            <div class="mb-4">
                <h2 class="serif text-[18px] font-bold text-[#111111]">Status Anggota/Staf</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Status keaktifan akun anggota/staf</p>
            </div>

            <form action="{{ route('users.settings.usertype.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <input type="text" name="type_name" class="hivi-input flex-1" placeholder="Nama status baru..." required>
                <button type="submit" class="hivi-btn-primary whitespace-nowrap text-[13px] px-5">+ Tambah</button>
            </form>

            <div class="space-y-3">
                @forelse($userTypes as $item)
                    <div class="flex items-center justify-between group" id="ut-row-{{ $item->id }}">
                        {{-- Display Mode --}}
                        <div class="display-mode flex items-center justify-between w-full">
                            <span class="hivi-badge hivi-badge-blue font-medium text-[11px]">{{ $item->type_name }}</span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="toggleEdit('ut', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[var(--color-text)]">
                                    <i data-lucide="pencil" class="size-4"></i>
                                </button>
                                <button onclick="toggleDelete('ut', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[#E57373]">
                                    <i data-lucide="trash" class="size-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Edit Mode --}}
                        <div class="edit-mode hidden w-full flex items-center gap-2">
                            <input type="text" id="ut-input-{{ $item->id }}" value="{{ $item->type_name }}" class="hivi-input flex-1 text-[13px] py-1">
                            <button onclick="saveEdit('ut', {{ $item->id }}, '{{ route('users.settings.usertype.update', $item->id) }}')" class="p-1 text-[#E62129]">
                                <i data-lucide="check" class="size-4"></i>
                            </button>
                            <button onclick="toggleEdit('ut', {{ $item->id }}, false)" class="p-1 text-[#6B7280]">
                                <i data-lucide="x" class="size-4"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation Mode --}}
                        <div class="delete-mode hidden w-full flex items-center justify-between bg-[#FEE2E2] px-3 py-1 rounded-lg">
                            <span class="text-[#991B1B] text-[11px] font-medium">Apakah Anda yakin?</span>
                            <div class="flex gap-2">
                                <form action="{{ route('users.settings.usertype.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#991B1B] text-[11px] font-bold">Ya</button>
                                </form>
                                <button onclick="toggleDelete('ut', {{ $item->id }}, false)" class="text-[#6B7280] text-[11px]">Tidak</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8">
                        <i data-lucide="inbox" class="size-8 text-[#D1D5DB] mb-2"></i>
                        <p class="font-poppins text-[12px] text-[#9CA3AF]">Belum ada status</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 3: Role Sistem --}}
        <div class="hivi-card">
            <div class="mb-5">
                <h2 class="serif text-[18px] font-bold text-[#111111]">Peran Sistem</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Informasi hak akses fitur berdasarkan peran sistem yang tersedia</p>
            </div>

            <div class="space-y-4">
                @foreach($roleTypes as $item)
                    @php
                        $roleKey = strtolower($item->role_type);
                        $isHR = str_contains($roleKey, 'hr') || str_contains($roleKey, 'human');
                        $isSup = str_contains($roleKey, 'supervisor') || str_contains($roleKey, 'head');
                        $isStaff = str_contains($roleKey, 'staff');

                        $bgColor = $isHR ? 'bg-[var(--color-bg-light)]' : ($isSup ? 'bg-[#EFF6FF]' : 'bg-[#F5F5F7]');
                        $borderColor = $isHR ? 'border-[rgba(230,33,41,0.2)]' : ($isSup ? 'border-[#DBEAFE]' : 'border-[#F3F4F6]');
                        $badgeColor = $isHR ? 'hivi-badge-green' : ($isSup ? 'hivi-badge-blue' : 'hivi-badge-gray');
                        $textColor = $isHR ? 'text-[var(--color-text)]' : ($isSup ? 'text-[#1E40AF]' : 'text-[#4B5563]');
                    @endphp

                    <div class="p-4 rounded-[28px] {{ $bgColor }} border {{ $borderColor }} transition-all hover:shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="hivi-badge {{ $badgeColor }} font-bold text-[10px] uppercase">{{ $item->role_type }}</span>
                            <i data-lucide="shield-check" class="size-4 {{ $textColor }} opacity-40"></i>
                        </div>
                        
                        <ul class="space-y-2">
                            @if($isHR)
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Manajemen penuh data anggota, presensi, & master data</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Akses ke Analisis AI untuk pemrosesan data presensi</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Konfigurasikan format dokumen & alur kerja persetujuan</span>
                                </li>
                            @elseif($isSup)
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Pemantauan presensi & lembur tim secara real-time</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Wewenang untuk menyetujui atau menolak dokumen</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Akses ke laporan ringkasan departemen/bidang terkait</span>
                                </li>
                            @else
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Pengajuan dokumen mandiri (izin, cuti, dll.)</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Kelola profil pribadi & tanda tangan digital</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Terima notifikasi status untuk pengajuan dokumen</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    function toggleEdit(prefix, id, show) {
        const row = document.getElementById(`${prefix}-row-${id}`);
        const display = row.querySelector('.display-mode');
        const edit = row.querySelector('.edit-mode');
        const del = row.querySelector('.delete-mode');

        if (show) {
            display.classList.add('hidden');
            edit.classList.remove('hidden');
            del.classList.add('hidden');
            row.querySelector('input').focus();
        } else {
            display.classList.remove('hidden');
            edit.classList.add('hidden');
        }
    }

    function toggleDelete(prefix, id, show) {
        const row = document.getElementById(`${prefix}-row-${id}`);
        const display = row.querySelector('.display-mode');
        const del = row.querySelector('.delete-mode');
        const edit = row.querySelector('.edit-mode');

        if (show) {
            display.classList.add('hidden');
            del.classList.remove('hidden');
            edit.classList.add('hidden');
        } else {
            display.classList.remove('hidden');
            del.classList.add('hidden');
        }
    }

    function saveEdit(prefix, id, url) {
        const input = document.getElementById(`${prefix}-input-${id}`);
        const value = input.value;
        const fieldName = prefix === 'pos' ? 'position' : (prefix === 'ut' ? 'type_name' : 'role_type');

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ [fieldName]: value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update DOM
                const row = document.getElementById(`${prefix}-row-${id}`);
                row.querySelector('.display-mode span').textContent = value;
                toggleEdit(prefix, id, false);
                
                // Show success toast
                if (window.toastr) {
                    toastr.success(data.message);
                } else if (window.Swal) {
                    Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                } else {
                    location.reload();
                }
            } else {
                alert(data.message || 'Failed to save changes');
            }
        })
        .catch(err => {
            console.error(err);
            location.reload();
        });
    }
</script>
@endsection

