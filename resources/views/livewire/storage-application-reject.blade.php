<div>
    {{-- Main Content --}}
    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'storageDetails', 'item', 'applicationDate', 'rejectedDate', 'status'],
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
    }" x-init="toggleAll()" class="bg-white shadow-sm rounded-lg overflow-hidden">
        <x-card-matrix :items="[
            [
                'title' => $this->countTotalRejected,
                'subtitle' => 'Total Rejected Application',
                'icon' => 'bi-x-circle-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->rejectedThisSemester,
                'subtitle' => 'Rejected (Current Semester)',
                'icon' => 'bi-calendar-x-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->countTotalRejectedToday,
                'subtitle' => 'Total Rejected Today',
                'icon' => 'bi-dash-circle-fill',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->rejectedThisWeek,
                'subtitle' => 'Rejected This Week',
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->countRejectedLockers,
                'subtitle' => 'Rejected Lockers',
                'icon' => 'bi-lock-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->countRejectedOpenAreas,
                'subtitle' => 'Rejected Open Areas',
                'icon' => 'bi-grid-3x3-gap-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
        ]" />


        <x-filter-options>
            {{-- Room --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Room</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="room">
                    <option value="">All Room</option>
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

            {{-- Storage Type --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Type</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="storageType">
                    <option value="">All Types</option>
                    <option value="locker">Locker</option>
                    <option value="openArea">Open Area</option>
                </select>
            </div>

            {{-- Sort By Date --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Application Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortApplicationDate">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>


            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Rejected Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="rejectedDate">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            {{-- Sort By Name --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Applicant Name
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortName">
                    <option value="">Default (No Sorting)</option>
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>

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

                        @foreach (['studentInfo' => 'Student Info', 'storageDetails' => 'Storage Details', 'item' => 'Items', 'applicationDate' => 'Application Date', 'rejectedDate' => 'Rejected Date', 'status' => 'Status'] as $val => $label)
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

        <x-table>
            <x-slot:header>
                <th class="align-middle py-2">
                    ID
                </th>
                <th class="align-middle py-2" x-show="cols.includes('studentInfo')">
                    Student Info
                </th>
                <th class="align-middle py-2" x-show="cols.includes('storageDetails')">
                    Storage Details
                </th>
                <th class="align-middle py-2" x-show="cols.includes('item')">
                    Items
                </th>
                <th class="align-middle py-2" x-show="cols.includes('applicationDate')">
                    Application Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('rejectedDate')">
                    Rejected Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Status
                </th>
                <th class="align-middle py-2">
                    Reason
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>

            @forelse($this->rejectedStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}" style="white-space: nowrap;">
                    <td class="align-middle" style="width: 1%;">
                        <span class="text-secondary small user-select-none">#</span>
                        <span class="fw-bold text-dark">{{ $application->id }}</span>
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
                    <td x-show="cols.includes('item')">
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
                    <td class="align-middle" x-show="cols.includes('applicationDate')">
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

                    {{-- Rejected Date --}}
                    <td class="align-middle" x-show="cols.includes('rejectedDate')">
                        @if ($application->updated_at)
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">
                                    {{ $application->updated_at->format('M d, Y') }}
                                </span>
                                <span class="small text-muted">
                                    {{ $application->updated_at->format('h:i A') }}
                                </span>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td x-show="cols.includes('status')" class="align-middle"> {{-- Status --}}
                        <span class="badge text-bg-danger">
                            {{ ucfirst($application->storage_application_status) }}
                        </span>
                    </td>
                    <td class="align-middle">
                        <div class="border rounded bg-light p-2 small text-secondary"
                            style="max-height: 120px; overflow-y: auto; min-width: 180px;">
                            @if (!empty($application->note))
                                <div class="lh-sm">
                                    {{ $application->note }}
                                </div>
                            @else
                                <span class="text-muted fst-italic opacity-50">N/A</span>
                            @endif
                        </div>
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <a href="{{ route('storage_application_detail', $application->id) }}"
                                title="View Details" class="btn btn-sm btn-outline-secondary"
                                style="padding: 0 2px;">
                                data-bs-target="#viewModal">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <button
                                wire:click="$set('modalData2', {{ $application->id }}); $dispatch('display_modal2')"
                                class="btn btn-sm btn-outline-info" style="padding: 0 2px;"
                                title="Edit Rejection Note">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <button wire:click="undoRejectApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to undo reject the application?"
                                class="btn btn-sm btn-outline-warning" style="padding: 0 2px;" title="Undo Reject">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-folder2-open mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Rejected Applications Match the Selected Filters</h3>
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

            <x-slot:pagination>
                {{ $this->rejectedStorageApplications->links('vendor.livewire.bootstrap') }}
            </x-slot:pagination>
        </x-table>
    </div>

    {{-- Application Details Modal --}}
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Application Details</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData }}</span>
            </div>
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="modalData" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div wire:loading.remove="modalData" class="text-start">
            @if ($this->modalApplication)
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
                                    <span class="me-2">Locker</span>
                                    <span
                                        class="badge {{ match ($this->modalApplication->locker->status) {
                                            'available' => 'text-bg-success',
                                            'occupied' => 'text-bg-danger',
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
                                <span class="me-2">Locker</span>
                                <span
                                    class="badge {{ match ($this->modalApplication->locker->status) {
                                        'available' => 'bg-success',
                                        'occupied' => 'bg-danger',
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
                <div class="mb-4 pb-3 border-bottom">
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

                                        {{-- Item Details --}}
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
                @if ($this->modalApplication->approver)
                    <div class="mb-2">
                        <h5 class="text-info mb-3">Rejected by:</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">ID</label>
                                <p class="mb-0 fw-medium">#{{ $this->modalApplication->approver->id ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Name</label>
                                <p class="mb-0 fw-medium">{{ $this->modalApplication->approver->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Email</label>
                                <p class="mb-0 fw-medium text-break">
                                    {{ $this->modalApplication->approver->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Phone</label>
                                <p class="mb-0 fw-medium text-break">
                                    {{ $this->modalApplication->approver->phone_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-2">No reviewer information available.</p>
                @endif
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
        @php
            $this->rejectionNote = $this->modalApplication2?->note ?? '';
        @endphp
        <div wire:loading.remove="modalData2" class="pb-3">
            @if ($this->modalApplication2)
                {{-- No need to pass ID, the component already knows it via $modalData2 --}}
                <form wire:submit.prevent="updateNote">
                    <div class="mb-4">
                        <label for="rejectionNote" class="form-label fw-semibold">
                            <i class="bi bi-chat-left-text me-2"></i>Note for the Applicant
                        </label>

                        {{-- Added wire:dirty to show visual feedback when text changes --}}
                        <textarea wire:model="rejectionNote" id="rejectionNote"
                            class="form-control @error('rejectionNote') is-invalid @enderror" rows="5"
                            placeholder="Provide a clear reason..." required></textarea>

                        @error('rejectionNote')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            This message will be visible to:
                            <strong>{{ $this->modalApplication2->applicant->name ?? 'Student' }}</strong>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        {{-- Use wire:click to explicitly close/reset if needed --}}
                        <button type="button" class="btn btn-secondary" wire:click="$dispatch('close-modal')">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Note
                        </button>
                    </div>
                </form>
            @endif
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
