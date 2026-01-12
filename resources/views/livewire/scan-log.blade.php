<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    @include('livewire.includes.header2', [
        'title' => 'QR Code Scan Logs',
        'subtitle' => 'View and monitor all QR code scan activities in the system.',
    ])

    <div class="overflow-hidden container-fluid">
        {{-- Scan Logs Metrics --}}
        <x-card-matrix :items="[
            [
                'title' => $this->totalLogs,
                'subtitle' => 'Total Logs (Current Semester)',
                'icon' => 'bi-journal-text',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->todayScans,
                'subtitle' => 'Logs Today',
                'icon' => 'bi-calendar-day-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->logs->where('event_type', 'check_in')->count(),
                'subtitle' => 'Total Check Ins',
                'icon' => 'bi-box-arrow-in-down-right',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->logs->where('event_type', 'check_out')->count(),
                'subtitle' => 'Total Check Outs',
                'icon' => 'bi-box-arrow-up-right',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
        ]" />

        <x-filter-options>
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Check In/Check Out</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="event_type">
                    <option value="">All Type</option>
                    <option value="check_in">Check In</option>
                    <option value="check_out">Check Out</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Storage Room</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="storage_room">
                    <option value="">All Room</option>
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

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Scan Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortDate">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Sort By User Name</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortName">
                    <option value="">Default (No Sorting)</option>
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
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

        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="align-middle py-2">
                            Student ID
                        </th>
                        <th class="align-middle py-2">
                            Storage Details
                        </th>
                        <th class="align-middle py-2">
                            Student Information
                        </th>
                        <th class="align-middle py-2">
                            Items
                        </th>
                        <th class="align-middle py-2">
                            Activity Type
                        </th>
                        <th class="align-middle py-2">
                            Timestamp
                        </th>
                        <th class="align-middle py-2 text-center">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider align-middle">
                    @forelse($this->logs as $log)
                        <tr wire:key="row-{{ $log->id }}">
                            @php
                                $applicant = $log->qrCode->storageApplication->applicant;
                                $application = $log->qrCode->storageApplication;
                            @endphp
                            <td>
                                <span class="text-secondary small user-select-none">#</span>
                                <span class="fw-bold text-dark">{{ $applicant->id }}</span> {{-- Student ID --}}
                            </td>
                            <td> {{-- Active Storage --}}
                                <div class="d-flex flex-column align-items-center">
                                    {{-- ID Badge --}}
                                    <div>
                                        <span class="badge bg-secondary">
                                            Application ID: {{ $application->id }}
                                        </span>
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
                                                    <svg class="bi" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
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
                                                    <svg class="bi" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
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
                            <td class="align-middle"> {{-- Student Information --}}
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

                                        <div class="small text-muted mb-1">
                                            {{ $applicant->email ?? 'N/A' }}
                                            <span class="mx-1">&bull;</span>
                                            {{ $applicant->phone_number ?? 'N/A' }}
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
                            <td class="align-middle" style="min-width: 150px;"> {{-- Set min-width to prevent squishing --}}
                                @php
                                    $items = $log->qrCode->storageApplication->storedItems;
                                    $count = $items->count();
                                @endphp

                                @if ($count > 0)
                                    <div x-data="{ open: false }">

                                        {{-- Header: Badge + Tiny Toggle Link --}}
                                        <div class="d-flex align-items-center justify-content-center">
                                            <button @click="open = !open"
                                                class="btn badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                                {{ $count }} {{ Str::plural('Item', $count) }}
                                                <i class="bi"
                                                    :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
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
                                                                style="max-width: 100px;"
                                                                title="{{ $item->item_name }}">
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
                            <td class="align-middle">
                                {{-- Activty Type --}}
                                @php
                                    $arr = explode('_', $log->event_type);
                                    $event_type = implode(' ', $arr);
                                    $event_type = ucwords($event_type);
                                @endphp
                                <div class="text-center">
                                    <span
                                        class="badge @if ($log->event_type === 'check_in') bg-warning text-dark @elseif($log->event_type === 'check_out') bg-success @endif">
                                        {{ $event_type }}
                                    </span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column font-monospace">
                                    <span class="text-dark fw-bold" style="font-size: 0.9rem;">
                                        {{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                    <span class="text-secondary small">
                                        {{ $log->created_at ? $log->created_at->format('h:i:s A') : '-' }}
                                    </span>
                                    <span class="small text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $log->semester->academic_year }}
                                        - {{ $log->semester->semester_no }}
                                    </span>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center gap-1">

                                    {{-- View Storage Application --}}
                                    <a href="{{ route('storage_application_detail', $application->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="View Storage Application"
                                        style="padding: 0 2px;">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    {{-- View QR History --}}
                                    <button
                                        wire:click="$set('modalData', {{ $log->qr_code_id }}); $dispatch('display_modal');"
                                        class="btn btn-sm btn-outline-primary" title="View QR Scan History"
                                        style="padding: 0 2px;">
                                        <i class="bi bi-clock-history"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="20" class="px-3 py-5 text-center">
                                <div>
                                    <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                                    <h3 class="mt-4">No Scan Logs</h3>
                                    <p class="mt-2">There are no scan logs to view.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $this->logs->links('vendor.livewire.bootstrap') }}
        </div>
    </div>

    <x-custom-modal>
        <x-slot:title>
            QR Scan History
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="modalData" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>


        <table wire:loading.remove="modalData" class="table table-sm">
            @if ($this->qrHistoryLogs)
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->qrHistoryLogs as $history)
                        <tr>
                            <td>
                                <span
                                    class="badge
                                            {{ $history->event_type === 'check_in' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucwords(str_replace('_', ' ', $history->event_type)) }}
                                </span>
                            </td>
                            <td>{{ $history->created_at->format('M d, Y') }}</td>
                            <td>{{ $history->created_at->format('h:i A') }}</td>
                            <td>
                                {{ $history->semester->academic_year }}
                                - {{ $history->semester->semester_no }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                No scan history found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            @endif
        </table>
    </x-custom-modal>
</div>
