@extends('admin.layouts.app')

@section('title', 'View User')
@section('page-title', 'User Details')

@section('content')
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                {{-- User Information Card --}}
                <div class="card card-info card-outline mb-4">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-2"></i>
                            User Information
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>

                            <a href="{{ route('users.index') }}"
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Full Name</strong>
                                <p class="text-muted">{{ $user->full_name }}</p>
                            </div>

                            <div class="col-md-6">
                                <strong>Email</strong>
                                <p class="text-muted">
                                    <a href="mailto:{{ $user->email }}">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Gender</strong>
                                <p class="text-muted">{{ ucfirst($user->gender) }}</p>
                            </div>

                            <div class="col-md-6">
                                <strong>Date of Birth</strong>
                                <p class="text-muted">
                                    {{ $user->dob->format('d M, Y') }}
                                    <br>
                                    <small>
                                        ({{ $user->dob->diffInYears(now()) }} years old)
                                    </small>
                                </p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Role</strong><br>
                                <span class="badge badge-info">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <div class="col-md-6">
                                <strong>Department</strong>
                                <p class="text-muted">
                                    @if($user->department)
                                        <a href="{{ route('departments.show', $user->department->id) }}">
                                            {{ $user->department->name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Account Status</strong><br>
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i>
                                        Verified
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending Verification
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <strong>Member Since</strong>
                                <p class="text-muted">
                                    {{ $user->created_at->format('d M, Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Leave Requests --}}
                        @if($user->LeaveRequests()->count() > 0)
                            <hr>

                            <h5 class="mb-3">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Leave Requests
                                <span class="badge badge-secondary">
                                    {{ $user->LeaveRequests()->count() }}
                                </span>
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->LeaveRequests as $leave)
                                            @php
                                                $statusColors = [
                                                    'submitted' => 'info',
                                                    'pending' => 'warning',
                                                    'on_progress' => 'primary',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                ];
                                                $color = $statusColors[$leave->status] ?? 'secondary';
                                            @endphp
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $leave->request_type)) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $color }}">
                                                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>

                    <div class="card-footer text-right">
                        <small class="text-muted">
                            Last updated:
                            {{ $user->updated_at->format('d M, Y \a\t H:i') }}
                        </small>
                    </div>

                </div>

                {{-- Danger Zone --}}
                @if(auth()->user()->role === 'admin')
                    <div class="card card-danger card-outline">

                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-trash mr-2"></i>
                                Danger Zone
                            </h3>
                        </div>

                        <div class="card-body">
                            @if(auth()->id() !== $user->id)
                                <p class="text-muted">
                                    Deleting this user is permanent and cannot be undone.
                                </p>

                                <form action="{{ route('users.destroy', $user->id) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to permanently delete this user?')">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete User
                                    </button>
                                </form>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    You cannot delete your own account.
                                </p>
                            @endif
                        </div>

                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
