@extends('layouts.admin')
@section('title', 'Program Management')

@section('actions')
<button onclick="document.getElementById('newProgramModal').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2 transform active:scale-95">
    <i class="fa-solid fa-plus"></i>
    Add New Program
</button>
@endsection

@section('content')
<div class="space-y-6">
    <!-- STATS OVERVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Programs</p>
                    <p class="text-2xl font-black text-slate-900">{{ $programs->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Programs</p>
                    <p class="text-2xl font-black text-slate-900">{{ $programs->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
            <div class="flex flex-wrap gap-2 justify-center">
                <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-[10px] font-bold uppercase">Mental Health</span>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase">Academic</span>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold uppercase">Guidance</span>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white border border-slate-200 rounded-[2rem] overflow-hidden shadow-lg">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest">Program Info</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest">Category</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($programs as $program)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $program->name }}</span>
                            <span class="text-xs text-slate-500 line-clamp-1">{{ $program->description ?? 'No description provided.' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ strtolower($program->category) === 'mental health' ? 'bg-rose-100 text-rose-700' : 
                               (strtolower($program->category) === 'academic support' ? 'bg-indigo-100 text-indigo-700' : 
                               (strtolower($program->category) === 'guidance' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600')) }}">
                            {{ $program->category }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <form action="{{ route('admin.programs.toggle-status', $program) }}" method="POST">
                            @csrf
                            <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $program->is_active ? 'bg-indigo-600' : 'bg-slate-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $program->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button onclick="editProgram({{ json_encode($program) }})" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this program?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fa-solid fa-folder-open text-4xl text-slate-200 mb-4"></i>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">No programs configured</p>
                            <p class="text-slate-400 text-[10px] mt-1">Click "Add New Program" to get started.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- NEW PROGRAM MODAL -->
<div id="newProgramModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative w-full max-w-lg bg-white rounded-[2rem] p-8 shadow-2xl animate-fade-in text-left">
        <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest mb-6">Create Counseling Program</h3>
        
        <form action="{{ route('admin.programs.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Name</label>
                <input type="text" name="name" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g., Mental Health First Aid" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</label>
                <select name="category" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none">
                    <option value="Mental Health">Mental Health</option>
                    <option value="Academic Support">Academic Support</option>
                    <option value="Guidance">Guidance</option>
                    <option value="Wellness">Wellness</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</label>
                <textarea name="description" rows="3" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Briefly describe the program goals..."></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('newProgramModal').classList.add('hidden')" class="flex-1 py-3 text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all active:scale-95">Create Program</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT PROGRAM MODAL -->
<div id="editProgramModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative w-full max-w-lg bg-white rounded-[2rem] p-8 shadow-2xl animate-fade-in text-left">
        <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest mb-6">Modify Program</h3>
        
        <form id="editProgramForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Name</label>
                <input type="text" name="name" id="edit_name" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20" required>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</label>
                <select name="category" id="edit_category" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none">
                    <option value="Mental Health">Mental Health</option>
                    <option value="Academic Support">Academic Support</option>
                    <option value="Guidance">Guidance</option>
                    <option value="Wellness">Wellness</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('editProgramModal').classList.add('hidden')" class="flex-1 py-3 text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all active:scale-95">Update Program</button>
            </div>
        </form>
    </div>
</div>

<script>
function editProgram(program) {
    const modal = document.getElementById('editProgramModal');
    const form = document.getElementById('editProgramForm');
    
    form.action = `/admin/programs/${program.id}`;
    document.getElementById('edit_name').value = program.name;
    document.getElementById('edit_category').value = program.category;
    document.getElementById('edit_description').value = program.description || '';
    
    modal.classList.remove('hidden');
}
</script>
@endsection
