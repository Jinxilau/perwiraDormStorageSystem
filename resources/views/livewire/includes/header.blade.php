<header id="navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <button id="toggle-btn" class="navbar-toggler" type="button" onclick="toggleSidebar()">
            <i class="bi bi-list h2 text-white" id="hambur"></i>
        </button>

        <a href="{{ route('student_dashboard') }}">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ Vite::asset('resources\images\icons\perwira_logo.png') }}" alt="Logo"
                    id="logo-perwira">
                <div id="brand">Perwira Dorm Storage System</div>
            </div>
        </a>

        <div class="d-flex align-items-center justify-content-end gap-2">
            <livewire:component.notification-dropdown />
            @auth
                <div x-data="{ show: false }" @click.away="show = false" @keydown.escape.window="show = false"
                    class="user-dropdown">
                    <button @click="show = !show;" class="btn btn-outline-light rounded-pill" type="button">
                        <span class="d-inline-flex align-items-center gap-2">
                            <span class="avatar-circle">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-caret-down-fill" :class="{ 'rotate-180': show }"
                                style="transition: transform 0.2s; font-size: 16px"></i>
                        </span>
                    </button>

                    <div x-show="show" x-transition class="shadow custom-dropdown-menu shadow-lg" style="display: none;">
                        <div class="dropdown-header-custom">
                            <div class="fw-semibold text-dark">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                        <hr class="mb-2 text-dark">
                        <a class="dropdown-item-custom d-flex align-items-center gap-2" href="{{ route('profile') }}">
                            <i class="bi bi-person" style="font-size: 20px;"></i>
                            <span>Profile</span>
                        </a>
                        <hr class="mt-2 mb-0 text-dark">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="dropdown-item-custom text-danger d-flex align-items-center gap-2 border-0 bg-transparent w-100 text-start"
                                type="submit">
                                <i class="bi bi-box-arrow-right" style="font-size: 20px"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
