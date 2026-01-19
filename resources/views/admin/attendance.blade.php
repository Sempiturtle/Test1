@extends('layouts.admin')

@section('title', 'Attendance Logs')

@section('content')
    <div class="space-y-6">

        <!-- Simulate RFID Tap Form -->
        <form id="simulateForm" class="bg-gray-900/20 p-6 rounded-2xl border border-gray-700/40 shadow-md">
            @csrf
            <h2 class="text-lg font-semibold text-indigo-400 mb-4">Simulate RFID Tap</h2>
            <input type="text" id="rfid_uid" placeholder="Scan RFID UID"
                class="w-full p-3 mb-4 rounded-lg bg-gray-800/50 border border-gray-600 text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2 rounded-lg transition">
                Submit
            </button>
            <p id="simulateMessage" class="mt-2 text-sm"></p>
        </form>

        <!-- Attendance Logs Table -->
        <div class="bg-gray-900/20 p-6 rounded-2xl border border-gray-700/40 shadow-md overflow-x-auto">
            <h2 class="text-lg font-semibold text-indigo-400 mb-4">Recent Attendance Logs</h2>
            <table class="min-w-full table-auto text-white">
                <thead class="bg-gray-800/50 text-gray-400 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-2 text-left">Student</th>
                        <th class="px-4 py-2 text-left">RFID</th>
                        <th class="px-4 py-2 text-left">Time In</th>
                        <th class="px-4 py-2 text-left">Time Out</th>
                        <th class="px-4 py-2 text-left">Session</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody">
                    @foreach ($logs as $log)
                        <tr class="border-b border-gray-700 hover:bg-gray-800/30">
                            <td class="px-4 py-2">{{ $log->student->name }}</td>
                            <td class="px-4 py-2 font-mono">{{ $log->rfid_uid }}</td>
                            <td class="px-4 py-2">{{ $log->time_in }}</td>
                            <td class="px-4 py-2">{{ $log->time_out ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $log->session }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- AJAX Script -->
    <script>
        const form = document.getElementById('simulateForm');
        const input = document.getElementById('rfid_uid');
        const message = document.getElementById('simulateMessage');
        const tableBody = document.getElementById('attendanceBody');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch("{{ route('admin.attendance.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        rfid_uid: input.value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    message.textContent = data.message;
                    message.className = data.status === 'success' ? 'text-green-400 mt-2' : 'text-red-400 mt-2';
                    input.value = '';

                    // Refresh table after tap
                    fetch("{{ route('admin.attendance.latestLogs') }}")
                        .then(res => res.json())
                        .then(logs => {
                            tableBody.innerHTML = '';
                            logs.forEach(log => {
                                const tr = document.createElement('tr');
                                tr.className = "border-b border-gray-700 hover:bg-gray-800/30";
                                tr.innerHTML = `
                            <td class="px-4 py-2">${log.student.name}</td>
                            <td class="px-4 py-2 font-mono">${log.rfid_uid}</td>
                            <td class="px-4 py-2">${log.time_in}</td>
                            <td class="px-4 py-2">${log.time_out ?? '-'}</td>
                            <td class="px-4 py-2">${log.session}</td>
                        `;
                                tableBody.appendChild(tr);
                            });
                        });
                });
        });
    </script>
@endsection
