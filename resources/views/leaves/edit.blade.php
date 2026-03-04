@extends('admin.layouts.app')

@section('title', 'Manage Leave Request')
@section('page-title', 'Manage Leave Request')

@section('css')
<style>
    .signature-canvas {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        cursor: crosshair;
    }
    .signature-box {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Manage Leave Request
                    </h5>
                </div>

                <div class="card-body">
                    <div class="mb-4 p-3 bg-light border rounded">
                        <h6 class="mb-3">Leave Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Employee:</strong> {{ $leaveRequest->user->full_name }}
                                </p>
                                <p class="mb-2">
                                    <strong>Email:</strong> {{ $leaveRequest->user->email }}
                                </p>
                                <p class="mb-0">
                                    <strong>Department:</strong> {{ $leaveRequest->user->department->name ?? '—' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Leave Type:</strong> {{ ucfirst(str_replace('_', ' ', $leaveRequest->request_type)) }}
                                </p>
                                <p class="mb-2">
                                    <strong>Duration:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Days:</strong> {{ \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays(\Carbon\Carbon::parse($leaveRequest->end_date)) + 1 }} days
                                </p>

                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Validation errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('leaves.update', $leaveRequest->id) }}" method="POST" enctype="multipart/form-data" id="leaveForm">
                        @csrf
                        @method('PUT')

                        {{-- Support Document Section --}}
                        @if($leaveRequest->report_path)
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-file me-2"></i>Support Document</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#fileViewerModal">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                    <a href="{{ asset('storage/' . $leaveRequest->report_path) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="submitted" {{ old('status', $leaveRequest->status) === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="pending" {{ old('status', $leaveRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="on_progress" {{ old('status', $leaveRequest->status) === 'on_progress' ? 'selected' : '' }}>On Progress</option>
                                <option value="approved" {{ old('status', $leaveRequest->status) === 'approved' ? 'selected' : '' }} class="text-success">Approved</option>
                                <option value="rejected" {{ old('status', $leaveRequest->status) === 'rejected' ? 'selected' : '' }} class="text-danger">Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Update the status of this leave request</small>
                        </div>

                        {{-- HOD Signature Section --}}
                        @if(auth()->user()->role === 'hod' && auth()->user()->department_id === $leaveRequest->user->department_id)
                            <div class="signature-box border border-success">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><i class="fas fa-pen-fancy me-2"></i>Head of Department Action</h6>
                                    @if(!$leaveRequest->hod_signature)
                                        <span class="badge bg-warning">Pending Your Approval</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>
                                    @endif
                                </div>
                                <hr>

                                @if(!$leaveRequest->hod_signature)
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Your Digital Signature</strong> <span class="text-danger">*</span></label>
                                        <div class="border rounded p-2 bg-white">
                                            <canvas id="hodSignatureCanvas" class="signature-canvas w-100" width="500" height="150"></canvas>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearHodSignature()">
                                                <i class="fas fa-eraser me-1"></i> Clear
                                            </button>
                                        </div>
                                        <input type="hidden" name="hod_signature" id="hodSignatureInput" value="">
                                    </div>

                                    <div class="mb-3">
                                        <label for="hod_remarks" class="form-label">Remarks <small>(Optional)</small></label>
                                        <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3" placeholder="Add any remarks for this approval..."></textarea>
                                    </div>

                                    <script>
                                        const hodCanvas = document.getElementById('hodSignatureCanvas');
                                        const hodCtx = hodCanvas.getContext('2d');
                                        let isDrawingHod = false;

                                        hodCanvas.addEventListener('mousedown', () => isDrawingHod = true);
                                        hodCanvas.addEventListener('mouseup', () => {
                                            isDrawingHod = false;
                                            document.getElementById('hodSignatureInput').value = hodCanvas.toDataURL();
                                        });
                                        hodCanvas.addEventListener('mousemove', (e) => {
                                            if (!isDrawingHod) return;
                                            const rect = hodCanvas.getBoundingClientRect();
                                            const x = (e.clientX - rect.left) * (hodCanvas.width / rect.width);
                                            const y = (e.clientY - rect.top) * (hodCanvas.height / rect.height);

                                            hodCtx.lineWidth = 2;
                                            hodCtx.lineCap = 'round';
                                            hodCtx.strokeStyle = '#000';
                                            hodCtx.lineTo(x, y);
                                            hodCtx.stroke();
                                        });

                                        function clearHodSignature() {
                                            hodCtx.clearRect(0, 0, hodCanvas.width, hodCanvas.height);
                                            document.getElementById('hodSignatureInput').value = '';
                                        }
                                    </script>
                                @else
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Already Approved by HOD</strong>
                                        <p class="mb-0 mt-2">Approved on: {{ \Carbon\Carbon::parse($leaveRequest->hod_signed_at)->format('d M Y H:i:s') }}</p>
                                        @if($leaveRequest->hod_remarks)
                                            <p class="mb-0">Remarks: {{ $leaveRequest->hod_remarks }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Admin Signature Section --}}
                        @if(auth()->user()->role === 'admin' && $leaveRequest->status === 'on_progress' && !$leaveRequest->admin_signature)
                            <div class="signature-box border border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><i class="fas fa-pen-fancy me-2"></i>Administrative Review & Approval</h6>
                                    <span class="badge bg-warning">Pending Your Signature</span>
                                </div>
                                <hr>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Your Digital Signature</strong> <span class="text-danger">*</span></label>
                                    <div class="border rounded p-2 bg-white">
                                        <canvas id="adminSignatureCanvas" class="signature-canvas w-100" width="500" height="150"></canvas>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAdminSignature()">
                                            <i class="fas fa-eraser me-1"></i> Clear
                                        </button>
                                    </div>
                                    <input type="hidden" name="admin_signature" id="adminSignatureInput" value="">
                                </div>

                                <div class="mb-3">
                                    <label for="admin_remarks" class="form-label">Remarks <small>(Optional)</small></label>
                                    <textarea class="form-control" id="admin_remarks" name="admin_remarks" rows="3" placeholder="Add any remarks for this approval..."></textarea>
                                </div>

                                <script>
                                    const adminCanvas = document.getElementById('adminSignatureCanvas');
                                    const adminCtx = adminCanvas.getContext('2d');
                                    let isDrawingAdmin = false;

                                    adminCanvas.addEventListener('mousedown', () => isDrawingAdmin = true);
                                    adminCanvas.addEventListener('mouseup', () => {
                                        isDrawingAdmin = false;
                                        document.getElementById('adminSignatureInput').value = adminCanvas.toDataURL();
                                    });
                                    adminCanvas.addEventListener('mousemove', (e) => {
                                        if (!isDrawingAdmin) return;
                                        const rect = adminCanvas.getBoundingClientRect();
                                        const x = (e.clientX - rect.left) * (adminCanvas.width / rect.width);
                                        const y = (e.clientY - rect.top) * (adminCanvas.height / rect.height);

                                        adminCtx.lineWidth = 2;
                                        adminCtx.lineCap = 'round';
                                        adminCtx.strokeStyle = '#000';
                                        adminCtx.lineTo(x, y);
                                        adminCtx.stroke();
                                    });

                                    function clearAdminSignature() {
                                        adminCtx.clearRect(0, 0, adminCanvas.width, adminCanvas.height);
                                        document.getElementById('adminSignatureInput').value = '';
                                    }
                                </script>
                            </div>
                        @elseif(auth()->user()->role === 'admin' && $leaveRequest->admin_signature)
                            <div class="signature-box">
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Already Approved by Admin</strong>
                                    <p class="mb-0 mt-2">Approved on: {{ \Carbon\Carbon::parse($leaveRequest->admin_signed_at)->format('d M Y H:i:s') }}</p>
                                    @if($leaveRequest->admin_remarks)
                                        <p class="mb-0">Remarks: {{ $leaveRequest->admin_remarks }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="form-group d-flex gap-2">
                            @if(auth()->user()->role === 'hod' && auth()->user()->department_id === $leaveRequest->user->department_id && !$leaveRequest->hod_signature)
                                <button type="submit" class="btn btn-success" onclick="setStatusBeforeSubmit('on_progress')">
                                    <i class="fas fa-check me-2"></i> Approve (Sign)
                                </button>
                            @elseif(auth()->user()->role === 'admin' && $leaveRequest->status === 'on_progress' && !$leaveRequest->admin_signature)
                                <button type="submit" class="btn btn-success" onclick="setStatusBeforeSubmit('approved')">
                                    <i class="fas fa-check me-2"></i> Approve (Sign)
                                </button>
                            @else
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i> Update Request
                                </button>
                            @endif

                            <a href="{{ route('leaves.show', $leaveRequest->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </a>
                        </div>
                    </form>
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

<script>
    function setStatusBeforeSubmit(status) {
        document.getElementById('status').value = status;
    }
</script>
@endsection
