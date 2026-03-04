@extends('admin.layouts.app')

@section('title','Leave Request Details')

@section('content')
<div class="container py-4">

@php
    $user = $leaveRequest->user;
    $department = $user?->department;

    $days = ($leaveRequest->start_date && $leaveRequest->end_date)
        ? \Carbon\Carbon::parse($leaveRequest->start_date)
            ->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1
        : null;

    $status = $leaveRequest->status;

    $progress = match($status) {
        'submitted' => 25,
        'pending'   => 60,
        'approved'  => 100,
        'rejected'  => 100,
        default     => 10
    };

    $canApproveOrReject = false;

    if(auth()->user()->role === 'hod' && $status === 'submitted'){
        $canApproveOrReject = true;
    }

    if(auth()->user()->role === 'admin' && $status === 'pending'){
        $canApproveOrReject = true;
    }

    $histories = $leaveRequest->leavehistories()
                    ->with('user')
                    ->orderBy('created_at')
                    ->get();
@endphp


{{-- ALERTS --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif


<div class="card shadow border-0">

{{-- HEADER --}}
<div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h5 class="fw-bold mb-0">Leave Request</h5>
            <small class="text-muted">
                Submitted {{ $leaveRequest->created_at?->format('d M Y H:i') }}
                ({{ $leaveRequest->created_at?->diffForHumans() }})
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge
                @if($status=='approved') bg-success
                @elseif($status=='rejected') bg-danger
                @elseif($status=='pending') bg-warning
                @else bg-secondary
                @endif px-3 py-2">
                {{ strtoupper($status) }}
            </span>

            @if($status === 'approved')
            <a href="{{ route('leaves.download', $leaveRequest->id) }}"
               class="btn btn-outline-primary btn-sm">
                Download PDF
            </a>
            @endif
        </div>
    </div>

    {{-- PROGRESS --}}
    <div class="mt-3">
        <div class="progress" style="height:8px;">
            <div class="progress-bar
                @if($status=='approved') bg-success
                @elseif($status=='rejected') bg-danger
                @else bg-primary
                @endif"
                style="width: {{ $progress }}%">
            </div>
        </div>
    </div>
</div>


{{-- BODY --}}
<div class="card-body">
<div class="row">

<div class="col-md-6 border-end">

<h6 class="fw-bold text-primary">Employee Information</h6>
<hr>

<p><strong>Full Name:</strong>
{{ $user ? trim($user->fname.' '.$user->mname.' '.$user->lname) : 'N/A' }}
</p>

<p><strong>Department:</strong> {{ $department?->name ?? 'N/A' }}</p>
<p><strong>Position:</strong> {{ $user?->assigned_as ?? 'N/A' }}</p>
<p><strong>Request Type:</strong> {{ ucfirst($leaveRequest->request_type) }}</p>

<h6 class="fw-bold text-primary mt-4">Leave Details</h6>
<hr>

<p><strong>Start Date:</strong> {{ $leaveRequest->start_date?->format('d M Y') }}</p>
<p><strong>End Date:</strong> {{ $leaveRequest->end_date?->format('d M Y') }}</p>
<p><strong>Total Days:</strong> {{ $days }}</p>
<p><strong>Reason:</strong><br>{{ $leaveRequest->reasons }}</p>
<p><strong>Destination:</strong><br>{{ $leaveRequest->destination }}</p>

</div>


{{-- TIMELINE --}}
<div class="col-md-6">
<h6 class="fw-bold text-primary">Approval Timeline</h6>
<hr>

@foreach($histories as $history)

@php
$color = 'primary';
if(str_contains($history->action,'approved')) $color='success';
if(str_contains($history->action,'rejected')) $color='danger';
@endphp

<div class="mb-3 border-start border-4 border-{{ $color }} ps-3">
<strong>{{ ucwords(str_replace('_',' ', $history->action)) }}</strong><br>
<small class="text-muted">
{{ $history->created_at?->format('d M Y H:i') }}
@if($history->user)
- {{ $history->user->fname }} {{ $history->user->lname }}
({{ strtoupper($history->user->role) }})
@endif
</small>

@if($history->remarks)
<div class="mt-1 text-muted">
<strong>Remarks:</strong> {{ $history->remarks }}
</div>
@endif
</div>

@endforeach

</div>
</div>
</div>


{{-- FOOTER --}}
<div class="card-footer text-end bg-white">

@if($canApproveOrReject)
<button class="btn btn-success btn-sm me-2"
        data-bs-toggle="modal"
        data-bs-target="#approveModal">
Approve
</button>

<button class="btn btn-danger btn-sm me-2"
        data-bs-toggle="modal"
        data-bs-target="#rejectModal">
Reject
</button>
@endif

<a href="{{ route('leaves.index') }}" class="btn btn-secondary btn-sm">
Back
</a>

</div>
</div>
</div>


{{-- APPROVE MODAL --}}
<div class="modal fade" id="approveModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST" action="{{ route('leaves.approve', $leaveRequest->id) }}">
@csrf
@method('PATCH')
<div class="modal-header bg-success text-white">
<h5 class="modal-title">Approve Leave</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<p>
@if(auth()->user()->role === 'hod')
This will move the request to <strong>Pending (Admin Review)</strong>.
@else
This will fully approve the leave request.
@endif
</p>

<div class="mb-3">
<label class="form-label">Remarks (Optional)</label>
<textarea name="remarks"
          class="form-control"
          rows="4"
          maxlength="1500"
          placeholder="Enter approval remarks...">{{ old('remarks') }}</textarea>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>
<button type="submit" class="btn btn-success">
Confirm Approval
</button>
</div>

</form>

</div>
</div>
</div>


{{-- REJECT MODAL --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<form method="POST" action="{{ route('leaves.reject', $leaveRequest->id) }}">
@csrf
@method('PATCH')

<div class="modal-header bg-danger text-white">
<h5 class="modal-title">Reject Leave</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<p class="text-danger">
This action will permanently reject this leave request.
</p>

<div class="mb-3">
<label class="form-label">Rejection Reason (Optional)</label>
<textarea name="remarks"
          class="form-control"
          rows="4"
          maxlength="1500"
          placeholder="Enter rejection reason...">{{ old('remarks') }}</textarea>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>
<button type="submit" class="btn btn-danger">
Confirm Rejection
</button>
</div>

</form>

</div>
</div>
</div>

@endsection
