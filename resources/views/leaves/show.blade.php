@extends('admin.layouts.app')

@section('title', 'View Leave Request')
@section('page-title', 'Leave Details')

@section('css')
<style>
    .signature-image {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        max-width: 300px;
        background-color: #f9f9f9;
    }
    .file-viewer-modal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    .file-viewer-modal img,
    .file-viewer-modal iframe {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('leaves.showmy') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to My Leaves
        </a>
    </div>

    {{-- Leave Details Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Leave Request Details</h5>
                @if($leaveRequest->admin_signature && auth()->user()->id === $leaveRequest->user_id)
                    <a href="{{ route('leaves.downloadPDF', $leaveRequest->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i> Download PDF
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">

            {{-- Leave Type --}}
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Leave Type:</div>
                <div class="col-md-8">{{ ucfirst($leaveRequest->request_type) }}</div>
            </div>

            {{-- Duration --}}
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Duration:</div>
                <div class="col-md-8">
                    {{ $leaveRequest->start_date ? \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') : '—' }}
                    -
                    {{ $leaveRequest->end_date ? \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') : '—' }}
                </div>
            </div>

            {{-- Status --}}
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Status:</div>
                <div class="col-md-8">
                    @php
                        $statuses = [
                            'submitted' => ['class' => 'bg-info', 'icon' => 'fa-paper-plane'],
                            'pending' => ['class' => 'bg-warning', 'icon' => 'fa-clock'],
                            'on_progress' => ['class' => 'bg-primary', 'icon' => 'fa-spinner'],
                            'approved' => ['class' => 'bg-success', 'icon' => 'fa-check'],
                            'rejected' => ['class' => 'bg-danger', 'icon' => 'fa-times'],
                        ];
                        $statusData = $statuses[$leaveRequest->status] ?? ['class' => 'bg-secondary', 'icon' => 'fa-question'];
                    @endphp

                    <span class="badge {{ $statusData['class'] }} px-3 py-2">
                        <i class="fas {{ $statusData['icon'] }} me-1"></i>
                        {{ strtoupper(str_replace('_', ' ', $leaveRequest->status)) }}
                    </span>
                </div>
            </div>

            {{-- Report File --}}
            @if($leaveRequest->report_path)
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Attached Report:</div>
                <div class="col-md-8">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fileViewerModal">
                            <i class="fas fa-eye me-1"></i> View
                        </button>
                        <a href="{{ asset('storage/' . $leaveRequest->report_path) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- HOD Signature --}}
            @if($leaveRequest->hod_signature)
            <div class="row mb-3 mt-4 pt-3 border-top">
                <div class="col-md-4 fw-semibold">HOD Approval:</div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <img src="{{ $leaveRequest->hod_signature }}" alt="HOD Signature" class="signature-image img-fluid">
                    </div>
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar me-1"></i> Signed on: {{ \Carbon\Carbon::parse($leaveRequest->hod_signed_at)->format('d M Y H:i:s') }}
                    </small>
                    @if($leaveRequest->hod_remarks)
                        <small class="text-muted d-block">
                            <i class="fas fa-comment me-1"></i> Remarks: {{ $leaveRequest->hod_remarks }}
                        </small>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin Signature --}}
            @if($leaveRequest->admin_signature)
            <div class="row mb-3 pt-3 border-top">
                <div class="col-md-4 fw-semibold">Admin Approval:</div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <img src="{{ $leaveRequest->admin_signature }}" alt="Admin Signature" class="signature-image img-fluid">
                    </div>
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar me-1"></i> Signed on: {{ \Carbon\Carbon::parse($leaveRequest->admin_signed_at)->format('d M Y H:i:s') }}
                    </small>
                    @if($leaveRequest->admin_remarks)
                        <small class="text-muted d-block">
                            <i class="fas fa-comment me-1"></i> Remarks: {{ $leaveRequest->admin_remarks }}
                        </small>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin Remark --}}
            @if($leaveRequest->admin_remark)
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Admin Remark:</div>
                <div class="col-md-8">
                    <span class="text-muted">{{ $leaveRequest->admin_remark }}</span>
                </div>
            </div>
            @endif

            {{-- Created & Updated --}}
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Submitted At:</div>
                <div class="col-md-8">
                    {{ $leaveRequest->created_at?->format('d M Y H:i') ?? '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Last Updated:</div>
                <div class="col-md-8">
                    {{ $leaveRequest->updated_at?->format('d M Y H:i') ?? '—' }}
                </div>
            </div>

        </div>
    </div>

</div>

{{-- File Viewer Modal --}}
@if($leaveRequest->report_path)
<div class="modal fade file-viewer-modal" id="fileViewerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @php
                    $filePath = $leaveRequest->report_path;
                    $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                @endphp

                @if(in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $filePath) }}" alt="Document" class="img-fluid">
                @elseif($fileExt === 'pdf')
                    <iframe src="{{ asset('storage/' . $filePath) }}" width="100%" height="600px"></iframe>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        File format not supported for inline viewing.
                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn btn-sm btn-primary ms-2">
                            <i class="fas fa-download me-1"></i> Download Instead
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
