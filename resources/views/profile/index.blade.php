@extends('admin.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">

            {{-- LEFT SIDE PROFILE CARD --}}
            <div class="col-md-4">
                <div class="card card-primary card-outline text-center">
                    <div class="card-body box-profile">

                        <img class="profile-user-img img-fluid img-circle mb-3"
                             src="{{ auth()->user()->profile_image ?? asset('default-profile.png') }}"
                             alt="User profile picture"
                             style="width:130px;height:130px;object-fit:cover;">

                        <h4 class="mb-1">
                            {{ auth()->user()->fname }}
                            {{ auth()->user()->mname }}
                            {{ auth()->user()->lname }}
                        </h4>

                        <p class="text-muted mb-1">
                            {{ ucfirst(auth()->user()->role) }}
                        </p>

                        <span class="badge bg-success">
                            {{ ucfirst(auth()->user()->status ?? 'active') }}
                        </span>

                        <hr>

                        <a href="{{ route('profile.edit') }}"
                           class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE DETAILS --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <strong>Profile Details</strong>
                    </div>

                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-4 text-muted">Email</div>
                            <div class="col-md-8">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 text-muted">Department</div>
                            <div class="col-md-8">
                                {{ auth()->user()->department->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 text-muted">Position</div>
                            <div class="col-md-8">
                                {{ auth()->user()->assigned_as ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 text-muted">Role</div>
                            <div class="col-md-8">
                                {{ ucfirst(auth()->user()->role) }}
                            </div>
                        </div>

                        <hr>

                        {{-- Digital Signature --}}
                        <h6 class="text-primary">Digital Signature</h6>

                        @if(auth()->user()->signature)
                            <div class="mt-2">
                                <img src="{{ asset(auth()->user()->signature) }}"
                                     style="max-height:120px; border:1px solid #ddd; padding:10px; background:#fff;"
                                     class="rounded">
                            </div>
                        @else
                            <div class="alert alert-warning mt-2">
                                No digital signature uploaded.
                                <a href="{{ route('profile.edit') }}" class="ml-2">
                                    Upload Now
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
