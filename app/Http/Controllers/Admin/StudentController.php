<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show(Student $student)
    {
        $student->load(['attendances.student', 'cases.user']); // Eager load
        return view('admin.students.show', compact('student'));
    }

    public function index(Request $request)
    {
        $query = Student::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        $students = $query->paginate(15)->withQueryString();
        return view('admin.students', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:students',
            'name' => 'required',
            'course' => 'required',
            'rfid_uid' => 'required|unique:students',
        ]);

        $student = Student::create($request->all());

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_STUDENT',
            'details' => "Registered student: {$student->name} ({$student->student_id})",
            'ip_address' => request()->ip()
        ]);

        return back()->with('success', 'Student registered successfully');
    }

    // ✅ AJAX EDIT
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'name' => 'required',
            'course' => 'required',
            'rfid_uid' => 'required|unique:students,rfid_uid,' . $student->id,
        ]);

        $student->update([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'course' => $request->course,
            'rfid_uid' => $request->rfid_uid,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_STUDENT',
            'details' => "Updated student: {$student->name} ({$student->student_id})",
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'student' => $student
        ]);
    }

    // ✅ AJAX DELETE
    public function destroy(Student $student)
    {
        $student->delete();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_STUDENT',
            'details' => "Deleted student: {$student->name} ({$student->student_id})",
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true
        ]);
    }


}
