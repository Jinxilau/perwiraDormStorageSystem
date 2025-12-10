<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%;">
        <div class="card-body p-5">

            <h4 class="card-title text-center mb-4 fw-bold">{{ __('Reset Password') }}</h4>

            <div class="mb-4 text-muted small text-center">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            <form wire:submit="sendPasswordResetLink">
                <div class="mb-3">
                    <label for="email"
                        class="form-label text-secondary small text-uppercase fw-bold">{{ __('Email Address') }}</label>

                    <input wire:model="email" id="email"
                        class="form-control form-control-lg @error('email') is-invalid @enderror" type="email"
                        name="email" placeholder="name@example.com" required autofocus>

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
