<header id="navbar">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center py-2">
            <!-- Left: Toggle Button -->
            <button id="toggle-btn" class="navbar-toggler border-0 p-2" type="button" aria-label="Toggle navigation">
                <i class="bi bi-list h4 mb-0 text-dark" id="hambur"></i>
            </button>

            <!-- Center: Brand/Logo -->
            <a href="{{ route('student_dashboard') }}" class="brand-link text-decoration-none d-flex align-items-center gap-3 flex-grow-1 justify-content-center">
                <img src="{{ Vite::asset('resources/images/icons/perwira_logo.png') }}" alt="Perwira Dorm Logo" class="brand-logo">
                
                {{-- Text Logo - Remove this if using image logo --}}
                <div class="brand-content text-center">
                    <div class="brand-title">Perwira Dorm Storage System</div>
                    <div class="brand-subtitle">Simplifying Your Semester Move.</div>
                </div>
            </a>

            <!-- Right: Notifications & User Dropdown -->
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <!-- Notification Dropdown -->
                <div class="notification-wrapper">
                    <livewire:component.notification-dropdown />
                </div>

                <!-- User Dropdown -->
                @auth
                    <div x-data="{ show: false }" 
                         @click.away="show = false" 
                         @keydown.escape.window="show = false"
                         class="user-dropdown position-relative">
                        <button @click="show = !show" 
                                class="btn btn-user-profile" 
                                type="button"
                                aria-expanded="false"
                                aria-haspopup="true">
                            <span class="d-inline-flex align-items-center gap-2">
                                <span class="avatar-circle">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="username d-none d-md-inline">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                <i class="bi bi-chevron-down dropdown-arrow" 
                                   :class="{ 'rotate-180': show }"></i>
                            </span>
                        </button>

                        <div x-show="show" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="custom-dropdown-menu shadow-lg" 
                             style="display: none;"
                             role="menu">
                            
                            <!-- User Info Header -->
                            <div class="dropdown-header-custom">
                                <div class="user-avatar-large mb-2">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="fw-semibold text-dark">{{ auth()->user()->name }}</div>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                            
                            <div class="dropdown-divider"></div>
                            
                            <!-- Profile Link -->
                            <a class="dropdown-item-custom" 
                               href="{{ route('profile') }}"
                               role="menuitem">
                                <i class="bi bi-person-circle"></i>
                                <span>My Profile</span>
                                <i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            
                            <div class="dropdown-divider"></div>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button class="dropdown-item-custom dropdown-item-danger" 
                                        type="submit"
                                        role="menuitem">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>