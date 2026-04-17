@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">detail slip gaji</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('hr/penggajian/page') }}" class="text-slate-400 dark:text-zink-200">data penggajian</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        detail slip gaji
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <h6 class="text-15">slip gaji {{ $penggajian->user->name ?? '-' }}</h6>
                        <button onclick="window.print()" class="text-white btn bg-blue-500 border-blue-500 hover:text-white hover:bg-blue-600 hover:border-blue-600">cetak</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- informasi karyawan -->
                        <div>
                            <div class="border-b pb-4 mb-4">
                                <h6 class="text-14 font-semibold mb-3">informasi karyawan</h6>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">nama</span>
                                        <span class="font-medium">{{ $penggajian->user->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">nip</span>
                                        <span class="font-medium">{{ $penggajian->user->nip ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">departemen</span>
                                        <span class="font-medium">{{ $penggajian->user->department ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">jabatan</span>
                                        <span class="font-medium">{{ $penggajian->user->designation ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- informasi periode -->
                        <div>
                            <div class="border-b pb-4 mb-4">
                                <h6 class="text-14 font-semibold mb-3">informasi periode</h6>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">periode</span>
                                        <span class="font-medium">{{ $penggajian->periode }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-zink-300">status</span>
                                        <span class="font-medium">
                                            @if($penggajian->status == 'draft')
                                                <span style="color:#6b7280">draft</span>
                                            @elseif($penggajian->status == 'diproses')
                                                <span style="color:#f59e0b">diproses</span>
                                            @elseif($penggajian->status == 'dibayar')
                                                <span style="color:#22c55e">dibayar</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- rincian gaji -->
                    <div class="border-t pt-6">
                        <h6 class="text-14 font-semibold mb-4">rincian gaji</h6>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-zink-300">gaji pokok</span>
                                <span class="font-medium">Rp. {{ number_format($penggajian->gaji_pokok, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-zink-300">total tunjangan</span>
                                <span class="font-medium">Rp. {{ number_format($penggajian->total_tunjangan, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-zink-300">total potongan</span>
                                <span class="font-medium">Rp. {{ number_format($penggajian->total_potongan, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-3 border-t">
                                <span>gaji bersih</span>
                                <span style="color:#22c55e">Rp. {{ number_format($penggajian->gaji_bersih, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- catatan -->
                    @if($penggajian->catatan)
                        <div class="border-t mt-6 pt-6">
                            <h6 class="text-14 font-semibold mb-2">catatan</h6>
                            <p class="text-sm text-slate-600 dark:text-zink-300">{{ $penggajian->catatan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 print:hidden">
                <a href="{{ route('hr/penggajian/page') }}" class="text-slate-500 bg-white btn hover:text-slate-500 hover:bg-slate-100 focus:text-slate-500 focus:bg-slate-100 active:text-slate-500 active:bg-slate-100 dark:bg-zink-600 dark:hover:bg-zink-500 dark:focus:bg-zink-500 dark:active:bg-zink-500">kembali</a>
            </div>
        </div>
    </div>
    <!-- End Page-content -->
@endsection
