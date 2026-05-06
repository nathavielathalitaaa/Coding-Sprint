@extends('layouts.master')
@section('content')
<div class="p-6 bg-gradient-to-br from-[#F6F6F6] via-[#80BB9B]/10 to-[#4F6560]/10 min-h-[calc(100vh-100px)] font-poppins rounded-3xl -m-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-playfair font-bold text-[#1A2B24]">Kelola Flow Approval Surat</h1>
                <p class="text-sm text-gray-500 mt-2">Atur siapa saja yang harus menyetujui tiap jenis surat</p>
            </div>
            <a href="{{ route('hr.approval-flow.reassign') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-full text-sm font-semibold shadow-sm transition">
                <i data-lucide="user-check" class="w-4 h-4"></i>
                Reassign Darurat
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($flows as $type => $flow)
        <div class="bg-white/80 backdrop-blur-md rounded-[24px] shadow-sm p-6 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 border border-white/60">
            <div class="flex items-start justify-between mb-6 pb-4 border-b border-gray-100/60">
                <div>
                    <h3 class="text-lg font-playfair font-bold text-[#1A2B24] flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-[#80BB9B]"></i> {{ $flow['label'] }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Flow Persetujuan</p>
                </div>
                <a href="{{ route('hr.approval-flow.edit', $type) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#80BB9B]/20 text-[#4F6560] rounded-full text-xs font-semibold hover:bg-[#80BB9B]/30 transition-colors">
                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i> Edit
                </a>
            </div>

            @if($flow['steps']->isEmpty())
                <div class="text-center py-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-sm text-slate-400 mb-2">Belum ada flow</p>
                    <a href="{{ route('hr.approval-flow.edit', $type) }}" class="text-sm font-semibold text-[#4F6560] hover:text-[#3d504c] inline-flex items-center gap-1"><i data-lucide="plus" class="w-4 h-4"></i> Tambah flow</a>
                </div>
            @else
                <div class="flex flex-col gap-4">
                @foreach($flow['steps'] as $i => $step)
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 bg-[#80BB9B]/30 text-[#4F6560]">
                        {{ $step->step_order }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-800">{{ $step->label }}</p>
                        <p class="text-xs text-[#80BB9B] font-medium mt-0.5">{{ $step->jabatan }}</p>
                    </div>
                </div>
                @endforeach
                </div>
            @endif
        </div>
        @endforeach
        </div>
    </div>
</div>
@endsection
