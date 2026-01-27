@extends('layouts.admin')
@section('title', 'Attendance Logs')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    .attendance-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .scanner-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .scanner-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }

    .scanner-content {
        position: relative;
        z-index: 1;
    }

    .status-dot {
        animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }

    .table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .status-badge {
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    .modal-backdrop {
        animation: fadeIn 0.2s ease-out;
    }

    .modal-content {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .btn-manual {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.2s ease;
    }

    .btn-manual:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .toast {
        animation: toastSlide 0.3s ease-out;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    @keyframes toastSlide {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    tbody tr {
        transition: all 0.15s ease;
    }

    tbody tr:hover {
        background-color: var(--gray-50);
        transform: translateX(2px);
    }

    .icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="attendance-container space-y-6">

    <!-- RFID SCANNER CARD -->
    <div class="max-w-2xl">
        <div class="scanner-card rounded-2xl p-8 shadow-2xl">
            <div class="scanner-content">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shadow-lg icon-wrapper">
                            <i class="fa-solid fa-id-card text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-1">RFID Scanner</h2>
                            <p class="text-white/80 text-sm font-medium">Place card near reader to log attendance</p>
                        </div>
                    </div>
                    
                    <!-- Manual Entry Button -->
                    <button 
                        id="openModalBtn"
                        type="button"
                        class="btn-manual px-5 py-2.5 text-white text-sm font-semibold rounded-xl flex items-center gap-2.5">
                        <i class="fa-solid fa-keyboard"></i>
                        Manual Entry
                    </button>
                </div>

                <!-- Hidden RFID Input -->
                @csrf
                <input
                    type="text"
                    id="rfid_uid"
                    autofocus
                    autocomplete="off"
                    class="opacity-0 absolute -z-10"
                    style="position: absolute; left: -9999px;"
                />
                
                <!-- Status Indicator -->
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md rounded-xl px-4 py-3 border border-white/20">
                    <div class="status-dot h-3 w-3 rounded-full bg-green-400 shadow-lg shadow-green-400/50"></div>
                    <span class="text-white font-medium text-sm">System Active · Ready to scan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE TABLE -->
    <div class="table-wrapper overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Attendance Records</h2>
                    <p class="text-gray-600 text-sm">Real-time student check-in and check-out logs</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fa-solid fa-clock"></i>
                    <span>Last 20 entries</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">RFID Number</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time In</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time Out</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Duration</th>
                    </tr>
                </thead>

                <tbody id="attendanceBody" class="bg-white divide-y divide-gray-100">
                    @foreach ($logs as $log)
                        <tr id="attendance-{{ $log->id }}" class="group">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ strtoupper(substr($log->student->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $log->student->name }}</span>
                                </div>
                            </td>

                            <td class="px-8 py-4">
                                <code class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-mono font-medium">
                                    {{ $log->rfid_uid }}
                                </code>
                            </td>

                            <td class="px-8 py-4">
                                <span class="text-gray-700 font-medium text-sm">
                                    {{ $log->time_in ? $log->time_in->timezone('Asia/Manila')->format('h:i:s A') : '—' }}
                                </span>
                            </td>

                            <td class="px-8 py-4">
                                <span class="text-gray-700 font-medium text-sm">
                                    {{ $log->time_out ? $log->time_out->timezone('Asia/Manila')->format('h:i:s A') : '—' }}
                                </span>
                            </td>

                            <td class="px-8 py-4">
                                @if($log->time_out)
                                    <span class="status-badge inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-green-100 text-green-700 text-xs">
                                        <i class="fa-solid fa-check-circle"></i>
                                        Completed
                                    </span>
                                @else
                                    <span class="status-badge inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-700 text-xs">
                                        <i class="fa-solid fa-clock"></i>
                                        In Session
                                    </span>
                                @endif
                            </td>

                            <td class="px-8 py-4">
                                <span class="text-gray-700 font-semibold text-sm">
                                    {{ $log->duration_minutes ? $log->duration_minutes.' mins' : '—' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MANUAL ENTRY MODAL -->
<div id="manualEntryModal" class="modal-backdrop hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shadow-lg icon-wrapper">
                        <i class="fa-solid fa-keyboard text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Manual Entry</h3>
                        <p class="text-white/80 text-sm">For students without RFID card</p>
                    </div>
                </div>
                <button id="closeModalBtn" class="text-white/80 hover:text-white transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="manualEntryForm" class="p-8">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="manual_rfid_uid" class="block text-sm font-semibold text-gray-700 mb-2">
                        RFID Number
                    </label>
                    <input
                        type="text"
                        id="manual_rfid_uid"
                        placeholder="Enter student RFID number"
                        class="w-full rounded-xl border-2 border-gray-200 bg-gray-50 px-4 py-3.5
                               text-gray-900 placeholder-gray-400 font-medium
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                               transition-all duration-200"
                        required
                    />
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        id="cancelModalBtn"
                        class="flex-1 px-5 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-5 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-500/30">
                        Submit
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toast" class="toast fixed bottom-6 right-6 hidden px-6 py-4 rounded-xl text-white font-semibold text-sm"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('rfid_uid');
    const body = document.getElementById('attendanceBody');
    const modal = document.getElementById('manualEntryModal');
    const manualInput = document.getElementById('manual_rfid_uid');
    const manualForm = document.getElementById('manualEntryForm');
    
    let locked = false;
    let rfidBuffer = '';
    let rfidTimeout = null;

    // ============================================
    // AUTO RFID SCANNING (Background)
    // ============================================
    
    function maintainFocus() {
        if (!modal.classList.contains('hidden')) return;
        if (document.activeElement !== input) {
            input.focus();
        }
    }

    setInterval(maintainFocus, 100);

    input.addEventListener('input', (e) => {
        const value = e.target.value;
        
        if (rfidTimeout) clearTimeout(rfidTimeout);
        
        rfidBuffer = value;
        
        rfidTimeout = setTimeout(() => {
            if (rfidBuffer.trim()) {
                submitRFID(rfidBuffer.trim());
                rfidBuffer = '';
                input.value = '';
            }
        }, 100);
    });

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (rfidTimeout) clearTimeout(rfidTimeout);
            
            const uid = input.value.trim();
            if (uid) {
                submitRFID(uid);
                input.value = '';
                rfidBuffer = '';
            }
        }
    });

    // ============================================
    // MANUAL ENTRY MODAL
    // ============================================
    
    document.getElementById('openModalBtn').addEventListener('click', () => {
        modal.classList.remove('hidden');
        setTimeout(() => manualInput.focus(), 100);
    });

    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('cancelModalBtn').addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    function closeModal() {
        modal.classList.add('hidden');
        manualInput.value = '';
        setTimeout(() => input.focus(), 100);
    }

    manualForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const uid = manualInput.value.trim();
        if (uid) {
            submitRFID(uid);
            closeModal();
        }
    });

    // ============================================
    // RFID SUBMISSION
    // ============================================
    
    function submitRFID(uid) {
        if (locked || !uid) return;

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
            if (res.attendance) updateRow(res.attendance);
        })
        .catch(err => {
            showToast('Connection error', 'error');
            console.error(err);
        })
        .finally(() => {
            locked = false;
            input.focus();
        });
    }

    // ============================================
    // UI UPDATES
    // ============================================
    
    function updateRow(a) {
        let row = document.getElementById('attendance-' + a.id);

        if (!row) {
            row = document.createElement('tr');
            row.id = 'attendance-' + a.id;
            row.className = 'group';
            body.prepend(row);
        }

        const initial = a.student_name ? a.student_name.charAt(0).toUpperCase() : '?';
        const statusIcon = a.time_out ? 'fa-check-circle' : 'fa-clock';
        const statusClass = a.time_out ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700';
        const statusText = a.time_out ? 'Completed' : 'In Session';

        row.innerHTML = `
            <td class="px-8 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        ${initial}
                    </div>
                    <span class="font-semibold text-gray-900">${a.student_name}</span>
                </div>
            </td>
            <td class="px-8 py-4">
                <code class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-mono font-medium">${a.rfid_uid}</code>
            </td>
            <td class="px-8 py-4">
                <span class="text-gray-700 font-medium text-sm">${a.time_in ?? '—'}</span>
            </td>
            <td class="px-8 py-4">
                <span class="text-gray-700 font-medium text-sm">${a.time_out ?? '—'}</span>
            </td>
            <td class="px-8 py-4">
                <span class="status-badge inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full ${statusClass} text-xs">
                    <i class="fa-solid ${statusIcon}"></i>
                    ${statusText}
                </span>
            </td>
            <td class="px-8 py-4">
                <span class="text-gray-700 font-semibold text-sm">
                    ${a.duration_minutes ? a.duration_minutes + ' mins' : '—'}
                </span>
            </td>
        `;
    }

    function showToast(msg, status) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        
        const colors = {
            success: 'bg-gradient-to-r from-green-500 to-green-600',
            error: 'bg-gradient-to-r from-red-500 to-red-600',
            info: 'bg-gradient-to-r from-amber-500 to-amber-600'
        };
        
        t.className = `toast fixed bottom-6 right-6 px-6 py-4 rounded-xl text-white font-semibold text-sm shadow-2xl ${colors[status] || colors.info}`;
        t.classList.remove('hidden');
        
        setTimeout(() => t.classList.add('hidden'), 3000);
    }

    maintainFocus();
});
</script>
@endsection