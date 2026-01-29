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
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-lg">
                <h3 class="text-sm font-black text-slate-900 tracking-widest uppercase mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-indigo-600"></i>
                    Categories
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors uppercase">Mental Health</span>
                    </div>
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors uppercase">Academic Support</span>
                    </div>
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors uppercase">Peer Support</span>
                    </div>
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="w-3 h-3 rounded-full bg-cyan-500 shadow-sm"></div>
                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors uppercase">Wellness</span>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-6 shadow-lg">
                <h3 class="text-sm font-black text-indigo-900 tracking-widest uppercase mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    Optimization
                </h3>
                <p class="text-[11px] leading-relaxed text-indigo-700 font-medium">
                    The AI suggests scheduling more <span class="text-indigo-600 font-black">Mental Health</span> sessions on Wednesdays to meet current student demand trends.
                </p>
                <button class="mt-4 w-full py-3 bg-white hover:bg-indigo-100 border border-indigo-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-indigo-600 transition-all shadow-sm">
                    View AI Insights
                </button>
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

<!-- FullCalendar Dependencies -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<style>
    :root {
        --fc-border-color: #e2e8f0;
        --fc-daygrid-event-dot-width: 8px;
        --fc-list-event-dot-width: 8px;
        --fc-today-bg-color: #f8fafc;
        --fc-page-bg-color: transparent;
    }

    .fc {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 900;
        letter-spacing: -0.025em;
        color: #0f172a;
    }

    .fc .fc-button {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 1.25rem;
        border-radius: 1rem;
        transition: all 0.2s;
        color: #64748b;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .fc .fc-button:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: white;
    }

    .fc-theme-standard th {
        border: none;
        padding: 1.5rem 0;
        font-size: 0.7rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
    }

    .fc-theme-standard td, .fc-theme-standard .fc-scrollgrid {
        border-color: #e2e8f0;
    }

    .fc-day-today {
        background-color: var(--fc-today-bg-color) !important;
    }

    .fc-event {
        border-radius: 0.75rem;
        padding: 4px 8px;
        font-size: 0.7rem;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.2s;
        border: none !important;
        margin: 2px 4px !important;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .fc-event:hover {
        transform: scale(1.02);
    }

    .fc-h-event .fc-event-main {
        color: white;
    }

    /* Scrollbar */
    .fc-scroller::-webkit-scrollbar {
        width: 4px;
    }
    .fc-scroller::-webkit-scrollbar-track {
        background: transparent;
    }
    .fc-scroller::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: '{{ route("admin.calendar.events") }}',
            eventMouseEnter: function(info) {
                // Add tooltip logic if desired
            },
            eventClick: function(info) {
                // Show session details modal
                console.log(info.event.extendedProps);
            }
        });
        calendar.render();
    });
</script>
@endsection
