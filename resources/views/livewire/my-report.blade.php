<div class="container">
    {{-- In work, do what you enjoy. --}}
    <div class="mb-4">
        <h1 class="display-5 fw-bold text-dark">My Reports</h1>
        <p class="text-muted">View and track your missing item reports</p>
    </div>

    @forelse ($this->studentStorageApplication->qrCode->missingReports as $report)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title fw-semibold">Report #{{ $report->id }}</h5>
                        <p class="text-muted small mb-0">
                            Reported on {{ $report->created_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div>
                        @if ($report->status === 'resolved')
                            <span class="badge bg-success">Resolved</span>
                        @elseif($report->status === 'investigating')
                            <span class="badge bg-info text-dark">Investigating</span>
                        @elseif($report->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($report->status === 'closed')
                            <span class="badge bg-secondary">Closed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($report->status) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Report Details --}}
                <div class="row g-3 mt-2">
                    @foreach ($report->storedItems as $item)
                        <div class="col-md-6">
                            <p class="text-muted small mb-1 fw-semibold">Item Name</p>
                            <p class="mb-0">{{ $item->item_name }}</p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted small mb-1 fw-semibold">Item Category</p>
                            <p class="mb-0">{{ ucfirst($item->item_type) }}</p>
                        </div>

                        @if ($item->item_description)
                            <div class="col-md-6">
                                <p class="text-muted small mb-1 fw-semibold">Item Description</p>
                                <p class="mb-0">{{ ucfirst($item->item_description) }}</p>
                            </div>
                        @endif
                        {{-- Uploaded Images/Documents --}}
                        @if ($item->item_photo_path)
                            <div class="mt-3">
                                <p class="text-muted small mb-2 fw-semibold">Photo Item</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ asset('storage/' . $item->item_photo_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-paperclip"></i> View Photo
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Last Seen Date</p>
                        <p class="mb-0">
                            @if ($report->last_seen_date)
                                {{ $report->last_seen_date->format('M d, Y') }}
                            @elseif ($report->last_seen_range)
                                @php
                                    $range_arr = explode('_', $report->last_seen_range);
                                    $last_seen_range = implode(' ', $range_arr);
                                @endphp
                                {{ ucfirst($last_seen_range) }}
                            @else
                                @php
                                    $type_arr = explode('_', $report->last_seen_type);
                                    $last_seen_type = implode(' ', $type_arr);
                                @endphp
                                {{ ucwords($last_seen_type) }}
                            @endif
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Last Seen Location</p>
                        <p class="mb-0">{{ $report->last_seen_location ?? 'N/A' }}</p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Date Discover</p>
                        <p class="mb-0">
                            @if ($report->discover_missing_date)
                                {{ $report->discover_missing_date->format('M d, Y') }}
                            @elseif ($report->discovered_missing_range)
                                @php
                                    $range_arr2 = explode('_', $report->discovered_missing_range);
                                    $discovered_missing_range = implode(' ', $range_arr2);
                                @endphp

                                {{ ucfirst($discovered_missing_range) }}
                            @else
                                @php
                                    $type_arr2 = explode('_', $report->discovered_missing_type);
                                    $discovered_missing_type = implode(' ', $type_arr2);
                                @endphp
                                {{ ucwords($discovered_missing_type) }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Additional Details --}}
                @if ($report->witnesses_and_info)
                    <div class="mt-3">
                        <p class="text-muted small mb-1 fw-semibold">Additional Details</p>
                        <p class="mb-0">{{ $report->witnesses_and_info ?? 'N/A' }}</p>
                    </div>
                @endif

                {{-- Resolution Details --}}
                @if ($report->status === 'resolved' && ($report->resolution_details || $report->found_location))
                    <div class="mt-3 p-3 bg-success bg-opacity-10 rounded border-start border-success border-4">
                        <p class="text-success small mb-1 fw-semibold">
                            <i class="bi bi-check-circle"></i> Item Found
                        </p>
                        @if ($report->found_location)
                            <p class="mb-1"><strong>Found at:</strong> {{ $report->found_location }}</p>
                        @endif
                        @if ($report->resolution_details)
                            <p class="mb-0">{{ $report->resolution_details }}</p>
                        @endif
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-4 d-flex gap-2">
                    <button wire:click="viewDetails({{ $report->id }})" class="btn btn-primary">
                        <i class="bi bi-eye"></i> View Full Details
                    </button>

                    @if ($report->status === 'pending' || $report->status === 'investigating')
                        <button wire:click="updateReport({{ $report->id }})" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Update Report
                        </button>
                    @endif

                    @if ($report->status === 'pending')
                        <button wire:click="cancelReport({{ $report->id }})"
                            wire:confirm="Are you sure you want to cancel this report?" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Cancel Report
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <svg class="text-muted mb-3" width="64" height="64" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <h5 class="card-title">No Reports Found</h5>
                <p class="text-muted">You haven't submitted any missing item reports yet.</p>
                <div class="mt-3">
                    <a href="{{ route('missing_report') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Submit New Report
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
