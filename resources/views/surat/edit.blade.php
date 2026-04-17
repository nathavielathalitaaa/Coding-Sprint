@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Revisi Surat</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1 before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('surat.index') }}" class="text-slate-400 dark:text-zink-200">Surat</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Revisi Surat
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="text-15 mb-4">Revisi Surat: {{ $surat->nomor_surat }}</h6>

                    @if ($errors->any())
                        <div class="mb-4 padding-3 relative text-base text-red-800 bg-red-50 rounded-lg dark:bg-red-900/20 dark:text-red-300">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4 p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-900/50 rounded-lg">
                        <h6 class="text-sm font-medium text-orange-800 dark:text-orange-300 mb-2">Catatan Revisi</h6>
                        <p class="text-sm text-orange-700 dark:text-orange-200">{{ $surat->catatan_revisi }}</p>
                    </div>

                    <form action="{{ route('surat.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="jenis_surat" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">Jenis Surat</label>
                            <select id="jenis_surat" name="jenis_surat" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" readonly disabled>
                                <option value="{{ $surat->jenis_surat }}" selected>{{ $surat->jenis_surat }}</option>
                            </select>
                            <p class="text-xs text-slate-500 dark:text-zink-400 mt-1">Jenis surat tidak dapat diubah saat revisi</p>
                        </div>

                        <div class="mb-4">
                            <label for="perihal" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">Perihal</label>
                            <textarea id="perihal" name="perihal" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" readonly disabled>{{ $surat->perihal }}</textarea>
                            <p class="text-xs text-slate-500 dark:text-zink-400 mt-1">Perihal tidak dapat diubah saat revisi</p>
                        </div>

                        <div class="mb-4">
                            <label for="file_pdf" class="block text-sm font-medium text-slate-700 dark:text-zink-200 mb-2">File PDF Baru <span class="text-red-500">*</span></label>
                            <input type="file" id="file_pdf" name="file_pdf" accept=".pdf" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" required>
                            <p class="text-xs text-slate-500 dark:text-zink-400 mt-1">Format: PDF, Ukuran maksimal: 5MB</p>
                            @error('file_pdf')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 text-white bg-orange-600 border border-orange-600 rounded-lg hover:bg-orange-700 hover:border-orange-700 dark:ring-orange-400/20">
                                Kirim Revisi
                            </button>
                            <a href="{{ route('surat.show', $surat->id) }}" class="px-4 py-2 text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 dark:bg-zink-700 dark:border-zink-500 dark:text-zink-200 dark:hover:bg-zink-600">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
