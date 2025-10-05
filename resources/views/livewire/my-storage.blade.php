<div class="container">
    {{-- The best athlete wants his opponent at his best. --}}

    <div class="mb-4">
        <h1 class="display-5 fw-bold text-dark">My Storage</h1>
        <p class="text-muted">View and track your storage application requests</p>
    </div>

    @forelse ($this->studentStorageApplications as $application)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title fw-semibold">Application #{{ $application->id }}</h5>
                        <p class="text-muted small mb-0">
                            Submitted on {{ $application->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div>
                        @if ($application->storage_application_status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($application->storage_application_status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($application->storage_application_status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span
                                class="badge bg-secondary">{{ ucfirst($application->storage_application_status) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Application Details --}}
                <div class="row g-3 mt-2 mb-3">
                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Storage Period</p>
                        <p class="mb-0">
                            {{ $application->start_date ? $application->start_date->format('M d, Y') : 'N/A' }} -
                            {{ $application->end_date ? $application->end_date->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Storage Type</p>
                        @if ($application->locker_id && $application->open_area_id)
                            <p class="mb-0">Locker + Open Area</p>
                        @elseif ($application->locker_id)
                            <p class="mb-0">Locker</p>
                        @elseif ($application->open_area_id)
                            <p class="mb-0">Open Area</p>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Number of Items</p>
                        <p class="mb-0">{{ count($application->storedItems) }}</p>
                    </div>

                    <div class="col-md-6">
                        <p class="text-muted small mb-1 fw-semibold">Storage Location</p>
                        <p class="mb-0">{{ $application->storage_room->room_name }}</p>
                    </div>

                    @if ($application->locker_id)
                        <div class="col-md-6">
                            <p class="text-muted small mb-1 fw-semibold">Locker</p>
                            <p class="mb-0">{{ $application->locker_id }}</p>
                        </div>
                    @endif
                </div>

                {{-- QR Code --}}
                @if ($application->qrCode)
                    <div class="bg-light rounded p-2 d-flex justify-content-center gap-5">
                        <div>
                            <p class="text-muted small mb-1 fw-semibold">QR Code:</p>
                            {!! QrCode::size(200)->generate($application->qrCode->qr_token) !!}
                        </div>
                        <div class="row border-start border-dark">
                            <p class="col-6 text-muted small mb-1 fw-semibold">Check In Date:</p>
                            <p class="col-6">
                                {{ $application->qrCode->scanLogs->where('event_type', 'check_in')->first()->created_at ?? 'N/A' }}
                            </p>
                            <p class="col-6 text-muted small mb-1 fw-semibold">Check Out Date:</p>
                            <p class="col-6">
                                {{ $application->qrCode->scanLogs->where('event_type', 'check_out')->first()->created_at ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3 d-flex gap-2">
                    @if ($application->status === 'pending')
                        <button wire:click="cancelApplication({{ $application->id }})"
                            wire:confirm="Are you sure you want to cancel this application?" class="btn btn-danger">
                            Cancel Application
                        </button>
                    @endif

                    <button wire:click="viewDetails({{ $application->id }})" class="btn btn-primary">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <svg class="text-muted mb-3" width="64" height="64" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                    </path>
                </svg>
                <h5 class="card-title">No Applications Found</h5>
                <p class="text-muted">You haven't submitted any storage applications yet.</p>
                <div class="mt-3">
                    <a href="{{ route('storage') }}" class="btn btn-primary">
                        Submit New Application
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
