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

    {{-- Departments Card --}}
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building me-2"></i> Departments</h3>
        </div>

        <div class="card-body table-responsive">
            <table id="dataTable" class="table table-hover table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th>HOD</th>
                        <th>Staff Count</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($departments as $department)
                        @php
                            $hod = $department->users()->where('role', 'hod')->first();
                        @endphp
                        <tr>
                            <td>{{ $department->name }}</td>

                            <td>
                                @if($department->description)
                                    {{ Str::limit($department->description, 60) }}
                                @else
                                    <em class="text-muted">No description</em>
                                @endif
                            </td>

                            <td>
                                @if($hod)
                                    <span class="badge bg-primary">{{ $hod->full_name }}</span>
                                @else
                                    <span class="text-muted">No HOD Assigned</span>
                                @endif
                            </td>

                            <td>
                                @if($department->users_count > 0)
                                    <span class="badge bg-info">
                                        {{ $department->users_count }} User{{ $department->users_count !== 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="text-muted">No staff assigned</span>
                                @endif
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
                                          method="POST" style="display:inline;">
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

        <div class="card-footer text-muted">
            Total Departments: {{ $departments->count() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        }
    });
</script>
@endpush
