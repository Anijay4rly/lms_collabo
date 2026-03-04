@extends('admin.layouts.app')

@section('title', 'Update User')
@section('page-title', 'Update User')

@section('content')
<div class="content">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>
                            Update User
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

                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="fname"
                                               value="{{ old('fname', $user->fname) }}"
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
                                               value="{{ old('mname', $user->mname) }}"
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
                                       value="{{ old('lname', $user->lname) }}"
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
                                               value="{{ old('email', $user->email) }}"
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
                                            <option value="{{ $user->gender }}">{{ $user->gender }}</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
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
       value="{{ old('dob', \Carbon\Carbon::parse($user->dob)->format('Y-m-d')) }}"
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
                                            <option value="{{ $user->role }}">{{ $user->role }}</option>
                                            <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                            <option value="hod" {{ old('role', $user->role) == 'hod' ? 'selected' : '' }}>HOD</option>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
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
                                       value="{{ old('assigned_as', $user->assigned_as) }}"
                                       class="form-control @error('assigned_as') is-invalid @enderror">
                                @error('assigned_as')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">E.g. "Head of Finance Department"</small>
                            </div>

                            {{-- Department --}}
                            <div class="form-group">
    <label>Department</label>
    <select name="department_id"
            class="form-control @error('department_id') is-invalid @enderror">

        <option value="">-- Select Department --</option>

        @foreach ($departments as $department)
            <option value="{{ $department->id }}"
                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
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
                                        <label>Password</label>
                                        <input type="password"
                                               name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               >
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control"
                                               >
                                        <small class="text-muted">Must match password</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    Update User
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
