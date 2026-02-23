<aside class="main-sidebar sidebar-dark-primary elevation-4">

    {{-- Brand --}}
    <a href="#" class="brand-link text-center">
        <span class="brand-text font-weight-light">Leave System</span>
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
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ================= EMPLOYEE MENU (ALL ROLES) ================= --}}
                <li class="nav-header">EMPLOYEE</li>

                <li class="nav-item">
                    <a href="{{ route('leaves.create')}}" class="nav-link">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Apply Leave</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('leaves.showmy')}}" class="nav-link">
                        <i class="nav-icon fas fa-list"></i>
                        <p>My Leave Requests</p>
                    </a>
                </li>

                {{-- ================= HOD MENU (HOD + ADMIN) ================= --}}
                @if(auth()->user()->role === 'hod' || auth()->user()->role === 'admin')
                    <li class="nav-header">HOD</li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>Pending Requests</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Approved Requests</p>
                        </a>
                    </li>
                @endif

                {{-- ================= ADMIN MENU (ADMIN ONLY) ================= --}}
                @if(auth()->user()->role === 'admin')
                    <li class="nav-header">ADMIN</li>

                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>All Leave Requests</p>
                        </a>
                    </li>
                @endif

                {{-- Logout --}}
                <li class="nav-item mt-3">
                    <a href="{{route('logout')}}" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
