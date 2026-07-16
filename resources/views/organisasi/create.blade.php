@extends('layouts.master')

@section('title', 'Tambah Sub Organ — SIMORA')

@section('content')
<div class="mb-6">
    <a href="{{ route('organisasi.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-900 transition-colors mb-2">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Kelola Organisasi
    </a>
    <h1 class="text-3xl font-sans font-bold text-[#111111] mt-2">Tambah Sub Organ Baru</h1>
    <p class="text-[13px] font-light text-[#6B7280] mt-1">Sub Organ adalah ekskul, UKM, atau organisasi non-inti yang bernaung di bawah OSIS/MPK.</p>
</div>

<div class="hivi-card max-w-[540px]">
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('organisasi.store') }}">
        @csrf
        <div class="mb-5">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2" for="nama">
                Nama Sub Organ <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nama" name="nama" class="hivi-input" value="{{ old('nama') }}"
                   placeholder="cth: ROHIS, Pramuka, Paduan Suara..." required>
        </div>
        <div class="mb-5">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2" for="deskripsi">
                Deskripsi
            </label>
            <textarea id="deskripsi" name="deskripsi" class="hivi-input" style="border-radius: 20px;" rows="4"
                      placeholder="Deskripsi singkat Sub Organ...">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('organisasi.index') }}" class="hivi-btn-outline">Batal</a>
            <button type="submit" class="hivi-btn-primary">
                <i data-lucide="plus" style="width:16px;height:16px;"></i>
                Buat Sub Organ
            </button>
        </div>
    </form>
</div>
@endsection

