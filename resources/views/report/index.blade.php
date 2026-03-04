@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1 fw-bold text-primary">Generate Reports</h2>

    <div class="card shadow-sm mb-2 border-0">
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('report.index') }}" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-bold">From Date:</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">To Date:</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Department:</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Status:</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end mt-2">
                    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary me-2">Reset</a>
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter Results
                    </button>
                    <button type="submit" name="export_pdf" value="true" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Report Results ({{ $leaves->count() }} records found)</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $leave->user->first_name }} {{ $leave->user->last_name }}</td>
                            <td>{{ $leave->user->department->name ?? '-' }}</td>
                            <td>{{ ucfirst($leave->request_type) }}</td>
                            <td>{{ $leave->start_date }}</td>
                            <td>
                                <span class="badge bg-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-4">No records match your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $leaves->withQueryString()->links() }}
    </div>
</div>
@endsection
