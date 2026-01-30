<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('name')->get();
        return view('admin.programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        Program::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'capacity' => $validated['capacity'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Program created successfully.');
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return back()->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return back()->with('success', 'Program deleted successfully.');
    }

    public function toggleStatus(Program $program)
    {
        $program->update(['is_active' => !$program->is_active]);
        return back()->with('success', 'Program status updated.');
    }
}
