@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Detail Surat</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1 before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('surat.index') }}" class="text-slate-400 dark:text-zink-200">Surat</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Detail Surat
                    </li>
                </ul>
            </div>

            @if ($message = Session::get('success'))
                <div class="mb-4 padding-3 relative text-base text-green-800 bg-green-50 rounded-lg dark:bg-green-900/20 dark:text-green-300" role="alert">
                    {{ $message }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="text-15">{{ $surat->nomor_surat }}</h6>
                        <a href="{{ route('surat.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-zink-400 dark:hover:text-zink-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </a>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- Left Column -->
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Nomor Surat</label>
                                <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">{{ $surat->nomor_surat }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Jenis Surat</label>
                                <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">{{ $surat->jenis_surat }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Perihal</label>
                                <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">{{ $surat->perihal }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Status</label>
                                <p class="mt-1">
                                    @if($surat->status === 'submitted')
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Diajukan</span>
                                    @elseif($surat->status === 'approved_supervisor')
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">Approval Supervisor</span>
                                    @elseif($surat->status === 'approved_owner')
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Disetujui Pemilik</span>
                                    @elseif($surat->status === 'rejected')
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Ditolak</span>
                                    @elseif($surat->status === 'revised')
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">Revisi</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Pembuat Surat</label>
                                <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">{{ $surat->user->name ?? '-' }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Tanggal Dibuat</label>
                                <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">{{ $surat->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">File PDF</label>
                                @if($surat->file_pdf)
                                    <p class="mt-1">
                                        @can('download', $surat)
                                            <a href="{{ route('surat.download', $surat->id) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300">
                                                Download PDF
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-500 dark:text-zink-400">File tersedia (akses terbatas)</span>
                                        @endcan
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-slate-500 dark:text-zink-400">Tidak ada file</p>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zink-200">Approval Supervisor</label>
                                @if($surat->approved_by_supervisor)
                                    <p class="mt-1 text-sm text-slate-900 dark:text-zink-100">
                                        {{ $surat->supervisorApproval->name ?? '-' }}
                                        <br>
                                        <span class="text-xs text-slate-500 dark:text-zink-400">{{ $surat->tgl_approved_supervisor?->format('d/m/Y H:i') }}</span>
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-slate-500 dark:text-zink-400">Belum di-approve</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($surat->catatan_revisi)
                        <div class="mt-4 p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-900/50 rounded-lg">
                            <h6 class="text-sm font-medium text-orange-800 dark:text-orange-300 mb-2">Catatan Revisi</h6>
                            <p class="text-sm text-orange-700 dark:text-orange-200">{{ $surat->catatan_revisi }}</p>
                        </div>
                    @endif

                    <!-- Section Tindakan -->
                    @if(Auth::user()->hasRole('supervisor') && $surat->status === 'submitted')
                        <div class="mt-6 p-4 bg-slate-50 dark:bg-zink-700 border border-slate-200 dark:border-zink-600 rounded-lg">
                            <h6 class="text-sm font-medium text-slate-900 dark:text-zink-100 mb-3">Tindakan Supervisor</h6>
                            
                            <div class="mb-3">
                                <form action="{{ route('surat.approve-supervisor', $surat->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg">Setujui</button>
                                </form>
                            </div>

                            <div class="p-3 bg-white dark:bg-zink-800 border border-red-200 dark:border-red-900/30 rounded-lg">
                                <form action="{{ route('surat.reject-supervisor', $surat->id) }}" method="POST">
                                    @csrf
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-300 mb-2">Catatan Revisi</label>
                                    <textarea name="catatan_revisi" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" required></textarea>
                                    @error('catatan_revisi')
                                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                    @enderror
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="px-3 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg">Tolak Surat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasRole('admin') && $surat->status === 'approved_supervisor')
                        <div class="mt-6 p-4 bg-slate-50 dark:bg-zink-700 border border-slate-200 dark:border-zink-600 rounded-lg">
                            <h6 class="text-sm font-medium text-slate-900 dark:text-zink-100 mb-3">Tindakan Owner</h6>
                            
                            <div class="mb-3">
                                <form action="{{ route('surat.approve-owner', $surat->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg">Setujui oleh Owner</button>
                                </form>
                            </div>

                            <div class="p-3 bg-white dark:bg-zink-800 border border-red-200 dark:border-red-900/30 rounded-lg">
                                <form action="{{ route('surat.reject-owner', $surat->id) }}" method="POST">
                                    @csrf
                                    <label class="block text-sm font-medium text-slate-700 dark:text-zink-300 mb-2">Catatan Revisi</label>
                                    <textarea name="catatan_revisi" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg dark:bg-zink-700 dark:border-zink-500 dark:text-zink-100" required></textarea>
                                    @error('catatan_revisi')
                                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                    @enderror
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="px-3 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg">Tolak Surat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Section Revisi Surat (untuk Staff) -->
                    @if($surat->status === 'revised' && Auth::id() === $surat->user_id)
                        <div class="mt-6 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-900/50 rounded-lg">
                            <h6 class="text-sm font-medium text-orange-800 dark:text-orange-300 mb-3">Revisi Surat</h6>
                            <p class="text-sm text-orange-700 dark:text-orange-200 mb-3">Surat Anda ditolak. Silakan revisi berdasarkan catatan di atas, kemudian upload kembali.</p>
                            <a href="{{ route('surat.edit', $surat->id) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded bg-orange-600 text-white hover:bg-orange-700">
                                Revisi Surat
                            </a>
                        </div>
                    @endif

                    <div class="flex gap-2 mt-6">
                        <a href="{{ route('surat.index') }}" class="px-4 py-2 text-slate-700 bg-slate-100 border border-slate-300 rounded-lg hover:bg-slate-200 dark:bg-zink-700 dark:border-zink-500 dark:text-zink-200 dark:hover:bg-zink-600">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
