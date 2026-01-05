<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // dd(Auth::user()->role_id);
        $page = match (Auth::user()->role_id) {
            1 => 'admin_dashboard',
            2 => 'staff_dashboard',
            3 => 'student_dashboard',
        };

        // dd($page);
        $this->redirectIntended(default: route($page, absolute: false), navigate: false);
    }
};
?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light py-4">
    <div class="row w-100 g-0" style="max-width: 1100px;">

        <!-- Left Side - Branding -->
        <div
            class="col-lg-5 d-none d-lg-flex flex-column justify-content-center align-items-center bg-primary bg-gradient bg-opacity-75 text-white p-5 rounded-start shadow-sm border">
            <div class="text-center">
                <div>
                    <img src="{{ Vite::asset('resources/images/icons/perwira_logo.png') }}" alt="Logo"
                        class="img-fluid" style="width: 500px; object-fit: cover; margin-bottom: -30px">
                </div>
                <h1 class="h2 fw-bold mb-3">Perwira Dorm<br>Storage System</h1>
                <p class="lead mb-4">Manage your semester break storage easily</p>
                <div class="bg-white bg-opacity-10 rounded p-3 mt-4">
                    <p class="mb-0 small">
                        <strong>Kolej Kediaman Luar Kampus</strong><br>
                        Universiti Tun Hussein Onn Malaysia
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 rounded-end h-100">
                <div class="card-body d-flex flex-column justify-content-center p-lg-5 p-4">

                    <!-- Mobile Header (only visible on small screens) -->
                    <div class="d-lg-none text-center mb-4">
                        <h1 class="h4 fw-bold text-primary mb-2">Perwira Dorm Storage System</h1>
                        <p class="text-muted small">PDSS</p>
                    </div>

                    <div class="mx-auto w-100" style="max-width: 400px;">
                        <form wire:submit="login">

                            <!-- Header -->
                            <div class="mb-4">
                                <h2 class="h3 fw-bold text-dark mb-2">
                                    {{ __('Log in to your account') }}
                                </h2>
                                <p class="text-muted mb-0">
                                    {{ __('Welcome back! Please enter your credentials.') }}
                                </p>
                            </div>
                            
                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            @if (session('success'))
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <svg class="me-2" width="20" height="20" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>You have successfully applied! You will receive an email once your account is approved.</span>
                                </div>
                            @endif

                            <!-- Email Address -->
                            <div class="mb-3">
                                <x-input-label for="email" :value="__('Email')" class="form-label fw-semibold" />
                                <x-text-input wire:model="form.email" id="email"
                                    class="form-control form-control-lg" type="email" name="email" required
                                    autofocus autocomplete="username" placeholder="xxx@student.uthm.edu.my" />
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <x-input-label for="password" :value="__('Password')" class="form-label fw-semibold" />
                                <x-text-input wire:model="form.password" id="password"
                                    class="form-control form-control-lg" type="password" name="password" required
                                    autocomplete="current-password" placeholder="Enter your password" />
                            </div>

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger d-flex align-items-start mb-3" role="alert">
                                    <svg class="me-2 flex-shrink-0" width="20" height="20" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input wire:model="form.remember" id="remember" type="checkbox"
                                        class="form-check-input border border-dark" name="remember">
                                    <label for="remember" class="form-check-label">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="text-primary text-decoration-none" href="{{ route('password.request') }}"
                                        wire:navigate>
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-4">
                                <x-primary-button class="w-100 btn btn-primary btn-lg" wire:loading.attr="disabled"
                                    wire:target="login">
                                    <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
                                    <span wire:loading wire:target="login">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            {{ __('Signing in...') }}
                                        </div>
                                    </span>
                                </x-primary-button>
                            </div>

                            <!-- Sign Up Link -->
                            <div class="text-center pt-3 border-top">
                                <p class="text-muted mb-0">
                                    {{ __("Student: don't have an account?") }}
                                    <a href="{{ route('account_application') }}" wire:navigate
                                        class="text-primary fw-semibold text-decoration-none">
                                        {{ __('Apply Account') }}
                                    </a>
                                </p>
                            </div>

                        </form>
                    </div>

                    <!-- Footer Info (Mobile) -->
                    <div class="d-lg-none text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-0">
                            <strong>Kolej Kediaman Luar Kampus</strong><br>
                            Universiti Tun Hussein Onn Malaysia
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
