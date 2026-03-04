@extends('admin.layouts.app')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personal Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3 text-center">
                            <img src="{{ auth()->user()->profile_image ?? '/default-profile.png' }}" class="rounded-circle mb-2" width="120" height="120">
                            <input type="file" name="profile_image" class="form-control mt-2">
                        </div>
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="{{ auth()->user()->full_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Change Password</label>
                            <input type="password" name="password" class="form-control" placeholder="New Password">
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
