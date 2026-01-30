@extends('layouts.admin')
@section('title', 'Student Directory')

@section('actions')
<button 
    onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
    <i class="fa-solid fa-user-plus"></i>
    Register Student
</button>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- SEARCH & FILTERS -->
    <!-- SEARCH & FILTERS -->
    <form method="GET" class="flex items-center justify-between">
        <div class="relative group">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID or course..." class="bg-white border border-slate-200 rounded-2xl pl-12 pr-6 py-3 text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all w-80 lg:w-96 shadow-sm">
        </div>
        
        <div class="flex items-center gap-4">
            <select name="course" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-bold uppercase tracking-wider cursor-pointer focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 outline-none">
                <option value="">All Courses</option>
                <option value="BSCS" {{ request('course') == 'BSCS' ? 'selected' : '' }}>BSCS - Computer Science</option>
                <option value="BSOA" {{ request('course') == 'BSOA' ? 'selected' : '' }}>BSOA - Office Administration</option>
                <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM - Criminology</option>
                <option value="BSTM" {{ request('course') == 'BSTM' ? 'selected' : '' }}>BSTM - Tourism Management</option>
                <option value="BSA" {{ request('course') == 'BSA' ? 'selected' : '' }}>BSA - Accountancy</option>
                <option value="SHS" {{ request('course') == 'SHS' ? 'selected' : '' }}>Senior High School</option>
            </select>
            <button type="submit" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-300 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>

    <x-glass-table :headers="['Student Profile', 'Academic Unit', 'Risk Status', 'Join Date', 'Actions']">
        @foreach($students as $student)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xs font-black text-indigo-600">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 leading-tight">{{ $student->name }}</h4>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Student ID: {{ $student->student_id }}</p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4">
                    <span class="text-xs font-bold text-slate-600 uppercase">{{ $student->course }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($student->is_at_risk)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-black uppercase tracking-wider">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            {{ $student->risk_level }} risk
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-wider">
                            <i class="fa-solid fa-circle-check"></i>
                            Stable
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-xs font-bold text-slate-500">
                    {{ $student->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                        <!-- VIEW PROFILE -->
                        <a href="{{ route('admin.students.show', $student->id) }}" 
                           class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-slate-400 hover:text-indigo-600 transition-all shadow-sm active:scale-95 group/view">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                            <span class="text-[9px] font-black uppercase tracking-tighter hidden group-hover/view:block">View</span>
                        </a>

                        <!-- EDIT PROTOCOL -->
                        <button 
                            type="button"
                            onclick='protocol_ModifyStudent(@json($student))'
                            class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-slate-400 hover:text-indigo-600 transition-all shadow-sm active:scale-95 group/edit">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span class="text-[9px] font-black uppercase tracking-tighter hidden group-hover/edit:block">Edit</span>
                        </button>



                        <!-- TERMINATION PROTOCOL -->
                        <button 
                            type="button"
                            onclick="protocol_TerminateRecord({{ $student->id }}, '{{ addslashes($student->name) }}')"
                            class="flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 rounded-xl text-slate-400 hover:text-rose-600 transition-all shadow-sm active:scale-95 group/del">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                            <span class="text-[9px] font-black uppercase tracking-tighter hidden group-hover/del:block">Delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-glass-table>

    <!-- PAGINATION -->
    <div class="flex items-center justify-between px-4">
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
            Exhibiting {{ $students->count() }} of {{ method_exists($students, 'total') ? $students->total() : $students->count() }} entities
        </p>
        <div class="flex gap-2">
            @if(method_exists($students, 'links'))
                {{ $students->links() }}
            @endif
        </div>
    </div>

</div>

<!-- ADD STUDENT MODAL -->
<div id="addStudentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6 transition-all duration-300">
    <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden relative">
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-50 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="p-10 relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-user-plus text-xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Enroll Student</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Add new subject to registry</p>
                    </div>
                </div>
                <button onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Full Legal Name</label>
                            <input type="text" name="name" placeholder="Enter student name..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold placeholder-slate-400 shadow-inner" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Student ID No.</label>
                            <input type="text" name="student_id" placeholder="e.g. 2024-001" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold placeholder-slate-400 shadow-inner" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Academic Unit</label>
                            <select name="course" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold appearance-none cursor-pointer outline-none shadow-inner" required>
                                <option value="" disabled selected>Select course...</option>
                                <option value="BSCS">BSCS - Computer Science</option>
                                <option value="BSOA">BSOA - Office Administration</option>
                                <option value="BSCRIM">BSCRIM - Criminology</option>
                                <option value="BSTM">BSTM - Tourism Management</option>
                                <option value="BSA">BSA - Accountancy</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">RFID Protocol ID</label>
                            <div class="relative group/input">
                                <input type="text" name="rfid_uid" id="registration_rfid" placeholder="Tap card to scan..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold placeholder-slate-400 shadow-inner" required>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse hidden" id="scan-indicator"></div>
                                    <i class="fa-solid fa-rss text-slate-400 group-focus-within/input:text-indigo-600 transition-colors"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-600/20 transition-all">Register Entity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT STUDENT MODAL -->
<div id="editStudentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-6 transition-all duration-300">
    <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden relative">
        <div class="absolute -top-32 -right-32 w-64 h-64 bg-cyan-50 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="p-10 relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-cyan-50 rounded-2xl flex items-center justify-center border border-cyan-100">
                        <i class="fa-solid fa-user-pen text-xl text-cyan-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Modify Student</h3>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Update entity credentials</p>
                    </div>
                </div>
                <button onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="editStudentForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Full Legal Name</label>
                            <input type="text" name="name" id="edit_name" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold shadow-inner" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Student ID No.</label>
                            <input type="text" name="student_id" id="edit_student_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold shadow-inner" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Academic Unit</label>
                            <select name="course" id="edit_course" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold appearance-none cursor-pointer outline-none shadow-inner" required>
                                <option value="BSCS">BSCS - Computer Science</option>
                                <option value="BSOA">BSOA - Office Administration</option>
                                <option value="BSCRIM">BSCRIM - Criminology</option>
                                <option value="BSTM">BSTM - Tourism Management</option>
                                <option value="BSA">BSA - Accountancy</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">RFID Protocol ID</label>
                            <input type="text" name="rfid_uid" id="edit_rfid_uid" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-slate-900 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500/50 transition-all font-bold shadow-inner" required>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="flex-1 py-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 transition-all">Cancel</button>
                    <button type="submit" id="updateRegistryBtn" class="flex-1 py-4 bg-cyan-600 hover:bg-cyan-500 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-cyan-600/20 transition-all">Update Registry</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    /**
     * @protocol_UpdateRegistry
     * Submits the modified student data via AJAX
     */
    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const action = form.action;
        
        // Show loading state
        const btn = document.getElementById('updateRegistryBtn');
        const originalText = btn ? btn.textContent : 'Update Registry';
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Synchronizing...';
        }

        console.log(`SYNCHRONIZING: Sending update to ${action}`);

        fetch(action, {
            method: 'POST', // Laravel handles @method('PUT') via POST
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                alert('UPDATE REJECTED: Connection failed or data mismatch.');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('CRITICAL ERROR: Mainframe communication failure.');
            if (btn) btn.textContent = originalText;
        });
    });

    /**
     * @protocol_ModifyStudent
     * Populates and opens the edit terminal
     */
    function protocol_ModifyStudent(student) {
        console.log('PROTOCOL INITIATED: Modify Student');
        try {
            if (!student) throw new Error('Data payload missing');
            
            console.log(`ENTITY IDENTIFIED: ${student.name} (ID: ${student.id})`);

            const modal = document.getElementById('editStudentModal');
            const form = document.getElementById('editStudentForm');
            
            // Sync Data to Bio-interface
            document.getElementById('edit_name').value = student.name || '';
            document.getElementById('edit_student_id').value = student.student_id || '';
            document.getElementById('edit_course').value = student.course || '';
            document.getElementById('edit_rfid_uid').value = student.rfid_uid || '';
            
            // Re-route form target
            form.action = `/admin/students/${student.id}`;
            
            // Disable Hidden State
            modal.classList.remove('hidden');
            console.log('TERMINAL ACTIVE: Edit Modal opened');
        } catch (err) {
            console.error('PROTOCOL FAILURE:', err);
            alert('ACCESS DENIED: Could not decode student entity data.');
        }
    }

    /**
     * @protocol_TerminateRecord
     * Initiates deletion protocol for student entity
     */
    function protocol_TerminateRecord(id, name) {
        if (confirm(`CRITICAL ACTION REQUIRED: Are you sure you wish to terminate the record for [${name}]? All associated data will be purged.`)) {
            // Signal main console
            console.warn(`DELETION PROTOCOL INITIATED: StudentID ${id}`);
            
            fetch(`/admin/students/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // Refresh Mainframe
                    location.reload();
                } else {
                    alert('ACCESS DENIED: Record termination failed.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('SYSTEM ERROR: Could not establish secure deletion line.');
            });
        }
    }

    /**
     * DOM Pulse Initialization
     */
    document.addEventListener('DOMContentLoaded', () => {
        // Enforce RFID Capture Focus in Modals
        const setupRFIDFocus = (modalId, inputId, indicatorId) => {
            const modal = document.getElementById(modalId);
            const rfidInput = document.getElementById(inputId);
            const indicator = document.getElementById(indicatorId);
            
            if (!modal || !rfidInput) return;

            window.addEventListener('keydown', (e) => {
                if (!modal.classList.contains('hidden') && 
                    document.activeElement.tagName !== 'INPUT' && 
                    document.activeElement.tagName !== 'SELECT' &&
                    document.activeElement.tagName !== 'TEXTAREA') {
                    rfidInput.focus();
                }
            });

            rfidInput.addEventListener('focus', () => indicator?.classList.remove('hidden'));
            rfidInput.addEventListener('blur', () => indicator?.classList.add('hidden'));

            // Auto-focus next field on scanner 'Enter' if missing data
            rfidInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const form = rfidInput.closest('form');
                    const nameInput = form.querySelector('input[name="name"]');
                    const sidInput = form.querySelector('input[name="student_id"]');
                    if (!nameInput.value || !sidInput.value) {
                        e.preventDefault();
                        if (!nameInput.value) nameInput.focus();
                        else if (!sidInput.value) sidInput.focus();
                    }
                }
            });
        };

        setupRFIDFocus('addStudentModal', 'registration_rfid', 'scan-indicator');
        // Edit modal also gets auto-focus for easy RFID re-scanning
        setupRFIDFocus('editStudentModal', 'edit_rfid_uid', null);
    });
</script>
@endpush
@endsection