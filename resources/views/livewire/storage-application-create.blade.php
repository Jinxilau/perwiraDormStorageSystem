<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @include('livewire.includes.student-title', [
        'title' => 'Storage Application Process',
        'subtitle' => 'Follow the steps below to submit your storage request.',
    ])

    @if ($this->hasApplied())
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
        @if (!$this->isApplicationEnabled())
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
            <!-- Main Content -->
            <div class="px-3">
                {{-- FORM --}}
                <livewire:storage-application />
            </div>
        @endif
    @endif
</div>
