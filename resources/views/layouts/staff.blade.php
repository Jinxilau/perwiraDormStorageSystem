<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources\css\admin.css', 'resources\css\sidebar2.css', 'resources\css\notification.css'])
    @stack('styles')
    {{-- Livewire Style --}}
    @livewireStyles
    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="d-md-flex min-vh-100">
    <x-sidebar2 :items="[
        [
            'title' => 'Dashboard',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'staff/dashboard',
        ],
        [
            'title' => 'Manage Storage Application',
            'url' => route('manage_storage_pending'),
            'icon' => 'bi-speedometer',
            'path' => 'storage/*',
        ],
        [
            'title' => 'Manage Storage Room',
            'url' => route('storage_room'),
            'icon' => 'bi-speedometer',
            'path' => 'room/*',
        ],
        [
            'title' => 'Scan Logs',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'storage/*',
        ],
        [
            'title' => 'System Activity Log',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'storage/*',
        ],
        [
            'title' => 'Notifications',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'storage/*',
        ],
        [
            'title' => 'Scan QR Code',
            'url' => route('staff_dashboard'),
            'icon' => 'bi-speedometer',
            'path' => 'storage/*',
        ],
    ]" />

    {{-- Main Content --}}
    <main id="main-content">
        {{ $slot }}
    </main>

    {{-- Livewire --}}
    @livewireScripts
    <script>
        window.userId = {{ Auth::id() }};
    </script>
    @vite(['resources\js\app.js'])
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    @stack('scripts')
    <script>
        let userid = {{ Auth::user()->id }};
        Echo.private(`App.Models.User.${userid}`).notification((notification) => {
            Livewire.dispatch('notificationReceived');
        });
    </script>
</body>

</html>
