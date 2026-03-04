<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login page.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Handle login form submission.
     */
    public function submit(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration page.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
        ]);

        User::create([
            'fname' => $validated['fname'],
            'mname' => $validated['mname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('status', 'Account created successfully! You can now login.');
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Show dashboard page for logged-in users.
     */
    public function dashboard()
    {
        $currentUser = Auth::user();

        // Fetch all leave requests with user relation
        $leaveRequests = LeaveRequest::with('user')->get();

        /*
        |--------------------------------------------------------------------------
        | USER-SPECIFIC STATS
        |--------------------------------------------------------------------------
        */
        $submittedCount = $leaveRequests->where('user_id', $currentUser->id)
            ->where('status', 'submitted')->count();

        $pendingCount = $leaveRequests->where('user_id', $currentUser->id)
            ->whereIn('status', ['pending', 'on_progress'])->count();

        $approvedCount = $leaveRequests->where('user_id', $currentUser->id)
            ->where('status', 'approved')->count();

        $rejectedCount = $leaveRequests->where('user_id', $currentUser->id)
            ->where('status', 'rejected')->count();

        /*
        |--------------------------------------------------------------------------
        | HOD DEPARTMENT STATS
        |--------------------------------------------------------------------------
        */
        $hodSubmittedCount = 0;
        $hodOnProgressCount = 0;
        $hodApprovedCount = 0;
        $hodRejectedCount = 0;

        if ($currentUser->role === 'hod' && $currentUser->department_id) {
            // Get all employee IDs in this department
            $departmentUserIds = User::where('department_id', $currentUser->department_id)
                ->where('role', 'employee')
                ->pluck('id');

            $hodSubmittedCount = $leaveRequests
                ->whereIn('user_id', $departmentUserIds)
                ->where('status', 'submitted')
                ->count();

        $hodOnProgressCount = $leaveRequests
            ->where('user_id', $departmentUserIds)
            ->where('status', 'pending')
            ->count();

            $hodApprovedCount = $leaveRequests
                ->whereIn('user_id', $departmentUserIds)
                ->where('status', 'approved')
                ->whereNotNull('admin_signature')
                ->count();

            $hodRejectedCount = $leaveRequests
                ->whereIn('user_id', $departmentUserIds)
                ->where('status', 'rejected')
                ->whereNotNull('hod_remarks')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / GLOBAL STATS
        |--------------------------------------------------------------------------
        */
        $allSubmittedCount = $leaveRequests->where('status', 'submitted')->count();
        $allPendingCount = $leaveRequests->whereIn('status', ['pending', 'on_progress'])->count();
        $allApprovedCount = $leaveRequests->where('status', 'approved')->count();
        $allRejectedCount = $leaveRequests->where('status', 'rejected')->count();

        /*
        |--------------------------------------------------------------------------
        | RECENT 3 WAITING YOUR APPROVAL (FOR ADMIN / HOD)
        |--------------------------------------------------------------------------
        */
        $recentPendingLeaves = collect();
        if (in_array($currentUser->role, ['admin', 'hod'])) {
            $recentPendingLeaves = $leaveRequests
                ->whereIn('status', ['pending', 'on_progress'])
                ->sortByDesc('created_at')
                ->take(3)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | CHART DATA (Grouped by request_type)
        |--------------------------------------------------------------------------
        */
        $chartData = $leaveRequests
            ->groupBy('request_type')
            ->map(fn ($group) => $group->count());

        $chartLabels = $chartData->keys()->toArray();
        $chartValues = $chartData->values()->toArray();

        // Employee chart data
        $employeeChartLabels = ['Submitted', 'Pending', 'Approved', 'Rejected'];
        $employeeChartValues = [
            $submittedCount,
            $pendingCount,
            $approvedCount,
            $rejectedCount
        ];

        // HOD chart data
        $hodChartLabels = ['Submitted', 'On Progress', 'Approved', 'Rejected'];
        $hodChartValues = [
            $hodSubmittedCount,
            $hodOnProgressCount,
            $hodApprovedCount,
            $hodRejectedCount
        ];

        return view('admin.dashboard', compact(
            'submittedCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'hodSubmittedCount',
            'hodOnProgressCount',
            'hodApprovedCount',
            'hodRejectedCount',
            'allSubmittedCount',
            'allPendingCount',
            'allApprovedCount',
            'allRejectedCount',
            'recentPendingLeaves',
            'chartLabels',
            'chartValues',
            'leaveRequests',
            'employeeChartLabels',
            'employeeChartValues',
            'hodChartLabels',
            'hodChartValues'
        ));
    }
}
