<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon.jpg') }}">
    <title>{{ $title . ' | Perwira Dorm Storage System' }}</title>
    @vite(['resources/css/student.css'])
    @stack('styles')
    {{-- Livewire Style --}}
    @livewireStyles
    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="d-flex flex-column min-vh-100">
    {{-- Header --}}
    @include('livewire.includes.header')

    <div class="row">
        <!-- Sidebar -->
        <x-sidebar :items="[
            [
                'icon' => 'bi-speedometer',
                'title' => 'Dashboard',
                'url' => route('student_dashboard'),
                'path' => 'student/dashboard',
            ],
            [
                'icon' => 'bi-house-add',
                'title' => 'Storage Application',
                'url' => route('storage'),
                'path' => 'student/storage',
            ],
        
            [
                'icon' => 'bi-box-seam',
                'title' => 'My Storage',
                'url' => route('my_storage'),
                'path' => 'student/my_storage',
            ],
            [
                'icon' => 'bi-search',
                'title' => 'Report Missing Item',
                'url' => route('missing_report'),
                'path' => 'student/missing_report',
            ],
            [
                'icon' => 'bi-file-earmark-break',
                'title' => 'My Missing Item',
                'url' => route('my_report'),
                'path' => 'student/my_report',
            ],
            [
                'icon' => 'bi-bell',
                'title' => 'Notification',
                'url' => route('student_notification'),
                'path' => 'student/notification',
            ],
            [
                'icon' => 'bi-info-circle',
                'title' => 'Help & Support',
                'url' => route('help_and_support'),
                'path' => 'student/help_and_support',
            ],
        ]" />
        <!-- Main Content -->
        <main id="maincontent" class="col-md-9 px-4 pe-lg-5 pt-4 bg-light" style="padding-bottom: 60px;">
            {{ $slot }}
        </main>
    </div>

    {{-- Toast Message --}}
    <x-toast-message />

    {{-- Footer --}}
    @include('livewire.includes.footer')

    {{-- Scripts --}}
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

    {{-- Alpine js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        document.addEventListener('livewire:navigated', () => {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('toggle-btn');
            const icon = toggleButton.querySelector('i');
            const mainContent = document.getElementById('maincontent');
            const mediaQuery = window.matchMedia('(max-width: 768px)');

            toggleButton.addEventListener('click', toggleSidebar);

            if (mediaQuery.matches) {
                sidebar.classList.add('close');
                icon.classList.replace('bi-x-lg', 'bi-list');
            } else {
                sidebar.classList.remove('close');
                icon.classList.replace('bi-list', 'bi-x-lg');
            }

            mediaQuery.addEventListener('change', e => {
                if (mediaQuery.matches) {
                    sidebar.classList.add('close');
                    icon.classList.replace('bi-x-lg', 'bi-list');
                    toggleButton.classList.toggle('rotate');
                } else {
                    sidebar.classList.remove('close');
                    icon.classList.replace('bi-list', 'bi-x-lg');
                    toggleButton.classList.toggle('rotate');
                }
            });

            function toggleSidebar() {
                sidebar.classList.toggle('close');
                toggleButton.classList.toggle('rotate');
                mainContent.classList.toggle('col-md-9');

                if (sidebar.classList.contains('close')) {
                    icon.classList.replace('bi-x-lg', 'bi-list');
                } else {
                    icon.classList.replace('bi-list', 'bi-x-lg');
                }
            }
        }, {
            once: true
        });
    </script>

</body>

</html>
