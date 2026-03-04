@extends('admin.layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="container-fluid">


{{-- ================= EMPLOYEE ================= --}}
@if(auth()->user()->role === 'employee')
<div class="row">
    @include('admin.layouts.employee-cards')
</div>
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">My Leave Overview</h3>
            </div>
            <div class="card-body">
                <canvas id="employeeBarChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">My Leave Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="employeePieChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif


{{-- ================= HOD ================= --}}
@if(auth()->user()->role === 'hod')
<div class="row">
    <div class="col-lg-3 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $hodSubmittedCount }}</h3>
                <p>Pending request</p>
            </div>
            <div class="icon"><i class="fas fa-paper-plane"></i></div>
            <a href="{{ route('leaves.pending') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $hodOnProgressCount }}</h3>
                <p>On_progress request</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('leaves.pending') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $hodApprovedCount }}</h3>
                <p>Approved Requests</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('leaves.approved') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $hodRejectedCount }}</h3>
                <p>Rejected Requests</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <a href="{{ route('leaves.rejected') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Department Leave Overview</h3>
            </div>
            <div class="card-body">
                <canvas id="hodBarChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Department Leave Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="hodPieChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif


{{-- ================= ADMIN ================= --}}
@if(auth()->user()->role === 'admin')
<div class="row">

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $allSubmittedCount }}</h3>
                <p>Total Submitted</p>
            </div>
            <div class="icon"><i class="fas fa-paper-plane"></i></div>
            <a href="{{ route('leaves.index') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $allPendingCount }}</h3>
                <p>Total Pending</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
            <a href="{{ route('leaves.index') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $allApprovedCount }}</h3>
                <p>Total Approved</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('leaves.index') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $allRejectedCount }}</h3>
                <p>Total Rejected</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <a href="{{ route('leaves.index') }}" class="small-box-footer">
                View Info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

</div>

{{-- CHARTS --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Leave Overview</h3>
            </div>
            <div class="card-body">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Leave Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- RECENT WAITING --}}
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-hourglass-half mr-2"></i>
                    3 Recent Leaves — Waiting Your Approval
                </h3>
            </div>

            <div class="card-body">
                @forelse($recentPendingLeaves as $leave)
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>{{ $leave->user->full_name }}</h5>
                            <small>{{ $leave->start_date }} → {{ $leave->end_date }}</small>
                        </div>
                        <span class="badge badge-warning px-3 py-2">
                            Waiting Your Approval
                        </span>
                    </div>
                    <div class="mt-2">
                        <strong>Reason:</strong>
                        <p class="mb-0 text-muted">{{ $leave->reason }}</p>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No pending leave requests.</p>
                @endforelse
            </div>

            <div class="card-footer text-right">
                <a href="{{ route('leaves.index') }}" class="btn btn-warning btn-sm">
                    View All Requests
                </a>
            </div>
        </div>
    </div>
</div>

@endif

</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(auth()->user()->role === 'admin')
<script>
const dataValues = [
    {{ $allSubmittedCount }},
    {{ $allPendingCount }},
    {{ $allApprovedCount }},
    {{ $allRejectedCount }}
];
const labels = ['Submitted','Pending','Approved','Rejected'];
const colors = ['#17a2b8','#ffc107','#28a745','#dc3545'];
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: { labels: labels,
        datasets: [{ data: dataValues, backgroundColor: colors }]
    }
});
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: { labels: labels,
        datasets: [{ data: dataValues, backgroundColor: colors }]
    }
});
</script>
@endif

@if(auth()->user()->role === 'employee')
<script>
const employeeLabels = @json($employeeChartLabels);
const employeeValues = @json($employeeChartValues);
const employeeColors = ['#17a2b8','#ffc107','#28a745','#dc3545'];
if (employeeValues.some(v => v > 0)) {
    new Chart(document.getElementById('employeeBarChart'), {
        type: 'bar',
        data: { labels: employeeLabels,
            datasets: [{ data: employeeValues, backgroundColor: employeeColors }]
        }
    });
    new Chart(document.getElementById('employeePieChart'), {
        type: 'pie',
        data: { labels: employeeLabels,
            datasets: [{ data: employeeValues, backgroundColor: employeeColors }]
        }
    });
} else {
    document.getElementById('employeeBarChart').parentElement.innerHTML = '<div class="alert alert-info">No leave data to display.</div>';
    document.getElementById('employeePieChart').parentElement.innerHTML = '<div class="alert alert-info">No leave data to display.</div>';
}
</script>
@endif

@if(auth()->user()->role === 'hod')
<script>
const hodLabels = @json($hodChartLabels);
const hodValues = @json($hodChartValues);
const hodColors = ['#17a2b8','#007bff','#28a745','#dc3545'];
if (hodValues.some(v => v > 0)) {
    new Chart(document.getElementById('hodBarChart'), {
        type: 'bar',
        data: { labels: hodLabels,
            datasets: [{ data: hodValues, backgroundColor: hodColors }]
        }
    });
    new Chart(document.getElementById('hodPieChart'), {
        type: 'pie',
        data: { labels: hodLabels,
            datasets: [{ data: hodValues, backgroundColor: hodColors }]
        }
    });
} else {
    document.getElementById('hodBarChart').parentElement.innerHTML = '<div class="alert alert-info">No department leave data to display.</div>';
    document.getElementById('hodPieChart').parentElement.innerHTML = '<div class="alert alert-info">No department leave data to display.</div>';
}
</script>
@endif
@endsection
