@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">View Detail Leave (Employee)</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="#!">Leaves Manage</a>
                </li>
                <li class="text-slate-700 font-medium">/ Detail Leave (Employee)</li>
            </ul>
        </div>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-9">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40">
                    <h6 class="mb-6 text-lg font-bold text-slate-800 border-b border-gray-100 pb-4">Detail Leave</h6>
                    <form id="applyLeave">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-12">
                            <div class="xl:col-span-6">
                                <div>
                                    <label for="leaveType" class="block mb-2 text-sm font-bold text-slate-700">Leave Type</label>
                                    <input type="text" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDetail->leave_type }}" disabled>
                                </div>
                            </div>
                            <div class="xl:col-span-6">
                                <div>
                                    <label for="remainingLeaves" class="block mb-2 text-sm font-bold text-slate-700">Remaining Leaves</label>
                                    <input type="text" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDetail->remaining_leave }}" disabled>
                                </div>
                            </div>
                            <div class="xl:col-span-6">
                                <label for="fromInput" class="block mb-2 text-sm font-bold text-slate-700">From</label>
                                <input type="text" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDetail->date_from }}" disabled>
                            </div>
                            <div class="xl:col-span-6">
                                <label for="date_to" class="block mb-2 text-sm font-bold text-slate-700">To</label>
                                <input type="text" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDetail->date_to }}" disabled>
                            </div>
                            @foreach($leaveDate as $key => $date)
                                <div class="xl:col-span-6">
                                    <label for="leave_date_{{ $key }}" class="block mb-2 text-sm font-bold text-slate-700">Leave Date {{ $key + 1 }}</label>
                                    <input type="text" id="leave_date_{{ $key }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $date }}" disabled>
                                </div>
                                <div class="xl:col-span-6">
                                    <label for="leave_day_{{ $key }}" class="block mb-2 text-sm font-bold text-slate-700">Leave Day {{ $key + 1 }}</label>
                                    <input type="text" id="leave_day_{{ $key }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDay[$key] ?? '' }}" disabled>
                                </div>
                            @endforeach
                            <div class="xl:col-span-12">
                                <div>
                                    <label for="number_of_day" class="block mb-2 text-sm font-bold text-slate-700">Number of Days</label>
                                    <input type="text" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="{{ $leaveDetail->number_of_day }}" disabled>
                                </div>
                            </div>
                            <div class="md:col-span-2 xl:col-span-12">
                                <div>
                                    <label for="reason" class="block mb-2 text-sm font-bold text-slate-700">Reason</label>
                                    <textarea name="reason" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" rows="3" disabled>{{ $leaveDetail->reason }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('hr/leave/employee/page') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-[#4F6560] text-[#4F6560] bg-white/50 hover:bg-white rounded-xl text-sm font-bold transition shadow-sm backdrop-blur">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="xl:col-span-3">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-sm border border-white/40 sticky top-24">
                    <h6 class="mb-6 text-base font-bold text-[#4F6560] flex items-center gap-2"><i data-lucide="info" class="w-5 h-5"></i> Leave Info ({{ date('Y') }})</h6>
                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100">
                        <table class="w-full mb-0 text-sm">
                            <tbody class="divide-y divide-gray-100">
                                @foreach($leaveInformation as $key => $value)
                                    <tr>
                                        <td class="py-3 text-slate-600 font-medium">{{ $value->leave_type }}</td>
                                        <th class="py-3 text-right text-slate-900">{{ $value->leave_days }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@section('script')
  
@endsection
@endsection

