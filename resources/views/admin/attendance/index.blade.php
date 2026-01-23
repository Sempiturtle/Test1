@extends('layouts.admin')
@section('title', 'Attendance Logs')

@section('content')
<div class="space-y-6">

    <!-- RFID TAP FORM -->
    <form id="simulateForm" action="javascript:void(0)" class="bg-white shadow-lg p-6 rounded-2xl border w-full max-w-md">
        @csrf
        <h2 class="text-xl font-semibold text-indigo-700 mb-2">RFID Tap</h2>

        <input
            type="text"
            id="rfid_uid"
            placeholder="Tap RFID Card"
            autofocus
            autocomplete="off"
            class="w-full p-3 rounded-lg border focus:ring-2 focus:ring-indigo-500"
        >
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
                        <span class="px-2 py-1 text-xs rounded
                            {{ $log->time_out ? 'bg-green-600 text-white' : 'bg-yellow-500 text-black' }}">
                            {{ $log->time_out ? 'Completed' : 'In Session' }}
                        </span>
                    </td>
                    <td class="py-2">
                        {{ $log->duration_minutes ? $log->duration_minutes.' mins' : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- TOAST -->
<div id="toast"
    class="fixed bottom-5 right-5 hidden px-4 py-3 rounded-lg text-white shadow-lg">
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {



    const form = document.getElementById('simulateForm');
    const input = document.getElementById('rfid_uid');
    const body  = document.getElementById('attendanceBody');

    // 🔥 PREVENT PAGE RELOAD (CRITICAL)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitRFID();
    });

    let isSubmitting = false;

function submitRFID() {

    // 🚫 BLOCK MULTIPLE SENDS
    if (isSubmitting) return;

    const uid = input.value.trim();
    if (!uid) return;

    isSubmitting = true; // 🔒 LOCK

    fetch("{{ route('admin.attendance.simulate') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ rfid_uid: uid })
    })
    .then(res => res.json())
    .then(res => {
        showToast(res.message, res.status);

        input.value = '';
        input.focus();

        if (res.attendance) {
            updateAttendanceRow(res.attendance);
        }
    })
    .catch(() => {
        showToast('Something went wrong', 'error');
    })
    .finally(() => {
        // ⏱ small delay to absorb scanner double-enter
        setTimeout(() => {
            isSubmitting = false;
        }, 800); // adjust if needed
    });
}


    function updateAttendanceRow(attendance) {
        const rowId = 'attendance-' + attendance.id;
        const existingRow = document.getElementById(rowId);

        const statusClass = attendance.time_out
            ? 'bg-green-600 text-white'
            : 'bg-yellow-500 text-black';

        const statusText = attendance.time_out ? 'Completed' : 'In Session';

        const html = `
            <td class="py-2">${attendance.student_name}</td>
            <td class="py-2 font-mono">${attendance.rfid_uid}</td>
            <td class="py-2">${attendance.time_in}</td>
            <td class="py-2">${attendance.time_out ?? '—'}</td>
            <td class="py-2">
                <span class="px-2 py-1 text-xs rounded ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="py-2">
                ${attendance.duration_minutes ? attendance.duration_minutes + ' mins' : '—'}
            </td>
        `;

        if (existingRow) {
            existingRow.innerHTML = html;
        } else {
            const row = document.createElement('tr');
            row.id = rowId;
            row.className = 'border-b hover:bg-indigo-50';
            row.innerHTML = html;
            body.prepend(row);
        }
    }

    function showToast(message, status) {
        const toast = document.getElementById('toast');

        toast.textContent = message;
        toast.className = `
            fixed bottom-5 right-5 px-4 py-3 rounded-lg text-white shadow-lg
            ${status === 'success' ? 'bg-green-600' :
              status === 'error' ? 'bg-red-600' :
              'bg-yellow-500'}
        `;

        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 2500);
    }
});
</script>
@endsection
