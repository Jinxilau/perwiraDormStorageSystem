<div>
    {{-- Bulk Reject Application Modal --}}
    <div x-data="{
        show: false,
        openModal() {
            this.show = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.show = false;
            document.body.style.overflow = '';
        },
    }" @display_modal3.window="openModal();" x-show='show' tabindex="-1" id="modal2"
        :class="{ 'display': show }" style="display: none;" x-transition.scale>

        {{-- Background Overlay --}}
        <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

        {{-- Modal --}}
        <div id="modal-content2" class="card" x-transition>
            {{-- Title --}}
            <div class="card-header">
                Bulk Reject Application
            </div>
            <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">
                <div wire:loading="modalData2" class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div wire:loading.remove="modalData2" class="pb-3">
                    <form wire:submit.prevent="bulkReject">
                        <div class="mb-4">
                            <label for="rejectionNote" class="form-label fw-semibold">
                                <i class="bi bi-chat-left-text me-2"></i>Note for the Applicant
                            </label>
                            <textarea wire:model="rejectionNote" id="rejectionNote" class="form-control" rows="5"
                                placeholder="Provide a clear reason for the rejection..." required></textarea>
                            @error('rejectionNote')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                This message will be sent to the student.
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-octagon me-1"></i>Submit Rejection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'items', 'storageDetails', 'status'],
        toggleAll() {
            if (this.allCol) {
                this.cols = [...this.allOptions];
            } else {
                this.cols = [];
            }
        },
        checkIfAllSelected() {
            this.allCol = this.cols.length === this.cols.length && this.allOptions.every(e => this.cols.includes(e));
        },
    }" x-init="toggleAll()" class="shadow-sm rounded-lg overflow-hidden">

        <x-card-matrix :items="[
            [
                'title' => $this->storageCounts['total'],
                'subtitle' => 'Pending Applications (Current Semester)',
                'icon' => 'bi-collection-fill',
                'color' => 'text-dark',
                'bg' => 'bg-dark bg-opacity-10',
            ],
            [
                'title' => $this->storageCounts['locker'],
                'subtitle' => 'Locker Requests (Current Semester)',
                'icon' => 'bi-lock-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->storageCounts['open_area'],
                'subtitle' => 'Open Area Requests (Current Semester)',
                'icon' => 'bi-map-fill',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
            [
                'title' => $this->staleCount,
                'subtitle' => 'Stale (> 7 Days Waiting)',
                'icon' => 'bi-hourglass-split',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->newTodayCount,
                'subtitle' => 'Application Received Today',
                'icon' => 'bi-file-earmark-plus-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
        ]" />

        <x-filter-options>
            {{-- Room --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Room</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="room">
                    <option value="">All Rooms</option>
                    @foreach ($this->rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Semester Filter --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Year & Semester</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="semester">
                    <option value="">All Semesters</option>
                    @foreach ($this->semesters as $sem)
                        {{-- Adjust 'name' below to whatever your Semester column is called (e.g., semester_name, label) --}}
                        <option value="{{ $sem->id }}">{{ $sem->academic_year }} - {{ $sem->semester_no }}
                            @if ($sem->is_current)
                                (Currently Active)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Conflict Filter --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Conflicts</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="conflictOnly">
                    <option value="0">All</option>
                    <option value="1">Conflicted Only</option>
                </select>
            </div>

            {{-- Stale Filter --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Waiting Time</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="staleOnly">
                    <option value="0">All</option>
                    <option value="1">Waiting > 7 Days</option>
                </select>
            </div>

            {{-- Storage Type --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Type</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="storageType">
                    <option value="">All Types</option>
                    <option value="locker">Locker</option>
                    <option value="openArea">Open Area</option>
                    {{-- <option value="lockerOpenArea">Locker + Open Area</option> --}}
                </select>
            </div>

            {{-- Date Range (Grouped Visually) --}}
            <div class="col-md-6 col-lg-5">
                <label class="form-label small text-muted fw-bold">Date Range</label>

                <div class="input-group">
                    {{-- Start Date --}}
                    <input type="date" wire:model.live.debounce.300ms="fromRange" class="form-control bg-light border-0"
                        placeholder="Start Date" aria-label="Start Date">

                    {{-- Separator --}}
                    <span class="input-group-text bg-light border-0 text-secondary">to</span>

                    {{-- End Date --}}
                    <input type="date" wire:model.live.debounce.300ms="toRange" class="form-control bg-light border-0"
                        placeholder="End Date" aria-label="End Date">
                </div>
            </div>

            {{-- Sort Options --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Sort: Date</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortDate">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Sort: Name</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortName">
                    <option value="">Default (No Sorting)</option>
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                </select>
            </div>

            {{-- Column Visibility (AlpineJS) --}}
            <div class="col-md-4 col-lg-3" x-data="{ show: false }">
                <label class="form-label small text-muted fw-bold">Columns</label>
                <div class="position-relative">
                    <button @click.prevent="show = !show"
                        class="form-select bg-light border-0 text-start d-flex justify-content-between align-items-center">
                        <span x-text="cols.length === 4 ? 'All Visible' : 'Custom'"></span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="show" @click.away="show = false" x-transition.opacity.duration.200ms
                        class="position-absolute bg-white border rounded-3 shadow p-3 mt-1 z-3"
                        style="display: none; min-width: 220px; z-index: 1050;">

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="allCol" x-model="allCol"
                                @change="toggleAll()">
                            <label class="form-check-label small" for="allCol">Select All</label>
                        </div>
                        <hr class="my-2">
                        @foreach (['studentInfo' => 'Student Info', 'items' => 'Items', 'storageDetails' => 'Storage Details', 'status' => 'Status'] as $val => $label)
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" value="{{ $val }}"
                                    x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label small">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Per Page</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="paginationInt">
                    <option value="10">10 Rows</option>
                    <option value="25">25 Rows</option>
                    <option value="50">50 Rows</option>
                    <option value="100">100 Rows</option>
                </select>
            </div>
        </x-filter-options>

        <!-- Bulk Actions Bar -->
        <div class="bg-primary-subtle border-bottom border-primary rounded-top px-3 py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-blue-700">
                        {{ count($this->selectedApplications) }} application(s) selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="bulkApprove" @disabled($this->bulkApproveDisabled)
                        wire:confirm="Are you sure you want to approve the selected applications?"
                        class="btn btn-success">
                        Approve Selected
                    </button>
                    <button type="button" wire:click="$dispatch('display_modal3');"
                        wire:confirm="Are you sure you want to reject the selected applications?"
                        @empty($this->selectedApplications) disabled @endempty class="btn btn-danger">
                        Reject Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <x-table>
            <x-slot:header>
                <th class="text-uppercase align-middle">
                    <input type="checkbox" wire:model.live="selectAll">
                </th>
                <th class="align-middle py-2">
                    ID
                </th>
                <th class="align-middle py-2" x-show="cols.includes('studentInfo')">
                    Student Info
                </th>
                <th class="align-middle py-2" x-show="cols.includes('storageDetails')">
                    Storage Details
                </th>
                <th class="align-middle py-2" x-show="cols.includes('items')">
                    Items
                </th>
                <th class="align-middle py-2">
                    Application Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Status
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>
            @forelse($this->pendingStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}"
                    class="{{ in_array($application->id, $this->selectedApplications) ? 'table-active' : '' }}">
                    <td class="align-middle"> {{-- Select --}}
                        <input type="checkbox" value="{{ $application->id }}"
                            wire:model.live="selectedApplications">
                    </td>
                    <td class="align-middle" style="width: 1%;">
                        <span class="text-secondary small user-select-none">#</span><span
                            class="fw-bold text-dark">{{ $application->id }}</span>
                    </td>
                    <td class="align-middle" x-show="cols.includes('studentInfo')">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex justify-content-center align-items-center text-uppercase"
                                    style="height: 30px; width: 30px;">
                                    {{ substr($application->applicant->name ?? 'NA', 0, 2) }}
                                </div>
                            </div>

                            <div>
                                <div class="fw-bold text-dark mb-0">
                                    {{ $application->applicant->name ?? 'N/A' }}
                                </div>

                                <div class="d-flex flex-wrap gap-1">
                                    <span
                                        class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-normal">
                                        {{ $application->applicant->matric_no ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle" x-show="cols.includes('storageDetails')">
                        <div class="d-flex flex-column justify-content-center">

                            {{-- 1. Room Name Display (Simplified) --}}
                            <div class="fw-bold text-dark mb-1">
                                @php
                                    // Resolve the room from either relation
                                    $room = $application->locker?->storageRoom ?? $application->openArea?->storageRoom;
                                @endphp

                                @if ($room)
                                    {{ $room->room_name }}
                                @else
                                    <span class="text-muted fst-italic">No Assignment</span>
                                @endif
                            </div>

                            {{-- 2. Badges Container --}}
                            <div class="d-flex flex-wrap gap-1 justify-content-center">

                                {{-- A. Locker Badge --}}
                                @if ($application->locker_id && $application->locker)
                                    @php
                                        $isLockerAvailable = $application->locker->status === 'available';
                                    @endphp

                                    <span
                                        class="badge border {{ $isLockerAvailable ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }}"
                                        @if (!$isLockerAvailable) title="Warning: Locker is {{ $application->locker->status }}" data-bs-toggle="tooltip" @endif>

                                        @if (!$isLockerAvailable)
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        @else
                                            <i class="bi bi-box-seam me-1"></i>
                                        @endif

                                        Locker {{ $application->locker->code }}
                                    </span>
                                @endif

                                {{-- B. Open Area Badge --}}
                                @if ($application->open_area_id && $application->openArea)
                                    @php
                                        $openArea = $application->openArea;
                                        // Check if the openArea is available or full
                                        $isRoomFull = $openArea && $openArea->area_status !== 'available';
                                    @endphp

                                    <span
                                        class="badge border {{ $isRoomFull ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-info-subtle text-info-emphasis border-info-subtle' }}"
                                        @if ($isRoomFull) title="Warning: Room is not available" data-bs-toggle="tooltip" @endif>

                                        @if ($isRoomFull)
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                        @else
                                            <i class="bi bi-grid me-1"></i>
                                        @endif

                                        Open Area
                                    </span>
                                @endif

                            </div>
                        </div>
                    </td>
                    <td x-show="cols.includes('items')">
                        <div>
                            <span class="badge bg-primary">
                                {{ $application->storedItems->count() }}
                                {{ Str::plural('Item', $application->storedItems->count()) }}
                            </span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($application->storedItems as $item)
                                {{-- Define size-based colors --}}
                                @php
                                    $size = strtolower($item->estimated_size);
                                    $badgeClass = match ($size) {
                                        'small' => 'bg-success-subtle text-success border-success-subtle',
                                        'medium' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        'large' => 'bg-danger-subtle text-danger border-danger-subtle',
                                        default => 'bg-light text-secondary border-secondary-subtle', // Fallback
                                    };

                                    // Optional: Border color for the container line on the left
                                    $borderClass = match ($size) {
                                        'small' => 'border-success',
                                        'medium' => 'border-warning',
                                        'large' => 'border-danger',
                                        default => 'border-secondary',
                                    };
                                @endphp

                                <div wire:key="item-{{ $item->id }}"
                                    class="border-start border-3 {{ $borderClass }} ps-2"> {{-- Added ps-2 for spacing --}}
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-grow-1">
                                            {{-- Apply the dynamic badge class here --}}
                                            <span class="badge border {{ $badgeClass }}">
                                                <i class="bi bi-tag-fill me-1"></i>
                                                {{ ucfirst($item->estimated_size) }}
                                            </span>
                                            {{-- Optional: Show item name if available --}}
                                            {{-- <span class="ms-1 small text-muted">{{ $item->name }}</span> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    {{-- Application Date --}}
                    <td class="align-middle">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">
                                {{ $application->created_at ? $application->created_at->format('M d, Y') : '-' }}
                            </span>
                            <span class="small text-muted">
                                {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                            </span>
                            <span class="small text-muted">
                                <i class="bi bi-calendar-event me-1"></i> {{ $application->semester->academic_year }}
                                - {{ $application->semester->semester_no }}
                            </span>
                        </div>
                    </td>

                    <td x-show="cols.includes('status')" class="align-middle"> {{-- Status --}}
                        @php
                            $comApp = $this->getCompetingApplications($application);
                        @endphp

                        <span class="badge text-bg-warning">
                            {{ ucfirst($application->storage_application_status) }}
                            @if (in_array($application->id, $this->conflictedApplicationIds))
                                <i class="bi bi-exclamation-diamond-fill ms-1" data-bs-toggle="tooltip"
                                    title="Multiple students applied for the same locker"></i>
                            @endif
                        </span>
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <a href="{{ route('storage_application_detail', $application->id) }}"
                                title="View Details" class="btn btn-sm btn-outline-secondary"
                                style="padding: 0 2px;">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <button wire:click="approveApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to approve this application?"
                                class="btn btn-sm btn-outline-success" style="padding: 0 2px;" title="Approve">
                                <i class="bi bi-check"></i>
                            </button>

                            <button
                                wire:click="$set('modalData2', {{ $application->id }}); $dispatch('display_modal2');"
                                class="btn btn-sm btn-outline-danger" style="padding: 0 2px;" title="Reject">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-folder2-open mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Pending Applications Match the Selected Filters</h3>
                            <p class="mt-2 text-muted">Try adjusting your filters to see more results.</p>
                            <div class="mt-3">
                                <button class="btn btn-link text-danger text-decoration-none fw-bold small"
                                    wire:click.prevent="clearFilters" @click="allCol = true; toggleAll()">
                                    <i class="bi bi-x-circle me-1"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
            <x-slot:pagination>{{ $this->pendingStorageApplications->links('vendor.livewire.bootstrap') }}</x-slot:pagination>
        </x-table>
    </div>

    {{-- Application Details --}}
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Application Details</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData }}</span>
            </div>
        </x-slot:title>

        <div wire:loading="modalData" class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div wire:loading.remove="modalData" class="text-start">
            @if ($this->modalApplication)
                @php
                    $competingApplications = $this->getCompetingApplications($this->modalApplication);
                @endphp
                @if ($competingApplications->count() > 1)
                    <div class="alert alert-warning mb-4">
                        <div class="fw-bold mb-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Competing Applications
                        </div>

                        <div class="small text-muted mb-2">
                            Locker: <strong>{{ $this->modalApplication->locker->code }}</strong>
                            · {{ $competingApplications->count() }} applicants
                        </div>

                        <ul class="list-unstyled mb-0">
                            @foreach ($competingApplications as $competing)
                                <li class="d-flex align-items-center mb-1">
                                    @if ($competing->id === $this->modalApplication->id)
                                        <i class="bi bi-eye-fill text-primary me-2"></i>
                                        <strong>{{ $competing->applicant->name }} (You are reviewing)</strong>
                                    @else
                                        <i class="bi bi-person text-secondary me-2"></i>
                                        <h6 class="mb-0 fw-bold text-dark me-3">
                                            {{ $competing->applicant->name }}
                                        </h6>
                                        <small class="text-muted me-3">
                                            {{ $competing->applicant->matric_no }}
                                        </small>
                                        <div>
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                ID: {{ $competing->id }}
                                            </span>
                                        </div>

                                        {{-- Indicate if the competing application was created earlier --}}
                                        @if ($competing->created_at->lt($this->modalApplication->created_at))
                                            <span class="badge bg-secondary ms-2">Applied earlier</span>
                                        @endif
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="text-info mb-3"><i class="bi bi-person-circle me-2"></i>Applicant Information</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">ID</label>
                            <p class="mb-0 fw-medium">{{ $this->modalApplication->applicant->id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Matric No</label>
                            <p class="mb-0 fw-medium">{{ $this->modalApplication->applicant->matric_no ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Name</label>
                            <p class="mb-0 fw-medium">{{ $this->modalApplication->applicant->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Email</label>
                            <p class="mb-0 fw-medium text-break">
                                {{ $this->modalApplication->applicant->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="mb-0 fw-medium text-break">
                                {{ $this->modalApplication->applicant->phone_number ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="text-info mb-3"><i class="bi bi-box-seam me-2"></i>Storage Details</h5>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Storage Room:</label>
                        <p class="mb-0 fw-medium">
                            @if (!empty($this->modalApplication->open_area_id) && !empty($this->modalApplication->locker_id))
                                {{ $this->modalApplication->openArea?->storageRoom?->room_name }}
                            @elseif (!empty($this->modalApplication->locker_id))
                                {{ $this->modalApplication->locker?->storageRoom?->room_name }}
                            @elseif (!empty($this->modalApplication->open_area_id))
                                {{ $this->modalApplication->openArea?->storageRoom?->room_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-muted small d-block mb-2">Storage Type:</span>

                        @if (!empty($this->modalApplication->open_area_id) && !empty($this->modalApplication->locker_id))
                            <div class="d-flex flex-wrap gap-2">
                                <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                    <i class="bi bi-lock me-2"></i>
                                    <span class="me-2">Locker {{ $this->modalApplication->locker->code }}</span>
                                    <span
                                        class="badge {{ match ($this->modalApplication->locker->status) {
                                            'available' => 'text-bg-success',
                                            'occupied' => 'text-bg-danger',
                                            'reserved' => 'bg-warning text-dark',
                                            default => 'text-bg-secondary',
                                        } }}">
                                        {{ ucfirst($this->modalApplication->locker->status) }}
                                    </span>
                                </div>
                                <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                    <i class="bi bi-grid-3x3 me-2"></i>
                                    <span class="me-2">Open Area</span>
                                    <span
                                        class="badge {{ match ($this->modalApplication->openArea->area_status) {
                                            'available' => 'text-bg-success',
                                            'occupied' => 'text-bg-danger',
                                            default => 'text-bg-secondary',
                                        } }}">
                                        {{ ucfirst($this->modalApplication->openArea->area_status) }}
                                    </span>
                                </div>
                            </div>
                        @elseif (!empty($this->modalApplication->locker_id))
                            <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                <i class="bi bi-lock me-2"></i>
                                <span class="me-2">Locker {{ $this->modalApplication->locker->code }}</span>
                                <span
                                    class="badge {{ match ($this->modalApplication->locker->status) {
                                        'available' => 'bg-success',
                                        'occupied' => 'bg-danger',
                                        'reserved' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ ucfirst($this->modalApplication->locker->status) }}
                                </span>
                            </div>
                        @elseif (!empty($this->modalApplication->open_area_id))
                            <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                <i class="bi bi-grid-3x3 me-2"></i>
                                <span class="me-2">Open Area</span>
                                <span
                                    class="badge {{ match ($this->modalApplication->openArea->area_status) {
                                        'available' => 'bg-success',
                                        'occupied' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ ucfirst($this->modalApplication->openArea->area_status) }}
                                </span>
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h5 class="text-info mb-3"><i class="bi bi-archive me-2"></i>Stored Items</h5>
                    @if ($this->modalApplication->storedItems->count() > 0)
                        <div class="row g-3">
                            @foreach ($this->modalApplication->storedItems as $item)
                                <div class="col-12">
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        {{-- Item Photo --}}
                                        @if ($item->item_photo_path)
                                            <img src="{{ \Storage::disk('public')->url($item->item_photo_path) }}"
                                                alt="Item Photo" class="rounded shadow-sm me-3"
                                                style="width: 80px; height: 80px; object-fit: cover;" />
                                        @else
                                            {{-- Placeholder for No Image --}}
                                            <div class="d-flex align-items-center justify-content-center bg-light rounded border text-secondary shadow-sm me-3"
                                                style="width: 60px; height: 60px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                                                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                    <path
                                                        d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 fw-bold">{{ ucfirst($item->item_name) }}</h6>
                                            <div class="mb-2">
                                                <span
                                                    class="badge bg-primary me-1">{{ ucfirst($item->item_type) }}</span>
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($item->estimated_size) }}</span>
                                            </div>
                                            <p class="mb-0 small text-muted">{{ ucfirst($item->item_description) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0 py-3">No stored items found.</p>
                    @endif
                </div>
            @else
                <p>No application data found.</p>
            @endif
        </div>
    </x-custom-modal>

    {{-- Reject Application Modal --}}
    <x-custom-modal2>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Reject Application</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData2 }}</span>
            </div>
        </x-slot:title>
        <div class="d-flex justify-content-center">
            <div wire:loading="modalData2" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div wire:loading.remove="modalData2" class="pb-3">
            <form wire:submit.prevent="rejectApplication({{ $this->modalData2 }})">
                <div class="mb-4">
                    <label for="rejectionNote" class="form-label fw-semibold">
                        <i class="bi bi-chat-left-text me-2"></i>Note for the Applicant
                    </label>
                    <textarea wire:model="rejectionNote" id="rejectionNote" class="form-control" rows="5"
                        placeholder="Provide a clear reason for the rejection..." required></textarea>
                    @error('rejectionNote')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        This message will be sent to the student.
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-octagon me-1"></i>Submit Rejection
                    </button>
                </div>
            </form>
        </div>
    </x-custom-modal2>
</div>
@assets
    <style>
        .avatar {
            width: 2.5rem;
            height: 2.5rem;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
        }

        .avatar-initial {
            width: 100%;
            height: 100%;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        .fade-out {
            animation: fadeOut 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
    </style>
@endassets
