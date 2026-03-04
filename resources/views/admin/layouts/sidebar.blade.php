<aside class="main-sidebar sidebar-dark-primary elevation-4">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">

        {{-- User Name Only --}}
        <div class="user-panel mt-3 pb-3 mb-3 text-center">
            <div class="info">
                <span class="d-block text-white font-weight-bold">
                    {{ auth()->user()->full_name ?? 'User' }}
                </span>
                <small class="text-muted text-capitalize">
                    {{ auth()->user()->role }}
                </small>
            </div>
        </div>

        {{-- Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                {{-- DASHBOARD (ALL ROLES) --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p style="white-space: nowrap;">Dashboard</p>
                    </a>
                </li>

                {{-- ================= EMPLOYEE MENU (EMPLOYEE ONLY) ================= --}}
                @if(auth()->user()->role === 'employee')
                    <li class="nav-header" style="white-space: nowrap;">EMPLOYEE MENU</li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.create') }}" class="nav-link {{ request()->routeIs('leaves.create') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p style="white-space: nowrap;">Apply Leave</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.showmy') }}" class="nav-link {{ request()->routeIs('leaves.showmy') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p style="white-space: nowrap;">My Leave Requests</p>
                        </a>
                    </li>
                @endif

                {{-- ================= HOD MENU (HOD ONLY) ================= --}}
                {{-- ================= HOD MENU (HOD ONLY) ================= --}}
@if(auth()->user()->role === 'hod')
    <li class="nav-header">HOD MENU</li>

    <li class="nav-item">
        <a href="{{ route('leaves.staff') }}"
           class="nav-link {{ request()->routeIs('leaves.staff') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>Department Staff</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('leaves.index') }}"
           class="nav-link {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>
                All Leave Requests
                <span class="badge bg-info ms-2">
                    {{ \App\Models\LeaveRequest::count() }}
                </span>
            </p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('leaves.pending') }}"
           class="nav-link {{ request()->routeIs('leaves.pending') ? 'active' : '' }}">
            <i class="nav-icon fas fa-hourglass-half"></i>
            <p>
                Pending Leaves
                <span class="badge bg-warning ms-2">
                    {{ \App\Models\LeaveRequest::where('status', 'submitted')->count() }}
                </span>
            </p>
        </a>
    </li>
    {{-- Waiting final approve // on progess leave --}}
    <li class="nav-item">
        <a href="{{ route('leaves.onprogress') }}"
           class="nav-link {{ request()->routeIs('leaves.onprogress') ? 'active' : '' }}">
            <i class="nav-icon fas fa-spinner"></i>
            <p>
                On Progress Leaves
                <span class="badge bg-primary ms-2">
                    {{ \App\Models\LeaveRequest::where('status', 'pending')->count() }}
                </span>
            </p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('leaves.approved') }}"
           class="nav-link {{ request()->routeIs('leaves.approved') ? 'active' : '' }}">
            <i class="nav-icon fas fa-check-circle"></i>
            <p>
                Approved Leaves
                <span class="badge bg-success ms-2">
                    {{ \App\Models\LeaveRequest::where('status', 'approved')->count() }}
                </span>
            </p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('leaves.rejected') }}"
           class="nav-link {{ request()->routeIs('leaves.rejected') ? 'active' : '' }}">
            <i class="nav-icon fas fa-times-circle"></i>
            <p>
                Rejected Leaves
                <span class="badge bg-danger ms-2">
                    {{ \App\Models\LeaveRequest::where('status', 'rejected')->count() }}
                </span>
            </p>
        </a>
    </li>
@endif

                {{-- ================= ADMIN MENU (ADMIN ONLY) ================= --}}
                @if(auth()->user()->role === 'admin')
                    <li class="nav-header" style="white-space: nowrap;">ADMIN MENU</li>

                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p style="white-space: nowrap;">Users Management</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p style="white-space: nowrap;">Departments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p style="white-space: nowrap;">Leave Requests</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p style="white-space: nowrap;">Reports</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('audit-logs.index') }}" class="nav-link {{ request()->routeIs('audit-logs.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p style="white-space: nowrap;">User Log</p>
                        </a>
                    </li>
                @endif

                <hr class="mx-2 my-3 bg-secondary">

                {{-- Logout --}}
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link text-danger w-100 text-start" style="border: none; background: none;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p style="white-space: nowrap;">Logout</p>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>

