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
            <a href="{{ route('hr/system/monitor') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-3xl font-playfair font-bold text-[#1A2B24]">Archive Manager</h1>
        </div>
        <p class="text-gray-500 ml-11">Review and compress old document files to free up storage space.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left Column: Document List Preview --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="manager-card">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-playfair font-bold text-[#1A2B24] flex items-center gap-2">
                    <i data-lucide="folder-clock" class="w-5 h-5 text-[#4F6560]"></i>
                    Archivable Documents Preview
                </h2>
                <span class="px-3 py-1 bg-[#F0F7F3] text-[#2E7D5E] rounded-full text-xs font-semibold">
                    {{ $archivableSurats->count() }} Ready
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="previewTable">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Document No</th>
                            <th class="py-3 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Type</th>
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
                                <span class="text-sm font-bold text-[#4F6560]">{{ $surat->nomor_surat ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-500 uppercase">{{ str_replace('_', ' ', $surat->jenis_surat) }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                                    <i data-lucide="file-check-2" class="w-3 h-3"></i> Ready
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">No archivable documents found.</p>
                                <p class="text-xs mt-1">All documents are already archived or none exist.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div id="noMatchMessage" class="hidden py-8 text-center text-gray-400">
                <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm">No documents match the selected date range.</p>
            </div>
        </div>
    </div>

    {{-- Right Column: Action Form --}}
    <div class="lg:col-span-1">
        <div class="manager-card sticky top-24">
            <h2 class="text-lg font-playfair font-bold text-[#1A2B24] mb-2">Execute Archive</h2>
            <p class="text-sm text-gray-500 mb-6">Select a date range to compress documents into a ZIP file. The original PDFs will be removed from the server.</p>

            <form action="{{ route('hr/system/monitor/archive') }}" method="POST" id="archiveForm">
                @csrf
                <div class="space-y-4 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="start_date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#4F6560] focus:ring-1 focus:ring-[#4F6560] bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#4F6560] focus:ring-1 focus:ring-[#4F6560] bg-white">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Only documents shown in the preview list that fall within the selected date range will be included in the archive.
                    </p>
                </div>

                <button type="submit" id="submitBtn" class="w-full px-4 py-3.5 bg-[#4F6560] text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-100 hover:bg-[#3d504c] transition flex items-center justify-center gap-2">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                    Start Archive Process
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
