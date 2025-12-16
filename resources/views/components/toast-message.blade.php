<div x-data="{ show: false, message: '', type: 'success' }"
    @show_toast.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        setTimeout(() => show = false, 5000);
    "
    x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4" class="position-fixed bottom-0 end-0 p-4"
    style="z-index: 9999; max-width: 400px; display:none" role="alert" aria-live="polite">

    <div class="toast-custom shadow-lg border-0 show"
        :class="{
            'toast-success': type === 'success',
            'toast-error': type === 'error' || type === 'fail',
            'toast-warning': type === 'warning',
            'toast-info': type === 'info'
        }">
        <div class="toast-header border-0 pb-2">
            <div class="toast-icon me-2">
                <!-- Success Icon -->
                <template x-if="type === 'success'">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.25rem;"></i>
                </template>

                <!-- Error Icon -->
                <template x-if="type === 'error' || type === 'fail'">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.25rem;"></i>
                </template>

                <!-- Warning Icon -->
                <template x-if="type === 'warning'">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 1.25rem;"></i>
                </template>

                <!-- Info Icon -->
                <template x-if="type === 'info'">
                    <i class="bi bi-info-circle-fill text-info" style="font-size: 1.25rem;"></i>
                </template>
            </div>

            <strong class="me-auto fw-bold"
                x-text="type === 'success' ? 'Success' : 
                           (type === 'error' || type === 'fail') ? 'Error' : 
                           type === 'warning' ? 'Warning' : 
                           type === 'info' ? 'Info' : 'Notification'">
            </strong>

            <button type="button" class="btn-close btn-close-custom" @click="show = false" aria-label="Close">
            </button>
        </div>

        <div class="toast-body pt-1" x-text="message"></div>

        <!-- Progress bar for auto-dismiss -->
        <div class="toast-progress">
            <div class="toast-progress-bar"
                :class="{
                    'bg-success': type === 'success',
                    'bg-danger': type === 'error' || type === 'fail',
                    'bg-warning': type === 'warning',
                    'bg-info': type === 'info'
                }">
            </div>
        </div>
    </div>
</div>

<style>
    /* Toast Custom Styles */
    .toast-custom {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        min-width: 300px;
        animation: slideIn 0.3s ease-out;
    }

    /* Toast Variants */
    .toast-success {
        border-left: 4px solid #198754;
    }

    .toast-error {
        border-left: 4px solid #dc3545;
    }

    .toast-warning {
        border-left: 4px solid #ffc107;
    }

    .toast-info {
        border-left: 4px solid #0dcaf0;
    }

    /* Toast Header */
    .toast-custom .toast-header {
        background: transparent;
        padding: 1rem 1rem 0.5rem 1rem;
    }

    /* Toast Body */
    .toast-custom .toast-body {
        padding: 0.5rem 1rem 1rem 1rem;
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Close Button */
    .btn-close-custom {
        opacity: 0.5;
        transition: opacity 0.2s ease;
    }

    .btn-close-custom:hover {
        opacity: 1;
    }

    /* Progress Bar */
    .toast-progress {
        height: 4px;
        background: rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .toast-progress-bar {
        height: 100%;
        width: 100%;
        animation: progressBar 5s linear;
        transform-origin: left;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes progressBar {
        from {
            transform: scaleX(1);
        }

        to {
            transform: scaleX(0);
        }
    }

    /* Responsive */
    @media (max-width: 576px) {
        .toast-custom {
            min-width: calc(100vw - 2rem);
        }
    }

    /* Hover effect */
    .toast-custom:hover .toast-progress-bar {
        animation-play-state: paused;
    }
</style>
