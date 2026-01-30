@extends('layouts.admin')
@section('title', 'Session Calendar')

@section('actions')
<button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
    <i class="fa-solid fa-plus"></i>
    New Session
</button>
@endsection

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- SIDEBAR INFO -->
        <div class="space-y-6">
            <button onclick="document.getElementById('newAppointmentModal').classList.remove('hidden')" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 group transform hover:scale-105">
                <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i>
                Schedule Session
            </button>

            <!-- UPCOMING SESSIONS LIST -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-lg">
                <h3 class="text-sm font-black text-slate-900 tracking-widest uppercase mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                    Upcoming Sessions
                </h3>
                <div class="space-y-4">
                    @forelse($upcoming as $appt)
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl group hover:border-amber-200 transition-colors cursor-pointer" onclick="goToEvent('appt-{{ $appt->id }}')">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">{{ $appt->scheduled_at->format('M d, h:i A') }}</p>
                        <p class="text-xs font-bold text-slate-800">{{ $appt->student->name }}</p>
                    </div>
                    @empty
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest text-center py-4">No upcoming sessions</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-lg">
                <h3 class="text-sm font-black text-slate-900 tracking-widest uppercase mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-indigo-600"></i>
                    Legend
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-amber-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase font-inter">Counseling</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase font-inter">Mental Health</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase font-inter">Academic</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CALENDAR MAIN -->
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-lg relative overflow-hidden">
                <div class="absolute -top-32 -right-32 w-80 h-80 bg-indigo-50 rounded-full blur-[100px] pointer-events-none"></div>
                <div id="calendar" class="min-h-[700px]"></div>
            </div>
        </div>
    </div>
</div>

<!-- NEW APPOINTMENT MODAL -->
<div id="newAppointmentModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl animate-fade-in">
        <h3 class="text-xl font-black text-slate-900 uppercase tracking-wider mb-6">Schedule Counseling</h3>
        <form id="appointmentForm" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Student</label>
                <select name="student_id" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" required>
                    <option value="" disabled selected>Select student...</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" required>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Duration (Mins)</label>
                    <select name="duration_minutes" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="15">15 Minutes</option>
                        <option value="30">30 Minutes</option>
                        <option value="45">45 Minutes</option>
                        <option value="60" selected>1 Hour</option>
                        <option value="90">1.5 Hours</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Session objectives..."></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('newAppointmentModal').classList.add('hidden')" class="flex-1 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 uppercase tracking-wider">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold uppercase tracking-widest shadow-lg shadow-indigo-500/20">Confirm Schedule</button>
            </div>
        </form>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<style>
    :root {
        --fc-border-color: #f1f5f9;
        --fc-daygrid-event-dot-width: 8px;
        --fc-today-bg-color: #f8fafc;
    }
    .fc { font-family: 'Inter', sans-serif; }
    .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.025em; }
    .fc .fc-button { background: #fff; border: 1px solid #e2e8f0; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; padding: 0.6rem 1rem; border-radius: 0.75rem; color: #64748b; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05); }
    .fc .fc-button:hover { background: #f8fafc; color: #0f172a; }
    .fc .fc-button-primary:not(:disabled).fc-button-active { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .fc-theme-standard th { border: none; padding: 1rem 0; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; }
    .fc-daygrid-day-number { font-size: 0.75rem; font-weight: 700; color: #64748b; padding: 10px !important; }
    .fc-event { border-radius: 0.5rem; padding: 2px 6px; font-size: 0.65rem; font-weight: 700; border: none !important; margin: 1px 2px !important; }
</style>

<script>
    let calendar;
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: '{{ route("admin.calendar.events") }}',
            eventClick: function(info) {
                alert('Session: ' + info.event.title + '\nStatus: ' + info.event.extendedProps.status);
            }
        });
        calendar.render();

        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('{{ route("admin.appointments.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    calendar.refetchEvents();
                    document.getElementById('newAppointmentModal').classList.add('hidden');
                    this.reset();
                    window.location.reload(); // Reload to update sidebar
                }
            });
        });
    });

    function goToEvent(id) {
        // Find event and move to its date
        const event = calendar.getEventById(id);
        if(event) {
            calendar.gotoDate(event.start);
        }
    }
</script>
@endsection
