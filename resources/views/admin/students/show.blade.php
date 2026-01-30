@extends('layouts.admin')
@section('title', 'Student Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT COLUMN: STUDENT BIO -->
    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-lg text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
            
            <div class="relative z-10 -mt-4">
                <div class="w-32 h-32 mx-auto rounded-3xl bg-white p-2 shadow-xl mb-4">
                    <div class="w-full h-full rounded-2xl bg-slate-100 flex items-center justify-center text-4xl font-black text-slate-400">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                </div>
                
                <h2 class="text-2xl font-black text-slate-900">{{ $student->name }}</h2>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold uppercase tracking-wider border border-indigo-100">{{ $student->student_id }}</span>
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase tracking-wider border border-slate-200">{{ $student->course }}</span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 border-t border-slate-100 pt-8">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Attendance</p>
                    <p class="text-2xl font-black text-slate-900">{{ $student->attendances->count() }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Risk Level</p>
                    <p class="text-2xl font-black {{ $student->risk_level === 'High' ? 'text-rose-500' : 'text-emerald-500' }}">
                        {{ $student->risk_level ?? 'Low' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- NEW CASE BUTTON -->
        <button onclick="document.getElementById('newCaseModal').classList.remove('hidden')" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 group">
            <i class="fa-solid fa-folder-plus group-hover:scale-110 transition-transform"></i>
            Open New Case
        </button>
    </div>

    <!-- RIGHT COLUMN: TABS -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- TABS HEADER -->
        <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
            <button onclick="switchTab('anecdotal')" id="btn-anecdotal" class="text-sm font-black uppercase tracking-wider text-indigo-600 border-b-2 border-indigo-600 pb-4 -mb-4.5 transition-colors">Anecdotal Records</button>
            <button onclick="switchTab('attendance')" id="btn-attendance" class="text-sm font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600 transition-colors pb-4 -mb-4.5 border-b-2 border-transparent">Attendance History</button>
        </div>

        <!-- ANECDOTAL RECORDS TAB -->
        <div id="tab-anecdotal" class="space-y-6">
            @forelse($student->cases as $case)
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full 
                    {{ $case->severity === 'Critical' ? 'bg-rose-500' : 
                      ($case->severity === 'High' ? 'bg-orange-500' : 
                      ($case->severity === 'Medium' ? 'bg-amber-500' : 'bg-emerald-500')) }}">
                </div>

                <div class="flex items-start justify-between mb-4 pl-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider 
                                {{ $case->status === 'Open' ? 'bg-indigo-100 text-indigo-700' : 
                                  ($case->status === 'Resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $case->status }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $case->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $case->title }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Handler</p>
                        <p class="text-xs font-bold text-slate-700">{{ $case->user->name ?? 'Unknown' }}</p>
                    </div>
                </div>

                <div class="pl-4 prose prose-indigo prose-sm text-slate-600 mb-4">
                    {{ $case->description }}
                </div>

                <div class="pl-4 pt-4 border-t border-slate-50 flex items-center gap-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Severity: {{ $case->severity }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type: {{ $case->type }}</span>
                    
                    <button onclick="editCase({{ $case->id }}, '{{ addslashes($case->title) }}', '{{ $case->status }}', '{{ addslashes($case->description) }}')" class="ml-auto text-indigo-600 hover:text-indigo-800 text-xs font-bold uppercase tracking-wider">
                        Update Status
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                <i class="fa-solid fa-folder-open text-4xl text-slate-300 mb-4"></i>
                <p class="text-slate-500 font-bold">No anecdotal records found.</p>
                <p class="text-slate-400 text-sm">Create a new case to start tracking.</p>
            </div>
            @endforelse
        </div>

        <!-- ATTENDANCE HISTORY TAB -->
        <div id="tab-attendance" class="hidden space-y-6">
            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Time Out</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($student->attendances as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">{{ $log->time_in->format('M d, Y') }}</span>
                                <p class="text-xs text-slate-500">{{ $log->time_in->format('h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->time_out)
                                    <span class="text-sm font-bold text-slate-900">{{ $log->time_out->format('h:i A') }}</span>
                                @else
                                    <span class="text-xs font-bold text-slate-400 uppercase italic">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-600">
                                    {{ $log->time_out ? $log->time_in->diffInMinutes($log->time_out) . ' mins' : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider 
                                    {{ $log->time_out ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $log->time_out ? 'Completed' : 'On-going' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-bold text-sm">
                                No attendance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- NEW CASE MODAL -->
<div id="newCaseModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl animate-fade-in text-left">
        <h3 class="text-xl font-black text-slate-900 uppercase tracking-wider mb-6">Open New Case</h3>
        
        <form action="{{ route('admin.cases.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Case Title</label>
                <input type="text" name="title" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g., Tardiness Issue" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Type</label>
                    <select name="type" class="w-full mt-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none">
                        <option value="Behavioral">Behavioral</option>
                        <option value="Academic">Academic</option>
                        <option value="Attendance">Attendance</option>
                        <option value="Personal">Personal</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Severity</label>
                    <select name="severity" class="w-full mt-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Case Description</label>
                <textarea name="description" rows="4" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Summary of the situation and initial findings..." required></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('newCaseModal').classList.add('hidden')" class="flex-1 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 uppercase tracking-wider">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold uppercase tracking-widest shadow-lg shadow-indigo-500/20">Open Case</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT CASE MODAL -->
<div id="editCaseModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl animate-fade-in text-left">
        <h3 class="text-xl font-black text-slate-900 uppercase tracking-wider mb-6">Update Case Status</h3>
        
        <form id="editCaseForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Case Title</label>
                <input type="text" id="edit_title" name="title" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-400 outline-none" readonly>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Current Status</label>
                <select name="status" id="edit_status" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Description Update (Optional)</label>
                <textarea name="description" id="edit_description" rows="4" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('editCaseModal').classList.add('hidden')" class="flex-1 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 uppercase tracking-wider">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold uppercase tracking-widest shadow-lg shadow-indigo-500/20">Update Case</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script>
function switchTab(tab) {
    // Buttons
    const btnAnecdotal = document.getElementById('btn-anecdotal');
    const btnAttendance = document.getElementById('btn-attendance');
    
    // Content
    const tabAnecdotal = document.getElementById('tab-anecdotal');
    const tabAttendance = document.getElementById('tab-attendance');

    if (tab === 'anecdotal') {
        // Activate Anecdotal
        btnAnecdotal.classList.add('text-indigo-600', 'border-indigo-600');
        btnAnecdotal.classList.remove('text-slate-400', 'border-transparent');
        
        btnAttendance.classList.add('text-slate-400', 'border-transparent');
        btnAttendance.classList.remove('text-indigo-600', 'border-indigo-600');
        
        tabAnecdotal.classList.remove('hidden');
        tabAttendance.classList.add('hidden');
    } else {
        // Activate Attendance
        btnAttendance.classList.add('text-indigo-600', 'border-indigo-600');
        btnAttendance.classList.remove('text-slate-400', 'border-transparent');
        
        btnAnecdotal.classList.add('text-slate-400', 'border-transparent');
        btnAnecdotal.classList.remove('text-indigo-600', 'border-indigo-600');
        
        tabAttendance.classList.remove('hidden');
        tabAnecdotal.classList.add('hidden');
    }
}

function editCase(id, title, status, description) {
    const modal = document.getElementById('editCaseModal');
    const form = document.getElementById('editCaseForm');
    
    form.action = `/admin/cases/${id}`;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_description').value = description;
    
    modal.classList.remove('hidden');
}
</script>
@endsection
