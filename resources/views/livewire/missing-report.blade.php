<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    @if (!$this->isReportEnabled())
        <div class="text-center py-5">
            <div class="card shadow-sm border-0 bg-white d-inline-block p-4" style="max-width: 550px;">
                <div class="card-body">
                    {{-- Icon: Clipboard with X (Signifying Form Disabled) --}}
                    <div class="mb-3 text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                            class="bi bi-clipboard-x" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z" />
                            <path
                                d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                            <path
                                d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                        </svg>
                    </div>

                    <h4 class="fw-bold text-dark">Missing Report Paused</h4>

                    <p class="text-muted mb-4">
                        The online missing item report system is currently offline.
                        If you have lost an item or need immediate assistance, please visit the
                        <strong class="text-dark">Perwira Office</strong> directly.
                    </p>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('student_dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Return to Dashboard
                        </a>
                        {{-- Optional: Add a 'Contact Us' button if relevant --}}
                        <a href="tel:+123456789" class="btn btn-primary">
                            <i class="bi bi-telephone-fill me-1"></i> Call Perwira Office
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include('livewire.includes.student-title', [
            'title' => 'Report Missing Item',
            'subtitle' => 'Follow the steps below to submit your report.',
        ])

        @if ($this->studentApplications->isNotEmpty())
            @if ($showSuccessMessage)
                @include('livewire.includes.report-ok') {{-- Success message --}}
            @else
                {{-- Progress Indicator --}}
                <div class="container mb-2">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 d-flex justify-content-center">
                            <ul x-data="{ current: $wire.entangle('currentStep') }"
                                class="steper-step list-unstyled p-0 m-3 position-relative stepper-vertical d-inline-flex justify-content-center">
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
                                                        Select Storage
                                                    @elseif($i == 2)
                                                        Select Missing Items
                                                    @elseif($i == 3)
                                                        Incident Details
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

                {{-- Forms --}}
                @include('livewire.includes.report-form')
            @endif {{-- Show Success Message --}}
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    @php
                        $applications = \App\Models\StorageApplication::where('applicant_id', auth()->user()->id)
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->first();
                    @endphp
                    {{-- CASE 1: Application Exists but is Pending/Under Review --}}
                    @if ($applications && $applications->storage_application_status === 'pending')
                        <p class="mb-0" style="font-size: 64px">⏳</p>
                        <h5 class="card-title h3 mt-3">Application Under Review</h5>
                        <p class="text-muted col-md-8 mx-auto">
                            Your storage application is currently being processed by the administration.
                            You can report missing items once your application is <strong>approved</strong>.
                        </p>
                        {{-- Optional: Button to check status details --}}
                        <div class="mt-3">
                            <a href="{{ route('my_storage') }}" class="btn btn-outline-primary">
                                View my Application Status
                            </a>
                        </div>

                        {{-- CASE 2: No Application Found (The original "Apply" screen) --}}
                    @else
                        <p class="mb-0" style="font-size: 64px">❓</p>
                        <h5 class="card-title h3 mt-3">No Active Storage</h5>
                        <p class="text-muted col-md-8 mx-auto">
                            You cannot report a missing item because you do not have any registered storage.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('storage') }}" class="btn btn-primary">
                                Apply for Storage
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    @endif
</div>
@assets
    <style>
        .stepper-head-icon {
            width: 2rem;
            height: 2rem;
            background-color: #dee2e6;
            color: rgb(255, 255, 255);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .stepper.actived .stepper-head-icon {
            background-color: #0d6efd;
            color: white;
            transform: scale(1.15) rotate(-2deg);
        }

        .stepper.completed .stepper-head-icon {
            background-color: #1c9a00 !important;
            color: white;
        }

        .stepper.completed .stepper-head-icon::after {
            content: '✓';
            font-size: 20px;
        }

        .stepper-content {
            /* margin-left: 3rem; */
            /* margin-top: -0.2rem; */
            width: 100%;
            height: 1rem;
        }

        .stepper-head-line {
            position: absolute;
            top: 1rem;
            left: 50%;
            width: 100%;
            height: 2px;
            z-index: 1;
            background-color: #dee2e6;
            /* margin-right: 1rem; */
        }

        /* .stepper.actived .stepper-head-line:not(:last-child) {
        background-color: #0d6efd;
    } */
        .stepper.completed .stepper-head-line {
            background-image: linear-gradient(90deg, #1c9a00 20%, #5675ff 100%);
        }

        /* SIZE INPUT */
        .hover-border-primary:hover {
            border-color: var(--bs-primary) !important;
            cursor: pointer;
        }

        .transition-all {
            transition: all 0.2s ease;
        }
    </style>
@endassets
