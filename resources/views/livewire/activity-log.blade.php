<div>
    @include('livewire.includes.header2', [
        'title' => 'Activity Log',
        'subtitle' => 'Monitor user activities and actions within the system.',
    ])

    <div class="overflow-hidden container-fluid">
        <x-card-matrix :items="[
            [
                'title' => $this->todayLogs,
                'subtitle' => 'Activity Today',
                'icon' => 'bi-calendar-check-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->logs->count(),
                'subtitle' => 'Total Activity',
                'icon' => 'bi-collection-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->activeUsersToday,
                'subtitle' => 'Users (Today)',
                'icon' => 'bi-people-fill',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
            [
                'title' => $this->createCount,
                'subtitle' => 'Created',
                'icon' => 'bi-plus-lg',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->updateCount,
                'subtitle' => 'Updated',
                'icon' => 'bi-pencil',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->deleteCount,
                'subtitle' => 'Deleted',
                'icon' => 'bi-trash',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->approveCount,
                'subtitle' => 'Approved',
                'icon' => 'bi-check-lg',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
        ]" />

        {{-- Filter Options --}}
        <x-filter-options>
            {{-- Role --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    User Role
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="role">
                    <option value="">All Type</option>
                    <option value="1">Administrator</option>
                    <option value="2">Staff</option>
                    <option value="3">Student</option>
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

            {{-- Date Range --}}
            <div class="col-md-6 col-lg-5">
                <label class="form-label small text-muted fw-bold">Date Range</label>

                <div class="input-group">
                    {{-- Start Date --}}
                    <input type="date" wire:model.live.debounce.300ms="fromRange"
                        class="form-control bg-light border-0" placeholder="Start Date" aria-label="Start Date">

                    {{-- Separator --}}
                    <span class="input-group-text bg-light border-0 text-secondary">to</span>

                    {{-- End Date --}}
                    <input type="date" wire:model.live.debounce.300ms="toRange"
                        class="form-control bg-light border-0" placeholder="End Date" aria-label="End Date">
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

            {{-- Pagination --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Per Page</label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="paginationInt">
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
                    <td class="fw-bold align-middle" style="white-space: nowrap;">
                        <span class="text-secondary small user-select-none">#</span>
                        <span class="fw-bold text-dark">{{ $log->id }}</span>
                    </td>

                    {{-- 2. User Profile --}}
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex justify-content-center align-items-center text-uppercase"
                                    style="height: 30px; width: 30px;">
                                    {{ substr($log->user->name ?? 'NA', 0, 2) }}
                                </div>
                            </div>

                            <div>
                                <div class="fw-bold text-dark mb-0">
                                    {{ $log->user->name ?? 'N/A' }}
                                </div>

                                <div class="d-flex flex-wrap gap-1">
                                    <span
                                        class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-normal">
                                        {{ $log->user->matric_no ?? ($log->user->id ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- 3. Role --}}
                    <td class="align-middle">
                        @php
                            $roleColor = match (strtolower($log->user->userRole->role_name ?? '')) {
                                'admin', 'administrator' => 'bg-primary-subtle text-primary border-primary-subtle',
                                'staff' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                default => 'bg-light text-secondary border',
                            };
                        @endphp
                        <span class="badge {{ $roleColor }} border fw-normal">
                            {{ ucfirst($log->user->userRole->role_name ?? 'Guest') }}
                        </span>
                    </td>

                    {{-- 4. Action --}}
                    <td>
                        @php
                            // 1. Define Color & Icon logic
                            [$colorClass, $icon] = match ($log->action) {
                                'created', 'store' => ['success', 'bi-plus-lg'], // Green for positive add
                                'updated', 'edit' => ['primary', 'bi-pencil'], // Blue for neutral edit
                                'deleted', 'destroy' => ['danger', 'bi-trash'], // Red for destructive
                                'approved', 'approve' => ['info', 'bi-check-lg'], // Info/Teal for status
                                'rejected', 'reject' => ['warning', 'bi-x-circle'], // Orange for warning
                                'opened' => ['success', 'bi-door-open'],
                                'closed' => ['warning', 'bi-door-closed'],
                                default => ['secondary', 'bi-activity'], // Grey fallback
                            };
                        @endphp

                        {{-- 2. The Badge --}}
                        <span
                            class="badge rounded-pill bg-{{ $colorClass }}-subtle text-{{ $colorClass }} border border-{{ $colorClass }}-subtle px-3 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ $icon }}"></i>
                                <span class="text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </div>
                        </span>
                    </td>

                    {{-- 5. Description --}}
                    <td class="align-middle">
                        <div class="border rounded bg-light p-2 small text-secondary"
                            style="max-height: 120px; overflow-y: auto; min-width: 100px;">
                            @if (!empty($log->description))
                                <div class="lh-sm">
                                    {{ $log->description }}
                                </div>
                            @else
                                <span class="text-muted fst-italic opacity-50">N/A</span>
                            @endif
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
                    <td class="align-middle">
                        <div class="d-flex flex-column font-monospace">
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                {{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}
                            </span>
                            <span class="text-secondary small">
                                {{ $log->created_at ? $log->created_at->format('h:i A') : '-' }}
                            </span>
                            <span class="small text-muted">
                                <i class="bi bi-calendar-event me-1"></i> {{ $log->semester->academic_year }}
                                - {{ $log->semester->semester_no }}
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
