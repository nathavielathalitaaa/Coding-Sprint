@extends('layouts.master')

@section('content')
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                @if(auth()->user()->hasRole('hr'))
                    <h5 class="text-xl font-bold text-slate-900">Daftar Absensi Karyawan</h5>
                    <p class="text-sm text-slate-500 mt-1">Catat dan pantau kehadiran karyawan harian</p>
                @else
                    <h5 class="text-xl font-bold text-slate-900">Rekap Absensi Saya</h5>
                    <p class="text-sm text-slate-500 mt-1">Lihat riwayat kehadiran kamu bulan ini</p>
                @endif
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40">

            <div class="flex items-center mb-6 gap-2">
                @if(auth()->user()->hasRole('hr'))
                    <h6 class="text-lg font-bold text-slate-800 grow">Data Absensi</h6>
                    <div class="shrink-0 flex gap-2">
                        <button onclick="document.getElementById('addAbsensiModal').classList.remove('hidden')" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Absensi
                        </button>
                        <a href="{{ route('hr/absensi/import') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[#4F6560] text-[#4F6560] bg-white/50 hover:bg-white rounded-xl text-sm font-bold transition shadow-sm backdrop-blur">
                            <i data-lucide="file-up" class="w-4 h-4"></i> Import Excel
                        </a>
                    </div>
                @else
                    <h6 class="text-lg font-bold text-slate-800 grow">Riwayat Absensi</h6>
                @endif
            </div>

            <form method="GET" action="{{ route('hr/absensi/page') }}" class="mb-6">
                <div class="flex gap-3 items-center">
                    <label class="text-sm font-medium text-slate-600">Filter Bulan:</label>
                    <input type="month" name="bulan" value="{{ $bulan }}" class="px-4 py-2 rounded-xl border border-gray-200 bg-white/70 focus:outline-none focus:ring-2 focus:ring-[#80BB9B] w-48 shadow-sm" onchange="this.form.submit()">
                </div>
            </form>

            <div class="overflow-x-auto">
                <table id="alternativePagination" class="display" style="width:100%">
                    <thead><tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        @if(auth()->user()->hasRole('hr'))
                            <th>Aksi</th>
                        @endif
                    </tr></thead>
                    <tbody>
                        @forelse($absensiList as $key => $absensi)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td class="font-medium text-slate-800">{{ $absensi->user->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }}</td>
                                <td>{{ $absensi->jam_masuk ?? '-' }}</td>
                                <td>{{ $absensi->jam_keluar ?? '-' }}</td>
                                <td>
                                    @if($absensi->status == 'hadir')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($absensi->status) }}</span>
                                    @elseif($absensi->status == 'sakit')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ ucfirst($absensi->status) }}</span>
                                    @elseif($absensi->status == 'alpha')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($absensi->status) }}</span>
                                    @elseif($absensi->status == 'izin')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($absensi->status) }}</span>
                                    @elseif($absensi->status == 'cuti')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ ucfirst($absensi->status) }}</span>
                                    @else
                                        <span>{{ ucfirst($absensi->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-slate-500">{{ $absensi->keterangan ?? '-' }}</td>
                                @if(auth()->user()->hasRole('hr'))
                                    <td>
                                        <a href="javascript:void(0)" onclick="deleteAbsensi({{ $absensi->id }})"
                                           class="inline-flex items-center justify-center rounded-lg size-8 bg-red-50 text-red-500 hover:text-white hover:bg-red-500 transition shadow-sm">
                                            <i data-lucide="trash-2" class="size-4"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('hr') ? 8 : 7 }}"
                                    class="text-center py-8 text-slate-500">
                                    <i data-lucide="calendar-x" class="size-8 mx-auto mb-2 text-slate-300"></i>
                                    Tidak ada data absensi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

@if(auth()->user()->hasRole('hr'))
<div id="addAbsensiModal" modal-center="" class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
    <div class="w-screen md:w-[30rem] bg-white shadow rounded-md dark:bg-zink-600">
        <div class="flex items-center justify-between p-6 border-b dark:border-zink-500">
            <h5 class="text-lg font-bold text-slate-900">Input Absensi Manual</h5>
            <button type="button" onclick="document.getElementById('addAbsensiModal').classList.add('hidden')" class="transition-all duration-200 ease-linear text-slate-400 hover:text-red-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="max-h-[calc(theme('height.screen')_-_180px)] p-6 overflow-y-auto">
            <form action="{{ route('hr/absensi/store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Karyawan</label>
                        <select name="user_id" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700 w-full" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="inline-block mb-2 text-base font-medium">tanggal</label>
                        <input type="date" name="tanggal" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" required>
                    </div>
                    <div>
                        <label class="inline-block mb-2 text-base font-medium">jam masuk</label>
                        <input type="time" name="jam_masuk" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700">
                    </div>
                    <div>
                        <label class="inline-block mb-2 text-base font-medium">jam keluar</label>
                        <input type="time" name="jam_keluar" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700">
                    </div>
                    <div>
                        <label class="inline-block mb-2 text-base font-medium">status</label>
                        <select name="status" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" required>
                            <option value="">-- pilih status --</option>
                            <option value="hadir">hadir</option>
                            <option value="izin">izin</option>
                            <option value="sakit">sakit</option>
                            <option value="alpha">alpha</option>
                            <option value="cuti">cuti</option>
                        </select>
                    </div>
                    <div>
                        <label class="inline-block mb-2 text-base font-medium">keterangan</label>
                        <textarea name="keterangan" class="hivi-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" rows="3"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('addAbsensiModal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Simpan Absensi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('alternativePagination')) return;
    var isHr = {{ auth()->user()->hasRole('hr') ? 'true' : 'false' }};
    new DataTable('#alternativePagination', {
        pagingType: 'full_numbers',
        columnDefs: [{ orderable: false, targets: [0, isHr ? 7 : 6] }],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            paginate: { first:'Pertama', last:'Terakhir', next:'Selanjutnya', previous:'Sebelumnya' },
            emptyTable: 'Tidak ada data'
        }
    });
});
@if(auth()->user()->hasRole('hr'))
function deleteAbsensi(id) {
    if (!confirm('Yakin ingin menghapus?')) return;
    fetch('/hr/absensi/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
    }).then(r => r.json()).then(() => location.reload()).catch(() => location.reload());
}
@endif
</script>
@endpush

@endsection