<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon.jpg') }}">
    <title>{{ $title . ' | Perwira Dorm Storage System' }}</title>
    @vite(['resources/css/admin.css'])
    @stack('styles')
    {{-- Livewire Style --}}
    @livewireStyles
    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- ApexChart --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- QR Reader --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    {{-- Alpine js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="d-md-flex min-vh-100">
    {{-- Sidebar --}}
    <x-sidebar2 :items="[
        [
            'title' => 'Dashboard',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'staff/dashboard',
        ],
        [
            'title' => 'Storage Application',
            'url' => route('manage_storage_application', ['status' => 'pending']),
            'icon' => 'bi-calendar-week',
            'path' => 'storage_applications',
        ],
        [
            'title' => 'Storage Room',
            'url' => route('storage_room'),
            'icon' => 'bi-box-seam',
            'path' => 'room',
        ],
        [
            'title' => 'Scan QR Code',
            'url' => route('scan_qr'),
            'icon' => 'bi-qr-code-scan',
            'path' => 'scanqr',
        ],
        [
            'title' => 'Scan Logs',
            'url' => route('scan_log'),
            'icon' => 'bi-card-list',
            'path' => 'scan_log',
        ],
        [
            'title' => 'System Activity Log',
            'url' => route('activity_log'),
            'icon' => 'bi-card-list',
            'path' => 'activity_log',
        ],
        [
            'title' => 'Notifications',
            'url' => route('staff_notification'),
            'icon' => 'bi-bell',
            'path' => 'staff/notifications',
        ],
    ]" />

    {{-- Main Content --}}
    <main id="main-content">
        {{ $slot }}
    </main>

    {{-- Toast Message --}}
    <x-toast-message />

    {{-- Livewire --}}
    @livewireScripts
    <script>
        window.userId = {{ Auth::id() }};
    </script>
    @vite(['resources/js/app.js'])
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    @stack('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle-btn');
            const icon = toggleButton.querySelector('i');
            const mainContent = document.getElementById('maincontent');
            const mediaQuery = window.matchMedia('(max-width: 768px)');

            if (!sidebar || !toggleButton || !icon) return;

            // Sidebar: Initial load check
            if (mediaQuery.matches) {
                sidebar.classList.add('closed');
                icon.classList.replace('bi-x-lg', 'bi-list');
            } else {
                sidebar.classList.remove('closed');
                icon.classList.replace('bi-list', 'bi-x-lg');
            }

            // Sidebar: Resize check
            mediaQuery.addEventListener('change', e => {
                if (e.matches) {
                    sidebar.classList.add('closed');
                    toggleButton.classList.toggle('rotate');
                    icon.classList.replace('bi-x-lg', 'bi-list');
                } else {
                    sidebar.classList.remove('closed');
                    toggleButton.classList.toggle('rotate');
                    icon.classList.replace('bi-list', 'bi-x-lg');
                }
            });

            // Sidebar: Toggle Button
            toggleButton.addEventListener('click', () => {
                sidebar.classList.toggle('closed');
                toggleButton.classList.toggle('rotate');

                if (sidebar.classList.contains('closed')) {
                    icon.classList.replace('bi-x-lg', 'bi-list');
                } else {
                    icon.classList.replace('bi-list', 'bi-x-lg');
                }
            })

        }, {
            once: true
        });
    </script>
</body>

</html>
