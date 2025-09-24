<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources\css\app.css'])
    @stack('styles')
    {{-- Livewire Style --}}
    @livewireStyles
    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .sidebarcontent:hover {
            background-color: rgba(0, 179, 255, 0.571);
        }

        #sidebar.close {
            padding: 5px;
            width: 60px;
            text-wrap: nowrap;
        }

        .rotate {
            rotate: -180deg;
        }

        @media(min-width: 768px) {
            .custom-vh-100 {
                height: 100vh;
            }
        }

        .text-outline {
            color: white;
            /* Set the text color */
            text-shadow:
                -0.3px -0.3px 0 black,
                /* Top-left shadow */
                0.3px -0.3px 0 black,
                /* Top-right shadow */
                -0.3px 0.3px 0 black,
                /* Bottom-left shadow */
                0.3px 0.3px 0 black;
            /* Bottom-right shadow */
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav id="navbar" class="" style="z-index: 1051; max-height: 90px; padding: 24px 10px">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <button id="toggle-btn" class="navbar-toggler" type="button" onclick="toggleSidebar()"
                style="transition: rotate 150ms ease;">
                <i class="bi bi-list h2 text-white" id="hambur"></i>
            </button>

            <div class="d-flex align-items-center gap-2">
                <img src="{{ Vite::asset('resources\images\icons\perwira_logo.png') }}" alt="Logo" id="logo-icon"
                    class="logo-icon"
                    style="object-fit: cover; overflow: visible; width: 130px; height: 30px; position: relative; z-index: 1050; top: 5px;">
                <div class="logo" id="logo" style="font-size: clamp(12px, 2.3vw, 28px)">Perwira Dorm Storage
                    System</div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle rounded-pill" type="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">{{ auth()->user()->email }}</h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                            {{-- <li><a class="dropdown-item" href="{{ route('favourites') }}">My favourites</a></li> --}}
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div id="sidebar" class="col-md-3 p-0 mb-4 mb-md-0 custom-vh-100 shadow-lg"
            style="background-color: rgb(255, 255, 255); position: sticky; top: 0; z-index: 10; transition: 300ms ease-in-out; overflow: hidden;">
            <div class="list-group ms-2">
                <a href="{{ route('student_dashboard') }}" wire:navigate
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer h4"></i>
                    <span class="ms-2">Dashboard</span>
                </a>
                <a href="{{ route('storage') }}" wire:navigate
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/storage') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week h4"></i>
                    <span class="ms-2">Storage Application</span>
                </a>
                <a href="admin_manage_retrieval.html"
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/notification') ? 'active' : '' }}">
                    <i class="bi bi-box-seam h4"></i>
                    <span class="ms-2">Notification</span>
                </a>
                <a href="admin_manage_user.html"
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/my_storage') ? 'active' : '' }}">
                    <i class="bi bi-person-gear h4"></i>
                    <span class="ms-2">My Storage</span>
                </a>
                <a href="{{ route('missing_report') }}"
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/missing_report') ? 'active' : '' }}">
                    <i class="bi bi-house-gear h4"></i>
                    <span class="ms-2">Report Missing Item</span>
                </a>
                <a href="admin_manage_storage.html"
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/my_missing_report') ? 'active' : '' }}">
                    <i class="bi bi-house-gear h4"></i>
                    <span class="ms-2">Missing Items</span>
                </a>
                <a href="admin_manage_storage.html"
                    class="sidebarcontent list-group-item list-group-item-action {{ request()->is('student/help') ? 'active' : '' }}">
                    <i class="bi bi-house-gear h4"></i>
                    <span class="ms-2">Help & Support</span>
                </a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md p-4">
            {{ $slot }}
        </div>
    </div>


    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid mx-2">
            <!-- Logo Button -->
            <div class="mb-4">
                <img src="{{ Vite::asset('resources\images\icons\perwira_logo.png') }}" alt="Logo" id="logo-icon"
                    class="logo-icon"
                    style="object-fit: cover; overflow: visible; width: 130px; height: 30px; position: relative; z-index: 1050; top: 5px;">
            </div>

            <!-- Social Icons -->
            <div>
                <div class="social-icons">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="d-flex justify-content-between">
                <div class="">
                    <p class="copyright">
                        © 2024 Universiti Tun Hussein Onn Malaysia | Perwira Dorm Storage System
                    </p>
                </div>
                <div class="footer-links" style="width: fit-content">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Cookie Policy</a>
                    <a href="#">Contact</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('toggle-btn');

            sidebar.classList.toggle('close'); // Adds or removes the 'close' class to/from the sidebar
            toggleButton.classList.toggle('rotate'); // Adds or removes the 'rotate' class to/from the button
        }
    </script>

    {{-- Livewire --}}
    @livewireScripts
    @vite(['resources\js\app.js'])
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    @stack('scripts')
</body>

</html>
