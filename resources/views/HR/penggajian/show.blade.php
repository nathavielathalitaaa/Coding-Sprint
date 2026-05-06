@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">Detail Slip Gaji</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="{{ route('hr/penggajian/page') }}">Data Penggajian</a>
                </li>
                <li class="text-slate-700 font-medium">
                    / Detail Slip Gaji
                </li>
            </ul>
        </div>

        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                <h6 class="text-xl font-bold text-slate-800">Slip Gaji {{ $penggajian->user->name ?? '-' }}</h6>
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-bold hover:bg-[#3d504c] shadow-sm transition print:hidden">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                
                <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                    <h6 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 text-[#80BB9B]"></i> Informasi Karyawan</h6>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nama</span>
                            <span class="font-bold text-slate-800">{{ $penggajian->user->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">NIP</span>
                            <span class="font-bold text-slate-800">{{ $penggajian->user->nip ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Departemen</span>
                            <span class="font-bold text-slate-800">{{ $penggajian->user->department ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jabatan</span>
                            <span class="font-bold text-slate-800">{{ $penggajian->user->designation ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                    <h6 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-[#80BB9B]"></i> Informasi Periode</h6>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Periode</span>
                            <span class="font-bold text-slate-800">{{ $penggajian->periode }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Status</span>
                            <span>
                                @if($penggajian->status == 'draft')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                @elseif($penggajian->status == 'diproses')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Diproses</span>
                                @elseif($penggajian->status == 'dibayar')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Dibayar</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#80BB9B]/10 rounded-2xl p-6 border border-[#80BB9B]/20">
                <h6 class="text-lg font-bold text-[#4F6560] mb-6 flex items-center gap-2"><i data-lucide="wallet" class="w-5 h-5"></i> Rincian Gaji</h6>
                <div class="space-y-4">
                    <div class="flex justify-between text-base">
                        <span class="text-slate-600">Gaji Pokok</span>
                        <span class="font-medium text-slate-800">Rp. {{ number_format($penggajian->gaji_pokok, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-slate-600">Total Tunjangan</span>
                        <span class="font-medium text-slate-800">Rp. {{ number_format($penggajian->total_tunjangan, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-slate-600">Total Potongan</span>
                        <span class="font-medium text-red-500">- Rp. {{ number_format($penggajian->total_potongan, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-4 border-t border-[#80BB9B]/30">
                        <span class="text-slate-800">Gaji Bersih</span>
                        <span class="text-[#4F6560]">Rp. {{ number_format($penggajian->gaji_bersih, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($penggajian->catatan)
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h6 class="text-sm font-bold text-slate-800 mb-2 flex items-center gap-2"><i data-lucide="sticky-note" class="w-4 h-4 text-slate-400"></i> Catatan</h6>
                    <p class="text-sm text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $penggajian->catatan }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 print:hidden">
            <a href="{{ route('hr/penggajian/page') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[#4F6560] text-[#4F6560] bg-white/50 hover:bg-white rounded-xl text-sm font-bold transition shadow-sm backdrop-blur">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>
    
@endsection
