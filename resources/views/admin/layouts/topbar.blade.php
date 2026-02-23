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
    <ul class="navbar-nav ml-auto">

        {{-- User Dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <span class="d-none d-md-inline ml-1">
                    {{ trim(
                        (auth()->user()->fname ?? '') . ' ' .
                        (auth()->user()->mname ?? '') . ' ' .
                        (auth()->user()->lname ?? '')
                    ) }}
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user-circle mr-2"></i> Profile
                </a>

                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>

    </ul>
</nav>
