<div>
    @include('livewire.includes.header2', [
        'title' => 'QR Code Scan Logs',
        'subtitle' => 'View and monitor all QR code scan activities in the system.',
    ])

    <div class="overflow-hidden container-fluid">
        <x-card-matrix :items="[
            ['title' => $this->todayLogs, 'subtitle' => 'Activity Today'],
            ['title' => $this->logs->count(), 'subtitle' => 'Total Activity']
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
                            IP Address
                        </th>
                        <th class="align-middle py-2">
                            Timestamp
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider align-middle">
                    @forelse($this->logs as $log)
                        <tr wire:key="row-{{ $log->id }}">
                            <td class="align-middle">
                                #{{ $log->qrCode->id }}
                            </td>
                            <td> {{-- User --}}
                                <div class="d-flex items-center gap-1">
                                    <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                        style="height: 30px; width:30px">
                                        {{ substr($log->user->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <div>
                                        <div>
                                            {{ $log->user->name ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ $log->user->email ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ $log->user->phone_number ?? 'N/A' }}
                                        </div>
                                        <div>
                                            ID: {{ $log->user->matric_no ?? $log->user->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge rounded-pill bg-info-subtle text-info">
                                    {{ $log->user->userRole->role_name }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $log->description }}</span>
                            </td>
                            <td class="align-middle">
                                <code class="text-muted small">{{ $log->ip_address }}</code>
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
                                    <h3 class="mt-4">No Activity Logs</h3>
                                    <p class="mt-2">There are no activity logs to view.</p>
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
