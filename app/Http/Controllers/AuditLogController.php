<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = \App\Models\AuditLog::with('user')->latest()->paginate(30);
        return view('admin.audit_logs.index', compact('logs'));
    }
}
