<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\LeaveHistory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $leaveRequests = LeaveRequest::with('user.department')->latest()->paginate(15);
        } elseif ($user->role === 'hod') {
            $leaveRequests = LeaveRequest::whereHas('user', fn($q) => $q->where('department_id', $user->department_id))
                ->with('user.department')->latest()->paginate(15);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('leaves.index', compact('leaveRequests'));
    }

    public function create()
    {

        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|string',
            'start_date'   => 'required|date|today_or_future',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'reasons'      => 'nullable|string',
            'destination'  => 'nullable|string',
        ], [
            'start_date.today_or_future' => 'Leave cannot start in the past.',
        ]);

        // Check if user has a signature
        if (empty($user->signature)) {
            return redirect()->route('profile.edit')
                ->with('error', 'You must upload a digital signature before approving leaves.');
        }

        $days = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

        if ($days > 14) {
            return back()->withInput()->with('error', 'Maximum allowed leave is 14 days.');
        }

        $user = Auth::user();

        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                  ->orWhereBetween('end_date',   [$validated['start_date'], $validated['end_date']])
                  ->orWhere(fn($q2) => $q2->where('start_date', '<=', $validated['start_date'])
                                           ->where('end_date',   '>=', $validated['end_date']));
            })->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'You already have an overlapping leave request.');
        }

        $data = $validated;
        $data['user_id'] = $user->id;
        $data['status']  = 'submitted';

        $leaveRequest = LeaveRequest::create($data);

        // Save initial submission in leave_histories
        LeaveHistory::create([
            'leave_request_id' => $leaveRequest->id,
            'user_id'          => $user->id,
            'action'           => 'submitted',
            'remarks'          => $validated['reasons'] ?? 'No reason provided',
            'created_at'      => now(),
            'updated_at'=> now(),
        ]);

        return redirect()->route('leaves.showmy')->with('success', 'Leave request submitted successfully.');
    }

    public function show($id)
    {
        $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'employee' && $user->id !== $leaveRequest->user_id) {
            abort(403);
        }
        if ($user->role === 'hod' && $leaveRequest->user->department_id !== $user->department_id) {
            abort(403);
        }

        return view('leaves.show', compact('leaveRequest'));
    }

    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user  = Auth::user();

        // Check role
        if (!in_array($user->role, ['hod', 'admin'])) {
            abort(403);
        }

        // HOD cannot approve outside their department
        if ($user->role === 'hod' && $leave->user->department_id !== $user->department_id) {
            return back()->with('error', 'Cannot approve leaves outside your department.');
        }

        // Check if user has a signature
        if (empty($user->signature)) {
            return redirect()->route('profile.edit')
                ->with('error', 'You must upload a digital signature before approving leaves.');
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:1500',
        ]);

        DB::transaction(function () use ($leave, $user, $validated, $request) {
            $action = $user->role === 'hod' ? 'hod_approved' : 'admin_approved';

            // Ensure leave is in correct state
            if ($user->role === 'hod' && $leave->status !== 'submitted') {
                throw new \Exception('Leave must be submitted to be approved by HOD.');
            }
            if ($user->role === 'admin' && $leave->status !== 'pending') {
                throw new \Exception('Leave must be pending to be approved by Admin.');
            }

            // Update leave status
            $leave->status = $user->role === 'hod' ? 'pending' : 'approved';
            $leave->save();

            // Insert into leave_histories
            LeaveHistory::create([
                'leave_request_id' => $leave->id,
                'user_id'          => $user->id,
                'action'           => $action,
                'remarks'          => $validated['remarks'] ?? 'No remarks provided',
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);

            // Insert into audit log
            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'approve',
                'description' => ucfirst($action) . " leave request ID: {$leave->id}",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        });

        $message = $user->role === 'hod'
            ? 'Leave approved by HOD. Awaiting final admin approval.'
            : 'Leave request fully approved.';

        return redirect()->route('leaves.show', $leave->id)
            ->with('success', $message);
    }
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user  = Auth::user();

        if (!in_array($user->role, ['hod', 'admin'])) abort(403);
        if ($user->role === 'hod' && $leave->user->department_id !== $user->department_id) {
            return back()->with('error', 'Cannot reject leaves outside your department.');
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:1500',
        ]);

        DB::transaction(function () use ($leave, $user, $validated, $request) {
            $leave->status = 'rejected';
            $leave->save();

            LeaveHistory::create([
                'leave_request_id' => $leave->id,
                'user_id'          => $user->id,
                'action'           => 'rejected',
                'remarks'          => $validated['remarks'] ?? 'No rejection reason provided',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'reject',
                'description' => 'Rejected leave request ID: ' . $leave->id,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        });

        return redirect()->route('leaves.show', $leave->id)
            ->with('success', 'Leave request rejected.');
    }


    public function pending()
    {
        $pendingLeaves = LeaveRequest::where('status', 'pending')->with('user.department')->latest()->paginate(15);
        return view('leaves.pending', compact('pendingLeaves'));
    }

    public function onprogress()
    {
        $onProgressLeaves = LeaveRequest::where('status', 'onprogress')->with('user.department')->latest()->paginate(15);
        return view('leaves.onprogress', compact('onProgressLeaves'));
    }

    public function approved()
    {
        $approvedLeaves = LeaveRequest::where('status', 'approved')->with('user.department')->latest()->paginate(15);
        return view('leaves.approved', compact('approvedLeaves'));
    }

    public function rejected()
    {
        $rejectedLeaves = LeaveRequest::where('status', 'rejected')->with('user.department')->latest()->paginate(15);
        return view('leaves.rejected', compact('rejectedLeaves'));
    }

    public function downloadPDF($id)
{
    $leaveRequest = LeaveRequest::with('user.department')->findOrFail($id);

    // Only approved leaves can be downloaded
    if ($leaveRequest->status !== 'approved') {
        abort(403, 'Only approved leaves can be downloaded as PDF.');
    }

    // Optionally, get the last leave for this user (for comparison or history)
    $lastLeave = LeaveRequest::where('user_id', $leaveRequest->user_id)
        ->where('id', '<', $leaveRequest->id)
        ->whereIn('status', ['approved', 'rejected'])
        ->orderBy('start_date', 'desc')
        ->first();

    $pdf = Pdf::loadView('leaves.pdf', compact('leaveRequest', 'lastLeave'));

    // Generate unique filename
    $unique = date('Ymd_His') . '_' . mt_rand(1000,9999);
    $fullName = trim($leaveRequest->user->fname.' '.$leaveRequest->user->lname);

    return $pdf->download("LeaveRequest_{$fullName}_{$unique}.pdf");
}

public function showMyLeave(Request $request)
{
    $query = LeaveRequest::where('user_id', auth()->id());

    // Filter by leave status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by request type
    if ($request->filled('request_type')) {
        $query->where('request_type', 'LIKE', '%' . $request->request_type . '%');
    }

    // Filter by request category (nullable field)
    if ($request->filled('request_category')) {
        $query->where('request_category', 'LIKE', '%' . $request->request_category . '%');
    }

    // Filter by start date (from)
    if ($request->filled('from_date')) {
        $query->whereDate('start_date', '>=', $request->from_date);
    }

    // Filter by end date (to)
    if ($request->filled('to_date')) {
        $query->whereDate('end_date', '<=', $request->to_date);
    }

    // Get paginated results, latest first
    $myleaves = $query->latest()->paginate(10);

    return view('leaves.showmy', compact('myleaves'));
}

    public function staff(){
        $staff = Auth::user();

        if ($staff->role === 'admin') {
            $leaveRequests = LeaveRequest::with('user.department')->latest()->paginate(15);
        } elseif ($staff->role === 'hod') {
            $leaveRequests = LeaveRequest::whereHas('user', fn($q) => $q->where('department_id', $staff->department_id))
                ->with('user.department')->latest()->paginate(15);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('leaves.staff', compact('leaveRequests'));
    }


}
