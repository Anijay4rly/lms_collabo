@extends('admin.layouts.app')

@section('title', 'My Leaves')
@section('page-title', 'My Leave Requests')

@section('content')
<div class="container-fluid">

    {{-- Top Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">My Leave Requests</h5>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Apply New Leave
        </a>
    </div>

    {{-- Leaves Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="dataTable" class="table table-hover align-middle table-striped w-100">
                <thead class="table-light">
                    <tr>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Reason for leave</th>
                        <th>Status</th>
                        <th>Report</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($myleaves as $leave)

                        @php
                            $statusColors = [
                                'submitted'   => 'info',
                                'pending'     => 'warning',
                                'on_progress' => 'primary',
                                'approved'    => 'success',
                                'rejected'    => 'danger',
                            ];

                            $icons = [
                                'submitted'   => 'fa-paper-plane',
                                'pending'     => 'fa-clock',
                                'on_progress' => 'fa-spinner',
                                'approved'    => 'fa-check',
                                'rejected'    => 'fa-times',
                            ];

                            $color = $statusColors[$leave->status] ?? 'secondary';
                            $icon  = $icons[$leave->status] ?? 'fa-question';
                        @endphp

                        <tr>

                            {{-- 1. Leave Type --}}
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $leave->request_type)) }}
                            </td>

                            {{-- 2. Duration --}}
                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                    <br> to <br>
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </small>
                            </td>

                            {{-- 3. Reason for Leave --}}
                            <td>
                                <small class="text-muted">
                                    {{ $leave->reasons ?? 'No reason provided' }}
                                </small>
                            </td>

                            {{-- 4. Status --}}
                            <td>
                                <span class="badge bg-{{ $color }} px-3 py-2">
                                    <i class="fas {{ $icon }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                                </span>
                            </td>

                            {{-- 5. Report --}}
                            <td class="text-center">
                                @if(!empty($leave->report_path))
                                    <a href="{{ route('leaves.download', $leave->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-info"
                                       title="Download Report">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- 5. Actions --}}
                            <td>
                                <div class="d-flex gap-1 flex-wrap">

                                    {{-- View --}}
                                    <a href="{{ route('leaves.show', $leave->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit & Delete only if submitted --}}
                                    @if($leave->status === 'submitted')

                                        <a href="{{ route('leaves.edit', $leave->id) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('leaves.destroy', $leave->id) }}"
                                              method="POST"
                                              style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this request?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                    <p class="lead mb-2">No leave requests found</p>
                                    <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Apply Leave
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {

        // Only initialize once
        if (!$.fn.DataTable.isDataTable('#dataTable')) {

            $('#dataTable').DataTable({
                paging: true,
                searching: true,      // Search enabled
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [3, 4] }
                ],
                language: {
                    search: "Search:",              // Search label
                    searchPlaceholder: "Type here..." // Placeholder
                }
            });

        }

    });
</script>
@endpush
