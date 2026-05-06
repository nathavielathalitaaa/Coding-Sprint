@extends('layouts.master')
@section('content')

        <div class="flex items-center justify-between py-6">
            <div>
                <h5 class="text-xl font-bold text-slate-900">Reassign Darurat (Emergency Reassignment)</h5>
                <p class="text-sm text-slate-500 mt-1">Ganti jabatan approver untuk surat yang sedang menunggu persetujuan</p>
            </div>
            <a href="{{ route('hr.approval-flow.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-[#4F6560] text-[#4F6560] bg-white/50 hover:bg-white rounded-xl text-sm font-bold transition shadow-sm backdrop-blur">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100">
                <ul class="text-sm text-red-600 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($surats->count() > 0)
            <div class="bg-white/80 backdrop-blur rounded-3xl overflow-hidden shadow-sm border border-white/40 mb-6">
                
                <div class="p-6 bg-amber-50/80 border-b border-amber-100/50">
                    <div class="flex gap-4 items-start">
                        <div class="shrink-0 mt-0.5">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-500"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-amber-900 mb-1">Emergency Reassignment</h5>
                            <p class="text-sm text-amber-700">Fitur ini digunakan untuk mengganti jabatan approver ketika approver yang ditunjuk tidak tersedia. Gunakan dengan bijak.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @foreach ($surats as $surat)
                        @php
                            $waitingStep = $surat->approvals->where('status', 'waiting')->first();
                        @endphp
                        @if ($waitingStep)
                            <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all duration-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-center">
                                    
                                    <div>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mb-2">Nomor Surat</p>
                                        <a href="{{ route('surat.show', $surat->id) }}" class="text-[#4F6560] hover:text-[#80BB9B] font-bold text-sm truncate block" title="{{ $surat->nomor_surat }}">
                                            {{ $surat->nomor_surat }}
                                        </a>
                                    </div>

                                    <div>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mb-2">Step Menunggu</p>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                            {{ $waitingStep->label }}
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mb-2">Jabatan Saat Ini</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $jabatanOptions[$waitingStep->jabatan] ?? ucfirst($waitingStep->jabatan) }}</p>
                                    </div>

                                    <div class="lg:col-span-2 flex items-center justify-between gap-4">
                                        <div class="flex-1">
                                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mb-2">Aksi Cepat</p>
                                            <form action="{{ route('hr.approval-flow.reassign.apply') }}" method="POST" class="flex gap-2 w-full">
                                                @csrf
                                                <input type="hidden" name="approval_id" value="{{ $waitingStep->id }}">
                                                <select name="jabatan_baru" class="flex-1 px-3 py-1.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white shadow-sm" required>
                                                    <option value="">Pilih Jabatan</option>
                                                    @foreach ($jabatanOptions as $jab => $label)
                                                        @if ($jab !== $waitingStep->jabatan)
                                                            <option value="{{ $jab }}">{{ $label }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition shadow-sm" onclick="return confirm('Ganti jabatan approval ini?')">
                                                    <i data-lucide="shuffle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            
            <div class="bg-white/80 backdrop-blur rounded-3xl p-12 shadow-sm border border-white/40 text-center">
                <div class="mb-4 p-4 rounded-full bg-green-50 inline-flex">
                    <i data-lucide="check-circle" class="w-10 h-10 text-green-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Tidak Ada Surat Menunggu Approval</h3>
                <p class="text-sm text-slate-500 mb-6">Semua surat sudah diproses atau tidak ada antrean persetujuan.</p>
                <a href="{{ route('hr.approval-flow.index') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-[#4F6560] text-[#4F6560] bg-white hover:bg-slate-50 rounded-xl text-sm font-bold transition shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Kelola Flow
                </a>
            </div>
        @endif

@endsection
