<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    {{-- Main Content --}}
    @include('livewire.includes.header2', [
        'title' => 'Missing Report Management',
        'subtitle' => 'View, verify, and take action on all submitted missing item reports.',
    ])

    <div class="container-fluid">

        {{-- Report Cases Metrics --}}
        <x-card-matrix :items="[
            [
                'title' => $this->thisSemester,
                'subtitle' => 'Total (Current Semester)',
                'icon' => 'bi-file-earmark-text-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->reportCounts['under_investigation'] ?? 0,
                'subtitle' => 'Under Investigation',
                'icon' => 'bi-search',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->reportCounts['resolved'] ?? 0,
                'subtitle' => 'Resolved Cases',
                'icon' => 'bi-check-circle-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->reportCounts['pending'] ?? 0,
                'subtitle' => 'Pending Cases',
                'icon' => 'bi-hourglass-split',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
            [
                'title' => $this->reportCounts['dismissed'] ?? 0,
                'subtitle' => 'Dismissed Cases',
                'icon' => 'bi-x-octagon-fill',
                'color' => 'text-secondary',
                'bg' => 'bg-secondary bg-opacity-10',
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
                <label class="form-label small text-muted fw-bold">Semester</label>
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

            {{-- Status --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Status</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="statusFilter">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="investigating">Investigating</option>
                    <option value="found">Found</option>
                    <option value="lost">Lost</option>
                    <option value="dismissed">Dismissed</option>
                </select>
            </div>

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
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
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
                <th class="align-middle py-2">
                    User Details
                </th>
                <th class="align-middle py-2">
                    Contact
                </th>
                <th class="align-middle py-2">
                    Missing Item
                </th>
                <th class="align-middle py-2">
                    Storage Details
                </th>
                <th class="align-middle py-2">
                    Report Date
                </th>
                <th class="align-middle py-2">
                    Status
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>

            @forelse($this->missingReports as $report)
                @php
                    $applicant = $report->storedItems->first()?->storageApplication->applicant;
                    $application = $report->storedItems->first()?->storageApplication;
                @endphp
                <tr wire:key="row-{{ $report->id }}">
                    <td class="align-middle"> {{-- Report Id --}}
                        <span class="text-secondary small user-select-none">#</span>
                        <span class="fw-bold text-dark">{{ $report->id }}</span>
                    </td>
                    <td class="align-middle"> {{-- User Details --}}
                        <div class="d-flex align-items-center gap-1">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex justify-content-center align-items-center text-uppercase"
                                    style="height: 30px; width: 30px;">
                                    {{ substr($applicant->name ?? 'NA', 0, 2) }}
                                </div>
                            </div>

                            <div>
                                <div class="fw-bold text-dark mb-0">
                                    {{ $applicant->name ?? 'N/A' }}
                                </div>

                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-secondary border fw-normal">
                                        {{ ucfirst($applicant->gender) ?? '-' }}
                                    </span>
                                    <span class="badge bg-light text-secondary border fw-normal">
                                        Year {{ $applicant->year_of_study ?? '-' }}
                                    </span>
                                    <span
                                        class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-normal">
                                        {{ $applicant->matric_no ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center text-dark" style="font-size: 0.85rem;">
                                <i class="bi bi-envelope text-muted me-2"></i>
                                <span class="text-truncate" style="max-width: 180px;"
                                    title="{{ $applicant->email }}">
                                    {{ $applicant->email ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center text-secondary small">
                                <i class="bi bi-telephone me-2"></i>
                                <span>{{ $applicant->phone_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle" style="min-width: 150px;"> {{-- Set min-width to prevent squishing --}}
                        @php
                            $items = $report->storedItems;
                            $count = $items->count();
                        @endphp

                        @if ($count > 0)
                            <div x-data="{ open: false }">

                                {{-- Header: Badge + Tiny Toggle Link --}}
                                <div class="d-flex align-items-center justify-content-center">
                                    <button @click="open = !open"
                                        class="btn badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                        {{ $count }} {{ Str::plural('Item', $count) }}
                                        <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                    </button>
                                </div>

                                {{-- Expanded Item List --}}
                                <div x-show="open" x-transition.opacity.duration.200ms style="display: none;"
                                    class="mt-2">
                                    <div class="card card-body p-2 bg-light border-0 rounded-3">
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($items as $index => $item)
                                                <li
                                                    class="d-flex align-items-center justify-content-between {{ !$loop->last ? 'border-bottom pb-1 mb-1' : '' }}">
                                                    {{-- Item Name --}}
                                                    <span class="text-dark small fw-medium text-truncate"
                                                        style="max-width: 100px;" title="{{ $item->item_name }}">
                                                        {{ ucfirst($item->item_name) }}
                                                    </span>

                                                    {{-- Item Type (Tiny Tag) --}}
                                                    <span class="badge bg-white text-secondary border"
                                                        style="font-size: 0.65rem;">
                                                        {{ ucfirst($item->item_type) }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        @else
                            <span class="text-muted small fst-italic opacity-50">No items</span>
                        @endif
                    </td>
                    <td> {{-- Storage Details --}}
                        <div class="d-flex flex-column align-items-center">
                            {{-- ID Badge --}}
                            <div>
                                <a href="{{ route('storage_application_detail', $application->id) }}" class="badge bg-secondary">
                                    Application ID: {{ $application->id }}
                                </a>
                            </div>

                            {{-- Storage Room Name and Type Badges --}}
                            <div class="d-flex flex-column justify-content-center">
                                <div class="fw-bold text-dark mb-1 text-center">
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

                                <div class="d-flex flex-wrap gap-1">
                                    @if (!empty($application->open_area_id) && !empty($application->locker_id))
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            <svg class="bi" width="16" height="16" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                            </svg>Locker {{ $application->locker->code }}
                                        </span>
                                        <span
                                            class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                            <i class="bi bi-grid me-1"></i>Open Area
                                        </span>
                                    @elseif (!empty($application->locker_id))
                                        <span
                                            class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            <svg class="bi" width="16" height="16" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                            </svg>Locker {{ $application->locker->code }}
                                        </span>
                                    @elseif (!empty($application->open_area_id))
                                        <span
                                            class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                            <i class="bi bi-grid me-1"></i>Open Area
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">{{-- Report Date --}}
                        <div class="d-flex flex-column font-monospace">
                            <span class="text-dark fw-bold" style="font-size: 0.9rem;">
                                {{ $report->created_at->format('M d, Y') }}
                            </span>
                            <span class="text-secondary small">
                                {{ $report->created_at->format('h:i:s A') }}
                            </span>
                            <span class="small text-muted">
                                <i class="bi bi-calendar-event me-1"></i> {{ $report->semester->academic_year }}
                                - {{ $report->semester->semester_no }}
                            </span>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span @class([
                            'badge border rounded-pill fw-normal px-3 d-inline-flex align-items-center gap-1',
                            'bg-warning-subtle text-warning-emphasis border-warning-subtle' =>
                                $report->status === 'pending',
                            'bg-info-subtle text-info-emphasis border-info-subtle' =>
                                $report->status === 'investigating',
                            'bg-success-subtle text-success border-success-subtle' =>
                                $report->status === 'found',
                            'bg-danger-subtle text-danger border-danger-subtle' =>
                                $report->status === 'lost',
                            'bg-secondary-subtle text-secondary border-secondary-subtle' =>
                                $report->status === 'dismissed',
                        ])>
                            {{-- Icon Logic --}}
                            @if ($report->status === 'pending')
                                <i class="bi bi-hourglass-split"></i>
                            @elseif($report->status === 'investigating')
                                <i class="bi bi-search"></i>
                            @elseif($report->status === 'found')
                                <i class="bi bi-check-circle-fill"></i>
                            @elseif($report->status === 'lost')
                                <i class="bi bi-x-circle-fill"></i>
                            @elseif($report->status === 'dismissed')
                                <i class="bi bi-slash-circle"></i>
                            @endif

                            {{-- Status Text --}}
                            <span>{{ ucfirst($report->status) }}</span>
                        </span>
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="d-flex align-items-center gap-1">

                            {{-- 1. View Details (Existing) --}}
                            <a href="{{ route('missing_report_detail', $report->id) }}" title="View Report Details"
                                class="btn btn-sm btn-outline-secondary" style="padding: 0 2px">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            {{-- 2. Update Status (Edit) --}}
                            {{-- Opens a different modal to change status or add admin notes --}}
                            <button wire:click="$set('modalData2', {{ $report->id }}); $dispatch('display_modal2');"
                                title="Update Status" class="btn btn-sm btn-outline-primary" style="padding: 0 2px"
                                type="button">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- 3. Contact Student (Mailto Link) --}}
                            {{-- Uses the relationship to get email directly --}}
                            <a href="mailto:{{ $report->storedItems->first()?->storageApplication->applicant->email }}"
                                title="Email Student" class="btn btn-sm btn-outline-info" style="padding: 0 2px"
                                type="button">
                                <i class="bi bi-envelope-fill"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-folder2-open mx-auto text-muted" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Missing Report Found</h3>
                            <p class="mt-2 text-muted">There are no missing report to review.</p>
                        </div>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $this->missingReports->links('vendor.livewire.bootstrap') }}
            </x-slot:pagination>
        </x-table>

    </div>

    <x-custom-modal2>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Update Status</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData2 }}</span>
            </div>
        </x-slot:title>

        <div wire:loading="modalData2" class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- BODY (The Form) --}}
        <div class="p-1" wire:loading.remove="modalData2">
            @if ($this->modalReport2)
                {{-- Status Selection --}}
                <div class="mb-4">
                    <label for="statusSelect" class="form-label fw-bold">New Status</label>
                    <select id="statusSelect" wire:model="newStatus" class="form-select form-select-lg">
                        <option value="pending">Pending</option>
                        <option value="investigating">Investigating (Checking Logs/CCTV)</option>
                        <option value="found">Found (Resolved)</option>
                        <option value="lost">Lost (Case Closed)</option>
                        <option value="dismissed">Dismissed (Invalid/False Alarm)</option>
                    </select>
                    <div class="form-text text-muted">
                        Changing status to <strong>Found</strong> or <strong>Lost</strong> will close the case.
                    </div>
                    @error('newStatus')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Admin Remarks --}}
                <div class="mb-3">
                    <label for="adminNote" class="form-label fw-bold">Admin Remarks (Optional)</label>
                    <textarea id="adminNote" wire:model="adminNote" class="form-control" rows="3"
                        placeholder="e.g., Item found at Guard House, or Student retrieved item..."></textarea>
                    @error('adminNote')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>

        {{-- FOOTER --}}
        <x-slot:footer>
            <button type="button" class="btn btn-light border" @click="closeModal()">
                Cancel
            </button>

            <button type="button" class="btn btn-primary" wire:click="updateStatus" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="updateStatus">Save Changes</span>
                <span wire:loading wire:target="updateStatus">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Saving...
                </span>
            </button>
        </x-slot:footer>
    </x-custom-modal2>
</div>
