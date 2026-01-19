<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('admin.students', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:students',
            'name' => 'required',
            'course' => 'required',
            'rfid_uid' => 'required|unique:students'
        ]);

        Student::create($request->all());

        return back()->with('success', 'Student registered successfully');
    }
}
