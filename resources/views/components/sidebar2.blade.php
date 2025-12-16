@props([
    'items' => [],
])
<aside id="sidebar" class="shadow-lg text-light d-flex flex-column">
    {{-- Sidebar Header --}}
    <div id="sidebar-header" class="p-3 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold mb-0" style="letter-spacing: 1px;">Perwira Dorm Storage System</h1>
        <button id="sidebar-toggle-btn" class="navbar-toggler" style="width: 22px; height: 22px;">
            <i class="bi bi-x-lg fs-4" id="hambur"></i>
        </button>
    </div>
    {{-- Sidebar Navigation Link --}}
    <nav id="sidebar-nav">
        @foreach ($items as $item)
            <div id="nav-item">
                <a id="nav-link" href="{{ $item['url'] }}" wire:navigate
                    class="{{ request()->is($item['path'], $item['path'] . '/*') ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }} fs-4"></i>
                    <span class="ms-2">{{ $item['title'] }}</span>
                </a>
            </div>
        @endforeach
    </nav>
    <div id="user-profile" class="user-profile">
        @auth
            <div id="user-dropdown" x-data="{ show: false }" @click.away="show = false"
                @keydown.escape.window="show = false">
                <button id="user-button" @click="show = !show;">
                    <span class="d-inline-flex align-items-center gap-2">
                        <span id="avatar-circle" class="text-light">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span id="user-button-name" class="text-light" style="flex-shrink: 1;">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="text-light small opacity-75">{{ auth()->user()->email }}</div>
                        </span>
                        <i class="bi bi-caret-up text-light" :class="{ 'rotate-180': show }"
                            style="transition: transform 0.2s; font-size: 16px; flex-shrink: 1;"></i>
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
</aside>