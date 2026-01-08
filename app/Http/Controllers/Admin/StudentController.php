<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('rfid_uid', 'like', "%{$request->search}%");
        })->get();

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
