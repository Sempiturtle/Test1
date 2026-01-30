<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'severity' => 'required|string',
            'description' => 'required|string'
        ]);

        $case = \App\Models\StudentCase::create([
            'student_id' => $validated['student_id'],
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'severity' => $validated['severity'],
            'status' => $request->input('status', 'Open'),
            'description' => $validated['description']
        ]);

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_CASE',
            'details' => "Opened new case for student ID {$validated['student_id']}: {$validated['title']}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Case record created successfully.');
    }

    public function update(Request $request, \App\Models\StudentCase $case)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'status' => 'required|string',
            'description' => 'required|string'
        ]);

        $case->update($validated);

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_CASE',
            'details' => "Updated case #{$case->id}: {$validated['title']}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Case record updated.');
    }
}
