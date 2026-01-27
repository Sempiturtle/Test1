@extends('layouts.admin')
@section('title', 'Students')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    .students-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .register-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .register-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }

    .register-content {
        position: relative;
        z-index: 1;
    }

    .form-input {
        transition: all 0.2s ease;
    }

    .form-input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    .success-alert {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    tbody tr {
        transition: all 0.15s ease;
    }

    tbody tr:hover {
        background-color: var(--gray-50);
        transform: translateX(4px);
    }

    .action-btn {
        transition: all 0.2s ease;
        font-weight: 600;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .modal-backdrop {
        animation: fadeIn 0.2s ease-out;
    }

    .modal-content {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .course-badge {
        display: inline-block;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .search-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    .search-input {
        padding-left: 40px;
    }
</style>

<div class="students-container space-y-6">

    {{-- REGISTER STUDENT CARD --}}
    <div class="register-card rounded-2xl shadow-2xl overflow-hidden">
        <div class="register-content p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-14 w-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-user-plus text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">Register New Student</h2>
                    <p class="text-white/80 text-sm font-medium">Add student information and assign RFID</p>
                </div>
            </div>

            @if(session('success'))
                <div class="success-alert mb-6 bg-white/20 backdrop-blur-md border border-white/30 text-white px-5 py-4 rounded-xl flex items-center gap-3 shadow-lg">
                    <i class="fa-solid fa-check-circle text-xl"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.students.store') }}"
                  class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf

                <div>
                    <label class="block text-white/90 text-sm font-semibold mb-2">Student ID</label>
                    <input name="student_id" placeholder="e.g., 2024-0001" required
                        class="form-input w-full p-3.5 rounded-xl border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-white/90 text-sm font-semibold mb-2">Full Name</label>
                    <input name="name" placeholder="e.g., Juan Dela Cruz" required
                        class="form-input w-full p-3.5 rounded-xl border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-white/90 text-sm font-semibold mb-2">Course</label>
                    <select name="course" required
                        class="form-input w-full p-3.5 rounded-xl border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent">
                        <option value="" disabled selected class="text-gray-900">Select Course</option>
                        <option value="BT" class="text-gray-900">BT - Bachelor of Technology</option>
                        <option value="BO" class="text-gray-900">BO - Business Operations</option>
                        <option value="BSCS" class="text-gray-900">BSCS - Computer Science</option>
                        <option value="BCrim" class="text-gray-900">BCrim - Criminology</option>
                        <option value="BAcct" class="text-gray-900">BAcct - Accountancy</option>
                    </select>
                </div>

                <div>
                    <label class="block text-white/90 text-sm font-semibold mb-2">RFID Number</label>
                    <input name="rfid_uid" placeholder="Tap RFID card or enter manually" required
                        class="form-input w-full p-3.5 rounded-xl border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent font-mono">
                </div>

                <div class="md:col-span-2 text-right pt-2">
                    <button type="submit" class="action-btn px-8 py-3.5 rounded-xl bg-white text-indigo-600 font-bold hover:bg-gray-50 shadow-lg inline-flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        Register Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- STUDENTS TABLE --}}
    <div class="table-wrapper overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Student Directory</h2>
                    <p class="text-gray-600 text-sm">Manage enrolled students and RFID assignments</p>
                </div>
                
                <div class="search-wrapper">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input id="searchInput" 
                        placeholder="Search students..." 
                        class="search-input w-64 p-3 pl-10 border-2 border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student ID</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">RFID Number</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Course</th>
                        <th class="px-8 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTable" class="bg-white divide-y divide-gray-100">
                    @foreach($students as $s)
                    <tr id="student-{{ $s->id }}" class="group">
                        <td class="px-8 py-4">
                            <span class="student_id font-semibold text-gray-900">{{ $s->student_id }}</span>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                                <span class="student_name font-semibold text-gray-900">{{ $s->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <code class="student_rfid px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-mono font-medium">
                                {{ $s->rfid_uid }}
                            </code>
                        </td>
                        <td class="px-8 py-4">
                            <span class="student_course course-badge px-3 py-1.5 rounded-full 
                                {{ $s->course === 'BSCS' ? 'bg-blue-100 text-blue-700' : 
                                   ($s->course === 'BCrim' ? 'bg-red-100 text-red-700' : 
                                   ($s->course === 'BAcct' ? 'bg-green-100 text-green-700' : 
                                   ($s->course === 'BT' ? 'bg-purple-100 text-purple-700' : 
                                   'bg-amber-100 text-amber-700'))) }}">
                                {{ $s->course }}
                            </span>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='openEdit(@json($s))'
                                    class="action-btn px-4 py-2 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </button>
                                <button onclick="deleteStudent({{ $s->id }})"
                                    class="action-btn px-4 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editModal" class="modal-backdrop fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="modal-content bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid fa-pen text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Edit Student</h3>
                        <p class="text-white/80 text-sm">Update student information</p>
                    </div>
                </div>
                <button onclick="closeEdit()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-8">
            <input type="hidden" id="edit_id">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Student ID</label>
                    <input id="edit_student_id" required
                        class="w-full p-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input id="edit_name" required
                        class="w-full p-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Course</label>
                    <select id="edit_course" required
                        class="w-full p-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        <option value="BT">BT - Bachelor of Technology</option>
                        <option value="BO">BO - Business Operations</option>
                        <option value="BSCS">BSCS - Computer Science</option>
                        <option value="BCrim">BCrim - Criminology</option>
                        <option value="BAcct">BAcct - Accountancy</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">RFID Number</label>
                    <input id="edit_rfid" required
                        class="w-full p-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all font-mono">
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="button" onclick="closeEdit()"
                    class="flex-1 px-5 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-5 py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl transition-all shadow-lg inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
const modal = document.getElementById('editModal');

function openEdit(student) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    edit_id.value = student.id;
    edit_student_id.value = student.student_id;
    edit_name.value = student.name;
    edit_course.value = student.course;
    edit_rfid.value = student.rfid_uid;
}

function closeEdit() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when clicking outside
modal.addEventListener('click', (e) => {
    if (e.target === modal) closeEdit();
});

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = edit_id.value;
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch(`/admin/students/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            student_id: edit_student_id.value,
            name: edit_name.value,
            course: edit_course.value,
            rfid_uid: edit_rfid.value
        })
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            const row = document.getElementById(`student-${id}`);
            const initial = res.student.name.charAt(0).toUpperCase();
            
            // Update student ID
            row.querySelector('.student_id').innerText = res.student.student_id;
            
            // Update name with avatar
            const nameCell = row.querySelector('.student_name').parentElement;
            nameCell.innerHTML = `
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                    ${initial}
                </div>
                <span class="student_name font-semibold text-gray-900">${res.student.name}</span>
            `;
            
            // Update RFID
            row.querySelector('.student_rfid').innerText = res.student.rfid_uid;
            
            // Update course with color coding
            const courseColors = {
                'BSCS': 'bg-blue-100 text-blue-700',
                'BCrim': 'bg-red-100 text-red-700',
                'BAcct': 'bg-green-100 text-green-700',
                'BT': 'bg-purple-100 text-purple-700',
                'BO': 'bg-amber-100 text-amber-700'
            };
            const courseSpan = row.querySelector('.student_course');
            courseSpan.innerText = res.student.course;
            courseSpan.className = `student_course course-badge px-3 py-1.5 rounded-full ${courseColors[res.student.course] || 'bg-gray-100 text-gray-700'}`;
            
            closeEdit();
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to update student');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-save"></i> Save Changes';
    });
});

function deleteStudent(id) {
    if(!confirm('Are you sure you want to delete this student? This action cannot be undone.')) return;

    const row = document.getElementById(`student-${id}`);
    row.style.opacity = '0.5';

    fetch(`/admin/students/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            row.style.transform = 'translateX(-100%)';
            setTimeout(() => row.remove(), 300);
        } else {
            row.style.opacity = '1';
            alert('Failed to delete student');
        }
    })
    .catch(err => {
        console.error(err);
        row.style.opacity = '1';
        alert('Failed to delete student');
    });
}

// Search functionality
searchInput.addEventListener('keyup', function(){
    const v = this.value.toLowerCase();
    document.querySelectorAll('#studentsTable tr').forEach(tr => {
        const text = tr.innerText.toLowerCase();
        const shouldShow = text.includes(v);
        tr.style.display = shouldShow ? '' : 'none';
        
        if (shouldShow && v) {
            tr.style.animation = 'none';
            setTimeout(() => {
                tr.style.animation = 'fadeIn 0.3s ease-out';
            }, 10);
        }
    });
});
</script>
@endsection