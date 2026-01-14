<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    @include('livewire.includes.student-title', [
        'title' => 'Edit Storage Application',
        'subtitle' => 'Modify your existing storage application below.',
    ])

    @if ($application->storage_application_status !== 'pending')
        <div class="text-center py-5">
            <div class="card shadow-sm border-0 bg-white d-inline-block p-4" style="max-width: 500px;">
                <div class="card-body">
                    {{-- Icon: Success Checkmark --}}
                    <div class="mb-3 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                            class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </div>

                    {{-- Title --}}
                    <h4 class="fw-bold text-dark">Application Approved</h4>

                    {{-- Message --}}
                    <p class="text-muted mb-4">
                        Your storage application has already been reviewed and <strong>approved</strong>.
                        <br><br>
                        To ensure data consistency, you can no longer modify this application.
                        If you need to make urgent changes, please contact the administration directly.
                    </p>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('student_dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Dashboard
                        </a>

                        <a href="{{ route('my_storage') }}" class="btn btn-success">View Application</a>
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
            <div class="px-3">
                <livewire:storage-application :applicationId="$application->id" />
            </div>
        @endif
    @endif
</div>
