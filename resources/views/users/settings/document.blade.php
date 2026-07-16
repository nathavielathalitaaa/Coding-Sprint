@extends('layouts.master')

@section('title', 'Pengaturan Dokumen — SIMORA')

@section('content')
<div class="py-4 px-4 sm:px-0">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-sans font-bold text-[#111111] break-words">Pengaturan Tanda Tangan Dokumen</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1 break-words">Konfigurasikan logo, nama organisasi, warna, dan font untuk dokumen yang dihasilkan sistem.</p>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-6 px-4 py-3 relative text-base text-green-800 bg-green-50 rounded-lg break-words" role="alert">
            {{ $message }}
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-6 px-4 py-3 relative text-base text-red-800 bg-red-50 rounded-lg break-words" role="alert">
            {{ $message }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Kiri: Pengaturan Teks & Warna --}}
        <div class="lg:col-span-2 w-full">
            <form action="{{ route('users.settings.document.update') }}" method="POST" class="w-full">
                @csrf
                <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-4 sm:p-6 w-full">
                    <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-100">
                        <i data-lucide="file-text" class="w-5 h-5 text-[var(--color-primary)] shrink-0"></i>
                        <h6 class="text-base font-bold text-slate-800">Branding & Teks Dokumen</h6>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Nama Organisasi / Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-[9999px] text-sm text-gray-800 focus:outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-red-100 transition" required>
                        @error('company_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div class="w-full">
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Warna Aksen (Hex) <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2 w-full">
                                <input type="color" name="accent_color" value="{{ old('accent_color', $settings['accent_color']) }}" class="h-10 w-10 shrink-0 rounded-full border border-gray-200 cursor-pointer p-0" required oninput="document.getElementById('accent_hex').value = this.value">
                                <input type="text" id="accent_hex" value="{{ old('accent_color', $settings['accent_color']) }}" class="flex-1 min-w-0 px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-[9999px] text-sm text-gray-500 uppercase font-mono" disabled>
                            </div>
                            @error('accent_color')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="w-full">
                            <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Jenis Font <span class="text-red-500">*</span></label>
                            <select name="font_family" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-[9999px] text-sm text-gray-800 focus:outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-red-100 transition" required>
                                <option value="Arial" {{ $settings['font_family'] === 'Arial' ? 'selected' : '' }}>Arial (Sans-serif)</option>
                                <option value="Times New Roman" {{ $settings['font_family'] === 'Times New Roman' ? 'selected' : '' }}>Times New Roman (Serif)</option>
                                <option value="Helvetica" {{ $settings['font_family'] === 'Helvetica' ? 'selected' : '' }}>Helvetica (Sans-serif)</option>
                                <option value="Georgia" {{ $settings['font_family'] === 'Georgia' ? 'selected' : '' }}>Georgia (Serif)</option>
                            </select>
                            @error('font_family')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Teks Kaki / Footer (Opsional)</label>
                        <textarea name="footer_text" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-[20px] text-sm text-gray-800 focus:outline-none focus:border-[var(--color-primary)] focus:ring-2 focus:ring-red-100 transition">{{ old('footer_text', $settings['footer_text']) }}</textarea>
                        @error('footer_text')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full justify-center text-center flex items-center gap-2 py-3 bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white font-semibold rounded-[9999px] shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4 shrink-0"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        {{-- Kanan: Upload Logo --}}
        <div class="lg:col-span-1 w-full">
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-4 sm:p-6 w-full">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-100">
                    <i data-lucide="image" class="w-5 h-5 text-[var(--color-primary)] shrink-0"></i>
                    <h6 class="text-base font-bold text-slate-800">Logo Dokumen</h6>
                </div>

                <div class="mb-6 text-center">
                    @if($settings['logo_path'])
                        <div class="p-4 border border-dashed border-gray-200 rounded-[20px] bg-slate-50 flex flex-col items-center justify-center min-h-[140px] gap-2 overflow-hidden">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['logo_path']) }}" alt="Logo Dokumen" class="max-w-full max-h-[90px] object-contain">
                            <span class="text-[10px] text-gray-400">Logo terpasang</span>
                        </div>
                    @else
                        <div class="p-4 border border-dashed border-gray-200 rounded-[20px] bg-slate-50 flex items-center justify-center min-h-[140px] overflow-hidden">
                            <p class="text-xs text-gray-400 font-light">Belum ada logo terpasang</p>
                        </div>
                    @endif
                </div>

                <form action="{{ route('users.settings.document.logo') }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Pilih Logo Baru</label>
                        <div class="relative w-full">
                            <input type="file" name="logo" id="logo_input" accept="image/png, image/jpeg, image/jpg" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" required onchange="document.getElementById('file_name_label').textContent = this.files[0] ? this.files[0].name : 'Pilih file logo...'">
                            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-[9999px] text-xs text-gray-500 hover:bg-gray-100 transition text-center justify-center overflow-hidden">
                                <i data-lucide="upload" class="w-4 h-4 text-red-500 shrink-0"></i>
                                <span id="file_name_label" class="truncate flex-1 text-left">Pilih file logo...</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2">Format: PNG, JPG (Max 2MB)</p>
                        @error('logo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full justify-center text-center flex items-center gap-2 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-[9999px] transition">
                        <i data-lucide="upload" class="w-4 h-4 shrink-0"></i> Unggah Logo
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
