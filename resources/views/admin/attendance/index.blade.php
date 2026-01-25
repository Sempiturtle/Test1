@extends('layouts.admin')
@section('title', 'Attendance Logs')

@section('content')
<div class="space-y-8">

    <!-- RFID TAP CARD -->
    <div class="max-w-lg">
        <form id="simulateForm" action="javascript:void(0)"
            class="bg-white/80 backdrop-blur-xl border border-gray-200 shadow-xl rounded-2xl p-6">

            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">RFID Attendance</h2>
                    <p class="text-sm text-gray-500">Tap student RFID card</p>
                </div>
            </div>

            @csrf
            <input
                type="text"
                id="rfid_uid"
                autofocus
                autocomplete="off"
                placeholder="Waiting for RFID scan…"
                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3
                       text-gray-900 placeholder-gray-400
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
        </form>
    </div>

    <!-- ATTENDANCE TABLE -->
    <div class="bg-white/90 backdrop-blur-xl border border-gray-200 shadow-xl rounded-2xl overflow-hidden">

        <div class="px-6 py-4 border-b flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Attendance Logs</h2>
                <p class="text-sm text-gray-500">Latest RFID activity</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 text-left">Student</th>
                        <th class="px-6 py-4 text-left">RFID</th>
                        <th class="px-6 py-4 text-left">Time In</th>
                        <th class="px-6 py-4 text-left">Time Out</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Duration</th>
                    </tr>
                </thead>

                <tbody id="attendanceBody" class="divide-y">
                    @foreach ($logs as $log)
                        <tr id="attendance-{{ $log->id }}" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $log->student->name }}
                            </td>

                            <td class="px-6 py-4 font-mono text-gray-700">
                                {{ $log->rfid_uid }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->time_in
                                    ? $log->time_in->timezone('Asia/Manila')->format('h:i:s A')
                                    : '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->time_out
                                    ? $log->time_out->timezone('Asia/Manila')->format('h:i:s A')
                                    : '—' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($log->time_out)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                                                 bg-green-100 text-green-700 text-xs font-semibold">
                                        ● Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                                                 bg-amber-100 text-amber-700 text-xs font-semibold">
                                        ● In Session
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->duration_minutes ? $log->duration_minutes.' mins' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast"
     class="fixed bottom-6 right-6 hidden px-4 py-3 rounded-xl text-white shadow-xl text-sm font-medium">
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('rfid_uid');
    const body = document.getElementById('attendanceBody');
    let locked = false;

    document.getElementById('simulateForm').addEventListener('submit', e => {
        e.preventDefault();
        submitRFID();
    });

    function submitRFID() {
        if (locked) return;
        const uid = input.value.trim();
        if (!uid) return;

        locked = true;

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
            if (res.attendance) updateRow(res.attendance);
        })
        .finally(() => locked = false);
    }

    function updateRow(a) {
        let row = document.getElementById('attendance-' + a.id);

        if (!row) {
            row = document.createElement('tr');
            row.id = 'attendance-' + a.id;
            row.className = 'hover:bg-gray-50 transition';
            body.prepend(row);
        }

        row.innerHTML = `
            <td class="px-6 py-4 font-medium text-gray-900">${a.student_name}</td>
            <td class="px-6 py-4 font-mono text-gray-700">${a.rfid_uid}</td>
            <td class="px-6 py-4 text-gray-700">${a.time_in ?? '—'}</td>
            <td class="px-6 py-4 text-gray-700">${a.time_out ?? '—'}</td>
            <td class="px-6 py-4">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                    ${a.time_out ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'}">
                    ● ${a.time_out ? 'Completed' : 'In Session'}
                </span>
            </td>
            <td class="px-6 py-4 text-gray-700">
                ${a.duration_minutes ? a.duration_minutes + ' mins' : '—'}
            </td>
        `;
    }

    function showToast(msg, status) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = `
            fixed bottom-6 right-6 px-4 py-3 rounded-xl text-white shadow-xl text-sm font-medium
            ${status === 'success' ? 'bg-green-600' :
              status === 'error' ? 'bg-red-600' : 'bg-amber-500'}
        `;
        t.classList.remove('hidden');
        setTimeout(() => t.classList.add('hidden'), 2500);
    }
});
</script>
@endsection
