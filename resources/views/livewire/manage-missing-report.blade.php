<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    @include('livewire.includes.header2', [
        'title' => 'Missing Report Management',
        'subtitle' => 'View, verify, and take action on all submitted missing item reports.',
    ])

    <div class="container">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">
            <!-- Table -->
            <div class="table-responsive-xxl">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
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
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @forelse($this->missingReports as $report)
                            <tr wire:key="row-{{ $report->id }}">
                                <td> {{-- Report Id --}}
                                    <div style="white-space: nowrap;">#{{ $report->id }}</div>
                                </td>
                                <td> {{-- User Details --}}
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar --}}
                                        <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center flex-shrink-0"
                                            style="height: 30px; width: 30px;">
                                            {{ strtoupper(substr($report->storedItems[0]->storageApplication->applicant->name ?? 'NA', 0, 2)) }}
                                        </div>

                                        {{-- User Info --}}
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark" style="white-space: nowrap;">
                                                {{ $report->storedItems[0]->storageApplication->applicant->name ?? 'N/A' }}
                                            </span>
                                            <small class="text-muted" style="white-space: nowrap;">
                                                ID: {{ $report->storedItems[0]->storageApplication->applicant->id }} •
                                                {{ $report->storedItems[0]->storageApplication->applicant->matric_no }}
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
                                                {{ $report->storedItems[0]->storageApplication->applicant->email ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted small fw-medium"
                                                style="min-width: 50px;">Phone:</span>
                                            <span class="text-dark" style="white-space: nowrap;">
                                                {{ $report->storedItems[0]->storageApplication->applicant->phone_number ?? 'N/A' }}
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
                                                ID: {{ $report->storedItems[0]->storageApplication->id }}
                                            </span>
                                        </div>

                                        {{-- Storage Location Info --}}
                                        <div class="d-flex flex-column">
                                            @if (
                                                !empty($report->storedItems[0]->storageApplication->open_area_id) &&
                                                    !empty($report->storedItems[0]->storageApplication->locker_id))
                                                <div class="fw-semibold text-dark">
                                                    {{ $report->storedItems[0]->storageApplication->openArea?->storageRoom?->room_name }}
                                                </div>
                                                <span class="d-inline-flex align-items-center gap-1 text-primary">
                                                    <svg class="bi" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                                    </svg>
                                                    <small>Locker + Open Area</small>
                                                </span>
                                            @elseif (!empty($report->storedItems[0]->storageApplication->locker_id))
                                                <div class="fw-semibold text-dark">
                                                    {{ $report->storedItems[0]->storageApplication->locker?->storageRoom?->room_name }}
                                                </div>
                                                <span class="d-inline-flex align-items-center gap-1 text-info">
                                                    <svg class="bi" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                                    </svg>
                                                    <small>Locker</small>
                                                </span>
                                            @elseif (!empty($report->storedItems[0]->storageApplication->open_area_id))
                                                <div class="fw-semibold text-dark">
                                                    {{ $report->storedItems[0]->storageApplication->openArea?->storageRoom?->room_name }}
                                                </div>
                                                <span class="d-inline-flex align-items-center gap-1 text-success">
                                                    <svg class="bi" width="16" height="16"
                                                        fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm5 10V4h6v8H5z" />
                                                    </svg>
                                                    <small>Open Area</small>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{-- Report Date --}}
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
                                    <span
                                        class="badge {{ $report->status === 'pending' ? 'text-bg-warning' : ($report->status === 'resolved' ? 'text-bg-success' : ($report->status === 'investigating' ? 'text-bg-info' : 'text-bg-danger')) }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="align-middle"> {{-- Actions --}}
                                    <div class="d-flex align-items-center gap-2" style="white-space: nowrap">
                                        <button @click="$dispatch('', { })" title="View Details"
                                            class="btn btn-sm border border-secondary" style="padding: 0;"
                                            type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button wire:click="" class="text-success btn btn-sm border border-success"
                                            style="padding: 0;" title="Approve">
                                            <i class="bi bi-check"></i>
                                        </button>

                                        <button wire:click="" class="text-danger btn btn-sm border border-danger"
                                            style="padding: 0;" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
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
                    </tbody>
                </table>
                {{ $this->missingReports->links('vendor.livewire.bootstrap') }}
            </div>
        </div>

    </div>
</div>
