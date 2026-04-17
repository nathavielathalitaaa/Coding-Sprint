@extends('layouts.master')
@section('content')
    <!-- Page-content -->
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Daftar Surat</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1 before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Surat</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Daftar Surat
                    </li>
                </ul>
            </div>

            @if ($message = Session::get('success'))
                <div class="mb-4 padding-3 relative text-base text-green-800 bg-green-50 rounded-lg dark:bg-green-900/20 dark:text-green-300" role="alert">
                    {{ $message }}
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="mb-4 padding-3 relative text-base text-red-800 bg-red-50 rounded-lg dark:bg-red-900/20 dark:text-red-300" role="alert">
                    {{ $message }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <h6 class="text-15 grow">Daftar Surat</h6>
                        <div class="shrink-0">
                            @can('create', App\Models\Surat::class)
                                <a href="{{ route('surat.create') }}" type="button" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="plus" class="lucide lucide-plus inline-block size-4">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>
                                    <span class="align-middle">Buat Surat Baru</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                    <br>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-zink-500 bg-slate-50 dark:bg-zink-700">
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">No</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Nomor Surat</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Jenis Surat</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Perihal</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Status</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Tanggal</th>
                                    <th class="px-3.5 py-2.5 text-left text-slate-700 dark:text-zink-200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($surats as $key => $surat)
                                    <tr class="border-b border-slate-200 dark:border-zink-500 hover:bg-slate-50 dark:hover:bg-zink-700">
                                        <td class="px-3.5 py-2.5">{{ $surats->firstItem() + $key }}</td>
                                        <td class="px-3.5 py-2.5">
                                            <a href="{{ route('surat.show', $surat->id) }}" class="text-custom-500 hover:text-custom-600">{{ $surat->nomor_surat }}</a>
                                        </td>
                                        <td class="px-3.5 py-2.5">{{ $surat->jenis_surat }}</td>
                                        <td class="px-3.5 py-2.5">{{ $surat->perihal }}</td>
                                        <td class="px-3.5 py-2.5">
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
                                        </td>
                                        <td class="px-3.5 py-2.5 text-sm">{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-3.5 py-2.5">
                                            <div class="flex gap-1">
                                                @can('view', $surat)
                                                    <a href="{{ route('surat.show', $surat->id) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-zink-700 dark:text-zink-200 dark:hover:bg-zink-600">
                                                        Lihat
                                                    </a>
                                                @endcan

                                                @if(Auth::user()->hasRole('supervisor') && $surat->status === 'submitted')
                                                    <button type="button" onclick="approveModal('{{ route('surat.approve-supervisor', $surat->id) }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300">
                                                        Setuju
                                                    </button>
                                                    <button type="button" onclick="rejectModal('{{ route('surat.reject-supervisor', $surat->id) }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300">
                                                        Tolak
                                                    </button>
                                                @endif

                                                @if(Auth::user()->hasRole('admin') && $surat->status === 'approved_supervisor')
                                                    <button type="button" onclick="approveModal('{{ route('surat.approve-owner', $surat->id) }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300">
                                                        Setuju
                                                    </button>
                                                    <button type="button" onclick="rejectModal('{{ route('surat.reject-owner', $surat->id) }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300">
                                                        Tolak
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3.5 py-4 text-center text-slate-500 dark:text-zink-400">
                                            Tidak ada data surat
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $surats->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
