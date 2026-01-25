<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->get();
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

        Student::create($request->all());

        return back()->with('success', 'Student registered successfully');
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'name' => 'required',
            'course' => 'required',
            'rfid_uid' => 'required|unique:students,rfid_uid,' . $student->id,
        ]);

        $student->update($request->only('student_id', 'name', 'course', 'rfid_uid'));

        return response()->json(['success' => true, 'student' => $student]);
    }



    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json(['success' => true]);
    }
}
