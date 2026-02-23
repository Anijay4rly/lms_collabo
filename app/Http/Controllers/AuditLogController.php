<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('audit-logs.index');
    }
}
