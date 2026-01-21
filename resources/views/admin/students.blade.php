@extends('layouts.admin')
@section('title', 'Students')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4 border border-green-300">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.students.store') }}"
        class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 mb-6">
        @csrf
        <input name="student_id" placeholder="Student ID" class="w-full mb-2 p-2 border rounded bg-gray-50">
        <input name="name" placeholder="Full Name" class="w-full mb-2 p-2 border rounded bg-gray-50">
        <input name="course" placeholder="Course" class="w-full mb-2 p-2 border rounded bg-gray-50">
        <input name="rfid_uid" placeholder="Tap RFID Card" class="w-full mb-2 p-2 border rounded bg-gray-50">
        <button class="bg-indigo-600 px-4 py-2 rounded text-white mt-2 hover:bg-indigo-500 transition">Register
            Student</button>
    </form>

    <table class="w-full bg-white shadow-lg rounded-2xl border border-gray-200 text-sm">
        <thead class="text-gray-700 bg-indigo-50 border-b border-gray-200">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">RFID UID</th>
                <th class="p-3 text-left">Course</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $s)
                <tr class="border-b hover:bg-indigo-50">
                    <td class="p-2">{{ $s->student_id }}</td>
                    <td class="p-2">{{ $s->name }}</td>
                    <td class="p-2 font-mono">{{ $s->rfid_uid }}</td>
                    <td class="p-2">{{ $s->course }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
