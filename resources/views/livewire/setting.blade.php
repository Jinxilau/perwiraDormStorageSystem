<div>
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Create a New Semester</span>
            </div>
        </x-slot:title>

        <form wire:submit.prevent="createSemester">

            {{-- Academic Year --}}
            <div class="mb-3">
                <label class="form-label">Academic Year</label>
                <input type="text" wire:model="new_academic_year" class="form-control" placeholder="e.g. 2024/2025">
                @error('new_academic_year')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            {{-- Semester No --}}
            <div class="mb-3">
                <label class="form-label">Semester Number</label>
                <select wire:model="new_semester_no" class="form-select">
                    <option value="">Select Semester</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3 (Short)</option>
                </select>
                @error('new_semester_no')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                {{-- Start Date --}}
                <div class="col-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" wire:model="new_start_date" class="form-control">
                    @error('new_start_date')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                {{-- End Date --}}
                <div class="col-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" wire:model="new_end_date" class="form-control">
                    @error('new_end_date')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-light" @click="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Semester</button>
            </div>
        </form>
    </x-custom-modal>


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
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="storage_application_enabled" id="storageCheck"
                                            style="transform: scale(1.2);">
                                        <label class="form-check-label fw-bold ms-2" for="storageCheck">
                                            Enable Storage Application
                                        </label>
                                        <div class="text-muted small mt-1">Allows students to submit new storage
                                            requests.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="missing_report_enabled" id="missingCheck"
                                            style="transform: scale(1.2);">
                                        <label class="form-check-label fw-bold ms-2" for="missingCheck">
                                            Enable Missing Report
                                        </label>
                                        <div class="text-muted small mt-1">Allows students to report missing items.
                                        </div>
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
                                <strong>Important:</strong> Changing the active semester will automatically route all
                                new student applications to the selected session.
                            </div>
                        </div>

                        <label for="semesterSelect" class="form-label fw-bold">Current Active Semester</label>
                        <div class="input-group">
                            <select wire:model.live="selectedSemesterId" id="semesterSelect"
                                class="form-select form-select-lg">
                                <option value="" disabled>-- Select Active Semester --</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}">
                                        {{ $semester->academic_year }} - Semester {{ $semester->semester_no }}
                                        @if ($semester->is_current)
                                            (Currently Active)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            {{-- New "Add" Button --}}
                            <button type="button" class="btn btn-success" wire:click="$dispatch('display_modal');">
                                <i class="bi bi-plus-circle me-1"></i> Add New
                            </button>

                            {{-- Delete Button --}}
                            <button type="button" wire:click="deleteSemester" {{-- Confirm Action --}}
                                wire:confirm="Are you sure you want to delete this semester? This cannot be undone."
                                {{-- Disable if nothing is selected --}} @if (!$selectedSemesterId) disabled @endif
                                class="btn btn-outline-danger" title="Delete Selected Semester">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        {{-- DYNAMIC SEMESTER DETAILS --}}
                        @if ($this->selectedSemester)
                            <div class="card bg-light border-0 mt-3 animate__animated animate__fadeIn">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">

                                        {{-- Start Date --}}
                                        <div class="col-md-5 border-end border-secondary-subtle">
                                            <small class="text-uppercase text-muted fw-bold"
                                                style="font-size: 0.7rem;">Start Date</small>
                                            <div class="fw-semibold text-dark fs-5">
                                                <i class="bi bi-calendar-event me-2 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($this->selectedSemester->start_date)->format('d M, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                ({{ \Carbon\Carbon::parse($this->selectedSemester->start_date)->diffForHumans() }})
                                            </small>
                                        </div>

                                        {{-- End Date --}}
                                        <div class="col-md-5">
                                            <small class="text-uppercase text-muted fw-bold"
                                                style="font-size: 0.7rem;">End Date</small>
                                            <div class="fw-semibold text-dark fs-5">
                                                <i class="bi bi-calendar-check me-2 text-danger"></i>
                                                {{ \Carbon\Carbon::parse($this->selectedSemester->end_date)->format('d M, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                ({{ \Carbon\Carbon::parse($this->selectedSemester->end_date)->diffForHumans() }})
                                            </small>
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="col-md-2 text-end">
                                            @if ($this->selectedSemester->is_current)
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Active
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
