@extends('admin.layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments Management')

@section('content')
<div class="container-fluid">

    {{-- Top Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Departments List</h5>
        <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add New Department
        </a>
    </div>

    {{-- Departments Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table id="dataTable" class="table table-hover align-middle table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th>Staff Count</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($departments as $department)
                        <tr>
                            <td>
                                <span class="fw-semibold">
                                    {{ $department->name }}
                                </span>
                            </td>

                            <td>
                                <small class="text-muted">
                                    @if($department->description)
                                        {{ Str::limit($department->description, 60) }}
                                    @else
                                        <em>No description</em>
                                    @endif
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-info px-3 py-2">
                                    {{ $department->users_count }} User{{ $department->users_count !== 1 ? 's' : '' }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex gap-1 flex-wrap">

                                    <a href="{{ route('departments.show', $department->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('departments.edit', $department->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('departments.destroy', $department->id) }}"
                                          method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="return confirm('Are you sure? Users in this department must be reassigned first.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
