@extends('layouts.master')
@section('content')
    
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-xl font-bold text-slate-900">Add Leave (Employee)</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li class="text-slate-400">
                    <a href="#!">Leaves Manage</a>
                </li>
                <li class="text-slate-700 font-medium">/ Add Leave (Employee)</li>
            </ul>
        </div>
        
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-9">
                <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-sm border border-white/40 mb-6">
                    <h6 class="mb-6 text-lg font-bold text-slate-800 border-b border-gray-100 pb-4">Apply Leave</h6>
                    <form id="applyLeave" action="{{ route('hr/create/leave/employee/save') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-12">
                            <div class="xl:col-span-6">
                                <div>
                                    <label for="leave_type" class="block mb-2 text-sm font-bold text-slate-700">Leave Type</label>
                                    <select name="leave_type" id="leave_type" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#80BB9B] focus:outline-none shadow-sm @error('leave_type') border-red-500 @enderror">
                                        <option value="">Select Leave Type</option>
                                        <option value="Medical Leave">Medical Leave</option>
                                        <option value="Casual Leave">Casual Leave</option>
                                        <option value="Sick Leave">Sick Leave</option>
                                        <option value="Annual Leave">Annual Leave</option>
                                    </select>
                                    @error('leave_type')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="xl:col-span-6">
                                <div>
                                    <label for="remaining_leave" class="block mb-2 text-sm font-bold text-slate-700">Remaining Leaves</label>
                                    <input type="text" name="remaining_leave" id="remaining_leave" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="0" readonly>
                                </div>
                            </div>
                            <div class="xl:col-span-6">
                                <label for="date_from" class="block mb-2 text-sm font-bold text-slate-700">From</label>
                                <input type="text" name="date_from" id="date_from" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#80BB9B] focus:outline-none shadow-sm @error('date_from') border-red-500 @enderror" placeholder="Select date" data-provider="flatpickr" data-date-format="d M, Y">
                                @error('date_from')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="xl:col-span-6">
                                <label for="date_to" class="block mb-2 text-sm font-bold text-slate-700">To</label>
                                <input type="text" name="date_to" id="date_to" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#80BB9B] focus:outline-none shadow-sm @error('date_to') border-red-500 @enderror" placeholder="Select date" data-provider="flatpickr" data-date-format="d M, Y">
                                @error('date_to')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="xl:col-span-6" id="leave_dates_display" style="display: none"></div>
                            <div class="xl:col-span-6" id="select_leave_day" style="display: none"></div>

                            <div class="xl:col-span-12">
                                <div>
                                    <label for="number_of_day" class="block mb-2 text-sm font-bold text-slate-700">Number of Days</label>
                                    <input type="text" name="number_of_day" id="number_of_day" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-slate-50 text-slate-600 shadow-sm" value="0" readonly>
                                </div>
                            </div>
                            <div id="leave_day_select" class="xl:col-span-12" style="display: block">
                                <label for="leave_day" class="block mb-2 text-sm font-bold text-slate-700">Leave Day</label>
                                <select name="select_leave_day[]" id="leave_day" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#80BB9B] focus:outline-none shadow-sm">
                                    <option value="Full-Day Leave">Full-Day Leave</option>
                                    <option value="Half-Day Morning Leave">Half-Day Morning Leave</option>
                                    <option value="Half-Day Afternoon Leave">Half-Day Afternoon Leave</option>
                                    <option value="Public Holiday">Public Holiday</option>
                                    <option value="Off Schedule">Off Schedule</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 xl:col-span-12">
                                <div>
                                    <label for="reason" class="block mb-2 text-sm font-bold text-slate-700">Reason</label>
                                    <textarea name="reason" class="w-full px-4 py-2 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#80BB9B] focus:outline-none shadow-sm @error('reason') border-red-500 @enderror" rows="3"></textarea>
                                    @error('reason')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                            <button type="reset" id="reset_btn" class="px-6 py-2 border border-red-200 text-red-500 bg-white hover:bg-red-50 rounded-xl text-sm font-bold transition shadow-sm">Reset</button>
                            <button type="submit" id="apply_leave" class="px-6 py-2 bg-[#4F6560] hover:bg-[#3d504c] text-white rounded-xl text-sm font-bold transition shadow-sm">Apply Leave</button>
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
    <script>
        // Define the URL for the AJAX request
        var url = "{{ route('hr/get/information/leave') }}";
        
        // Function to handle leave type change
        function handleLeaveTypeChange() {
            var leaveType   = $('#leave_type').val();
            var numberOfDay = $('#number_of_day').val();    
            $.post(url, {
                leave_type: leaveType,
                number_of_day: numberOfDay,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                if (data.response_code == 200) {
                    $('#remaining_leave').val(data.leave_type);
                }
            }, 'json');
        }
        
        function countLeaveDays()
        {
            var dateFrom = new Date($('#date_from').val());
            var dateTo = new Date($('#date_to').val());
            var leaveDay = $('#leave_day').val();

            if (!isNaN(dateFrom) && !isNaN(dateTo)) {
                var numDays = Math.ceil((dateTo - dateFrom) / (1000 * 3600 * 24)) + 1;
                if (leaveDay.includes('Half-Day')) numDays -= 0.5;
                
                $('#number_of_day').val(numDays);
                updateRemainingLeave(numDays);

                // Clear previous display
                $('#leave_dates_display').empty();
                $('#select_leave_day').empty();

                // Display each date one by one if numDays > 0
                if (numDays > 0) {
                    for (let d = 0; d < numDays; d++) {
                        let currentDate = new Date(dateFrom);
                        currentDate.setDate(currentDate.getDate() + d);
                        var formattedDate = currentDate.getDate() + ' ' + (currentDate.getMonth() + 1) + ',' + currentDate.getFullYear();

                        document.getElementById('leave_day_select').style.display = 'block'; // or 'flex', depending on your layout
                        // Append each leave date to the display
                        if (numDays > 1) {
                            document.getElementById('leave_dates_display').style.display = 'block'; // or 'flex', depending on your layout
                            document.getElementById('select_leave_day').style.display = 'block'; // or 'flex', depending on your layout

                            const inputDate = formattedDate;
                            let [day, month, year] = inputDate.split(/[\s,]+/);
                            let date = new Date(year, month - 1, day - 1);
                            let formattedDateConvert = currentDate.getDate() + ' ' + currentDate.toLocaleString('en-GB', { month: 'short' }) + ', ' + currentDate.getFullYear();

                            // Create unique IDs for inputs and labels
                            let leaveDateInputId = `leave_date_${d}`;

                            // Append each leave date to the display
                            $('#leave_dates_display').append(`
                                <label for="${leaveDateInputId}" class="inline-block mb-2 text-base font-medium">Leave Date ${d+1}</label>
                                <input type="text" id="${leaveDateInputId}" name="leave_date[]" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" value="${formattedDateConvert}" data-provider="flatpickr" data-date-format="d M, Y" readonly="">
                            `);
                            // Function to generate leave day select elements
                            function generateLeaveDaySelects(numDays) {
                                $('#select_leave_day').empty(); // Clear existing elements
                                for (let d = 0; d < numDays; d++) {
                                    let leaveDayId = `leave_day_${d}`;
                                    document.getElementById('leave_day_select').style.display = 'none'; // or 'flex', depending on your layout
                                    $('#select_leave_day').append(`
                                        <label for="${leaveDayId}" class="inline-block mb-2 text-base font-medium">Leave Day ${d+1}</label>
                                        <select id="${leaveDayId}" name="select_leave_day[]" class="form-input border-slate-200 focus:outline-none focus:border-custom-500">
                                            <option value="Full-Day Leave">Full-Day Leave</option>
                                            <option value="Half-Day Morning Leave">Half-Day Morning Leave</option>
                                            <option value="Half-Day Afternoon Leave">Half-Day Afternoon Leave</option>
                                            <option value="Public Holiday">Public Holiday</option>
                                            <option value="Off Schedule">Off Schedule</option>
                                        </select>
                                    `);
                                }
                            }

                            // Call this function when you need to set up the dropdowns
                            generateLeaveDaySelects(numDays);

                            // Function to update total leave days and remaining leave
                            function updateLeaveDaysAndRemaining() {
                                let totalDays = numDays; // Start with the total number of days
                                for (let d = 0; d < numDays; d++) {
                                    let leaveType = $(`#leave_day_${d}`).val(); // Get the selected leave type
                                    if (leaveType && leaveType.includes('Half-Day')) totalDays -= 0.5;
                                }
                                $('#number_of_day').val(totalDays);
                                // Update remaining leave
                                updateRemainingLeave(totalDays);
                            }

                            // Event listener for leave day selection change
                            $(document).on('change', '[id^="leave_day"]', updateLeaveDaysAndRemaining);

                            // Initial setup
                            updateLeaveDaysAndRemaining();
                        } else {
                            $('#leave_dates_display').hide();
                            $('#select_leave_day').hide();
                        }
                    }
                    
                }
            } else {
                $('#number_of_day').val('0');
                $('#leave_dates_display').text(''); // Clear the display in case of invalid dates
                $('#select_leave_day').text(''); // Clear the display in case of invalid dates
            }
        }
            
        // Function to update remaining leave
        function updateRemainingLeave(numDays) {
            $.post(url, {
                number_of_day: numDays,
                leave_type: $('#leave_type').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                if (data.response_code == 200) {
                    $('#remaining_leave').val(data.leave_type);
                    $('#apply_leave').prop('disabled', data.leave_type <= 0);
                    if (data.leave_type < 0) {
                        toastr.info('You cannot apply for leave at this time.');
                    }
                }
            }, 'json');
        }
        
        // Event listeners
        $(document).on('change', '#leave_type', handleLeaveTypeChange);
        $(document).on('change', '#date_from, #date_to, #leave_day', countLeaveDays);

        // reset data in form
        $(document).on('click', '#reset_btn', function() {
            // Clear the leave dates display
            $('#leave_dates_display').empty();
            // Clear the select leave day display
            $('#select_leave_day').empty();
            // Reset other relevant fields
            $('#number_of_day').val('');
            $('#date_from').val('');
            $('#date_to').val('');
            $('#leave_type').val(''); // Reset to default value if needed
            $('#remaining_leave').val('');
            // Optionally hide any UI elements
            $('#leave_day_select').hide(); // or reset to its original state
        });

    </script>
@endsection
@endsection

