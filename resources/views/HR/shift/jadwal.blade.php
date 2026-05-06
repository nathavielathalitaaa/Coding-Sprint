@extends('layouts.master')
@section('content')
    
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">jadwal shift</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">manajemen hr</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        jadwal shift
                    </li>
                </ul>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-15 mb-4">assign shift ke karyawan</h6>
                    <form action="{{ route('hr/shift/jadwal/store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="inline-block mb-2 text-base font-medium">pilih karyawan</label>
                                <select name="user_id" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" required>
                                    <option value="">-- pilih karyawan --</option>
                                    @foreach($karyawanList as $karyawan)
                                        <option value="{{ $karyawan->id }}">{{ $karyawan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-base font-medium">pilih shift</label>
                                <select name="shift_id" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" required>
                                    <option value="">-- pilih shift --</option>
                                    @foreach($shiftList as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }} ({{ $shift->jam_masuk }} - {{ $shift->jam_keluar }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-base font-medium">tanggal mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700" required>
                            </div>
                            <div>
                                <label class="inline-block mb-2 text-base font-medium">tanggal selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:text-zink-100 dark:bg-zink-700">
                            </div>
                        </div>
                        <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">simpan jadwal</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="text-15 mb-4">jadwal minggu ({{ \Carbon\Carbon::parse($mulaiMinggu)->format('d M Y') }} - {{ \Carbon\Carbon::parse($akhirMinggu)->format('d M Y') }})</h6>
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left py-2 px-3 border-b">no</th>
                                <th class="text-left py-2 px-3 border-b">nama karyawan</th>
                                <th class="text-left py-2 px-3 border-b">shift</th>
                                <th class="text-left py-2 px-3 border-b">tanggal mulai</th>
                                <th class="text-left py-2 px-3 border-b">tanggal selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalList as $key => $jadwal)
                                <tr class="border-b hover:bg-slate-50 dark:hover:bg-zink-500">
                                    <td class="py-2 px-3">{{ ++$key }}</td>
                                    <td class="py-2 px-3">{{ $jadwal->user->name ?? '-' }}</td>
                                    <td class="py-2 px-3">{{ $jadwal->shift->nama_shift ?? '-' }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($jadwal->tanggal_mulai)->format('d M Y') }}</td>
                                    <td class="py-2 px-3">{{ $jadwal->tanggal_selesai ? \Carbon\Carbon::parse($jadwal->tanggal_selesai)->format('d M Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-slate-500">tidak ada jadwal minggu ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
@endsection
