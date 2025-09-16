<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
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
        $page = match(Auth::user()->role_id){
            1 => 'admin_dashboard',
            2 => 'staff_dashboard',
            3 => 'student_dashboard'
        };

        // dd($page);
        $this->redirectIntended(default: route($page, absolute: false), navigate: true);
    }
};
?>

<div class="w-100 px-3" style="max-width: 480px;">
    <div class="card shadow">
        <div class="d-flex flex-column justify-content-center py-2 px-lg-4 px-sm-2 gap-2" style="height: 66vh;">
            {{-- Header --}}

            <div class="mx-auto w-100" style="max-width: 400px;">
                <form wire:submit="login">
                    <div class="text-center mb-4">
                        <h2 class="h3 mb-2 fw-bold text-dark">
                            {{ __('Log in to your account') }}
                        </h2>
                        <p class="text-muted mb-0">
                            {{ __('Welcome back! Please enter your credentials.') }}
                        </p>
                    </div>
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    @if (session('registered'))
                        <p class="text-success text-center">You have successfully registered! Please log in.</p>
                    @endif

                    <!-- Email Address -->
                    <div class="mb-3">
                        <x-input-label for="email" :value="__('Email')" class="form-label" />
                        <x-text-input wire:model="form.email" id="email" class="form-control" type="email"
                            name="email" required autofocus autocomplete="username" />
                        {{-- <x-input-error :messages="$errors->get('form.email')" class="mt-2" /> --}}
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <x-input-label for="password" :value="__('Password')" class="form-label" />
                        <x-text-input wire:model="form.password" id="password" class="form-control" type="password"
                            name="password" required autocomplete="current-password" />
                        {{-- <x-input-error :messages="$errors->get('form.password')" class="mt-2" /> --}}
                    </div>

                    @if ($errors->any())
                        <div class="d-flex justify-content-start align-items-center">
                            <svg width="20" height="20" fill="red" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-danger m-0 p-0">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <!-- Remember Me & Forgot Password-->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input wire:model="form.remember" id="remember" type="checkbox"
                                class="form-check-input border border-dark" name="remember">
                            <label for="remember" class="form-check-label">
                                {{ __('Remember me') }}
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <div>
                                <a class="text-dark" href="{{ route('password.request') }}" wire:navigate>
                                    {{ __('Forgot your password?') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="mb-3">
                        <x-primary-button class="w-100 btn btn-dark" wire:loading.attr="disabled" wire:target="login">
                            <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
                            <!-- Loading spinner (hidden by default) -->
                            <span wire:loading wire:target="login">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                {{ __('Signing in...') }}
                            </span>
                        </x-primary-button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center">
                        <p class="text-muted">
                            {{ __("Student: don't have an account?") }}
                            <a href="{{ route('account_application') }}" wire:navigate
                                class="text-dark">{{ __('Apply Account') }}</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
