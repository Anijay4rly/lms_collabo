@extends('admin.layouts.app')

@section('title', 'View Department')
@section('page-title', 'Department Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i> {{ $department->name }}
                    </h5>
                    <div>
                        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('departments.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Department Name</h6>
                            <p class="lead">{{ $department->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Total Staff Members</h6>
                            <p class="lead">
                                <span class="badge bg-primary px-3 py-2" style="font-size: 1.1rem;">
                                    {{ $department->users()->count() }} Member{{ $department->users()->count() !== 1 ? 's' : '' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($department->description)
                        <hr>
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase mb-2">Description</h6>
                            <p class="lead">{{ $department->description }}</p>
                        </div>
                    @endif

                    @if($department->users()->count() > 0)
                        <hr>
                        <h5 class="mb-3">
                            <i class="fas fa-users me-2"></i> Department Staff
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($department->users as $user)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $user->full_name }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info px-2 py-1">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('users.show', $user->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <hr>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This department currently has no assigned staff members.
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="text-muted small">
                        <p>
                            <i class="fas fa-calendar me-1"></i> Created: {{ $department->created_at->format('d M, Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-sync me-1"></i> Last Updated: {{ $department->updated_at->format('d M, Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trash me-2"></i> Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    @if($department->users()->count() == 0)
                        <p class="text-muted mb-3">Deleting this department is permanent and cannot be undone.</p>
                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to permanently delete this department? This action cannot be undone.')">
                                <i class="fas fa-trash me-2"></i> Delete Department
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This department cannot be deleted because it has {{ $department->users()->count() }} assigned staff member{{ $department->users()->count() !== 1 ? 's' : '' }}.
                            Please reassign or remove all staff members before attempting to delete this department.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
