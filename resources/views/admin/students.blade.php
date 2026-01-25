@extends('layouts.admin')
@section('title', 'Students')

@section('content')
<div class="space-y-10">

    {{-- ================= REGISTER STUDENT ================= --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Register Student</h2>

        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <form id="registerForm" method="POST" action="{{ route('admin.students.store') }}"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <input name="student_id" placeholder="Student ID"
                class="p-3 rounded-lg border bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500">

            <input name="name" placeholder="Full Name"
                class="p-3 rounded-lg border bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500">

            <select name="course"
                class="p-3 rounded-lg border bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500">
                <option value="" disabled selected>Select Course</option>
                <option value="BT">BT</option>
                <option value="BO">BO</option>
                <option value="BSCS">BSCS</option>
                <option value="BCrim">BCrim</option>
                <option value="BAcct">BAcct</option>
            </select>

            <input name="rfid_uid" placeholder="RFID UID"
                class="p-3 rounded-lg border bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500">

            <div class="md:col-span-2 text-right">
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                    Register
                </button>
            </div>
        </form>
    </div>

    {{-- ================= STUDENTS TABLE ================= --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Students</h2>

            <input id="searchInput" type="text"
                placeholder="Search name / ID / RFID..."
                class="p-2 rounded-lg border bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-800">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3 text-left">Student ID</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">RFID</th>
                        <th class="p-3 text-left">Course</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTable">
                    @foreach ($students as $s)
                        <tr id="student-{{ $s->id }}" class="border-b hover:bg-gray-50">
                            <td class="p-3 student_id">{{ $s->student_id }}</td>
                            <td class="p-3 student_name">{{ $s->name }}</td>
                            <td class="p-3 student_rfid font-mono">{{ $s->rfid_uid }}</td>
                            <td class="p-3 student_course">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $s->course === 'BSCS' ? 'bg-indigo-100 text-indigo-700' :
                                       ($s->course === 'BCrim' ? 'bg-red-100 text-red-700' :
                                       ($s->course === 'BT' ? 'bg-amber-100 text-amber-700' :
                                       ($s->course === 'BO' ? 'bg-blue-100 text-blue-700' :
                                       'bg-green-100 text-green-700'))) }}">
                                    {{ $s->course }}
                                </span>
                            </td>
                            <td class="p-3 text-center space-x-2">
                                <button onclick="openEdit(@json($s))"
                                    class="px-3 py-1 rounded bg-yellow-400 text-black text-xs font-semibold hover:bg-yellow-500">
                                    Edit
                                </button>

                                <button onclick="deleteStudent({{ $s->id }})"
                                    class="px-3 py-1 rounded bg-red-600 text-white text-xs font-semibold hover:bg-red-700">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ================= EDIT MODAL ================= --}}
<div id="editModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Student</h3>

        <form id="editForm">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                <input name="student_id" id="edit_student_id"
                    class="w-full p-3 border rounded-lg bg-gray-50 text-gray-800">

                <input name="name" id="edit_name"
                    class="w-full p-3 border rounded-lg bg-gray-50 text-gray-800">

                <select name="course" id="edit_course"
                    class="w-full p-3 border rounded-lg bg-gray-50 text-gray-800">
                    <option>BT</option>
                    <option>BO</option>
                    <option>BSCS</option>
                    <option>BCrim</option>
                    <option>BAcct</option>
                </select>

                <input name="rfid_uid" id="edit_rfid"
                    class="w-full p-3 border rounded-lg bg-gray-50 text-gray-800">
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEdit()"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPTS ================= --}}
<script>
    // SEARCH
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('#studentsTable tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // OPEN EDIT MODAL
    function openEdit(student) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');

        document.getElementById('edit_student_id').value = student.student_id;
        document.getElementById('edit_name').value = student.name;
        document.getElementById('edit_course').value = student.course;
        document.getElementById('edit_rfid').value = student.rfid_uid;

        document.getElementById('editForm').dataset.id = student.id;
    }

    function closeEdit() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // AJAX EDIT
    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = this.dataset.id;
        const data = {
            student_id: document.getElementById('edit_student_id').value,
            name: document.getElementById('edit_name').value,
            course: document.getElementById('edit_course').value,
            rfid_uid: document.getElementById('edit_rfid').value,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        fetch(`/admin/students/${id}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success){
                const row = document.getElementById(`student-${id}`);
                row.querySelector('.student_id').innerText = data.student_id;
                row.querySelector('.student_name').innerText = data.name;
                row.querySelector('.student_rfid').innerText = data.rfid_uid;
                row.querySelector('.student_course span').innerText = data.course;
                closeEdit();
            } else {
                alert('Failed to update student');
            }
        })
        .catch(err => alert('Error updating student'));
    });

    // AJAX DELETE
    function deleteStudent(id) {
        if(!confirm('Are you sure you want to delete this student?')) return;

        fetch(`/admin/students/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                const row = document.getElementById(`student-${id}`);
                row.remove();
            } else {
                alert('Failed to delete student');
            }
        })
        .catch(err => alert('Error deleting student'));
    }
</script>
@endsection
