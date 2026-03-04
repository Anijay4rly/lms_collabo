@extends('admin.layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="container-fluid">

    <!-- Top Actions & Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                Leave Requests
            </h4>
            @if(auth()->user()->role === 'hod')
                <small class="text-muted">
                    <i class="fas fa-building mr-1"></i>
                    Showing requests from {{ auth()->user()->department->name }} department only
                </small>
            @endif
        </div>

        <div>
            <!-- Optional: you can add Create button here later if needed -->
            <!-- <a href="{{ route('leaves.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> New Request
            </a> -->
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        @if(auth()->user()->role === 'admin')
            Viewing <strong>all leave requests</strong> across all departments.
        @elseif(auth()->user()->role === 'hod')
            Viewing <strong>department-level requests</strong> ({{ auth()->user()->department->name }}).
        @endif
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="far fa-list-alt mr-2"></i>
                All Leave Requests
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Reason for Leave</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($leaveRequests as $leave)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $leave->user->full_name }}</span>
                                    </div>
                                    <small class="text-muted d-block">{{ $leave->user->email }}</small>
                                </td>

                                <td>
                                    <span class="badge badge-secondary px-2 py-1">
                                        {{ $leave->user->department->name ?? '—' }}
                                    </span>
                                </td>

                                <td>
                                    {{ ucfirst(str_replace('_', ' ', $leave->request_type)) }}
                                </td>

                                <td>
                                    <small class="text-muted">
                                        {{ Str::limit($leave->reasons ?? 'No reason provided', 60) }}
                                    </small>
                                </td>

                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                        <span class="mx-1">→</span>
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                    </small>
                                </td>

                                <td>
                                    @php
                                        $statusColors = [
                                            'submitted'   => 'info',
                                            'pending'     => 'warning',
                                            'on_progress' => 'warning',
                                            'approved'    => 'success',
                                            'rejected'    => 'danger',
                                        ];
                                        $icons = [
                                            'submitted'   => 'fa-paper-plane',
                                            'pending'     => 'fa-clock',
                                            'on_progress' => 'fa-clock',
                                            'approved'    => 'fa-check-circle',
                                            'rejected'    => 'fa-times-circle',
                                        ];

                                        $realStatus = $leave->status;

                                        if (auth()->user()->role === 'hod' && $realStatus === 'pending') {
                                            $displayStatus = 'on_progress';
                                            $displayText   = 'On Progress';
                                        } else {
                                            $displayStatus = $realStatus;
                                            $displayText   = ucfirst(str_replace('_', ' ', $realStatus));
                                        }

                                        $color = $statusColors[$displayStatus] ?? 'secondary';
                                        $icon  = $icons[$displayStatus] ?? 'fa-question-circle';
                                    @endphp

                                    <span class="badge bg-{{ $color }} px-3 py-2">
                                        <i class="fas {{ $icon }} mr-1"></i>
                                        {{ $displayText }}
                                    </span>
                                </td>

                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('leaves.show', $leave->id) }}"
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @can('update', $leave)
                                            <a href="{{ route('leaves.edit', $leave->id) }}"
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @if(auth()->user()->id === $leave->user_id || auth()->user()->role === 'admin')
                                            <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                        title="Delete" onclick="return confirm('Delete this leave request?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="far fa-calendar-times fa-2x mb-3 d-block"></i>
                                    No leave requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white clearfix">
            {{ $leaveRequests->appends(request()->query())->links() }}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 15,
                "order": [[5, "desc"]]  // default sort by status (newest first-ish)
            });
        }
    });
</script>
@endpush
