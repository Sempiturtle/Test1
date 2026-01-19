@extends('layouts.admin')
@section('title', 'Students')
@section('content')

    @if (session('success'))
        <div class="bg-green-500/20 text-green-400 p-4 rounded mb-4 border border-green-400/30">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.students.store') }}"
        class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-6">
        @csrf
        <input name="student_id" placeholder="Student ID" class="w-full mb-2 p-2 border rounded bg-black/10">
        <input name="name" placeholder="Full Name" class="w-full mb-2 p-2 border rounded bg-black/10">
        <input name="course" placeholder="Course" class="w-full mb-2 p-2 border rounded bg-black/10">
        <input name="rfid_uid" placeholder="Tap RFID Card" class="w-full mb-2 p-2 border rounded bg-black/10">
        <button class="bg-indigo-600 px-4 py-2 rounded text-white mt-2 hover:bg-indigo-500 transition">Register
            Student</button>
    </form>

    <table class="w-full bg-white/5 backdrop-blur rounded-2xl border border-white/10 text-sm">
        <thead class="text-gray-400 border-b border-white/10">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">RFID UID</th>
                <th class="p-3 text-left">Course</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $s)
                <tr class="border-b border-white/5 hover:bg-gray-800">
                    <td class="p-2">{{ $s->student_id }}</td>
                    <td class="p-2">{{ $s->name }}</td>
                    <td class="p-2 font-mono">{{ $s->rfid_uid }}</td>
                    <td class="p-2">{{ $s->course }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
