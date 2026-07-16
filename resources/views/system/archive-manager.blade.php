@extends('layouts.master')

@section('content')
<style>
    .manager-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
</style>

<div class="mb-8 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('system/monitor') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Manajer Arsip</h1>
        </div>
        <p class="text-gray-500 ml-11">Tinjau dan kompres file dokumen lama untuk mengosongkan ruang penyimpanan.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column: Document List Preview --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="manager-card">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-sans font-bold text-[#111111] flex items-center gap-2">
                    <i data-lucide="folder-clock" class="w-5 h-5 text-[var(--color-text)]"></i>
                    Pratinjau Dokumen yang Dapat Diarsipkan
                </h2>
                <span class="px-3 py-1 bg-[var(--color-bg-light)] text-[#E62129] rounded-full text-xs font-semibold">
                    {{ $archivableSurats->count() }} Siap
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="previewTable">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Dokumen</th>
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tipe</th>
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($archivableSurats as $surat)
                        <tr class="hover:bg-gray-50/50 transition group" data-date="{{ $surat->created_at->format('Y-m-d') }}">
                            <td class="py-3 px-4">
                                <span class="text-sm font-medium text-gray-800">{{ $surat->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm font-bold text-[var(--color-text)]">{{ $surat->nomor_surat ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-500 uppercase">{{ str_replace('_', ' ', $surat->jenis_surat) }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                                    <i data-lucide="file-check-2" class="w-3 h-3"></i> Siap
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">Tidak ada dokumen yang dapat diarsipkan.</p>
                                <p class="text-xs mt-1">Semua dokumen sudah diarsipkan atau tidak ada.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div id="noMatchMessage" class="hidden py-8 text-center text-gray-400">
                <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm">Tidak ada dokumen yang cocok dengan rentang tanggal yang dipilih.</p>
            </div>
        </div>
    </div>

    {{-- Right Column: Action Form --}}
    <div class="lg:col-span-1">
        <div class="manager-card sticky top-24">
            <h2 class="text-lg font-sans font-bold text-[#111111] mb-2">Jalankan Pengarsipan</h2>
            <p class="text-sm text-gray-500 mb-6">Pilih rentang tanggal untuk mengompres dokumen menjadi file ZIP. PDF asli akan dihapus dari server.</p>

            <form action="{{ route('system/monitor/archive') }}" method="POST" id="archiveForm">
                @csrf
                <div class="space-y-4 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="start_date" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 text-sm focus:border-[var(--color-text)] focus:ring-1 focus:ring-[var(--color-text)] bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date" required class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 text-sm focus:border-[var(--color-text)] focus:ring-1 focus:ring-[var(--color-text)] bg-white">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Hanya dokumen yang ditampilkan dalam daftar pratinjau yang masuk dalam rentang tanggal yang dipilih yang akan dimasukkan ke dalam arsip.
                    </p>
                </div>

                <button type="submit" id="submitBtn" class="w-full px-4 py-3.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-100 hover:bg-[var(--color-primary-dark)] transition flex items-center justify-center gap-2">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                    Mulai Proses Pengarsipan
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const rows = document.querySelectorAll('#previewTable tbody tr[data-date]');
        const noMatch = document.getElementById('noMatchMessage');
        const table = document.getElementById('previewTable');
        
        function filterTable() {
            const startVal = startDateInput.value;
            const endVal = endDateInput.value;
            
            if (!startVal || !endVal) {
                // Tampilkan semua jika tanggal belum diisi lengkap
                rows.forEach(row => row.style.display = '');
                noMatch.classList.add('hidden');
                table.style.display = '';
                return;
            }

            const start = new Date(startVal);
            start.setHours(0,0,0,0);
            const end = new Date(endVal);
            end.setHours(23,59,59,999);

            let hasVisible = false;

            rows.forEach(row => {
                const rowDate = new Date(row.dataset.date);
                if (rowDate >= start && rowDate <= end) {
                    row.style.display = '';
                    hasVisible = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (hasVisible) {
                noMatch.classList.add('hidden');
                table.style.display = '';
            } else {
                noMatch.classList.remove('hidden');
                table.style.display = 'none';
            }
        }

        startDateInput.addEventListener('change', filterTable);
        endDateInput.addEventListener('change', filterTable);
        
        @if (Session::has('error'))
            alert("{{ Session::get('error') }}");
        @endif
    });
</script>
@endpush
@endsection

