<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @include('livewire.includes.student-title', [
        'title' => 'Storage Application Process',
        'subtitle' => 'Follow the steps below to submit your storage request.',
    ])

    @php
        $enabled = \App\Models\Setting::get('storage_application_enabled', true);
    @endphp
    @if (Auth::user()->applyStorage()->exists())
        <div class="text-center py-5">
            <div class="card shadow-sm border-0 bg-white d-inline-block p-4" style="max-width: 500px;">
                <div class="card-body">
                    <div class="mb-3 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                            class="bi bi-file-earmark-check" viewBox="0 0 16 16">
                            <path
                                d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z" />
                            <path
                                d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z" />
                        </svg>
                    </div>

                    <h4 class="fw-bold text-dark">Application Submitted</h4>

                    <p class="text-muted mb-4">
                        You have already submitted a storage application.
                        Your application is currently being processed. You can check the status in your dashboard.
                    </p>

                    {{-- Button Group --}}
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('student_dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>

                        <a href="{{ route('my_storage') }}" class="btn btn-primary">
                            View Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if (!$enabled)
            <div class="text-center py-5">
                <div class="card shadow-sm border-0 bg-white d-inline-block p-4" style="max-width: 500px;">
                    <div class="card-body">
                        {{-- Icon --}}
                        <div class="mb-3 text-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                class="bi bi-calendar-x" viewBox="0 0 16 16">
                                <path
                                    d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                            </svg>
                        </div>

                        <h4 class="fw-bold text-dark">Applications are Closed</h4>

                        <p class="text-muted mb-4">
                            The storage application period is currently inactive. Please check back later or contact the
                            administration for the next opening date.
                        </p>

                        {{-- Button --}}
                        <a href="{{ route('student_dashboard') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
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
                                        style="width: 16vw; min-width: 70px;">
                                        {{-- Stepper Icon --}}
                                        <span
                                            class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                            @if ($this->currentStep <= $i)
                                                {{ $i }}
                                            @endif
                                        </span>
                                        <div class="stepper-content py-0">
                                            <p class="text-muted fw-bold mb-0">
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
    @endif
</div>

@assets
    @vite('resources\css\progress_bar.css')
@endassets
