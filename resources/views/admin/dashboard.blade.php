@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">Total Students</p>
        <h2 class="text-3xl font-bold">{{ $totalStudents }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">Attendance Today</p>
        <h2 class="text-3xl font-bold">{{ $attendanceToday }}</h2>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <p class="text-gray-500">System Status</p>
        <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full">
            Active
        </span>
    </div>

</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="font-semibold mb-4">Weekly Attendance</h3>
        <div id="weeklyChart"></div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="font-semibold mb-4">Students Per Course</h3>
        <div id="courseChart"></div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    new ApexCharts(document.querySelector("#weeklyChart"), {
        chart: { type: 'line', height: 300 },
        series: [{ name: 'Attendance', data: @json($weeklyAttendance->pluck('total')) }],
        xaxis: { categories: @json($weeklyAttendance->pluck('date')) }
    }).render();

    new ApexCharts(document.querySelector("#courseChart"), {
        chart: { type: 'donut', height: 300 },
        series: @json($studentsPerCourse->pluck('total')),
        labels: @json($studentsPerCourse->pluck('course'))
    }).render();
</script>
@endpush
