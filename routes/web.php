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

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Users / Public Servants
    Route::resource('users', UserController::class);

    // Leave Requests
    Route::get('myleaves/', [LeaveRequestController::class,'showMyLeave'])
        ->name('leaves.showmy');

    Route::resource('leaves', LeaveRequestController::class);

    Route::get('leaves/{id}/download-pdf', [LeaveRequestController::class, 'downloadPDF'])
        ->name('leaves.downloadPDF');

    Route::resource('users', UserController::class);


    // Reports
    Route::resource('reports', ReportController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs');
});
