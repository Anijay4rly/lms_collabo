@extends('admin.layouts.app')

@section('title', 'Leave Requests')
@section('page-title', 'Leave Requests Management')

@section('content')
<div class="container-fluid">

    {{-- Top Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">Leave Requests</h5>
            @if(auth()->user()->role === 'hod')
                <small class="text-muted">
                    <i class="fas fa-building me-1"></i> Requests from {{ auth()->user()->department->name }} department
                </small>
            @endif
        </div>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Apply Leave
        </a>
    </div>

    {{-- Info Badge --}}
    <div class="alert alert-info mb-3" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        @if(auth()->user()->role === 'admin')
            You are viewing all leave requests from all departments.
        @elseif(auth()->user()->role === 'hod')
            You are viewing all leave requests from your department ({{ auth()->user()->department->name }}).
        @endif
    </div>

    {{-- Leave Requests Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table id="dataTable" class="table table-hover align-middle table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($leaveRequests as $leave)
                        <tr>
                            <td>
                                <span class="fw-semibold">
                                    {{ $leave->user->full_name }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $leave->user->email }}</small>
                            </td>

                            <td>
                                <span class="badge bg-secondary px-2 py-1">
                                    {{ $leave->user->department->name ?? '—' }}
                                </span>
                            </td>

                            <td>
                                {{ ucfirst(str_replace('_', ' ', $leave->request_type)) }}
                            </td>

                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                    <br>
                                    to
                                    <br>
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </small>
                            </td>

                            <td>
                                @php
                                    $statusColors = [
                                        'submitted' => 'info',
                                        'pending' => 'warning',
                                        'on_progress' => 'primary',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                    $color = $statusColors[$leave->status] ?? 'secondary';
                                    $icons = [
                                        'submitted' => 'fa-paper-plane',
                                        'pending' => 'fa-clock',
                                        'on_progress' => 'fa-spinner',
                                        'approved' => 'fa-check',
                                        'rejected' => 'fa-times',
                                    ];
                                    $icon = $icons[$leave->status] ?? 'fa-question';

                                    // Admin sees on_progress as "pending"
                                    $displayStatus = ($leave->status === 'on_progress' && auth()->user()->role === 'admin')
                                        ? 'pending'
                                        : $leave->status;
                                    $displayColor = ($leave->status === 'on_progress' && auth()->user()->role === 'admin')
                                        ? $statusColors['pending']
                                        : $color;
                                    $displayIcon = ($leave->status === 'on_progress' && auth()->user()->role === 'admin')
                                        ? $icons['pending']
                                        : $icon;
                                @endphp
                                <span class="badge bg-{{ $displayColor }} px-3 py-2">
                                    <i class="fas {{ $displayIcon }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $displayStatus)) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-1 flex-wrap">

                                    <a href="{{ route('leaves.show', $leave->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(auth()->user()->role === 'hod' || auth()->user()->role === 'admin')
                                        <a href="{{ route('leaves.edit', $leave->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="Approve/Reject">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->id === $leave->user_id || auth()->user()->role === 'admin')
                                        <form action="{{ route('leaves.destroy', $leave->id) }}"
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this request?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
