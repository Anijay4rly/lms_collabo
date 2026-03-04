@extends('admin.layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div class="content">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create New User
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Validation Errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            {{-- Name --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="fname"
                                               value="{{ old('fname') }}"
                                               class="form-control @error('fname') is-invalid @enderror"
                                               required>
                                        @error('fname')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text"
                                               name="mname"
                                               value="{{ old('mname') }}"
                                               class="form-control @error('mname') is-invalid @enderror">
                                        @error('mname')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="lname"
                                       value="{{ old('lname') }}"
                                       class="form-control @error('lname') is-invalid @enderror"
                                       required>
                                @error('lname')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email + Gender --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender"
                                                class="form-control @error('gender') is-invalid @enderror"
                                                required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- DOB + Role --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date"
                                               name="dob"
                                               value="{{ old('dob') }}"
                                               class="form-control @error('dob') is-invalid @enderror"
                                               required>
                                        @error('dob')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select name="role"
                                                class="form-control @error('role') is-invalid @enderror"
                                                required>
                                            <option value="">Select Role</option>
                                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                                            <option value="hod" {{ old('role') == 'hod' ? 'selected' : '' }}>HOD</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        @error('role')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- assigned_as --}}
                            <div class="form-group">
                                <label>Assigned As (Optional)</label>
                                <input type="text"
                                       name="assigned_as"
                                       value="{{ old('assigned_as') }}"
                                       class="form-control @error('assigned_as') is-invalid @enderror">
                                @error('assigned_as')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">E.g. "Head of IT Department"</small>
                            </div>

                            {{-- Department --}}
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department_id"
                                        class="form-control @error('department_id') is-invalid @enderror">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               required>
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control"
                                               required>
                                        <small class="text-muted">Must match password</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    Create User
                                </button>

                                <a href="{{ route('users.index') }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i>
                                    Cancel
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
