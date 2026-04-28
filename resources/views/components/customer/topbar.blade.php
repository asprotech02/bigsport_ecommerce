<div class="border-bottom py-2 bg-white" style="font-size: 12px; font-weight: 700;">
    <div class="container d-flex justify-content-between align-items-center">
        
        <div class="d-flex align-items-center text-truncate pe-2">
            <i class="bi bi-truck me-2 fs-6 fs-md-5"></i>
            <span class="text-truncate">FREE ONGKIR Se-Indonesia</span>
        </div>
        
        <div class="d-flex align-items-center gap-3 gap-md-4">
            
            <a href="/" class="text-dark text-decoration-none d-md-block">BERANDA</a>
            
            @guest
                <div class="d-none d-md-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="text-dark text-decoration-none hover-link">
                        MASUK
                    </a>
                    
                    <span class="text-muted" style="font-weight: 300;">|</span>
                    
                    <a href="{{ route('register') }}" class="text-dark text-decoration-none hover-link">
                        DAFTAR
                    </a>
                </div>

                <div class="d-md-none">
                    <a href="{{ route('login') }}" class="text-dark text-decoration-none">
                        <i class="bi bi-person fs-5"></i>
                    </a>
                </div>
            @endguest

            @auth
                <div class="d-none d-md-flex align-items-center gap-2">
                    <a href="{{ route('profile') }}" class="text-dark text-decoration-none text-uppercase text-truncate" style="max-width: 150px;">
                        HAI, {{ explode(' ', Auth::user()->name)[0] }}
                    </a>
                    
                    <span class="text-muted">|</span>
                    
                    <a href="{{ route('logout') }}" 
                       class="text-dark text-decoration-none"
                       onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">
                        KELUAR
                    </a>

                    <form id="topbar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            @endauth
            <div class="dropdown">
                <button class="btn p-0 border-0 text-dark d-flex align-items-center gap-1 gap-md-2 dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 12px; font-weight: 700;">
                    <span class="d-none d-sm-inline">INDONESIA</span>
                    <img src="https://flagcdn.com/w20/id.png" alt="ID Flag" width="20">
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-0 mt-2" style="font-size: 12px; min-width: 120px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 fw-bold" href="#">
                            INDONESIA
                            <img src="https://flagcdn.com/w20/id.png" alt="ID" width="20">
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between py-2 fw-bold" href="#">
                            ENGLISH
                            <img src="https://flagcdn.com/w20/gb.png" alt="EN" width="20">
                        </a>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
</div>