@extends('admin.layouts.app')

@section('title', 'Reject Leave Request')
@section('page-title', 'Reject Leave Request')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table id="dataTable" class="table table-hover align-middle table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                       <th>Reason for Leave</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($rejectedLeaves as $leave)
                        <tr>
                            <td>
                                <span class="fw-semibold">
                                    {{ $leave->user->full_name }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $leave->user->email }}</small>
                            </td>
                            <td>{{ $leave->user->department->name }}</td>
                            <td>{{ ucfirst($leave->request_type) }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $leave->reasons ?? 'No reason provided' }}
                                </small>
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
                                <span class="badge bg-danger px-2 py-1">
                                    Rejected
                                </span>
                            </td>
                            <td>
                                {{-- <a href="{{ route('leaves.show', $leave->id) }}" class="
                                    btn btn-info btn-sm
                                    @if($leave->admin_signature) me-2 @endif
                                ">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                @if($leave->admin_signature)
                                    <a href="{{ route('leaves.downloadPDF', $leave->id) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-download me-1"></i> Download PDF
                                    </a>
                                @endif --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

