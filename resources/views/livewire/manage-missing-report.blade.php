<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Report Details</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData }}</span>
            </div>
        </x-slot:title>

        <div wire:loading="modalData" class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- LOGIC BLOCK --}}
        @php
            $modalReport = App\Models\MissingReport::with([
                'storedItems.storageApplication.applicant',
                'storedItems.storageApplication.locker.storageRoom',
                'storedItems.storageApplication.openArea.storageRoom',
            ])->find($this->modalData);

            $application = $modalReport?->storedItems->first()?->storageApplication;

            // Status Badge Logic
            $statusColor = match ($modalReport?->status) {
                'approved', 'found', 'resolved' => 'success',
                'pending' => 'warning text-dark',
                'rejected' => 'danger',
                default => 'secondary',
            };
        @endphp

        <div wire:loading.remove="modalData">
            @if ($modalReport)
                <div class="container-fluid px-0">

                    {{-- TOP HEADER: Status & Date --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Current Status</small>
                            <div>
                                <span class="badge bg-{{ $statusColor }} fs-6 mt-1">
                                    {{ ucfirst($modalReport->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted text-uppercase fw-bold">Reported Date</small>
                            <div class="fw-bold text-dark">
                                {{ $modalReport->created_at->format('M d, Y') }}
                            </div>
                            <small class="text-muted">{{ $modalReport->created_at->format('h:i A') }}</small>
                        </div>
                    </div>

                    {{-- TIMELINE GRID --}}
                    <div class="row g-3 mb-4">
                        {{-- Last Seen Column --}}
                        <div class="col-md-6">
                            <div class="h-100 p-3 border rounded-3">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="bi bi-eye me-2"></i>Last Seen
                                </h6>
                                @if ($modalReport->last_seen_type !== 'dont_remember')
                                    <p class="mb-1 text-muted small">Time/Range</p>
                                    <p class="fw-medium">
                                        {{ $modalReport->last_seen_date?->format('M d, Y h:i A') ?? $modalReport->last_seen_range }}
                                    </p>
                                @endif
                                <p class="mb-1 text-muted small">Location</p>
                                <p class="fw-medium mb-0">{{ $modalReport->last_seen_location }}</p>
                            </div>
                        </div>

                        {{-- Discovered Missing Column --}}
                        <div class="col-md-6">
                            <div class="h-100 p-3 border rounded-3">
                                <h6 class="text-danger fw-bold mb-3">
                                    <i class="bi bi-exclamation-circle me-2"></i>Discovered Missing
                                </h6>
                                @if ($modalReport->discovered_missing_type !== 'dont_remember')
                                    <p class="mb-1 text-muted small">Time/Range</p>
                                    <p class="fw-medium">
                                        {{ $modalReport->discovered_missing_date?->format('M d, Y h:i A') ?? $modalReport->discovered_missing_range }}
                                    </p>
                                @else
                                    <span class="text-muted fst-italic">Time unknown</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- WITNESSES --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary text-uppercase small mb-2">Witnesses & Additional Info</h6>
                        <div class="bg-light p-3 rounded-3 border">
                            <p class="mb-0 text-break">
                                {{ $modalReport->witnesses_and_info ?? 'No additional information provided.' }}
                            </p>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="row g-4">
                        {{-- LEFT COL: Contact Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Reported By</h6>
                            <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
                                <div class="flex-shrink-0 bg-white p-2 rounded-circle border shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="currentColor" class="bi bi-person text-secondary" viewBox="0 0 16 16">
                                        <path
                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                                    </svg>
                                </div>
                                <div class="ms-3 overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate">
                                        {{ $application?->applicant?->name ?? 'N/A' }}</h6>
                                    <small
                                        class="text-muted d-block text-truncate">{{ $application?->applicant?->email ?? 'N/A' }}</small>
                                    <small
                                        class="text-muted">{{ $application?->applicant?->phone_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COL: Items --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Missing Items</h6>
                            <ul class="list-group list-group-flush border rounded-3">
                                @foreach ($modalReport->storedItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-medium">{{ ucfirst($item->item_name) }}</span>
                                        <span
                                            class="badge bg-light text-dark border">{{ ucfirst($item->item_type) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- FOOTER: Storage Location --}}
                    <div class="mt-4 p-3 bg-blue-50 border border-primary rounded-3" style="background-color: #f8f9fa;">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="text-primary mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                    <path
                                        d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                </svg>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Assigned Storage</h6>
                                @if ($application)
                                    @php
                                        $hasLocker = !empty($application->locker_id);
                                        $hasOpenArea = !empty($application->open_area_id);
                                    @endphp

                                    @if ($hasOpenArea && $hasLocker)
                                        <p class="mb-0 small">
                                            {{ $application->openArea?->storageRoom?->room_name }}
                                            <span class="text-muted mx-2">|</span>
                                            Locker + Open Area
                                        </p>
                                    @elseif ($hasLocker)
                                        <p class="mb-0 small">
                                            {{ $application->locker?->storageRoom?->room_name }}
                                            <span class="text-muted mx-2">|</span>
                                            Locker
                                        </p>
                                    @elseif ($hasOpenArea)
                                        <p class="mb-0 small">
                                            {{ $application->openArea?->storageRoom?->room_name }}
                                            <span class="text-muted mx-2">|</span>
                                            Open Area
                                        </p>
                                    @endif
                                @else
                                    <p class="mb-0 text-muted small">No storage application found associated with these
                                        items.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div> {{-- End Container --}}
            @endif
        </div>
    </x-custom-modal>

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

        {{-- LOGIC BLOCK --}}
        @php
            $modalReport2 = App\Models\MissingReport::with([
                'storedItems.storageApplication.applicant',
                'storedItems.storageApplication.locker.storageRoom',
                'storedItems.storageApplication.openArea.storageRoom',
            ])->find($this->modalData2);
        @endphp

        {{-- BODY (The Form) --}}
        <div class="p-1" wire:loading.remove="modalData2">
            @if ($modalReport2)
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


    {{-- Main Content --}}
    @include('livewire.includes.header2', [
        'title' => 'Missing Report Management',
        'subtitle' => 'View, verify, and take action on all submitted missing item reports.',
    ])

    <div class="container-fluid">

        <x-card-matrix :items="[
            ['title' => $this->reportCounts['under_investigation'], 'subtitle' => 'Under Investigation'],
            ['title' => $this->reportCounts['resolved'], 'subtitle' => 'Resolved Cases'],
            ['title' => $this->reportCounts['pending'], 'subtitle' => 'Pending Cases'],
            ['title' => $this->reportCounts['dismissed'], 'subtitle' => 'Dismissed Cases'],
        ]" />

        <x-filter-options>
            {{-- Room --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Room</label>
                <select class="form-select bg-light border-0" wire:model="room">
                    <option value="">All Rooms</option>
                    @foreach ($this->rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Status</label>
                <select class="form-select bg-light border-0" wire:model="statusFilter">
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
                    <input type="date" wire:model.live="fromRange" class="form-control bg-light border-0"
                        placeholder="Start Date" aria-label="Start Date">

                    {{-- Separator --}}
                    <span class="input-group-text bg-light border-0 text-secondary">to</span>

                    {{-- End Date --}}
                    <input type="date" wire:model.live="toRange" class="form-control bg-light border-0"
                        placeholder="End Date" aria-label="End Date">
                </div>
            </div>

            {{-- Sort Options --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Sort: Date</label>
                <select class="form-select bg-light border-0" wire:model="sortDate">
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
            </div>

            {{-- Pagination --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Per Page</label>
                <select class="form-select bg-light border-0" wire:model="paginationInt">
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
                        {{ count($this->selectedReports) }} report(s) selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="bulkApprove"
                        @empty($this->selectedReports) disabled @endempty
                        wire:confirm="Are you sure you want to approve the selected reports?" class="btn btn-success">
                        Approve Selected
                    </button>
                    <button type="button" wire:click="$dispatch('display_modal3');"
                        wire:confirm="Are you sure you want to reject the selected reports?"
                        @empty($this->selectedReports) disabled @endempty class="btn btn-danger">
                        Reject Selected
                    </button>
                </div>
            </div>
        </div>

        <x-table>
            <x-slot:header>
                <th class="text-uppercase align-middle">
                    <input type="checkbox" wire:model.live="selectAll">
                </th>
                <th class="align-middle py-2">
                    Report ID
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
                <tr wire:key="row-{{ $report->id }}"
                    class="{{ in_array($report->id, $this->selectedReports) ? 'table-active' : '' }}">
                    <td class="align-middle"> {{-- Select --}}
                        <input type="checkbox" value="{{ $report->id }}" wire:model.live="selectedReports">
                    </td>
                    <td> {{-- Report Id --}}
                        #{{ $report->id }}
                    </td>
                    <td> {{-- User Details --}}
                        <div class="d-flex align-items-center">
                            {{-- Avatar --}}
                            <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center flex-shrink-0"
                                style="height: 30px; width: 30px;">
                                {{ strtoupper(substr($report->storedItems->first()?->storageApplication->applicant->name ?? 'NA', 0, 2)) }}
                            </div>

                            {{-- User Info --}}
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark" style="white-space: nowrap;">
                                    {{ $report->storedItems->first()?->storageApplication->applicant->name ?? 'N/A' }}
                                </span>
                                <small class="text-muted" style="white-space: nowrap;">
                                    ID: {{ $report->storedItems->first()?->storageApplication->applicant->id }} •
                                    {{ $report->storedItems->first()?->storageApplication->applicant->matric_no }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td> {{-- Contact --}}
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center">
                                <span class="text-muted small fw-medium" style="min-width: 50px;">
                                    Email:
                                </span>
                                <span class="text-dark" style="white-space: nowrap;">
                                    {{ $report->storedItems->first()?->storageApplication->applicant->email ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-muted small fw-medium" style="min-width: 50px;">Phone:</span>
                                <span class="text-dark" style="white-space: nowrap;">
                                    {{ $report->storedItems->first()?->storageApplication->applicant->phone_number ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td> {{-- Missing Item --}}
                        <div>
                            <span class="badge bg-primary">
                                {{ $report->storedItems->count() }}
                                {{ Str::plural('Item', $report->storedItems->count()) }}
                            </span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($report->storedItems as $item)
                                <div wire:key="item-{{ $item->id }}"
                                    class="border-start border-3 border-primary ps-3">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark">
                                                {{ ucfirst($item->item_name) }}
                                            </div>
                                            <span class="badge bg-light text-secondary border">
                                                <i class="bi bi-tag-fill me-1"></i>
                                                {{ ucfirst($item->item_type) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td> {{-- Storage Details --}}
                        <div class="d-flex flex-column">
                            {{-- ID Badge --}}
                            <div>
                                <span class="badge bg-secondary">
                                    ID: {{ $report->storedItems->first()?->storageApplication->id }}
                                </span>
                            </div>

                            {{-- Storage Location Info --}}
                            <div class="d-flex flex-column">
                                @if (
                                    !empty($report->storedItems->first()?->storageApplication->open_area_id) &&
                                        !empty($report->storedItems->first()?->storageApplication->locker_id))
                                    <div class="fw-semibold text-dark">
                                        {{ $report->storedItems->first()?->storageApplication->openArea?->storageRoom?->room_name }}
                                    </div>
                                    <span class="d-inline-flex align-items-center gap-1 text-primary">
                                        <svg class="bi" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                        </svg>
                                        <small>Locker + Open Area</small>
                                    </span>
                                @elseif (!empty($report->storedItems->first()?->storageApplication->locker_id))
                                    <div class="fw-semibold text-dark">
                                        {{ $report->storedItems->first()?->storageApplication->locker?->storageRoom?->room_name }}
                                    </div>
                                    <span class="d-inline-flex align-items-center gap-1 text-info">
                                        <svg class="bi" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                        </svg>
                                        <small>Locker</small>
                                    </span>
                                @elseif (!empty($report->storedItems->first()?->storageApplication->open_area_id))
                                    <div class="fw-semibold text-dark">
                                        {{ $report->storedItems->first()?->storageApplication->openArea?->storageRoom?->room_name }}
                                    </div>
                                    <span class="d-inline-flex align-items-center gap-1 text-success">
                                        <svg class="bi" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm5 10V4h6v8H5z" />
                                        </svg>
                                        <small>Open Area</small>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">{{-- Report Date --}}
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-medium" style="white-space: nowrap;">
                                {{ $report->created_at->format('M d, Y') }}
                            </span>
                            <small class="text-muted">
                                {{ $report->created_at->format('h:i A') }}
                            </small>
                        </div>
                    </td>
                    <td> {{-- Status --}}
                        <span @class([
                            'badge',
                            'text-bg-warning' => $report->status === 'pending',
                            'text-bg-info' => $report->status === 'investigating',
                            'text-bg-success' => $report->status === 'found',
                            'text-bg-danger' => $report->status === 'lost',
                            'text-bg-secondary' => $report->status === 'dismissed',
                        ])>
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="d-flex align-items-center">

                            {{-- 1. View Details (Existing) --}}
                            <button wire:click="$set('modalData', {{ $report->id }}); $dispatch('display_modal');"
                                title="View Details" class="btn btn-sm btn-outline-secondary border-0"
                                style="padding: 0 2px" type="button">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            {{-- 2. Update Status (Edit) --}}
                            {{-- Opens a different modal to change status or add admin notes --}}
                            <button wire:click="$set('modalData2', {{ $report->id }}); $dispatch('display_modal2');"
                                title="Update Status" class="btn btn-sm btn-outline-primary border-0"
                                style="padding: 0 2px" type="button">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- 3. Contact Student (Mailto Link) --}}
                            {{-- Uses the relationship to get email directly --}}
                            <a href="mailto:{{ $report->storedItems->first()?->storageApplication->applicant->email }}"
                                title="Email Student" class="btn btn-sm btn-outline-info border-0"
                                style="padding: 0 2px" type="button">
                                <i class="bi bi-envelope-fill"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-5 text-center">
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
</div>
