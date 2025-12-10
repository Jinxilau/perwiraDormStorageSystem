<div>
    @include('livewire.includes.header2', [
        'title' => 'Setting',
        'subtitle' => 'Configure general settings, operating hours, and system features.',
    ])

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex align-items-center">
                <i class="bi bi-sliders me-2"></i>
                <h5 class="mb-0">General Configuration</h5>
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save">
                    
                    {{-- Section 1: Features --}}
                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="bi bi-toggles me-2"></i>Feature Management
                    </h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="storage_application_enabled" id="storageCheck" style="transform: scale(1.2);">
                                        <label class="form-check-label fw-bold ms-2" for="storageCheck">
                                            Enable Storage Application
                                        </label>
                                        <div class="text-muted small mt-1">Allows students to submit new storage requests.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="missing_report_enabled" id="missingCheck" style="transform: scale(1.2);">
                                        <label class="form-check-label fw-bold ms-2" for="missingCheck">
                                            Enable Missing Report
                                        </label>
                                        <div class="text-muted small mt-1">Allows students to report missing items.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Semester --}}
                    <h5 class="text-secondary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-calendar-range me-2"></i>Academic Session
                    </h5>

                    <div class="mb-4">
                        <div class="alert alert-info d-flex align-items-start" role="alert">
                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                            <div>
                                <strong>Important:</strong> Changing the active semester will automatically route all new student applications to the selected session.
                            </div>
                        </div>

                        <label for="semesterSelect" class="form-label fw-bold">Current Active Semester</label>
                        <select wire:model="selectedSemesterId" id="semesterSelect" class="form-select form-select-lg">
                            <option value="" disabled>-- Select Active Semester --</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}">
                                    {{ $semester->academic_year }} - Semester {{ $semester->semester_no }}
                                    @if ($semester->is_current) (Currently Active) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Ensure this matches the university's official current semester.</div>
                    </div>

                    {{-- Footer / Save --}}
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="bi bi-clock-history me-1"></i>Last updated: 
                            <span class="fw-semibold">
                                {{ optional(\App\Models\Setting::latest('updated_at')->first())->updated_at?->diffForHumans() ?? 'Never' }}
                            </span>
                        </span>
                        
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>