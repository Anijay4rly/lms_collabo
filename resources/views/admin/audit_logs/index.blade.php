@extends('admin.layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Log History')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3">Audit Log</h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->user->full_name ?? 'System' }}</td>
                            <td>{{ $log->action }}</td>
                            <td><pre class="mb-0">{{ $log->description }}</pre></td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ Str::limit($log->user_agent, 40) }}</td>
                            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: false,
                autoWidth: false,
                responsive: true
            });
        }
    });
</script>
@endpush
