@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">Data Penggajian</h5>
                <p class="text-sm text-slate-500 mt-1">Kelola data gaji dan generate slip gaji karyawan</p>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="#!">Manajemen HR</a>
                </li>
                <li class="text-slate-700 font-medium">
                    / Data Penggajian
                </li>
            </ul>
        </div>
        
        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">
            <div class="flex items-center justify-between mb-6">
                <form method="GET" action="{{ route('hr/penggajian/page') }}" class="flex gap-3 items-center">
                    <label class="text-sm font-medium text-slate-600">Filter Periode:</label>
                    <input type="month" name="periode" value="{{ $periode }}" class="px-4 py-2 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] w-48 shadow-sm" onchange="this.form.submit()">
                </form>
                
                <form action="{{ route('hr/penggajian/generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Generate Slip Gaji
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table id="alternativePagination" class="w-full whitespace-nowrap" style="width:100%">
                    <thead>
                        <tr class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase border-b border-gray-200">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Karyawan</th>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3">Gaji Pokok</th>
                            <th class="px-4 py-3">Gaji Bersih</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($penggajianList as $key => $penggajian)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3">{{ ++$key }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $penggajian->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $penggajian->periode }}</td>
                                <td class="px-4 py-3 text-slate-600">Rp. {{ number_format($penggajian->gaji_pokok, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 font-bold text-[#4F6560]">Rp. {{ number_format($penggajian->gaji_bersih, 2, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @if($penggajian->status == 'draft')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($penggajian->status) }}</span>
                                    @elseif($penggajian->status == 'diproses')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ ucfirst($penggajian->status) }}</span>
                                    @elseif($penggajian->status == 'dibayar')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($penggajian->status) }}</span>
                                    @else
                                        <span>{{ ucfirst($penggajian->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('hr/penggajian/show', $penggajian->id) }}" title="Lihat Slip" class="flex items-center justify-center rounded-lg size-8 text-[#4F6560] bg-[#80BB9B]/20 hover:text-white hover:bg-[#4F6560] transition shadow-sm">
                                            <i data-lucide="eye" class="size-4"></i>
                                        </a>
                                        <form action="{{ route('hr/penggajian/bayar') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $penggajian->id }}">
                                            <button type="submit" title="Tandai Dibayar" class="flex items-center justify-center text-green-500 transition-all duration-200 ease-linear bg-green-100 rounded-lg size-8 hover:text-white hover:bg-green-500 shadow-sm">
                                                <i data-lucide="check" class="size-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="receipt" class="w-8 h-8 text-slate-300 mb-2"></i>
                                        <p>Tidak ada data penggajian</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('alternativePagination')) {
        new DataTable('#alternativePagination', {
            pagingType: 'full_numbers',
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                paginate: {
                    first: 'Pertama', last: 'Terakhir',
                    next: 'Selanjutnya', previous: 'Sebelumnya'
                },
                emptyTable: 'Tidak ada data'
            }
        });
    }
});
</script>
@endpush
@endsection
