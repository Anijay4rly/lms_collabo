@extends('admin.layouts.app')
@section('title', 'Department Staff')
@section('page-title', 'Department Staff')
@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="dataTable" class="table table-hover align-middle table-striped w-100">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leaveRequests as $user)
                        <tr>
                            <td>{{ $user->fname }} {{ $user->mname }} {{ $user->lname }}</td>

                            <td>{{ $user->gender }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->date_of_birth)->format('d M Y') }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i> View Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No staff found in your department.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
