@extends('admin.layouts.app')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="content">
    <div class="container-fluid">

        {{-- Success/Error Messages --}}
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

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-circle mr-2"></i>
                    Personal Profile
                </h3>
            </div>

            <form action="{{ route('profile.update') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- ================= AVATAR ================= --}}
                    <div class="text-center mb-4">
                        <img src="{{ auth()->user()->profile_image ?? asset('default-profile.png') }}"
                             class="img-circle elevation-2 mb-2"
                             width="120"
                             height="120"
                             alt="Avatar">

                        <div class="form-group mt-2">
                            <label>Change Avatar</label>
                            <input type="file"
                                   name="profile_image"
                                   class="form-control">
                        </div>
                    </div>

                    {{-- ================= SIGNATURE SECTION ================= --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <strong>Digital Signature</strong>
                        </div>

                        <div class="card-body">

                            {{-- Current Signature Preview --}}
                            @if(auth()->user()->signature)
                                <div class="text-center mb-3">
                                    <p class="text-muted small mb-1">Current Signature</p>
                                    <img src="{{ asset(auth()->user()->signature) }}"
                                         style="max-height:120px; border:1px solid #ddd; padding:10px; background:#fff;"
                                         class="rounded">
                                </div>
                            @endif

                            {{-- Tabs --}}
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <button class="nav-link active"
                                            data-bs-toggle="tab"
                                            data-bs-target="#drawTab"
                                            type="button">
                                        ✍ Draw
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#uploadTab"
                                            type="button">
                                        📤 Upload
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">

                                {{-- DRAW TAB --}}
                                <div class="tab-pane fade show active"
                                     id="drawTab">

                                    <div class="signature-wrapper">
                                        <canvas id="signaturePad"></canvas>
                                    </div>

                                    <input type="hidden"
                                           name="signature_draw"
                                           id="signature_draw">

                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">
                                            Sign inside the box
                                        </small>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="clearPad()">
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                {{-- UPLOAD TAB --}}
                                <div class="tab-pane fade"
                                     id="uploadTab">

                                    <div class="mt-3">
                                        <input type="file"
                                               name="signature_file"
                                               class="form-control"
                                               accept="image/png,image/jpeg">
                                        <small class="text-muted">
                                            PNG or JPG only. Transparent PNG recommended.
                                        </small>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- ================= NAME ================= --}}
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text"
                                   name="fname"
                                   class="form-control"
                                   value="{{ auth()->user()->fname }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <input type="text"
                                   name="mname"
                                   class="form-control"
                                   value="{{ auth()->user()->mname }}">
                        </div>

                        <div class="col-md-4">
                            <input type="text"
                                   name="lname"
                                   class="form-control"
                                   value="{{ auth()->user()->lname }}"
                                   required>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ auth()->user()->email }}"
                               required>
                    </div>

                    <hr>

                    {{-- ================= PASSWORD ================= --}}
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-key mr-1"></i>
                        Change Password
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="New Password">
                        </div>

                        <div class="col-md-6">
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm Password">
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <button type="submit"
                            class="btn btn-primary"
                            onclick="saveSignature(event)">
                        <i class="fas fa-save mr-1"></i>
                        Update Profile
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection



{{-- ================= STYLES ================= --}}
<style>
.signature-wrapper {
    width: 100%;
    height: 220px;
    border: 2px dashed #ced4da;
    border-radius: 8px;
    background: #fff;
}

#signaturePad {
    width: 100%;
    height: 100%;
}
</style>



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
let pad;

document.addEventListener("DOMContentLoaded", function () {

    const canvas = document.getElementById('signaturePad');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);

    pad = new SignaturePad(canvas, {
        minWidth: 1.5,
        maxWidth: 3,
        penColor: "#000"
    });
});

function clearPad() {
    pad.clear();
}

function saveSignature(e) {

    let fileInput = document.querySelector('input[name="signature_file"]');

    // If uploading image, ignore drawn pad
    if (fileInput.files.length > 0) {
        return;
    }

    if (pad.isEmpty()) {
        alert("Please draw or upload your signature.");
        e.preventDefault();
        return;
    }

    document.getElementById('signature_draw').value =
        pad.toDataURL("image/png");
}
</script>
@endsection
