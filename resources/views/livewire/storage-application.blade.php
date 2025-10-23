<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <h2 class="card-title text-center">Storage Application Process</h2>
    <p class="text-muted text-center mb-2 small">
        Follow the steps below to submit your storage request.
    </p>
    @if ($showSuccessMessage)
        @include('livewire.includes.application-ok')
    @else
        {{-- Progress Indicator --}}
        <div class="container mb-2">
            <div class="row justify-content-center">
                <div class="col-lg-8 d-flex justify-content-center">
                    <ul x-data="{ current: $wire.entangle('currentStep') }"
                        class="steper-step list-unstyled p-0 mb-3 position-relative stepper-vertical d-inline-flex justify-content-center">
                        @for ($i = 1; $i <= $totalSteps; $i++)
                            <li class="stepper position-relative pb-1 d-inline-flex"
                                :class="{
                                    'actived': current == {{ $i }},
                                    'completed': current > {{ $i }},
                                }">
                                {{-- Stepper Head --}}
                                <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center"
                                    style="width: 12vw; min-width: 70px;">
                                    {{-- Stepper Icon --}}
                                    <span
                                        class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                        @if ($this->currentStep <= $i)
                                            {{ $i }}
                                        @endif
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
    <x-toast-message />
</div>

@assets
    @vite('resources\css\progress_bar.css')
@endassets
