@extends('admin.layouts.app')

@section('title', 'Users List')
@section('page-title', 'User List')

@section('content')
<div class="content">
    <div class="container-fluid">

        {{-- Top Actions --}}
        <div class="card card-primary card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Users List
                </h3>

                <div class="card-tools">
                    <a href="{{ route('users.create') }}"
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Add New User
                    </a>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table id="dataTable"
                           class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role | Assigned as</th>
                                <th>Department</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $user->fname }}
                                            {{ $user->mname }}
                                            {{ $user->lname }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="text-muted">
                                            {{ $user->email }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($user->role) }} |
                                            {{ ucfirst($user->assigned_as) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($user->department)
                                            <span class="text-muted">
                                                {{ $user->department->name }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}"
                                           class="btn btn-sm btn-primary"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}"
                                                  method="POST"
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection


@push('scripts')
<script>
$(function () {
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: true,
            info: true
        });
    }
});
</script>
@endpush
