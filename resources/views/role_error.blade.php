<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Access Denied</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Boostrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class=" d-flex flex-column justify-content-sm-center align-items-center pt-5 pt-sm-0"
        style="min-height: 80vh;">
        <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light py-5 px-3">
            <div class="col-md-6 col-lg-5">
                <div class="text-center">

                    {{-- Error Icon --}}
                    <div class="mx-auto mb-4 text-danger" style="width: 96px; height: 96px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            class="w-100 h-100">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                            1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732
                            0L3.732 19c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>

                    {{-- Error Title --}}
                    <h2 class="fw-bold text-dark mb-2">
                        Access Denied
                    </h2>

                    {{-- Error Code --}}
                    <p class="fs-5 text-muted mb-4">
                        Error 403 - Forbidden
                    </p>

                    {{-- Error Message --}}
                    <div class="alert alert-danger mb-4" role="alert">
                        @if (session('error'))
                            <p class="mb-0">{{ session('error') }}</p>
                        @else
                            <p class="mb-0">You do not have the required permissions to access this resource.</p>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        {{-- Home Button --}}
                        @auth
                            <a href="@if (Auth::user()->role_id === 1) {{ route('admin_dashboard') }} @elseif(Auth::user()->role_id === 2) {{ route('staff_dashboard') }} @elseif(Auth::user()->role_id === 3) {{ route('student_dashboard') }} @endif"
                                class="btn btn-primary d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0
                                        001 1h3m10-11l2 2m-2-2v10a1 1 0
                                        01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1
                                        0 011-1h2a1 1 0 011 1v4a1 1
                                        0 001 1m-6 0h6" />
                                </svg>
                                Go to Dashboard
                            </a>
                        @endauth
                    </div>

                    {{-- Footer Info --}}
                    <div class="mt-4 text-muted small">
                        <p class="mb-1">Request ID: {{ request()->header('X-Request-ID') ?? Str::random(8) }}</p>
                        <p class="mb-0">Time: {{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-dark py-4 rounded">
        <div class="container">
            <div class="d-flex justify-content-center gap-4 mb-3">
                <div class="rounded-circle bg-secondary" style="width: 0.8rem; height: 0.8rem;"></div>
                <div class="rounded-circle bg-secondary" style="width: 0.8rem; height: 0.8rem;"></div>
                <div class="rounded-circle bg-secondary" style="width: 0.8rem; height: 0.8rem;"></div>
                <div class="rounded-circle bg-secondary" style="width: 0.8rem; height: 0.8rem;"></div>
            </div>
            <p class="text-center text-white mb-0">© 2024 Universiti Tun Hussein Onn Malaysia | Perwira Dorm Storage
                System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
</body>

</html>
