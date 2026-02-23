@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">

    {{-- ================= EMPLOYEE STATS (ALL ROLES) ================= --}}
   <div class="row">

    <div class="col-lg-3 col-md-6 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $submittedCount }}</h3>
                <p>My Requests</p>
            </div>
            <div class="icon">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingCount }}</h3>
                <p>Pending</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $approvedCount }}</h3>
                <p>Approved</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $rejectedCount }}</h3>
                <p>Rejected</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

</div>


    {{-- ================= HOD STATS (HOD + ADMIN) ================= --}}
    @if(auth()->user()->role === 'hod' || auth()->user()->role === 'admin')
    <div class="row mt-4">

        <div class="col-lg-3 col-12">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $hodDepartmentUserCount }}</h3>
                    <p>Department Staff</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingCount }}</h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $approvedCount }}</h3>
                    <p>Approved by You</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $rejectedCount }}</h3>
                    <p>Rejected by You</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ================= ADMIN STATS (ADMIN ONLY) ================= --}}
    @if(auth()->user()->role === 'admin')
    <div class="row mt-4">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $allSubmittedCount }}</h3>
                    <p>Total Submitted</p>
                </div>
                <div class="icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $allPendingCount }}</h3>
                    <p>Total Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $allApprovedCount }}</h3>
                    <p>Total Approved</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $allRejectedCount }}</h3>
                    <p>Total Rejected</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

    </div>
    @endif

</div>
@endsection
