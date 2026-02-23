@extends('admin.layouts.app')

@section('title', 'Users List')
@section('page-title', 'User List')

@section('content')
<div class="container-fluid">

    {{-- Top Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Users List</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add New User
        </a>
    </div>

    {{-- Users Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table id="dataTable" class="table table-hover align-middle table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <span class="fw-semibold">
                                    {{ $user->fname }} {{ $user->mname }} {{ $user->lname }}
                                </span>
                            </td>

                            <td>
                                <small class="text-muted">
                                    {{ $user->email }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-info px-3 py-2">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td>
                                @if($user->department)
                                    <span class="text-muted small">{{ $user->department->name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-1 flex-wrap">

                                    <a href="{{ route('users.show', $user->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if(auth()->user()->id !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}"
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

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
