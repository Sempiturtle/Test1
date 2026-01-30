<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(20);
        return view('admin.audit.index', compact('logs'));
    }
}
