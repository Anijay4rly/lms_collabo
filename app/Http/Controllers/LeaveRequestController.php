<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use Carbon\Carbon;


class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     * Admins see all, HODs see only their department's requests.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin sees all leave requests
            $leaveRequests = LeaveRequest::with('user.department')
                ->latest()
                ->get();
        } elseif ($user->role === 'hod') {
            // HOD sees only their department's leave requests
            $leaveRequests = LeaveRequest::whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->with('user.department')
            ->latest()
            ->get();
        } else {
            // Others shouldn't see this page
            abort(403, 'Unauthorized');
        }

        return view('leaves.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        return view('leaves.create');
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|string',
            'start_date'   => 'required|date|today_or_future',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'report_path'  => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'start_date.today_or_future' => 'Leave start date cannot be in the past. Please select today or a future date.',
            'end_date.after_or_equal' => 'End date must be the same as or after the start date.',
        ]);

        $user = Auth::user();

        // Check for overlapping leave requests
        $overlappingLeave = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'pending', 'on_progress', 'approved'])
            ->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
            ->orWhere(function ($query) use ($validated, $user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['submitted', 'pending', 'on_progress', 'approved'])
                    ->whereBetween('end_date', [$validated['start_date'], $validated['end_date']]);
            })
            ->orWhere(function ($query) use ($validated, $user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['submitted', 'pending', 'on_progress', 'approved'])
                    ->where('start_date', '<=', $validated['start_date'])
                    ->where('end_date', '>=', $validated['end_date']);
            })
            ->first();

        if ($overlappingLeave) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have a leave request during this period (' .
                        \Carbon\Carbon::parse($overlappingLeave->start_date)->format('d M Y') . ' to ' .
                        \Carbon\Carbon::parse($overlappingLeave->end_date)->format('d M Y') . '). ' .
                        'Please select different dates or cancel the existing request first.');
        }

        $validated['user_id'] = $user->id;
        $validated['status'] = 'submitted';

        // Handle file upload
        if ($request->hasFile('report_path')) {
            $path = $request->file('report_path')->store('reports', 'public');
            $validated['report_path'] = $path;
        }

        LeaveRequest::create($validated);

        return redirect()->route('leaves.showmy')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified leave request.
     */
    public function show($id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);

        // Check if leave request's user still exists
        if (!$leaveRequest->user) {
            abort(404, 'Associated user not found for this leave request.');
        }

        // Check permission
        $user = Auth::user();
        if ($user->id !== $leaveRequest->user_id && $user->role === 'employee') {
            abort(403, 'Unauthorized');
        }

        // HOD can only view from their own department
        if ($user->role === 'hod' && (!$user->department_id || $user->department_id !== $leaveRequest->user->department_id)) {
            abort(403, 'You can only view leave requests from your department.');
        }

        return view('leaves.show', compact('leaveRequest'));
    }

    /**
     * Display user's own leave requests with filtering.
     */
    public function showMyLeave(Request $request)
    {
        $query = LeaveRequest::where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->has('request_type') && $request->request_type !== '') {
            $query->where('request_type', 'LIKE', '%' . $request->request_type . '%');
        }

        // Filter by date range - from date
        if ($request->has('from_date') && $request->from_date !== '') {
            $query->where('start_date', '>=', $request->from_date);
        }

        // Filter by date range - to date
        if ($request->has('to_date') && $request->to_date !== '') {
            $query->where('end_date', '<=', $request->to_date);
        }

        $myleaves = $query->latest()->get();

        return view('leaves.showmy', compact('myleaves'));
    }

    /**
     * Show the form for editing a leave request.
     */
    public function edit($id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $user = Auth::user();

        // Check if leave request's user still exists
        if (!$leaveRequest->user) {
            abort(404, 'Associated user not found for this leave request.');
        }

        // Only employee can edit their own unsubmitted requests, only HOD/Admin can change status
        if ($user->id !== $leaveRequest->user_id && $user->role !== 'hod' && $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // HOD can only edit requests from their own department
        if ($user->role === 'hod' && (!$user->department_id || $user->department_id !== $leaveRequest->user->department_id)) {
            abort(403, 'You can only edit leave requests from your department.');
        }

        return view('leaves.edit', compact('leaveRequest'));
    }

    /**
     * Update the leave request in storage.
     */
    public function update(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $user = Auth::user();

        // Check if leave request's user still exists
        if (!$leaveRequest->user) {
            abort(404, 'Associated user not found for this leave request.');
        }

        // Check permission - HOD can only update from their department
        if ($user->role === 'hod' && (!$user->department_id || $user->department_id !== $leaveRequest->user->department_id)) {
            return redirect()->back()->with('error', 'You can only update leave requests from your department.');
        }

        $validated = $request->validate([
            'status' => 'required|in:submitted,pending,on_progress,approved,rejected',
            'report_path' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'hod_signature' => 'nullable|string',
            'hod_remarks' => 'nullable|string',
            'admin_signature' => 'nullable|string',
            'admin_remarks' => 'nullable|string',
        ]);

        // Handle file upload if provided
        if ($request->hasFile('report_path')) {
            $path = $request->file('report_path')->store('reports', 'public');
            $validated['report_path'] = $path;
        }

        // HOD signing (changes status to on_progress)
        if ($user->role === 'hod' && $request->has('hod_signature')) {
            $validated['hod_signature'] = $request->hod_signature;
            $validated['hod_signed_at'] = now();
            $validated['hod_remarks'] = $validated['hod_remarks'] ?? null;
            $validated['status'] = 'on_progress';
        }

        // Admin signing (changes status to approved)
        if ($user->role === 'admin' && $request->has('admin_signature')) {
            $validated['admin_signature'] = $request->admin_signature;
            $validated['admin_signed_at'] = now();
            $validated['admin_remarks'] = $validated['admin_remarks'] ?? null;
            $validated['status'] = 'approved';
        }

        $leaveRequest->update($validated);

        return redirect()->route('leaves.show', $leaveRequest->id)->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove a leave request from storage.
     */
    public function destroy($id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $user = Auth::user();

        // Check if leave request's user still exists
        if (!$leaveRequest->user) {
            abort(404, 'Associated user not found for this leave request.');
        }

        // Only employee, admin, or HOD of same department can delete
        $isOwner = $user->id === $leaveRequest->user_id;
        $isAdmin = $user->role === 'admin';
        $isHodOfDept = $user->role === 'hod' && $user->department_id && $user->department_id === $leaveRequest->user->department_id;

        if (!$isOwner && !$isAdmin && !$isHodOfDept) {
            abort(403, 'Unauthorized');
        }

        $leaveRequest->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Download leave request as PDF with signatures
     */
    public function downloadPDF($id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $user = Auth::user();

        // Check if leave request's user still exists
        if (!$leaveRequest->user) {
            abort(404, 'Associated user not found for this leave request.');
        }

        // Only employee can download their own approved leaves, admin and HOD can download any
        if ($user->id !== $leaveRequest->user_id && $user->role !== 'admin' && $user->role !== 'hod') {
            abort(403, 'Unauthorized');
        }

        // HOD can only download from their department
        if ($user->role === 'hod' && (!$user->department_id || $user->department_id !== $leaveRequest->user->department_id)) {
            abort(403, 'Unauthorized');
        }

        // Create PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetDefaultMonospacedFont('Courier');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, 'MINISTRY OF HEALTH', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Leave Request Form', 0, 1, 'C');
        $pdf->Ln(10);

        // Employee Details
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 10, 'EMPLOYEE DETAILS', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);

        $details = "Employee Name: {$leaveRequest->user->full_name}\n";
        $details .= "Email: {$leaveRequest->user->email}\n";
        $details .= "Department: {$leaveRequest->user->department->name}\n";
        $details .= "Leave Type: {$leaveRequest->request_type}\n";
        $details .= "Start Date: " . Carbon::parse($leaveRequest->start_date)->format('d M Y') . "\n";
        $details .= "End Date: " . Carbon::parse($leaveRequest->end_date)->format('d M Y') . "\n";
        $details .= "Duration: " . $leaveRequest->start_date->diffInDays($leaveRequest->end_date) + 1 . " day(s)\n";
        $details .= "Status: " . strtoupper(str_replace('_', ' ', $leaveRequest->status)) . "\n";

        $pdf->MultiCell(0, 5, $details, 0, 'L');
        $pdf->Ln(5);

        // Signatures section
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 10, 'APPROVALS', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        // HOD Signature
        if ($leaveRequest->hod_signature) {
            $pdf->Ln(5);
            $pdf->Cell(0, 10, 'Head of Department Approval:', 0, 1);

            // Decode and display HOD signature image
            if (strpos($leaveRequest->hod_signature, 'data:image') === 0) {
                list($type, $data) = explode(';', $leaveRequest->hod_signature);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $imagePath = sys_get_temp_dir() . '/hod_sig.png';
                file_put_contents($imagePath, $data);
                $pdf->Image($imagePath, 20, $pdf->GetY(), 40, 20);
                $pdf->Ln(20);
                unlink($imagePath);
            }

            $pdf->SetFont('helvetica', '', 9);
            if ($leaveRequest->hod_remarks) {
                $pdf->Cell(0, 5, "Remarks: " . $leaveRequest->hod_remarks, 0, 1);
            }
            $pdf->Cell(0, 5, "Approved on: " . Carbon::parse($leaveRequest->hod_signed_at)->format('d M Y H:i:s'), 0, 1);
        }

        // Admin Signature
        if ($leaveRequest->admin_signature) {
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 10, 'Admin Approval:', 0, 1);

            // Decode and display Admin signature image
            if (strpos($leaveRequest->admin_signature, 'data:image') === 0) {
                list($type, $data) = explode(';', $leaveRequest->admin_signature);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $imagePath = sys_get_temp_dir() . '/admin_sig.png';
                file_put_contents($imagePath, $data);
                $pdf->Image($imagePath, 20, $pdf->GetY(), 40, 20);
                $pdf->Ln(20);
                unlink($imagePath);
            }

            $pdf->SetFont('helvetica', '', 9);
            if ($leaveRequest->admin_remarks) {
                $pdf->Cell(0, 5, "Remarks: " . $leaveRequest->admin_remarks, 0, 1);
            }
            $pdf->Cell(0, 5, "Approved on: " . Carbon::parse($leaveRequest->admin_signed_at)->format('d M Y H:i:s'), 0, 1);
        }

        // Footer
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(0, 10, 'This is an electronically signed document.', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Generated on: ' . now()->format('d M Y H:i:s'), 0, 1, 'C');

        // Output PDF
        return $pdf->Output('leave_request_' . $leaveRequest->id . '.pdf', 'D');
    }
}
