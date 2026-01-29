@extends('layouts.admin')
@section('title', 'Attendance Management')

@section('actions')
<form action="{{ route('admin.attendance.logs') }}" method="GET" class="flex items-center gap-3">
    <div class="flex items-center bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
        <i class="fa-solid fa-graduation-cap text-slate-400 text-[10px] mr-3"></i>
        <select name="course" onchange="this.form.submit()" class="bg-transparent border-none focus:ring-0 text-[10px] text-slate-700 font-extrabold uppercase tracking-widest cursor-pointer outline-none">
            <option value="">All Academic Units</option>
            <option value="BSCS" {{ request('course') == 'BSCS' ? 'selected' : '' }}>BSCS - Computer Science</option>
            <option value="BSOA" {{ request('course') == 'BSOA' ? 'selected' : '' }}>BSOA - Office Administration</option>
            <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM - Criminology</option>
            <option value="BSTM" {{ request('course') == 'BSTM' ? 'selected' : '' }}>BSTM - Tourism Management</option>
            <option value="BSA" {{ request('course') == 'BSA' ? 'selected' : '' }}>BSA - Accountancy</option>
        </select>
    </div>
    <div class="flex items-center bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm cursor-pointer hover:bg-slate-50 transition-colors group" onclick="this.querySelector('input[type=date]').showPicker()">
        <i class="fa-solid fa-calendar-day text-slate-400 text-[10px] mr-3 group-hover:text-indigo-500 transition-colors"></i>
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" onclick="event.stopPropagation()" class="bg-transparent border-none focus:ring-0 text-[10px] text-slate-700 font-extrabold uppercase tracking-widest cursor-pointer w-full" style="color-scheme: light;">
    </div>
    @if(request()->anyFilled(['course', 'date', 'search']))
        <a href="{{ route('admin.attendance.logs') }}" class="w-10 h-10 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-center text-rose-500 hover:bg-rose-100 transition-all shadow-sm" title="Clear Filters">
            <i class="fa-solid fa-filter-circle-xmark text-xs"></i>
        </a>
    @endif
</form>
@endsection

@section('content')
<div class="space-y-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- ATTENDANCE AUTHENTICATION -->
        <div class="lg:col-span-1 space-y-6">
            <div class="relative overflow-hidden bg-white border border-indigo-100 rounded-3xl p-8 shadow-lg group">
                <!-- Decoration -->
                <div class="absolute -top-20 -left-20 w-40 h-40 bg-indigo-50 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-100 transition-all duration-500"></div>
                
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-6 shadow-sm relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-indigo-100/50 to-transparent"></div>
                        <i class="fa-solid fa-id-card text-3xl text-indigo-600 animate-pulse"></i>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">Scanner Active</h3>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
                        Please tap your ID card <br> to synchronize attendance
                    </p>

                    <!-- SCANNER VISUALIZER -->
                    <div class="mt-8 relative w-full aspect-square max-w-[200px] flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full border-2 border-indigo-100 animate-[ping_3s_linear_infinite]"></div>
                        <div class="absolute inset-4 rounded-full border-2 border-indigo-200 animate-[ping_2s_linear_infinite]"></div>
                        <div class="absolute inset-8 rounded-full border border-indigo-300 animate-[spin_10s_linear_infinite]">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-indigo-600 rounded-full shadow-lg"></div>
                        </div>
                        <div class="relative w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center border border-indigo-100">
                            <i class="fa-solid fa-wifi text-2xl text-indigo-600 rotate-90"></i>
                        </div>
                    </div>

                    <div class="mt-8 w-full">
                        <button 
                            id="openModalBtn"
                            class="w-full py-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 transition-all flex items-center justify-center gap-3 shadow-sm">
                            <i class="fa-solid fa-keyboard text-xs"></i>
                            Manual Override
                        </button>
                    </div>
                </div>

                <!-- Hidden RFID Input -->
                @csrf
                <input
                    type="text"
                    id="rfid_uid"
                    autofocus
                    autocomplete="off"
                    class="opacity-0 absolute inset-0 cursor-default"
                />
            </div>

            <!-- STATUS MONITOR -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 font-mono text-slate-300 shadow-xl">
                <div class="flex items-center justify-between mb-4 border-b border-white/10 pb-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">System Logs</span>
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                </div>
                <div id="terminal-feed" class="space-y-2 text-[11px] leading-relaxed max-h-40 overflow-y-auto custom-scrollbar">
                    <div class="text-emerald-400">>> System initialized...</div>
                    <div class="text-indigo-400">>> RFID reader online.</div>
                    <div class="text-slate-500">>> Waiting for input...</div>
                </div>
            </div>
        </div>

        <!-- LOGS TABLE -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between px-2">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Real-time Authentication Feed</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Showing latest 20 protocols</p>
                </div>
                <div class="relative group">
                    <form action="{{ route('admin.attendance.logs') }}" method="GET">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Name..." class="bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-[10px] text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all w-48 lg:w-64 font-black uppercase tracking-widest shadow-sm">
                        @if(request('course')) <input type="hidden" name="course" value="{{ request('course') }}"> @endif
                        @if(request('date')) <input type="hidden" name="date" value="{{ request('date') }}"> @endif
                    </form>
                </div>
            </div>

            <script>window.attendanceRegistry = {};</script>
            <x-glass-table :headers="['Subject', 'Protocol ID', 'Clock In', 'Clock Out', 'Case Note', 'State']">
                <tbody id="attendanceBody">
                @foreach ($logs as $log)
                    <tr id="attendance-{{ $log->id }}" class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xs font-black text-indigo-600">
                                    {{ strtoupper(substr($log->student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $log->student->name }}</p>
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{ $log->student->course }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-[10px] bg-slate-100 px-2 py-1 rounded text-cyan-600 font-bold tracking-tighter border border-slate-200">
                                {{ $log->rfid_uid }}
                            </code>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-700">
                                    {{ $log->time_in ? $log->time_in->timezone('Asia/Manila')->format('h:i:s A') : '—' }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase">
                                    {{ $log->time_in ? $log->time_in->timezone('Asia/Manila')->format('M d, Y') : '' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-l border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-700">
                                    {{ $log->time_out ? $log->time_out->timezone('Asia/Manila')->format('h:i:s A') : '—' }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase">
                                    {{ $log->time_out ? 'Authenticated' : 'Pending' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <script>window.attendanceRegistry[{{ $log->id }}] = {!! json_encode($log->only(['id', 'notes', 'category', 'severity'])) !!};</script>
                            <button 
                                type="button"
                                onclick="openNoteModal({{ $log->id }})"
                                class="note-trigger-{{ $log->id }} group/note relative"
                            >
                                @if($log->notes)
                                    <i class="fa-solid fa-comment-dots text-indigo-500 text-lg hover:scale-110 transition-transform"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-2 py-1 rounded text-[9px] font-black uppercase whitespace-nowrap opacity-0 group-hover/note:opacity-100 transition-opacity">Read Analysis</span>
                                @else
                                    <i class="fa-solid fa-note-sticky text-slate-300 text-lg hover:text-indigo-500 transition-colors"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-2 py-1 rounded text-[9px] font-black uppercase whitespace-nowrap opacity-0 group-hover/note:opacity-100 transition-opacity">Add Note</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge 
                                type="{{ $log->time_out ? 'success' : 'warning' }}" 
                                label="{{ $log->time_out ? 'Validated' : 'Encrypted' }}" 
                            />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-glass-table>

            <!-- PROFESSIONAL PAGINATION -->
            <div class="mt-8">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MANUAL ENTRY MODAL -->
<div id="manualEntryModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6 transition-all duration-300">
    <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden relative">
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-50 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="p-10 relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-terminal text-xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Manual Override</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Administrative protocol required</p>
                    </div>
                </div>
                <button id="closeModalBtn" class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="manualEntryForm" class="space-y-6">
                @csrf
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Protocol Identification (RFID)</label>
                    <input 
                        type="text" 
                        id="manual_rfid_uid"
                        placeholder="Enter identification string..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold placeholder-slate-400 shadow-inner"
                        required
                    >
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" id="cancelModalBtn" class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-600/20 transition-all">Authenticate</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
<!-- CASE NOTE PROTOCOL MODAL -->
<div id="noteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] hidden flex items-center justify-center p-6">
    <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl max-w-xl w-full overflow-hidden relative">
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-50 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="p-10 relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-feather-pointed text-xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Case Analysis</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1 text-indigo-500/50">Counseling Documentation Protocol</p>
                    </div>
                </div>
                <button onclick="closeNoteModal()" class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="noteForm" class="space-y-6" onsubmit="return false;">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Session Category</label>
                        <select id="case_category" name="category" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold cursor-pointer block !pointer-events-auto outline-none shadow-inner" style="pointer-events: auto !important;">
                            <option value="Walk-in">Walk-in</option>
                            <option value="Academic">Academic Support</option>
                            <option value="Crisis">Crisis Intervention</option>
                            <option value="Personal">Personal Support</option>
                            <option value="Follow-up">Routine Follow-up</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Priority Level</label>
                        <select id="case_severity" name="severity" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold cursor-pointer block !pointer-events-auto outline-none shadow-inner" style="pointer-events: auto !important;">
                            <option value="low">Low Priority</option>
                            <option value="medium">Normal Attention</option>
                            <option value="high">Urgent Required</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Confidential Case Notes</label>
                    <textarea id="case_notes" name="notes" placeholder="Detailed session narrative..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold placeholder-slate-400 min-h-[150px] block !pointer-events-auto shadow-inner" style="pointer-events: auto !important;"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitNoteBtn" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-xs font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-indigo-600/20 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-shield-check"></i>
                        Commit to Case History
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="toast" class="fixed bottom-8 right-8 z-[10000] hidden animate-fade-in">
    <div class="flex items-center gap-4 bg-white border border-slate-200 rounded-2xl px-6 py-4 shadow-2xl relative overflow-hidden">
        <div id="toast-glow" class="absolute left-0 top-0 bottom-0 w-1.5 shadow-lg"></div>
        <div id="toast-icon" class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"></div>
        <div>
            <p id="toast-title" class="text-[10px] font-black uppercase tracking-[0.2em] mb-0.5"></p>
            <p id="toast-msg" class="text-sm font-bold text-slate-700"></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('rfid_uid');
    const terminal = document.getElementById('terminal-feed');
    const body = document.getElementById('attendanceBody');
    const modal = document.getElementById('manualEntryModal');
    
    window.protocolActive = false;

    function logToTerminal(msg, color = 'slate-500') {
        const line = document.createElement('div');
        line.className = `text-${color}`;
        line.innerHTML = `>> ${msg}`;
        terminal.prepend(line);
        if (terminal.children.length > 50) terminal.lastChild.remove();
    }

    function maintainFocus() {
        if (window.protocolActive) return;
        
        const manualModal = document.getElementById('manualEntryModal');
        if (!manualModal.classList.contains('hidden')) return;

        if (document.activeElement !== input && 
            document.activeElement.tagName !== 'INPUT' && 
            document.activeElement.tagName !== 'TEXTAREA' && 
            document.activeElement.tagName !== 'SELECT') {
            input.focus();
        }
    }
    setInterval(maintainFocus, 100);

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const uid = input.value.trim();
            if (uid) {
                logToTerminal(`Input detected: ${uid}`, 'indigo-400');
                submitRFID(uid);
                input.value = '';
            }
        }
    });

    document.getElementById('openModalBtn').addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('closeModalBtn').addEventListener('click', () => modal.classList.add('hidden'));
    document.getElementById('cancelModalBtn').addEventListener('click', () => modal.classList.add('hidden'));

    // Note Modal Handlers
    window.openNoteModal = (id) => {
        logToTerminal(`Initiating documentation for Registry #${id}...`, 'indigo-400');
        window.protocolActive = true;
        const data = window.attendanceRegistry[id] || { notes: '', category: 'Walk-in', severity: 'low' };

        const noteModal = document.getElementById('noteModal');
        const noteForm = document.getElementById('noteForm');
        
        noteForm.action = `/admin/attendance/${id}/note`;
        document.getElementById('case_notes').value = data.notes || '';
        document.getElementById('case_category').value = data.category || 'Walk-in';
        document.getElementById('case_severity').value = data.severity || 'low';
        
        noteModal.classList.remove('hidden');
        logToTerminal(`Interface unlocked. Awaiting administrative input.`, 'emerald-400');
        
        // Immediate focus transition
        setTimeout(() => document.getElementById('case_notes').focus(), 100);
    };

    window.closeNoteModal = () => {
        logToTerminal(`Protocol terminated. Resuming RFID monitor.`, 'slate-500');
        window.protocolActive = false;
        document.getElementById('noteModal').classList.add('hidden');
    };

    document.getElementById('noteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('submitNoteBtn');
        const originalText = submitBtn.innerHTML;
        
        logToTerminal(`Commit initiated. Validating data streams...`, 'indigo-400');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Synchronizing...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                logToTerminal(`Synchronization complete. Protocol saved.`, 'emerald-400');
                showToast('Synchronized', 'Case history updated successfully.', 'success');
                window.protocolActive = false;
                setTimeout(() => location.reload(), 600);
            } else {
                const errData = await response.json();
                logToTerminal(`Validation failure. Check required fields.`, 'rose-400');
                showToast('Sync Error', errData.message || 'Validation mismatch', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (err) {
            logToTerminal(`Network failure. Mainframe unreachable.`, 'rose-500');
            showToast('Protocol Error', 'Sync failed', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    document.getElementById('manualEntryForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const uid = document.getElementById('manual_rfid_uid').value.trim();
        if (uid) {
            submitRFID(uid);
            modal.classList.add('hidden');
            document.getElementById('manual_rfid_uid').value = '';
        }
    });

    function submitRFID(uid) {
        logToTerminal('Transmitting to mainframe...', 'cyan-400');
        
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
            showToast(res.status === 'success' ? 'Authenticated' : 'Protocol Alert', res.message, res.status);
            logToTerminal(res.message, res.status === 'success' ? 'emerald-400' : (res.status === 'warning' ? 'amber-400' : 'rose-400'));
            if (res.attendance) {
                window.attendanceRegistry[res.attendance.id] = res.attendance;
                updateRow(res.attendance);
            }
        })
        .catch(err => {
            showToast('Sync Error', 'Mainframe connection lost', 'error');
            logToTerminal('Critical error: Protocol failed', 'rose-500');
            console.error(err);
        });
    }

    function updateRow(a) {
        let row = document.getElementById('attendance-' + a.id);
        if (!row) {
            row = document.createElement('tr');
            row.id = 'attendance-' + a.id;
            row.className = 'hover:bg-slate-50 transition-colors group animate-fade-in';
            body.prepend(row);
        }

        const initial = a.student_name ? a.student_name.charAt(0).toUpperCase() : '?';
        const type = a.time_out ? 'success' : 'warning';
        const label = a.time_out ? 'Validated' : 'Encrypted';
        const statusBadge = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-wider shadow-sm transition-all ${type === 'success' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200'}">
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> ${label}</span>`;

        const hasNotes = a.notes && a.notes.length > 0;
        const noteBtnContent = hasNotes 
            ? `<i class="fa-solid fa-comment-dots text-indigo-500 text-lg hover:scale-110 transition-transform"></i>
               <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-2 py-1 rounded text-[9px] font-black uppercase whitespace-nowrap opacity-0 group-hover/note:opacity-100 transition-opacity">Read Analysis</span>`
            : `<i class="fa-solid fa-note-sticky text-slate-300 text-lg hover:text-indigo-500 transition-colors"></i>
               <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-2 py-1 rounded text-[9px] font-black uppercase whitespace-nowrap opacity-0 group-hover/note:opacity-100 transition-opacity">Add Note</span>`;

        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xs font-black text-indigo-600">
                        ${initial}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">${a.student_name}</p>
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">${a.student_course ?? 'N/A'}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <code class="text-[10px] bg-slate-100 px-2 py-1 rounded text-cyan-600 font-bold tracking-tighter border border-slate-200">${a.rfid_uid}</code>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="text-[11px] font-black text-slate-700">${a.time_in ?? '—'}</span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase">Timestamp Sync</span>
                </div>
            </td>
            <td class="px-6 py-4 border-l border-slate-100">
                <div class="flex flex-col">
                    <span class="text-[11px] font-black text-slate-700">${a.time_out ?? '—'}</span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase">${a.time_out ? 'Authenticated' : 'Pending'}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <button 
                    type="button"
                    onclick="openNoteModal(${a.id})"
                    class="note-trigger-${a.id} group/note relative"
                >
                    ${noteBtnContent}
                </button>
            </td>
            <td class="px-6 py-4">
                ${statusBadge}
            </td>
        `;
    }

    function showToast(titleText, msg, type) {
        const t = document.getElementById('toast');
        const icon = document.getElementById('toast-icon');
        const title = document.getElementById('toast-title');
        const text = document.getElementById('toast-msg');
        const glow = document.getElementById('toast-glow');

        title.textContent = titleText;
        text.textContent = msg;
        if (type === 'success') {
            icon.className = 'w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 border border-emerald-100';
            icon.innerHTML = '<i class="fa-solid fa-shield-check"></i>';
            title.className = 'text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 mb-0.5';
            glow.className = 'absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500 shadow-md';
        } else if (type === 'warning') {
            icon.className = 'w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-100';
            icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
            title.textContent = titleText;
            title.className = 'text-[10px] font-black uppercase tracking-[0.2em] text-amber-600 mb-0.5';
            glow.className = 'absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500 shadow-md';
        } else {
            icon.className = 'w-10 h-10 rounded-xl flex items-center justify-center bg-rose-50 text-rose-600 border border-rose-100';
            icon.innerHTML = '<i class="fa-solid fa-shield-exclamation"></i>';
            title.className = 'text-[10px] font-black uppercase tracking-[0.2em] text-rose-600 mb-0.5';
            glow.className = 'absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500 shadow-md';
        }

        t.classList.remove('hidden');
        setTimeout(() => t.classList.add('hidden'), 4000);
    }
});
</script>
@endsection