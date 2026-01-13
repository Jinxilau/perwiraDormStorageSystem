<div>
    <style>
        .view-all-btn {
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .view-all-btn:hover {
            transform: translateX(3px);
        }
    </style>

    @php
        $id = $report->id;
    @endphp

    @include('livewire.includes.header2', [
        'title' => 'Missing Report Details',
        'subtitle' => "Report #{$id} Details",
    ])

    @php
        $dashboard = Auth::user()->role_id === 1 ? 'admin_dashboard' : 'staff_dashboard';
    @endphp
    <div class="container-fluid mb-0 d-flex justify-content-between">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 ms-2">
                <li class="breadcrumb-item"><a href="{{ route($dashboard) }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage_missing') }}">Missing Reports</a></li>
                <li class="breadcrumb-item active">Report #{{ $report->id }}</li>
            </ol>
        </nav>
        <div>
            <button wire:click="exportPdf" class="btn btn-danger">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </button>
            <button class="btn btn-primary" wire:click="openStatusModal">
                <i class="bi bi-pencil"></i> Update Status
            </button>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-6">
                <!-- Report Status Card -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Report Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Current Status</label>
                                <div class="mt-1">
                                    <span class="badge bg-{{ $this->statusColor }} fs-6 text-capitalize">
                                        {{ $report->status }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Semester</label>
                                <p class="mb-0 fw-medium">
                                    {{ $report->semester ? $report->semester->academic_year . ' - Semester ' . $report->semester->semester_no : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Report Submitted</label>
                                <p class="mb-0">{{ $report->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Last Updated</label>
                                <p class="mb-0">{{ $report->updated_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Seen Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-eye text-primary me-2"></i>Last Seen Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">When Last Seen</label>
                                <p class="mb-0 fw-medium">{{ $this->lastSeenText }}</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small">Last Seen Location</label>
                                <p class="mb-0">{{ $report->last_seen_location }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discovery Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-search text-warning me-2"></i>Discovery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="text-muted small">When Discovered Missing</label>
                                <p class="mb-0 fw-medium">{{ $this->discoveredMissingText }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Additional Information</h5>
                    </div>
                    <div class="card-body">
                        <label class="text-muted small">Witnesses & Additional Info</label>
                        @if ($report->witnesses_and_info)
                            <p class="mb-0">{{ $report->witnesses_and_info }}</p>
                        @else
                            <p class="text-muted mb-0 fst-italic">No additional information provided</p>
                        @endif
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-clipboard-check text-success me-2"></i>Admin Notes</h5>
                    </div>
                    <div class="card-body">
                        @if ($report->admin_note)
                            <p class="mb-0">{{ $report->admin_note }}</p>
                        @else
                            <p class="text-muted mb-0 fst-italic">No admin notes yet</p>
                        @endif
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" wire:click="openNoteModal">
                                <i class="bi bi-plus-circle"></i> Add/Edit Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-6">
                <!-- Related Items Card -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Related Items</h5>
                    </div>
                    <div class="card-body">
                        @if ($report->storedItems && $report->storedItems->count() > 0)
                            @foreach ($report->storedItems as $item)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        @if ($item->item_photo_path)
                                            <img src="{{ Storage::disk('public')->url($item->item_photo_path) }}"
                                                alt="{{ $item->item_name }}" class="rounded me-3"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-seam fs-4 text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $item->item_name }}</h6>
                                            <p class="text-muted small mb-1">
                                                <span class="badge bg-secondary">{{ ucfirst($item->item_type) }}</span>
                                                <span class="badge bg-info">{{ ucfirst($item->estimated_size) }}</span>
                                            </p>
                                            @if ($item->item_description)
                                                <p class="small mb-0">{{ Str::limit($item->item_description, 60) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center mb-0">No items linked to this report</p>
                        @endif
                    </div>
                </div>

                <!-- Storage Allocation -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-dark bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-box text-warning"></i> Storage Allocation</h5>
                            <a href="{{ route('storage_application_detail', $application->id) }}"
                                class="view-all-btn">
                                View Storage Application <i class="bi bi-arrow-right ms-1"></i>
                            </a>

                        </div>
                    </div>
                    <div class="card-body">
                        @if ($application->locker_id)
                            {{-- Locker --}}
                            <div class="alert alert-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="alert-heading"><i class="bi bi-lock"></i> Locker Assigned</h6>
                                    <span
                                        class="badge {{ match (true) {
                                            // If the application is approved, show as success regardless of locker state
                                            $this->application->storage_application_status === 'approved' => 'bg-success',
                                        
                                            $application->locker->status === 'available' => 'bg-success',
                                            $application->locker->status === 'occupied' => 'bg-danger',
                                            $application->locker->status === 'reserved' => 'bg-warning text-dark',
                                            default => 'bg-secondary',
                                        } }}">
                                        {{ $this->application->locker->status === 'reserved' &&
                                        $this->application->storage_application_status === 'approved'
                                            ? 'Allocated'
                                            : ucfirst($this->application->locker->status) }}
                                    </span>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Locker Code</small>
                                        <p class="mb-0 fw-bold">{{ $application->locker->code }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Size</small>
                                        <p class="mb-0 fw-bold">{{ $application->locker->size }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Room</small>
                                        <p class="mb-0 fw-bold">
                                            {{ $application->storageRoom->room_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->open_area_id)
                            {{-- Open Area --}}
                            <div class="alert alert-success">
                                <h6 class="alert-heading"><i class="bi bi-grid-3x3"></i> Open Area Assigned</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Area ID</small>
                                        <p class="mb-0 fw-bold">#{{ $application->openArea->id ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Room</small>
                                        <p class="mb-0 fw-bold">
                                            {{ $application->storageRoom->room_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- No Storage Allocation --}}
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle"></i> No storage allocation yet
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Quick Actions Card -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" wire:click="quickUpdateStatus('found')"
                                wire:confirm="Are you sure you want to mark this report as Found?">
                                <i class="bi bi-check-circle"></i> Mark as Found
                            </button>
                            <button class="btn btn-danger" wire:click="quickUpdateStatus('lost')"
                                wire:confirm="Are you sure you want to mark this report as Lost?">
                                <i class="bi bi-x-circle"></i> Mark as Lost
                            </button>
                            <button class="btn btn-info" wire:click="quickUpdateStatus('investigating')"
                                wire:confirm="Are you sure you want to start investigation?">
                                <i class="bi bi-search"></i> Start Investigation
                            </button>
                            <button class="btn btn-secondary" wire:click="quickUpdateStatus('dismissed')"
                                wire:confirm="Are you sure you want to dismiss this report?">
                                <i class="bi bi-trash"></i> Dismiss Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        @if ($showStatusModal)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Report Status</h5>
                            <button type="button" class="btn-close" wire:click="closeStatusModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="pending">Pending</option>
                                    <option value="investigating">Investigating</option>
                                    <option value="found">Found</option>
                                    <option value="lost">Lost</option>
                                    <option value="dismissed">Dismissed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeStatusModal">Cancel</button>
                            <button type="button" class="btn btn-primary" wire:click="updateStatus">Update
                                Status</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add/Edit Note Modal -->
        @if ($showNoteModal)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Admin Note</h5>
                            <button type="button" class="btn-close" wire:click="closeNoteModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea wire:model="adminNote" class="form-control @error('adminNote') is-invalid @enderror" rows="5"
                                    placeholder="Add investigation notes, findings, or any relevant information..."></textarea>
                                @error('adminNote')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeNoteModal">Cancel</button>
                            <button type="button" class="btn btn-primary" wire:click="updateNote">Save Note</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- LOADING --}}
    <div wire:loading wire:target="openStatusModal, openNoteModal, updateStatus, quickUpdateStatus, updateNote">
        <div
            class="position-absolute top-50 start-50 translate-middle z-3 w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="bg-white p-4 rounded shadow-lg text-center border">
                <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="fw-bold text-muted">Processing...</div>
            </div>
        </div>
    </div>
</div>
