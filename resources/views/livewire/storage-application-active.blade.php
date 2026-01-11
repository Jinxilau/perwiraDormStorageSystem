<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    {{-- Main Content --}}
    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'storageDetails', 'item', 'applicationDate', 'status'],
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
                'title' => $this->activeCounts['total'],
                'subtitle' => 'Approved',
                'icon' => 'bi-check-circle-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->approvedThisSemester,
                'subtitle' => 'Approved (Current Semester)',
                'icon' => 'bi-calendar-check-fill',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
            [
                'title' => $this->activeCounts['locker'],
                'subtitle' => 'Applied Locker',
                'icon' => 'bi-lock-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->activeCounts['open_area'],
                'subtitle' => 'Applied Open Area',
                'icon' => 'bi-grid-3x3-gap-fill',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->pendingCheckinCount,
                'subtitle' => 'Pending Check-in Applications',
                'icon' => 'bi-clock-history',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->pendingCheckoutCount,
                'subtitle' => 'Pending Check-out Applications',
                'icon' => 'bi-box-arrow-right',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->totalItemsCount,
                'subtitle' => 'Total Stored Items (Includes haven\'t checked-in)',
                'icon' => 'bi-archive-fill',
                'color' => 'text-secondary',
                'bg' => 'bg-secondary bg-opacity-10',
            ],
            [
                'title' => $this->unclaimedCount,
                'subtitle' => 'Unclaimed Storages',
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
        ]" />

        {{-- Filtering Options --}}
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

            {{-- Unclaimed --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Show Unclaimed</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="unclaimedOnly">
                    <option value="0">All</option>
                    <option value="1">Unclaimed Only</option>
                </select>
            </div>

            {{-- QR Code Status --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">QR Status</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="qrStatus">
                    <option value="">All Status</option>
                    <option value="not_scanned">Not Checked In</option>
                    <option value="checked_in">Checked In</option>
                    <option value="checked_out">Checked Out</option>
                    {{-- <option value="expired">Expired</option> --}}
                </select>
            </div>

            {{-- Sort By Check-In Date --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Check-In Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortCheckIn">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            {{-- Sort By Check-Out Date --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Check-Out Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortCheckOut">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            {{-- Sort By Date --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Application Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortDate">
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

            {{-- Column Display --}}
            <div class="col-md-4 col-lg-3" x-data="{ show: false }">
                <label class="form-label small text-muted fw-bold">Columns</label>
                <div class="position-relative">
                    <button @click.prevent="show = !show"
                        class="form-select bg-light border-0 text-start d-flex justify-content-between align-items-center">
                        <span x-text="cols.length === 4 ? 'All Visible' : 'Custom'"></span>
                    </button>
                    {{-- Dropdown --}}
                    <div x-show="show" @click.away="show = false" x-transition.opacity.duration.200ms
                        class="position-absolute bg-white border rounded-3 shadow p-3 mt-1 z-3"
                        style="display: none; min-width: 220px; z-index: 1050;">

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="allCol" x-model="allCol"
                                @change="toggleAll()">
                            <label class="form-check-label small" for="allCol">Select All</label>
                        </div>
                        <hr class="my-2">

                        @foreach (['studentInfo' => 'Student Info', 'storageDetails' => 'Storage Details', 'item' => 'Items', 'applicationDate' => 'Application Date', 'status' => 'Status'] as $val => $label)
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

        {{-- Table --}}
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
                    Approve Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Storage Status
                </th>
                <th class="align-middle py-2">
                    Check-in Date
                </th>
                <th class="align-middle py-2">
                    Check-out Date
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>
            @forelse($this->approvedStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}">
                    <td class="align-middle" style="width: 1%; white-space: nowrap;"> {{-- ID --}}
                        <span class="text-secondary small user-select-none">#</span>
                        <span class="fw-bold text-dark">{{ $application->id }}</span>
                    </td>
                    <td class="align-middle" x-show="cols.includes('studentInfo')"> {{-- Student Info --}}
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
                    <td class="align-middle" x-show="cols.includes('storageDetails')"> {{-- Storage Details --}}
                        <div class="d-flex flex-column justify-content-center">
                            <div class="fw-bold text-dark mb-1">
                                @if (!empty($application->open_area_id) && !empty($application->locker_id))
                                    {{ $application->openArea?->storageRoom?->room_name ?? 'Unknown Room' }}
                                @elseif (!empty($application->locker_id))
                                    {{ $application->locker?->storageRoom?->room_name ?? 'Unknown Room' }}
                                @elseif (!empty($application->open_area_id))
                                    {{ $application->openArea?->storageRoom?->room_name ?? 'Unknown Room' }}
                                @else
                                    <span class="text-muted fst-italic">No Assignment</span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                @if (!empty($application->open_area_id) && !empty($application->locker_id))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="bi bi-box-seam me-1"></i>Locker {{ $application->locker->code }}
                                    </span>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        <i class="bi bi-grid me-1"></i>Open Area
                                    </span>
                                @elseif (!empty($application->locker_id))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="bi bi-box-seam me-1"></i>Locker {{ $application->locker->code }}
                                    </span>
                                @elseif (!empty($application->open_area_id))
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        <i class="bi bi-grid me-1"></i>Open Area
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
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                <span class="ms-1 small text-muted text-truncate"
                                                    style="max-width: 180px;" title="{{ $item->item_name }}">
                                                    {{ $item->item_name }}
                                                </span>

                                                <span class="badge bg-light text-secondary border flex-shrink-0">
                                                    <i class="bi bi-box-seam me-1"></i>
                                                    {{ ucfirst($item->item_condition) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="align-middle" x-show="cols.includes('applicationDate')"> {{-- Approve Date --}}
                        @if ($application->updated_at)
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">
                                    {{ $application->updated_at->format('M d, Y') }}
                                </span>
                                <span class="small text-muted">
                                    {{ $application->updated_at->format('h:i A') }}
                                </span>
                                <span class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $application->semester->academic_year }}
                                    - {{ $application->semester->semester_no }}
                                </span>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="align-middle" x-show="cols.includes('status')">
                        @if ($application->qrCode?->status === 'checked_out')
                            <span class="badge text-bg-success">
                                Checked Out
                            </span>
                        @elseif ($application->is_unclaimed)
                            <span class="badge text-bg-danger">
                                Unclaimed
                            </span>
                        @elseif ($application->qrCode?->status === 'checked_in')
                            <span class="badge text-bg-warning">
                                Checked In
                            </span>
                        @else
                            <span class="badge text-bg-secondary">
                                Not Checked In
                            </span>
                        @endif
                    </td>
                    {{-- Check-in Date --}}
                    <td class="align-middle">
                        @php
                            $checkin = $application->qrCode->scanLogs
                                ->where('event_type', 'check_in')
                                ->sortBy('created_at')
                                ->first();
                        @endphp
                        @if ($checkin)
                            <div class="d-flex align-items-center">
                                <div class="me-2 text-success opacity-75">
                                    <i class="bi bi-box-arrow-in-right fs-5"></i>
                                </div>
                                <div class="d-flex flex-column lh-sm">
                                    <span
                                        class="fw-bold text-dark small">{{ $checkin->created_at->format('M d, Y') }}</span>
                                    <span class="text-muted"
                                        style="font-size: 0.75rem;">{{ $checkin->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        @else
                            <span class="badge bg-light text-secondary border fw-normal">Pending</span>
                        @endif
                    </td>

                    {{-- Check-out Date --}}
                    <td class="align-middle">
                        @php
                            $checkout = $application->qrCode->scanLogs->where('event_type', 'check_out')->first();
                        @endphp
                        @if ($checkout)
                            <div class="d-flex align-items-center">
                                <div class="me-2 text-warning opacity-75">
                                    <i class="bi bi-box-arrow-right fs-5"></i>
                                </div>
                                <div class="d-flex flex-column lh-sm">
                                    <span
                                        class="fw-bold text-dark small">{{ $checkout->created_at->format('M d, Y') }}</span>
                                    <span class="text-muted"
                                        style="font-size: 0.75rem;">{{ $checkout->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small fst-italic opacity-50">-</span>
                        @endif
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <a href="{{ route('storage_application_detail', $application->id) }}"
                                title="View Details" class="btn btn-sm btn-outline-secondary"
                                style="padding: 0 2px;">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <a href="{{ route('check_in_application', $application->id) }}"
                                class="btn btn-sm btn-outline-success" style="padding: 0 2px;" title="Check In">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </a>

                            <a href="{{ route('check_in_application', $application->id) }}"
                                class="btn btn-sm btn-outline-danger" style="padding: 0 2px;" title="Check Out">
                                <i class="bi bi-box-arrow-left"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-folder2-open mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Active Applications Match the Selected Filters</h3>
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
                {{ $this->approvedStorageApplications->links('vendor.livewire.bootstrap') }}
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
                <div class="text-center pb-4 border-bottom mb-4">
                    <h5 class="text-info mb-3"><i class="bi bi-qr-code me-2"></i>QR Code</h5>
                    <p class="fw-bold mt-3 mb-0">{{ $this->modalApplication->qrCode->qr_token }}</p>
                    <div class="d-inline-block p-3 bg-white rounded shadow-sm border">
                        {!! QrCode::size(200)->generate($this->modalApplication->qrCode->qr_token) !!}
                    </div>
                    {{-- NEW: Download Button --}}
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary"
                            wire:click="downloadQrCode({{ $this->modalApplication->id }})">
                            <i class="bi bi-download me-1"></i> Download QR
                        </button>
                    </div>
                    <p class="text-muted small mt-3 mb-0">Scan this code for quick check-in and check-out.</p>
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
                        <h5 class="text-info mb-3">Approver</h5>
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
            @endif
        </div>
    </x-custom-modal>
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
