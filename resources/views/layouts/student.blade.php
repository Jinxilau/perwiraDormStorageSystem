<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources\css\student.css', 'resources\css\sidebar.css', 'resources\css\notification.css'])
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
                'icon' => 'bi-bell',
                'title' => 'Notification',
                'url' => route('student_notification'),
                'path' => 'student/notification',
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
                'title' => 'My Missing Report',
                'url' => route('my_report'),
                'path' => 'student/my_report',
            ],
            [
                'icon' => 'bi-info-circle',
                'title' => 'Help & Support',
                'url' => 'student_help',
                'path' => 'student/help',
            ],
        ]" />
        <!-- Main Content -->
        <main id="maincontent" class="col-md-9 p-4">
            {{ $slot }}
        </main>
    </div>


    {{-- Footer --}}
    @include('livewire.includes.footer')

    {{-- Scripts --}}
    @livewireScripts
    @vite(['resources\js\app.js'])
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    @stack('scripts')
</body>

</html>
