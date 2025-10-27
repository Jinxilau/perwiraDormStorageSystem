<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    @include('livewire.includes.header2', [
        'title' => 'QR Code Scan Logs',
        'subtitle' => 'View and monitor all QR code scan activities in the system.',
    ])

    <div class="overflow-hidden container-fluid">
        <x-card-matrix :items="[
            ['title' => $this->todayScans, 'subtitle' => 'Logs Today'],
            ['title' => $this->logs->count(), 'subtitle' => 'Total Logs'],
            ['title' => $this->logs->where('event_type', 'check_in')->count(), 'subtitle' => 'Total Check Ins'],
            ['title' => $this->logs->where('event_type', 'check_out')->count(), 'subtitle' => 'Total Check Outs'],
        ]" />

        {{-- Filter Options --}}
        <div class="bg-light row border-bottom border-top border-dark mb-3">
            {{-- Search --}}
            <div class="rounded p-3 d-flex gap-2 align-items-center">
                <div class="flex-grow-1 flex-shrink-1 position-relative">
                    <i class="bi bi-search fs-5 position-absolute"
                        style="left: 16px; top: 50%;transform: translateY(-50%);"></i>
                    <input wire:model.live="search" type="text" class="w-100" id="searchInput"
                        style="padding: 14px 16px 14px 48px; border: 2px solid #e5e7eb; border-radius: 8px;"
                        placeholder="Search applications by applicant name, matric no, or QR ID...">
                </div>
                <button type="button" class="btn btn-primary fw-bold">Search</button>
            </div>
            {{-- Dropdowns --}}
            <div class="border-rounded p-3 shadow-sm">
                <div class="row row-cols-3 mb-3">

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Check In/Check Out
                        </label>
                        <select class="form-select mb-3" wire:model.live="role">
                            <option value="">All Type</option>
                            <option value="check_in">Check In</option>
                            <option value="check_out">Check Out</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Storage Room
                        </label>
                        <select class="form-select mb-3" wire:model.live="storage_room">
                            <option value="">All Room</option>
                            @foreach ($this->rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            From Application Date Range
                        </label>
                        <input type="date" wire:model.live="fromRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            To Application Date Range
                        </label>
                        <input type="date" wire:model.live="toRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By Application Date
                        </label>
                        <select class="form-select mb-3 " wire:model.live="sortDate">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By User Name
                        </label>
                        <select class="form-select mb-3" wire:model.live="sortName">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Application Per Page <span class="text-muted small">(Pagination)</span>
                        </label>
                        <select class="form-select mb-3" wire:model.live="paginationInt">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-3 pt-2 border-top">
                    <button class="btn btn-danger fw-bold" wire:click.prevent="clearFilters"
                        @click="allCol = true; toggleAll()">Clear All</button>
                </div>
            </div>

        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="align-middle py-2">
                            QR ID
                        </th>
                        <th class="align-middle py-2">
                            Active Storage
                        </th>
                        <th class="align-middle py-2">
                            Student Information
                        </th>
                        <th class="align-middle py-2">
                            Items Counts
                        </th>
                        <th class="align-middle py-2">
                            Activty Type
                        </th>
                        <th class="align-middle py-2">
                            Timestamp
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider align-middle">
                    @forelse($this->logs as $log)
                        <tr wire:key="row-{{ $log->id }}">
                            <td>
                                #{{ $log->qrCode->id }} {{-- QR ID --}}
                            </td>
                            <td> {{-- Active Storage --}}
                                <div class="d-flex flex-column">
                                    {{-- ID Badge --}}
                                    <div>
                                        <span class="badge bg-secondary">
                                            ID: {{ $log->qrCode->storageApplication->id }}
                                        </span>
                                    </div>

                                    {{-- Storage Location Info --}}
                                    <div class="d-flex flex-column">
                                        @if (!empty($log->qrCode->storageApplication->open_area_id) && !empty($log->qrCode->storageApplication->locker_id))
                                            <div class="fw-semibold text-dark">
                                                {{ $log->qrCode->storageApplication->openArea?->storageRoom?->room_name }}
                                            </div>
                                            <span class="d-inline-flex align-items-center gap-1 text-primary">
                                                <svg class="bi" width="16" height="16" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                                </svg>
                                                <small>Locker + Open Area</small>
                                            </span>
                                        @elseif (!empty($log->qrCode->storageApplication->locker_id))
                                            <div class="fw-semibold text-dark">
                                                {{ $log->qrCode->storageApplication->locker?->storageRoom?->room_name }}
                                            </div>
                                            <span class="d-inline-flex align-items-center gap-1 text-info">
                                                <svg class="bi" width="16" height="16" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                                </svg>
                                                <small>Locker</small>
                                            </span>
                                        @elseif (!empty($log->qrCode->storageApplication->open_area_id))
                                            <div class="fw-semibold text-dark">
                                                {{ $log->qrCode->storageApplication->openArea?->storageRoom?->room_name }}
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
                            <td> {{-- Student Information --}}
                                <div class="d-flex items-center gap-1">
                                    <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                        style="height: 30px; width:30px">
                                        {{ substr($log->qrCode->storageApplication->applicant->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <div>
                                        <div>
                                            {{ $log->qrCode->storageApplication->applicant->name ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ $log->qrCode->storageApplication->applicant->email ?? 'N/A' }}
                                        </div>
                                        <div>
                                            ID: {{ $log->qrCode->storageApplication->applicant->matric_no ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td> {{-- Count Items --}}
                                @if ($log->qrCode->storageApplication->storedItems->count() > 0)
                                    <div class="d-flex flex-column">
                                        {{-- Items Count Badge --}}
                                        <div>
                                            <span class="badge bg-primary">
                                                {{ $log->qrCode->storageApplication->storedItems->count() }}
                                                {{ Str::plural('Item', $log->qrCode->storageApplication->storedItems->count()) }}
                                            </span>
                                        </div>

                                        {{-- Items List --}}
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($log->qrCode->storageApplication->storedItems as $item)
                                                <div wire:key="item-{{ $item->id }}"
                                                    class="border-start border-3 border-primary ps-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between gap-2">
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
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="text-muted small">No items stored</div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{-- Activty Type --}}
                                @php
                                    $arr = explode('_', $log->event_type);
                                    $event_type = implode(' ', $arr);
                                    $event_type = ucwords($event_type);
                                @endphp
                                <div>
                                    <span
                                        class="badge @if ($log->event_type === 'check_in') bg-warning text-dark @elseif($log->event_type === 'check_out') bg-success @endif">
                                        {{ $event_type }}
                                    </span>
                                </div>
                            </td>
                            <td> {{-- Timestamp --}}
                                <div style="white-space: nowrap">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div>
                                    {{ $log->created_at ? $log->created_at->format('h:i A') : '' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-5 text-center">
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
</div>
