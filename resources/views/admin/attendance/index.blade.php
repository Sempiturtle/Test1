@extends('layouts.admin')
@section('title', 'Attendance Logs')

@section('content')
    <div class="space-y-6">

        <!-- RFID Tap Form -->
        <form id="simulateForm"
            class="bg-white shadow-lg p-6 rounded-2xl border border-gray-200 flex flex-col gap-4 w-full max-w-md">
            @csrf
            <h2 class="text-xl font-semibold text-indigo-700">RFID Tap (Real-Time)</h2>
            <input type="text" id="rfid_uid" autofocus placeholder="Tap RFID Card"
                class="w-full p-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-lg transition-all">
                Submit
            </button>
            <p id="simulateMessage" class="text-sm mt-1"></p>
        </form>

        <!-- Attendance Logs Table -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-x-auto">
            <h2 class="text-lg font-semibold text-indigo-700 mb-4 p-4">Attendance Logs</h2>
            <table class="w-full text-sm rounded-lg overflow-hidden">
                <thead class="bg-indigo-50 text-gray-700 border-b border-gray-200">
                    <tr>
                        <th class="p-3 text-left">Student</th>
                        <th class="p-3 text-left">RFID</th>
                        <th class="p-3 text-left">Time In</th>
                        <th class="p-3 text-left">Time Out</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Duration</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody">
                    @foreach ($logs as $log)
                        <tr id="attendance-{{ $log->id }}" class="border-b hover:bg-indigo-50">
                            <td class="py-2">{{ $log->student->name }}</td>
                            <td class="py-2 font-mono">{{ $log->rfid_uid }}</td>
                            <td class="py-2">{{ optional($log->time_in)->format('h:i A') }}</td>
                            <td class="py-2">{{ $log->time_out ? $log->time_out->format('h:i A') : '—' }}</td>
                            <td class="py-2">
                                <span
                                    class="px-2 py-1 text-xs rounded {{ $log->time_out ? 'bg-green-600 text-white' : 'bg-yellow-500 text-black' }}">
                                    {{ $log->time_out ? 'Completed' : 'In Session' }}
                                </span>
                            </td>
                            <td class="py-2">{{ $log->duration_minutes ? $log->duration_minutes . ' mins' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const form = document.getElementById('simulateForm');
        const input = document.getElementById('rfid_uid');
        const message = document.getElementById('simulateMessage');
        const body = document.getElementById('attendanceBody');

        form.addEventListener('submit', e => {
            e.preventDefault();

            if (!input.value.trim()) {
                message.textContent = "Please tap your RFID card!";
                message.className = "text-red-600 mt-1";
                return;
            }

            fetch("{{ route('admin.attendance.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        rfid_uid: input.value
                    })
                })
                .then(res => res.json())
                .then(res => {
                    message.textContent = res.message;
                    message.className = res.status === 'success' ? 'text-green-600 mt-1' : 'text-red-600 mt-1';
                    input.value = '';
                    input.focus();

                    if (res.attendance) {
                        updateAttendanceRow(res.attendance);
                    }
                });
        });

        function updateAttendanceRow(attendance) {
            const existingRow = document.getElementById('attendance-' + attendance.id);

            const status = attendance.time_out ? 'Completed' : 'In Session';
            const statusClass = attendance.time_out ? 'bg-green-600 text-white' : 'bg-yellow-500 text-black';
            const duration = attendance.duration_minutes ? attendance.duration_minutes + ' mins' : '—';
            const time_in = attendance.time_in ? new Date(attendance.time_in).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            }) : '—';
            const time_out = attendance.time_out ? new Date(attendance.time_out).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            }) : '—';

            if (existingRow) {
                existingRow.innerHTML = `
                <td class="py-2">${attendance.student_name}</td>
                <td class="py-2 font-mono">${attendance.rfid_uid}</td>
                <td class="py-2">${time_in}</td>
                <td class="py-2">${time_out}</td>
                <td class="py-2"><span class="px-2 py-1 text-xs rounded ${statusClass}">${status}</span></td>
                <td class="py-2">${duration}</td>
            `;
            } else {
                const row = document.createElement('tr');
                row.id = 'attendance-' + attendance.id;
                row.className = 'border-b hover:bg-indigo-50';
                row.innerHTML = `
                <td class="py-2">${attendance.student_name}</td>
                <td class="py-2 font-mono">${attendance.rfid_uid}</td>
                <td class="py-2">${time_in}</td>
                <td class="py-2">${time_out}</td>
                <td class="py-2"><span class="px-2 py-1 text-xs rounded ${statusClass}">${status}</span></td>
                <td class="py-2">${duration}</td>
            `;
                body.prepend(row);
            }
        }
    </script>
@endsection
