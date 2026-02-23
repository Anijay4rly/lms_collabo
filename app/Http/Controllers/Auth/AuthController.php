<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
            // Redirect to the dashboard route
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
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
        ]);

        User::create([
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'password' => Hash::make($request->password),
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
    $userId = auth()->id();
    $currentUser = auth()->user();

    // Fetch all leave requests with user relation
    $leaveRequests = \App\Models\LeaveRequest::with('user')->get();

    // -------- User-specific stats --------
    $submittedCount = $leaveRequests->where('status', 'submitted')->where('user_id', $userId)->count();
    $pendingCount   = $leaveRequests->where('status', ['pending','on_progress'])->where('user_id', $userId)->count();
    $approvedCount  = $leaveRequests->where('status', 'approved')->where('user_id', $userId)->count();
    $rejectedCount  = $leaveRequests->where('status', 'rejected')->where('user_id', $userId)->count();

    // -------- Admin / all users stats --------
    $allSubmittedCount = $leaveRequests->where('status','submitted')->count();
    $allPendingCount   = $leaveRequests->where('status',['pending','on_progress'])->count();
    $allApprovedCount  = $leaveRequests->where('status','approved')->count();
    $allRejectedCount  = $leaveRequests->where('status','rejected')->count();

    // -------- HOD Department User Count --------
    $hodDepartmentUserCount = 0;
    if ($currentUser->role === 'hod' && $currentUser->department_id) {
        $hodDepartmentUserCount = User::where('department_id', $currentUser->department_id)->count();
    }

    // -------- Latest pending requests (admin view or user view) --------
    $latestPending = $leaveRequests
        ->where('status', ['pending','on_progress'])
        ->sortByDesc('created_at')
        ->take(6);

    // -------- Chart data (grouped by request_type) --------
    $chartData = $leaveRequests->groupBy('request_type')->map(function ($group) {
        return count($group);
    });
    $chartLabels = $chartData->keys()->toArray();
    $chartValues = $chartData->values()->toArray();

    return view('admin.dashboard', compact(
        'submittedCount',
        'pendingCount',
        'approvedCount',
        'rejectedCount',
        'allSubmittedCount',
        'allPendingCount',
        'allApprovedCount',
        'allRejectedCount',
        'hodDepartmentUserCount',
        'latestPending',
        'chartLabels',
        'chartValues',
        'leaveRequests' // optional, if you need full list in the view
    ));
}


}
