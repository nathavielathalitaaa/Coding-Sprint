@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">Leave Manage (Employee)</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="#!">Manajemen Cuti</a>
                </li>
                <li class="text-slate-700 font-medium">
                    / Cuti Saya
                </li>
            </ul>
        </div>
        
        <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2 xl:grid-cols-12 mb-6">
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-red-500 bg-red-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="file-bar-chart-2"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="23">0</span></h5>
                            <p class="text-sm text-slate-500">Total Saldo Cuti</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-green-500 bg-green-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="calendar-days"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="12">0</span></h5>
                            <p class="text-sm text-slate-500">Cuti Tahunan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center text-purple-500 bg-purple-100 rounded-xl size-12 text-xl shrink-0"><i data-lucide="stethoscope"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="4">0</span></h5>
                            <p class="text-sm text-slate-500">Cuti Sakit</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center rounded-xl size-12 text-sky-500 bg-sky-100 text-xl shrink-0"><i data-lucide="anchor"></i></div>
                        <div class="grow">
                            <h5 class="mb-1 text-2xl font-bold text-slate-800"><span class="counter-value" data-target="11">0</span></h5>
                            <p class="text-sm text-slate-500">Sisa Cuti</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h5 class="text-lg font-bold text-slate-900">Riwayat Cuti Saya</h5>
                    <p class="text-sm text-slate-500">Daftar semua pengajuan cuti yang telah Anda buat</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('hr/create/leave/employee/page') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> 
                        Ajukan Cuti
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="alternativePagination" class="w-full whitespace-nowrap" style="width:100%">
                    <thead>
                        <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-gray-200">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Leave Type</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">No Of Days</th>
                            <th class="px-4 py-3">From</th>
                            <th class="px-4 py-3">to</th>
                            <th class="px-4 py-3">Approved By</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leave as $key => $value)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3">{{ $key + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $value->leave_type }}</td>
                                <td class="px-4 py-3 text-slate-500 max-w-xs truncate" title="{{ $value->reason }}">{{ $value->reason }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ url('hr/view/detail/leave/employee/'.auth()->id()) }}" class="font-bold text-[#4F6560] hover:text-[#3d504c]">{{ $value->number_of_day }} Hari</a>
                                </td>
                                <td class="px-4 py-3">{{ $value->date_from }}</td>
                                <td class="px-4 py-3">{{ $value->date_to }}</td>
                                <td class="px-4 py-3">{{ $value->approved_by ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($value->status == 'Approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                    @elseif($value->status == 'menunggu' || $value->status == 'Pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                                    @elseif($value->status == 'Rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $value->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="#!" data-modal-target="leaveOverviewModal" class="flex items-center justify-center rounded-lg size-8 text-[#4F6560] bg-[#80BB9B]/20 hover:text-white hover:bg-[#4F6560] transition"><i data-lucide="info" class="size-4"></i></a>
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
