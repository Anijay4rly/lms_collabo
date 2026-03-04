@extends('admin.layouts.app')

@section('title', 'On Progress Leave Requests')


@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h3 class="card-title">
                <i class="fas fa-hourglass-half mr-2 text-warning"></i>
                On Progress Leave Requests
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="dataTable" class="table table-hover align-middle table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Reason for Leave</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($onProgressLeaves as $leave)
                            <tr>
                                <td>
                                    <span class="fw-semibold">
                                        {{ $leave->user->full_name }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $leave->user->email }}</small>
                                </td>
                                <td>{{ $leave->user->department->name ?? '—' }}</td>
                                <td>{{ ucfirst($leave->request_type) }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $leave->reasons ?? 'No reason provided' }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                        <br>to<br>
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-hourglass-half mr-1"></i>
                                        On Progress
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No on-progress leave requests at the moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer clearfix">
            {{ $onProgressLeaves->links() }}
        </div>
    </div>
</div>
@endsection
