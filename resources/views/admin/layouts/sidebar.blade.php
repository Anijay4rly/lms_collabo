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
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ================= EMPLOYEE MENU (ALL ROLES) ================= --}}
                <li class="nav-header">EMPLOYEE MENU</li>

                <li class="nav-item">
                    <a href="{{ route('leaves.create') }}" class="nav-link {{ request()->routeIs('leaves.create') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Apply Leave</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('leaves.showmy') }}" class="nav-link {{ request()->routeIs('leaves.showmy') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>My Leave Requests</p>
                    </a>
                </li>

                {{-- ================= HOD MENU (HOD + ADMIN) ================= --}}
                @if(auth()->user()->role === 'hod' || auth()->user()->role === 'admin')
                    <li class="nav-header">HOD MENU</li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>All Leave Requests</p>
                        </a>
                    </li>
                @endif

                {{-- ================= ADMIN MENU (ADMIN ONLY) ================= --}}
                @if(auth()->user()->role === 'admin')
                    <li class="nav-header">ADMIN MENU</li>

                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users Management</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Departments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->routeIs('leaves.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Leave Requests</p>
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
                            <p>Logout</p>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>
