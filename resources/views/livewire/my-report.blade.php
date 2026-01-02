<div class="container py-4">
    @include('livewire.includes.student-title2', [
        'title' => 'My Missing Items',
        'subtitle' => 'View and track your missing item reports',
    ])

    @if ($this->storageApplication)
        @php
            $items = $this->storageApplication->storedItems;
            $reports = $items->pluck('missingReport')->filter()->unique('id')->sortByDesc('created_at')->values();
        @endphp
        
        @forelse ($reports as $report)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- Header Section --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="card-title fw-bold mb-1">Report #{{ $report->id }}</h5>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-calendar3"></i>
                                {{ $report->created_at->format('M d, Y \a\t h:i A') }}
                            </p>
                        </div>
                        @php
                            $statusMap = [
                                'pending' => ['class' => 'warning', 'icon' => 'clock-history'],
                                'investigating' => ['class' => 'info', 'icon' => 'search'],
                                'found' => ['class' => 'success', 'icon' => 'check-circle'],
                                'lost' => ['class' => 'secondary', 'icon' => 'x-circle'],
                                'dismissed' => ['class' => 'dark', 'icon' => 'slash-circle'],
                            ];
                            $status = $statusMap[$report->status] ?? ['class' => 'secondary', 'icon' => 'circle'];
                        @endphp
                        <span class="badge bg-{{ $status['class'] }} px-3 py-2">
                            <i class="bi bi-{{ $status['icon'] }} me-1"></i>
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>

                    {{-- Storage Application Details --}}
                    @if ($this->storageApplication)
                        <div class="bg-light rounded p-3 mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-box-seam text-primary"></i> Storage Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                        <div>
                                            <small class="text-muted d-block">Open Area</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-lock text-muted me-2 mt-1"></i>
                                        <div>
                                            <small class="text-muted d-block">Locker</small>
                                            <strong>{{ $this->storageApplication->locker?->code ?? 'Not Assigned' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Missing Items Section --}}
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-card-list text-primary"></i> Missing Items
                    </h6>
                    
                    @foreach ($report->storedItems as $item)
                        <div class="border rounded p-3 mb-3 bg-white">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Item Name</small>
                                    <p class="mb-0 fw-semibold">{{ $item->item_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Category</small>
                                    <p class="mb-0">{{ ucfirst($item->item_type) }}</p>
                                </div>
                                
                                @if ($item->item_description)
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1">Description</small>
                                        <p class="mb-0">{{ $item->item_description }}</p>
                                    </div>
                                @endif
                                
                                @if ($item->item_photo_path)
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-2">Item Photo</small>
                                        <a href="{{ \Storage::disk('public')->url($item->item_photo_path) }}"
                                            target="_blank" 
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-image"></i> View Photo
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Report Timeline --}}
                    <h6 class="fw-semibold mt-4 mb-3">
                        <i class="bi bi-clock-history text-primary"></i> Timeline
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-3 border-info ps-3">
                                <small class="text-muted d-block mb-1">Last Seen</small>
                                @php
                                    function humanizeEnum($value) {
                                        return ucwords(str_replace('_', ' ', $value));
                                    }
                                @endphp
                                <p class="mb-1 fw-semibold">
                                    @if ($report->last_seen_date)
                                        {{ $report->last_seen_date->format('M d, Y') }}
                                    @elseif ($report->last_seen_range)
                                        {{ humanizeEnum($report->last_seen_range) }}
                                    @else
                                        {{ humanizeEnum($report->last_seen_type) }}
                                    @endif
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $report->last_seen_location ?? 'Location not specified' }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-3 border-warning ps-3">
                                <small class="text-muted d-block mb-1">Discovered Missing</small>
                                <p class="mb-0 fw-semibold">
                                    @if ($report->discovered_missing_date)
                                        {{ $report->discovered_missing_date->format('M d, Y') }}
                                    @elseif ($report->discovered_missing_range)
                                        {{ humanizeEnum($report->discovered_missing_range) }}
                                    @else
                                        {{ humanizeEnum($report->discovered_missing_type) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Details --}}
                    @if ($report->witnesses_and_info)
                        <div class="mt-4">
                            <h6 class="fw-semibold mb-2">
                                <i class="bi bi-info-circle text-primary"></i> Additional Information
                            </h6>
                            <div class="bg-light rounded p-3">
                                <p class="mb-0 text-dark">{{ $report->witnesses_and_info }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Status Messages --}}
                    @if ($report->status === 'found')
                        <div class="alert alert-success mt-4 border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Item Found!</h6>
                                    <p class="mb-0">Please contact the admin to retrieve your item.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($report->admin_note)
                        <div class="alert alert-warning mt-4 border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Admin Note</h6>
                                    <p class="mb-0">{{ $report->admin_note }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    @if ($report->status === 'pending')
                        <div class="mt-4 pt-3 border-top">
                            <button wire:click="cancelReport({{ $report->id }})"
                                wire:confirm="Are you sure you want to cancel this report?"
                                class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancel Report
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            {{-- No Reports State --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h5 class="card-title fw-bold">No Reports Found</h5>
                    <p class="text-muted mb-4">You haven't submitted any missing item reports yet.</p>
                    <a href="{{ route('missing_report') }}" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-2"></i>Submit New Report
                    </a>
                </div>
            </div>
        @endforelse
    @else
        {{-- No Storage State --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                </div>
                <h5 class="card-title fw-bold">No Active Storage</h5>
                <p class="text-muted mb-4">You currently have no active storage in Perwira.</p>
                <a href="{{ route('storage') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Apply for Storage
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .border-start {
        border-left-width: 3px !important;
    }
    
    .alert {
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>