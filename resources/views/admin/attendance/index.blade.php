@extends('layouts.admin')
@section('title', 'Attendance Logs')

@section('content')
    <div class="space-y-6">

        <!-- RFID TAP FORM -->
        <form id="simulateForm" class="bg-white shadow-lg p-6 rounded-2xl border w-full max-w-md">
            @csrf
            <h2 class="text-xl font-semibold text-indigo-700">RFID Tap</h2>
            <input type="text" id="rfid_uid" autofocus placeholder="Tap RFID Card"
                class="w-full p-3 rounded-lg border focus:ring-2 focus:ring-indigo-500">
            <p id="simulateMessage" class="text-sm mt-1"></p>
        </form>

        <!-- Attendance Table -->
        <div class="bg-white shadow-lg rounded-2xl border overflow-x-auto">
            <h2 class="text-lg font-semibold text-indigo-700 p-4">Attendance Logs</h2>
            <table class="w-full text-sm">
                <thead class="bg-indigo-50">
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
                            <td class="py-2">{{ $log->time_in?->format('h:i:s A') }}</td>
                            <td class="py-2">{{ $log->time_out?->format('h:i:s A') ?? '—' }}</td>
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
            const uid = input.value.trim();
            if (!uid) {
                message.textContent = "Please tap your RFID card.";
                message.className = "text-red-600";
                return;
            }

            fetch("{{ route('admin.attendance.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        rfid_uid: uid
                    })
                })
                .then(res => res.json())
                .then(res => {
                    message.textContent = res.message;
                    message.className = res.status === 'success' ? 'text-green-600' : 'text-red-600';
                    input.value = '';
                    input.focus();

                    if (res.attendance) {
                        updateAttendanceRow(res.attendance);
                    }
                });
        });

        function updateAttendanceRow(attendance) {
            const existingRow = document.getElementById('attendance-' + attendance.id);
            const statusClass = attendance.time_out ? 'bg-green-600 text-white' : 'bg-yellow-500 text-black';
            const status = attendance.time_out ? 'Completed' : 'In Session';
            const rowHTML = `
        <td class="py-2">${attendance.student_name}</td>
        <td class="py-2 font-mono">${attendance.rfid_uid}</td>
        <td class="py-2">${attendance.time_in || '—'}</td>
        <td class="py-2">${attendance.time_out || '—'}</td>
        <td class="py-2"><span class="px-2 py-1 text-xs rounded ${statusClass}">${status}</span></td>
        <td class="py-2">${attendance.duration_minutes ? attendance.duration_minutes + ' mins' : '—'}</td>
    `;

            if (existingRow) {
                existingRow.innerHTML = rowHTML;
            } else {
                const row = document.createElement('tr');
                row.id = 'attendance-' + attendance.id;
                row.className = 'border-b hover:bg-indigo-50';
                row.innerHTML = rowHTML;
                body.prepend(row);
            }
        }
    </script>
@endsection
