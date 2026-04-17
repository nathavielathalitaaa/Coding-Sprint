@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Buat Surat Baru</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1 before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('surat.index') }}" class="text-slate-400 dark:text-zink-200">Surat</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Buat Surat Baru
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="text-15 mb-4">Form Surat Baru</h6>

                    @if ($errors->any())
                        <div class="mb-4 padding-3 relative text-base text-red-800 bg-red-50 rounded-lg dark:bg-red-900/20 dark:text-red-300">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="jenis_surat" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">Jenis Surat <span class="text-red-500">*</span></label>
                            <select id="jenis_surat" name="jenis_surat" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                <option value="resign" @if(old('jenis_surat') === 'resign') selected @endif>Resign</option>
                                <option value="permohonan" @if(old('jenis_surat') === 'permohonan') selected @endif>Permohonan</option>
                                <option value="surat_tugas" @if(old('jenis_surat') === 'surat_tugas') selected @endif>Surat Tugas</option>
                                <option value="rekomendasi" @if(old('jenis_surat') === 'rekomendasi') selected @endif>Rekomendasi</option>
                                <option value="izin" @if(old('jenis_surat') === 'izin') selected @endif>Izin</option>
                                <option value="lainnya" @if(old('jenis_surat') === 'lainnya') selected @endif>Lainnya</option>
                            </select>
                            @error('jenis_surat')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="perihal" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">Perihal <span class="text-red-500">*</span></label>
                            <textarea id="perihal" name="perihal" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" required>{{ old('perihal') }}</textarea>
                            @error('perihal')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="file_pdf" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">File PDF</label>
                            <input type="file" id="file_pdf" name="file_pdf" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100">
                            <p class="text-xs text-slate-500 dark:text-zink-400 mt-1">Format: PDF, Ukuran maksimal: 5MB</p>
                            @error('file_pdf')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 text-white bg-custom-500 border border-custom-500 rounded-lg hover:bg-custom-600 hover:border-custom-600 dark:ring-custom-400/20">
                                Simpan
                            </button>
                            <a href="{{ route('surat.index') }}" class="px-4 py-2 text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 dark:bg-zink-700 dark:border-zink-500 dark:text-zink-200 dark:hover:bg-zink-600">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
