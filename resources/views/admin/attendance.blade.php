@extends('layouts.admin')

@section('title', 'Attendance')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 text-left">Student</th>
                <th class="p-4 text-left">RFID</th>
                <th class="p-4 text-left">Time In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4 font-medium">{{ $log->student->name }}</td>
                <td class="p-4 font-mono">{{ $log->rfid_uid }}</td>
                <td class="p-4">{{ $log->time_in }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
