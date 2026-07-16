@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-sans font-bold text-[#111111]">Laporan Pertanggungjawaban (LPJ)</h1>
            <p class="text-[13px] font-light text-[#6B7280] mt-1">
                Laporan pertanggungjawaban kegiatan yang telah disahkan dan diarsipkan di database SIMORA.
            </p>
        </div>
        <a href="{{ route('pelaksanaan.index') }}"
           class="px-5 py-2.5 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium text-gray-600 transition shadow-sm">
            Kembali
        </a>
    </div>

    {{-- LPJ Card --}}
    <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-6 md:p-8 space-y-8">
        
        {{-- Header Status --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-50 pb-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    LPJ Valid & Sah
                </span>
                <h2 class="text-2xl font-sans font-bold text-[#1A2B24] mt-2">{{ $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Organisasi: <span class="font-bold text-gray-700">{{ $surat->organisasi->nama ?? '-' }}</span> &bull; 
                    PIC: <span class="font-medium text-gray-700">{{ $surat->picUser->name }}</span>
                </p>
            </div>
            
            {{-- Archive Info --}}
            @if($lpj->archived_at)
                <div class="text-right">
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block">Tanggal Diarsipkan</span>
                    <span class="text-xs font-semibold text-gray-600">{{ $lpj->archived_at->translatedFormat('d M Y H:i') }}</span>
                </div>
            @endif
        </div>

        {{-- Grid Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Left: Ringkasan & Anggaran --}}
            <div class="space-y-6">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Laporan Ringkasan Kegiatan</span>
                    <div class="bg-gray-50 rounded-[28px] p-5 text-xs text-gray-600 leading-relaxed whitespace-pre-line border border-gray-100">
                        {{ $lpj->ringkasan_kegiatan }}
                    </div>
                </div>

                {{-- Anggaran --}}
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Realisasi Anggaran</span>
                    <div class="bg-white border border-gray-100 rounded-[28px] overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                                    <th class="py-3 px-4">Nama Pengeluaran / Item</th>
                                    <th class="py-3 px-4 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-600">
                                @php $totalAnggaran = 0; @endphp
                                @forelse($lpj->realisasi_anggaran ?? [] as $item)
                                    @php $totalAnggaran += $item['jumlah']; @endphp
                                    <tr>
                                        <td class="py-3 px-4">{{ $item['item'] }}</td>
                                        <td class="py-3 px-4 text-right font-semibold text-gray-800">Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 px-4 text-center text-gray-400">Tidak ada pengeluaran anggaran.</td>
                                    </tr>
                                @endforelse
                                @if(count($lpj->realisasi_anggaran ?? []) > 0)
                                    <tr class="bg-emerald-50/20 font-bold text-emerald-800 border-t border-gray-100">
                                        <td class="py-3 px-4">Total Pengeluaran</td>
                                        <td class="py-3 px-4 text-right text-base">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right: Lampiran & Pengesahan --}}
            <div class="space-y-6">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Berkas Lampiran Pendukung</span>
                    <div class="grid grid-cols-1 gap-3">
                        @forelse($lpj->lpjLampirans as $lampiran)
                            <div class="bg-gray-50 border border-gray-100 rounded-[28px] p-4 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-2xl bg-white border border-gray-150 text-gray-500 shrink-0">
                                        @if($lampiran->tipe === 'foto')
                                            <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                                        @elseif($lampiran->tipe === 'video')
                                            <i data-lucide="video" class="w-4 h-4 text-amber-500"></i>
                                        @elseif($lampiran->tipe === 'kwitansi')
                                            <i data-lucide="receipt" class="w-4 h-4 text-emerald-500"></i>
                                        @else
                                            <i data-lucide="file" class="w-4 h-4"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-700 capitalize">{{ $lampiran->tipe }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $lampiran->keterangan ?? 'Tanpa keterangan' }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $lampiran->file_path) }}" download
                                   class="px-4 py-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white rounded-lg font-bold text-[10px] transition">
                                    Unduh File
                                </a>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-gray-400 bg-gray-50/50 border border-dashed border-gray-200 rounded-[28px]">
                                Tidak ada berkas pendukung terlampir.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Sign & Verified By Info --}}
                <div class="pt-6 border-t border-gray-100">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-3">Pengesahan Dokumen</span>
                    <div class="p-4 bg-emerald-50/30 border border-emerald-100 rounded-[28px] flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-emerald-200 flex items-center justify-center font-bold text-[#2E7D5E] shrink-0">
                                <i data-lucide="check-check" class="w-5 h-5"></i>
                            </div>
                            <div class="text-xs">
                                <p class="font-bold text-emerald-800">Diverifikasi & Disahkan</p>
                                <p class="text-gray-500 mt-0.5">Oleh: {{ $lpj->verifiedBy->name ?? 'Pembina' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $lpj->verified_at ? $lpj->verified_at->translatedFormat('d M Y H:i') : '' }}</p>
                            </div>
                        </div>
                        @if($lpj->ttd_path)
                            <div class="text-center">
                                <img src="{{ route('ttd.preview.user', $lpj->verified_by) }}" alt="Tanda Tangan" 
                                     class="h-12 w-auto mx-auto object-contain border border-gray-100 rounded-lg p-1 bg-white">
                                <span class="text-[9px] text-gray-400 block mt-1">Tanda Tangan Digital</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection


