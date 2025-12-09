<div>
    @include('livewire.includes.header2', [
        'title' => 'QR Code Scan Logs',
        'subtitle' => 'View and monitor all QR code scan activities in the system.',
    ])

    <div class="overflow-hidden container-fluid">
        <x-card-matrix :items="[
            ['title' => $this->todayLogs, 'subtitle' => 'Activity Today'],
            ['title' => $this->logs->count(), 'subtitle' => 'Total Activity'],
        ]" />

        {{-- Filter Options --}}
        <x-filter-options>
            {{-- Role --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    User Role
                </label>
                <select class="form-select bg-light border-0" wire:model.live="role">
                    <option value="">All Type</option>
                    <option value="1">Administrator</option>
                    <option value="2">Staff</option>
                    <option value="3">Student</option>
                </select>
            </div>
            
            {{-- Date Range --}}
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

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Sort: Name</label>
                <select class="form-select bg-light border-0" wire:model="sortName">
                    <option value="asc">A-Z</option>
                    <option value="desc">Z-A</option>
                </select>
            </div>

            {{-- Pagination --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Per Page</label>
                <select class="form-select bg-light border-0" wire:model.live="paginationInt">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </x-filter-options>

        <!-- Table -->
        <x-table>
            <x-slot:header>
                <th class="align-middle py-2">
                    ID
                </th>
                <th class="align-middle py-2">
                    User
                </th>
                <th class="align-middle py-2">
                    Role
                </th>
                <th class="align-middle py-2">
                    Action
                </th>
                <th class="align-middle py-2">
                    Description
                </th>
                <th class="align-middle py-2">
                    Target
                </th>
                <th class="align-middle py-2">
                    Timestamp
                </th>
            </x-slot:header>

            @forelse($this->logs as $log)
                <tr wire:key="row-{{ $log->id }}" class="align-middle">

                    {{-- 1. ID --}}
                    <td class="text-secondary small fw-bold">
                        #{{ $log->id }}
                    </td>

                    {{-- 2. User Profile (Condensed) --}}
                    <td>
                        <div class="d-flex align-items-center">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 bg-primary-subtle text-primary fw-bold rounded-circle d-flex justify-content-center align-items-center border border-primary-subtle"
                                style="width: 40px; height: 40px; font-size: 0.9rem;">
                                {{ substr($log->user->name ?? '?', 0, 2) }}
                            </div>

                            {{-- Text Info --}}
                            <div class="ms-3">
                                <div class="fw-bold text-dark mb-0 text-truncate" style="max-width: 150px;">
                                    {{ $log->user->name ?? 'Unknown User' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $log->user->email ?? 'N/A' }}
                                    <span class="mx-1">&bull;</span>
                                    <span
                                        class="text-uppercase">{{ $log->user->matric_no ?? ($log->user->id ?? '--') }}</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- 3. Role --}}
                    <td>
                        <span class="badge rounded-pill bg-light text-dark border fw-normal">
                            {{ ucfirst($log->user->userRole->role_name ?? 'Guest') }}
                        </span>
                    </td>

                    {{-- 4. Action --}}
                    <td>
                        {{-- Dynamic Color Logic (Inline or move to helper) --}}
                        @php
                            $badgeClass = match ($log->action) {
                                'created', 'store' => 'text-bg-info',
                                'updated', 'edit' => 'text-bg-primary',
                                'deleted', 'destroy' => 'text-bg-danger',
                                'approved', 'approve' => 'text-bg-success',
                                'rejected', 'reject' => 'text-bg-warning',
                                default => 'text-bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} bg-opacity-75 text-uppercase"
                            style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            {{ $log->action }}
                        </span>
                    </td>

                    {{-- 5. Description --}}
                    <td>
                        <div class="text-dark small text-wrap" style="min-width: 200px;">
                            {{ $log->description }}
                        </div>
                        {{-- IP as subtitle --}}
                        <div class="small text-muted font-monospace mt-1">
                            IP: {{ $log->ip_address }}
                        </div>
                    </td>

                    {{-- 6. Target / Subject --}}
                    <td>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-database me-1"></i>
                            <span>
                                {{-- Converts 'App\Models\MissingReport' to 'MissingReport' --}}
                                {{ class_basename($log->subject_type) }}
                                <span class="fw-bold text-dark">#{{ $log->subject_id }}</span>
                            </span>
                        </div>
                    </td>

                    {{-- 7. Timestamp --}}
                    <td class="text-end">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark small">
                                {{ $log->created_at ? $log->created_at->format('M d, Y') : '-' }}
                            </span>
                            <span class="text-muted small">
                                {{ $log->created_at ? $log->created_at->format('h:i A') : '-' }}
                            </span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Activity Logs</h3>
                            <p class="mt-2">There are no activity logs to view.</p>
                        </div>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $this->logs->links('vendor.livewire.bootstrap') }}
            </x-slot:pagination>
        </x-table>

    </div>
</div>
