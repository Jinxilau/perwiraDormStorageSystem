<!-- Footer -->
<footer class="footer pb-2 py-3 d-flex flex-column align-items-center justify-contain-center">
    <!-- Social Media Links -->
    <div class="social-icons" style="width: fit-content">
        <a href="#" class="social-icon" aria-label="Follow us on Facebook" rel="noopener noreferrer" target="_blank">
            <i class="fab fa-facebook-f" aria-hidden="true"></i>
        </a>
        <a href="#" class="social-icon" aria-label="Follow us on Instagram" rel="noopener noreferrer"
            target="_blank">
            <i class="fab fa-instagram" aria-hidden="true"></i>
        </a>
    </div>

    <!-- Copyright -->
    <p class="copyright mb-0">
        © {{ date('Y') }} Universiti Tun Hussein Onn Malaysia | Perwira Dorm Storage System
    </p>

    <!-- Logo -->
    <div>
        <a href="{{ route('student_dashboard') }}" wire:navigate>
            <img src="{{ Vite::asset('resources/images/icons/perwira_logo.png') }}" alt="Perwira Dorm Storage System Logo"
                class="logo-icon" height="120">
        </a>
    </div>
</footer>
