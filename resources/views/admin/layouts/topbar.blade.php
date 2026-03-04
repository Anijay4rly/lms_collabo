<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- Left navbar links --}}
  <ul class="navbar-nav">
    <!-- Hamburger menu -->
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
        </a>
    </li>

    <!-- App Name -->
    <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/') }}" class="nav-link">
            {{ config('app.name') }}
        </a>
    </li>
</ul>


    {{-- Right navbar links --}}
    <ul class="navbar-nav ml-auto align-items-center">
        {{-- Notification Bell --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger navbar-badge">
                    {{ \App\Models\LeaveRequest::whereIn('status', ['pending','on_progress'])->count() }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <span class="dropdown-item">New pending leave requests</span>
                <div class="dropdown-divider"></div>
                <a href="{{ route('leaves.pending') }}" class="dropdown-item">View Pending Leaves</a>
            </div>
        </li>
        {{-- Search Bar --}}
        <li class="nav-item mx-2">
            <form class="form-inline" action="{{ route('leaves.index') }}" method="GET">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" name="search" placeholder="Search leaves/users" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </li>
        {{-- User Dropdown with Image --}}
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown" href="#">
                <img src="{{ auth()->user()->profile_image ?? '/default-profile.png' }}" class="rounded-circle border border-primary" width="36" height="36" alt="Profile">
                <span class="d-none d-md-inline ml-2 fw-bold">
                    {{ auth()->user()->full_name ?? 'User' }}
                </span>
                <i class="fas fa-caret-down ml-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px;">
                <div class="text-center mb-2">
                    <img src="{{ auth()->user()->profile_image ?? '/default-profile.png' }}" class="rounded-circle border border-secondary" width="64" height="64" alt="Profile">
                    <div class="fw-bold mt-2">{{ auth()->user()->full_name ?? 'User' }}</div>
                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                    <span class="badge bg-info text-dark mt-1">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile') }}" class="dropdown-item">
                    <i class="fas fa-user-circle mr-2"></i> Profile
                </a>
                <a href="{{ route('profile') }}#settings" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
