<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Tombol Toggle Sidebar (Mobile) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Judul Halaman -->
    <h5 class="m-0 font-weight-bold text-uppercase tracking-wider" style="color: var(--primary-neon, #e50914) !important; font-size: 1.1rem;">
        BigSport
    </h5>

    <!-- Navbar kanan -->
    <ul class="navbar-nav ml-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">

            <a class="nav-link dropdown-toggle"
               href="#"
               id="userDropdown"
               role="button"
               data-toggle="dropdown"
               data-bs-toggle="dropdown"
               aria-expanded="false">

                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::user()->name ?? 'Admin' }}
                </span>

                <img class="img-profile rounded-circle"
                     src="{{ asset('assets/admin/img/undraw_profile.svg') }}">

            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">

                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>

                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>

                <div class="dropdown-item-text small">
                    <i class="fas fa-clock fa-sm fa-fw mr-2 text-gray-400"></i>

                    Last Login:
                    <br>

                 

                </div>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </button>
                </form>

            </div>
        </li>
    </ul>
</nav>
<!-- End of Topbar -->