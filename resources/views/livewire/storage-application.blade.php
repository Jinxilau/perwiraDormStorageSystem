<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <h3 class="text-center">Storage Application Process</h3>
    @if ($showSuccessMessage)
        @include('livewire.includes.application-ok')
    @else
        {{-- Progress Indicator --}}
        <div class="container mb-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 d-flex justify-content-center">
                    <ul
                        class="steper-step list-unstyled p-0 m-0 position-relative stepper-vertical d-inline-flex justify-content-center">
                        @for ($i = 1; $i <= $totalSteps; $i++)
                            <li
                                class="stepper position-relative pb-1 {{ $currentStep >= $i ? 'actived' : '' }} {{ $i < $currentStep ? 'completed' : '' }} d-inline-flex">
                                {{-- Stepper Head --}}
                                <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center"
                                    style="width: 12vw; min-width: 70px;">
                                    {{-- Stepper Icon --}}
                                    <span
                                        class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                        {{ $i }}
                                    </span>
                                    <div class="stepper-content py-0">
                                        <p class="text-muted small mb-0">
                                            @if ($i == 1)
                                                Item Details
                                            @elseif($i == 2)
                                                Room Selection
                                            @else
                                                Review & Submit
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                {{-- Stepper Line --}}
                                @if ($i < $totalSteps)
                                    <span class="stepper-head-line"></span>
                                @endif
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-3">
            <!-- Success/Error Messages -->
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-success bg-opacity-10 border border-success text-success rounded">
                    {{ session('success') }}
                </div>
                <!-- Success Message -->
                @if (session()->has('message'))
                    <div class="alert alert-success mt-4">
                        {{ session('message') }}
                    </div>
                @endif
            @endif

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- FORM --}}
            @include('livewire.includes.application-form')
        </div>
    @endif
    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }"
        @show_toast.window="
            show = true;
            message = $event.detail.message;
            type = $event.detail.type;
            setTimeout(() => show = false, 4000);
        "
        x-show="show" x-transition:enter="fade-in" x-transition:leave="fade-out"
        class="position-fixed bottom-0 start-0 p-3" style="z-index: 1050; display: none;">
        <div class="toast show" role="alert">
            <div class="toast-header"
                :class="{
                    'bg-success': type === 'success',
                    'bg-danger': type === 'fail'
                }">
                <!-- Change color based on type -->
                <div :class="{
                    'bg-success': type === 'success',
                    'bg-danger': type === 'fail'
                }"
                    class="rounded me-2" style="width: 20px; height: 20px;">
                </div>

                <!-- Change title based on type -->
                <strong class="me-auto" x-text="type === 'success' ? 'Success' : 'Error'"></strong>

                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
            <div class="toast-body" x-text="message"></div>
        </div>
    </div>
</div>

@push('styles')
    @vite('resources\css\storage_application.css')
@endpush
