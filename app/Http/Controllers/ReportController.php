<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start the query
        $query = LeaveRequest::with('user.department');

        // 2. Apply Filters (If user selected them)

        // Filter by Date Range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        // Filter by Department (Requires joining tables to check department_id)
        if ($request->department_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by Status (Approved, Pending, etc.)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // 3. Get Results
        $leaves = $query->orderBy('start_date', 'desc')->paginate(6);
        $departments = Department::all(); // For the dropdown menu

        // 4. Check if "Export PDF" button was clicked
        if ($request->has('export_pdf')) {
            $pdf = Pdf::loadView('report.general_report', compact('leaves'));
            return $pdf->download('leave_report.pdf');
        }


        // 5. Otherwise, show the web page
        return view('report.index', compact('leaves', 'departments'));
    }


    public function individualreport($id){
        $leaveRequest = LeaveRequest::findorFail($id);
        $lastLeave = LeaveRequest::where('user_id', $leaveRequest->user_id)
        ->where('id', '<', $leaveRequest->id)
        ->where('start_date', '<', $leaveRequest->start_date)
        ->whereIn('status', ['approved', 'rejected']) //
        ->orderBy('start_date', 'desc')
        ->first();


        //create pdf according to the view
        $pdf = Pdf::loadView('report.individual_report', compact('leaveRequest', 'lastLeave'));


        return $pdf->download('leave_report_for'.$leaveRequest->user->fname. '.pdf');

    }
}
