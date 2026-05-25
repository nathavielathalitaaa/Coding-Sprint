@extends('layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-8">
        <h1 class="serif text-[32px] text-[#1A2B24] font-semibold">Master Data Settings</h1>
        <p class="font-poppins font-light text-[13px] text-[#6B7280]">Manage reference data used throughout the system</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Card 1: Jabatan --}}
        <div class="hivi-card">
            <div class="mb-4">
                <h2 class="serif text-[18px] font-bold text-[#1A2B24]">Positions</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Used in employee forms</p>
            </div>

            <form action="{{ route('hr.settings.position.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <input type="text" name="position" class="hivi-input flex-1" placeholder="New position name..." required>
                <button type="submit" class="hivi-btn-primary whitespace-nowrap text-[13px] px-5">+ Add</button>
            </form>

            <div class="space-y-3">
                @forelse($positions as $item)
                    <div class="flex items-center justify-between group" id="pos-row-{{ $item->id }}">
                        {{-- Display Mode --}}
                        <div class="display-mode flex items-center justify-between w-full">
                            <span class="hivi-badge hivi-badge-green font-medium text-[11px]">{{ $item->position }}</span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="toggleEdit('pos', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[#4F6560]">
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
                            <button onclick="saveEdit('pos', {{ $item->id }}, '{{ route('hr.settings.position.update', $item->id) }}')" class="p-1 text-[#2E7D5E]">
                                <i data-lucide="check" class="size-4"></i>
                            </button>
                            <button onclick="toggleEdit('pos', {{ $item->id }}, false)" class="p-1 text-[#6B7280]">
                                <i data-lucide="x" class="size-4"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation Mode --}}
                        <div class="delete-mode hidden w-full flex items-center justify-between bg-[#FEE2E2] px-3 py-1 rounded-lg">
                            <span class="text-[#991B1B] text-[11px] font-medium">Are you sure?</span>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.settings.position.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#991B1B] text-[11px] font-bold">Yes</button>
                                </form>
                                <button onclick="toggleDelete('pos', {{ $item->id }}, false)" class="text-[#6B7280] text-[11px]">No</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8">
                        <i data-lucide="inbox" class="size-8 text-[#D1D5DB] mb-2"></i>
                        <p class="font-poppins text-[12px] text-[#9CA3AF]">No positions yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 2: Status Karyawan --}}
        <div class="hivi-card">
            <div class="mb-4">
                <h2 class="serif text-[18px] font-bold text-[#1A2B24]">Employee Statuses</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Employee account activity statuses</p>
            </div>

            <form action="{{ route('hr.settings.usertype.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <input type="text" name="type_name" class="hivi-input flex-1" placeholder="New status name..." required>
                <button type="submit" class="hivi-btn-primary whitespace-nowrap text-[13px] px-5">+ Add</button>
            </form>

            <div class="space-y-3">
                @forelse($userTypes as $item)
                    <div class="flex items-center justify-between group" id="ut-row-{{ $item->id }}">
                        {{-- Display Mode --}}
                        <div class="display-mode flex items-center justify-between w-full">
                            <span class="hivi-badge hivi-badge-blue font-medium text-[11px]">{{ $item->type_name }}</span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="toggleEdit('ut', {{ $item->id }}, true)" class="p-1 text-[#6B7280] hover:text-[#4F6560]">
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
                            <button onclick="saveEdit('ut', {{ $item->id }}, '{{ route('hr.settings.usertype.update', $item->id) }}')" class="p-1 text-[#2E7D5E]">
                                <i data-lucide="check" class="size-4"></i>
                            </button>
                            <button onclick="toggleEdit('ut', {{ $item->id }}, false)" class="p-1 text-[#6B7280]">
                                <i data-lucide="x" class="size-4"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation Mode --}}
                        <div class="delete-mode hidden w-full flex items-center justify-between bg-[#FEE2E2] px-3 py-1 rounded-lg">
                            <span class="text-[#991B1B] text-[11px] font-medium">Are you sure?</span>
                            <div class="flex gap-2">
                                <form action="{{ route('hr.settings.usertype.destroy', $item->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#991B1B] text-[11px] font-bold">Yes</button>
                                </form>
                                <button onclick="toggleDelete('ut', {{ $item->id }}, false)" class="text-[#6B7280] text-[11px]">No</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center py-8">
                        <i data-lucide="inbox" class="size-8 text-[#D1D5DB] mb-2"></i>
                        <p class="font-poppins text-[12px] text-[#9CA3AF]">No statuses yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Card 3: Role Sistem --}}
        <div class="hivi-card">
            <div class="mb-5">
                <h2 class="serif text-[18px] font-bold text-[#1A2B24]">System Roles</h2>
                <p class="font-poppins font-light text-[12px] text-[#6B7280]">Feature access information based on available system roles</p>
            </div>

            <div class="space-y-4">
                @foreach($roleTypes as $item)
                    @php
                        $roleKey = strtolower($item->role_type);
                        $isHR = str_contains($roleKey, 'hr') || str_contains($roleKey, 'human');
                        $isSup = str_contains($roleKey, 'supervisor') || str_contains($roleKey, 'head');
                        $isStaff = str_contains($roleKey, 'staff');

                        $bgColor = $isHR ? 'bg-[#F0FAF4]' : ($isSup ? 'bg-[#EFF6FF]' : 'bg-[#F9FAFB]');
                        $borderColor = $isHR ? 'border-[#C1E4D0]' : ($isSup ? 'border-[#DBEAFE]' : 'border-[#F3F4F6]');
                        $badgeColor = $isHR ? 'hivi-badge-green' : ($isSup ? 'hivi-badge-blue' : 'hivi-badge-gray');
                        $textColor = $isHR ? 'text-[#4F6560]' : ($isSup ? 'text-[#1E40AF]' : 'text-[#4B5563]');
                    @endphp

                    <div class="p-4 rounded-2xl {{ $bgColor }} border {{ $borderColor }} transition-all hover:shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="hivi-badge {{ $badgeColor }} font-bold text-[10px] uppercase">{{ $item->role_type }}</span>
                            <i data-lucide="shield-check" class="size-4 {{ $textColor }} opacity-40"></i>
                        </div>
                        
                        <ul class="space-y-2">
                            @if($isHR)
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Full management of employee data, attendance, & master data</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Access to AI Analysis for attendance data processing</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Configure document formats & approval workflows</span>
                                </li>
                            @elseif($isSup)
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Real-time monitoring of team attendance & overtime</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Authority to approve or reject documents</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Access to summary reports of related departments</span>
                                </li>
                            @else
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Self-service document submission (leave, permits, etc.)</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Manage personal profile & digital signature</span>
                                </li>
                                <li class="flex gap-2 text-[11px] {{ $textColor }} leading-tight">
                                    <i data-lucide="check-circle-2" class="size-3.5 shrink-0 mt-0.5"></i>
                                    <span>Receive status notifications for document submissions</span>
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
