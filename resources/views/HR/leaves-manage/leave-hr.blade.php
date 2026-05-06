@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">Manajemen Cuti (HR)</h5>
                <p class="text-sm text-slate-500 mt-1">Kelola permohonan cuti dan ijin karyawan dengan efisien</p>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="#!">Manajemen Cuti</a>
                </li>
                <li class="text-slate-700 font-medium">
                    / Kelola Cuti (HR)
                </li>
            </ul>
        </div>

        <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2 xl:grid-cols-12 mb-6">
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center rounded-xl size-12 text-xl bg-[#80BB9B]/20 text-[#4F6560] shrink-0"><i data-lucide="file-bar-chart-2"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="18">0</span>/<span class="counter-value" data-target="60">0</span></h5>
                            <p class="text-sm text-slate-500">Total Karyawan Cuti</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-green-500 bg-green-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="calendar-check"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="5">0</span></h5>
                            <p class="text-sm text-slate-500">Cuti Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-purple-500 bg-purple-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="codepen"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="0">0</span></h5>
                            <p class="text-sm text-slate-500">Cuti Tak Terencana</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-yellow-500 bg-yellow-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="loader"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="6">0</span></h5>
                            <p class="text-sm text-slate-500">Menunggu Approval</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h5 class="text-lg font-bold text-slate-900">Daftar Pengajuan Cuti</h5>
                    <p class="text-sm text-slate-500">Kelola semua permohonan cuti karyawan</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('hr/create/leave/hr/page') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> 
                        Tambah Cuti
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="alternativePagination" class="w-full whitespace-nowrap" style="width:100%">
                    <thead>
                        <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-gray-200">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Karyawan</th>
                            <th class="px-4 py-3">Jenis Cuti</th>
                            <th class="px-4 py-3">Alasan</th>
                            <th class="px-4 py-3">Jumlah Hari</th>
                            <th class="px-4 py-3">Dari</th>
                            <th class="px-4 py-3">Sampai</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leaveList as $key => $leave)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3">{{ $key + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $leave->user?->name ?? $leave->employee_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $leave->leave_type }}</td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                            <td class="px-4 py-3 font-bold text-[#4F6560]">{{ $leave->number_of_day }} Hari</td>
                            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($leave->date_from)->format('d M, Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($leave->date_to)->format('d M, Y') }}</td>
                            <td class="px-4 py-3">
                                @if($leave->status == 'Approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                @elseif($leave->status == 'Rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @if($leave->status == 'menunggu' || $leave->status == 'Pending')
                                    <form action="{{ route('hr/leave/approve') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $leave->id }}">
                                        <button type="submit" title="Setujui" class="flex items-center justify-center text-green-500 transition-all duration-200 ease-linear bg-green-100 rounded-lg size-8 hover:text-white hover:bg-green-500 shadow-sm">
                                            <i data-lucide="check" class="size-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('hr/leave/reject') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $leave->id }}">
                                        <button type="submit" title="Tolak" class="flex items-center justify-center text-red-500 transition-all duration-200 ease-linear bg-red-100 rounded-lg size-8 hover:text-white hover:bg-red-500 shadow-sm">
                                            <i data-lucide="x" class="size-4"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="px-2.5 py-1 text-xs text-slate-400 font-medium">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p>Belum ada pengajuan cuti</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

@section('script')
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('alternativePagination')) {
        new DataTable('#alternativePagination', {
            pagingType: 'full_numbers',
            columnDefs: [
                { orderable: false, targets: [0, 8] }
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
