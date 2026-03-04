<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Default redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login Routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'submit'])->name('login.submit');

// Register Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.submit');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard & Resources (only accessible to authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Users / Public Servants
    Route::resource('users', UserController::class);

    // Leave Requests
    Route::get('myleaves/', [LeaveRequestController::class,'showMyLeave'])
        ->name('leaves.showmy');

    Route::get('leaves/approved', [LeaveRequestController::class,'approved'])->name('leaves.approved');
    Route::get('leaves/pending', [LeaveRequestController::class,'pending'])->name('leaves.pending');
    Route::get('leaves/rejected', [LeaveRequestController::class,'rejected'])->name('leaves.rejected');
    Route::get('leaves/onprogress', [LeaveRequestController::class,'onprogress'])->name('leaves.onprogress');
    Route::get('leaves/staff', [LeaveRequestController::class,'staff'])->name('leaves.staff');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::patch('leaves/{id}/approve', [LeaveRequestController::class,'approve'])->name('leaves.approve');
    Route::patch('leaves/{id}/reject', [LeaveRequestController::class,'reject'])->name('leaves.reject');
    Route::resource('leaves', LeaveRequestController::class);

    Route::get('leaves/{id}/download-pdf', [LeaveRequestController::class, 'downloadPDF'])
        ->name('leaves.download');

    Route::resource('users', UserController::class);


    // Reports
    Route::get('reports/leaves', [ReportController::class, 'index'])->name('report.index');
    Route::get('reports/{id}/individual', [ReportController::class, 'individualreport'])->name('report.individual');


    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //
    // Audit Logs (route defined above)
});
