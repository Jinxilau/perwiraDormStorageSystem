<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon.jpg') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

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

<body class="font-sans text-body bg-light" style="padding-top: 0;">
    <div class=" d-flex flex-column justify-content-sm-center align-items-center pt-5 pt-sm-0"
        style="min-height: 80vh;">

        {{ $slot }}
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
            <p class="text-center text-white mb-0">© {{ date('Y') }} Universiti Tun Hussein Onn Malaysia | Perwira Dorm Storage
                System</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
</body>

</html>
