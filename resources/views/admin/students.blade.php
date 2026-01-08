@extends('layouts.admin')

@section('title', 'Students')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 text-left">Student ID</th>
                <th class="p-4 text-left">Name</th>
                <th class="p-4 text-left">Course</th>
                <th class="p-4 text-left">RFID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4">{{ $student->student_id }}</td>
                <td class="p-4 font-medium">{{ $student->name }}</td>
                <td class="p-4">{{ $student->course }}</td>
                <td class="p-4 font-mono">{{ $student->rfid_uid }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
