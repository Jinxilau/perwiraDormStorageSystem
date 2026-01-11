<div class="container">
    {{-- The best athlete wants his opponent at his best. --}}

    @include('livewire.includes.student-title2', [
        'title' => 'My Storage',
        'subtitle' => 'View and track your storage application requests',
    ])

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <span>You have successfully applied! You will receive an email once your account is approved.</span>
            {{ session('success') }}

        </div>
    @endif

    @forelse ($this->studentStorageApplications as $application)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                    <div>
                        <h5 class="card-title fw-bold mb-1">Application #{{ $application->id }}</h5>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-calendar-event me-1"></i>
                            Submitted on {{ $application->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div>
                        @if ($application->storage_application_status === 'approved')
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>
                                @if ($application->qrCode?->status === 'checked_out')
                                    Checked Out
                                @elseif ($application->is_unclaimed)
                                    Unclaimed
                                @elseif ($application->qrCode?->status === 'checked_in')
                                    Checked In
                                @else
                                    Approved
                                @endif
                            </span>
                        @elseif($application->storage_application_status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="bi bi-clock me-1"></i>Pending
                            </span>
                        @elseif($application->storage_application_status === 'rejected')
                            <span class="badge bg-danger px-3 py-2">
                                <i class="bi bi-x-circle me-1"></i>Rejected
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                {{ ucfirst($application->storage_application_status) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- QR Code Section --}}
                @if ($application->qrCode)
                    <div class="bg-light rounded-3 p-4 mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-qr-code me-2"></i>QR Code & Check-in Information
                        </h6>
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                                    {!! QrCode::size(180)->generate($application->qrCode->qr_token) !!}
                                </div>
                                {{-- NEW: Download Button --}}
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="downloadQrCode({{ $application->id }})">
                                        <i class="bi bi-download me-1"></i> Download QR
                                    </button>
                                </div>
                                {{-- QR Code Token --}}
                                <p class="mt-2 text-break small text-muted">
                                    {{ $application->qrCode->qr_token }}
                                </p>
                            </div>
                            {{-- Check-in and Check-out Dates --}}
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-box-arrow-in-right text-success me-2 mt-1"></i>
                                            <div>
                                                <p class="text-muted small mb-1 fw-semibold">Check In Date</p>
                                                <p class="mb-0 fw-medium">
                                                    {{ $application->qrCode->scanLogs->where('event_type', 'check_in')->first()?->created_at?->format('M d, Y - h:i A') ?? 'Not checked in yet' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-box-arrow-right text-danger me-2 mt-1"></i>
                                            <div>
                                                <p class="text-muted small mb-1 fw-semibold">Check Out Date</p>
                                                <p class="mb-0 fw-medium">
                                                    {{ $application->qrCode->scanLogs->where('event_type', 'check_out')->first()?->created_at?->format('M d, Y - h:i A') ?? 'Not checked out yet' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Rejection Reason --}}
                @if ($application->note && $application->storage_application_status === 'rejected')
                    <div class="alert alert-danger border-0 border-start border-danger border-4 shadow-sm bg-danger-subtle mb-4"
                        role="alert">
                        <div class="d-flex align-items-start">
                            {{-- Icon: Slightly larger and aligned to top --}}
                            <div class="me-3 mt-1">
                                <i class="bi bi-x-circle-fill fs-4 text-danger"></i>
                            </div>

                            {{-- Content --}}
                            <div>
                                <h6 class="alert-heading fw-bold text-danger mb-1">Application Rejected</h6>
                                <p class="mb-0 text-danger-emphasis small">
                                    {{ $application->note }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Application Details --}}
                <div class="row g-4 mb-4">
                    {{-- Academic Period --}}
                    <div class="col-md-6">
                        <div class="detail-item">
                            <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-mortarboard me-1"></i>Academic Period
                            </p>
                            <p class="mb-0 fs-6 fw-medium">
                                {{ $application->semester ? $application->semester->academic_year : 'N/A' }} -
                                Semester {{ $application->semester ? $application->semester->semester_no : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- Number of Items --}}
                    <div class="col-md-6">
                        <div class="detail-item">
                            <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-box-seam me-1"></i>Number of Items
                            </p>
                            <p class="mb-0 fs-6 fw-medium">{{ count($application->storedItems) }}</p>
                        </div>
                    </div>

                    {{-- Storage Location --}}
                    <div class="col-md-6">
                        <div class="detail-item">
                            <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-geo-alt me-1"></i>Storage Location
                            </p>
                            <p class="mb-0 fs-6 fw-medium">{{ $application->storage_room->room_name }}</p>
                        </div>
                    </div>

                    {{-- Storage Type --}}
                    <div class="col-md-6">
                        <div class="detail-item">
                            <p class="text-muted small mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-archive me-1"></i>Storage Type
                            </p>
                            <p class="mb-0 fs-6 fw-medium">
                                @if ($application->locker_id && $application->open_area_id)
                                    Locker {{ $application->locker->code }} + Open Area
                                @elseif ($application->locker_id)
                                    Locker {{ $application->locker->code }}
                                @elseif ($application->open_area_id)
                                    Open Area
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Locker ID --}}
                    @if ($application->locker_id)
                        <div class="col-md-6">
                            <div class="detail-item">
                                <p class="text-muted small mb-1 text-uppercase fw-semibold"
                                    style="letter-spacing: 0.5px;">
                                    <i class="bi bi-key me-1"></i>Locker Number
                                </p>
                                <p class="mb-0 fs-6 fw-medium">{{ $application->locker->code }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Stored Items Section --}}
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-list-check me-2"></i>Stored Items
                    </h6>
                    <div class="row g-3">
                        @foreach ($application->storedItems as $item)
                            <div class="col-md-6">
                                <div class="card border h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex gap-3">
                                            @if ($item->item_photo_path)
                                                <img src="{{ \Storage::disk('public')->url($item->item_photo_path) }}"
                                                    alt="{{ $item->item_name }}" class="rounded"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold">{{ $item->item_name }}</h6>
                                                <p class="text-muted small mb-1">
                                                    <span
                                                        class="badge bg-light text-dark border">{{ ucwords(str_replace('_', ' ', $item->item_type)) }}</span>
                                                </p>
                                                <p class="text-muted small mb-1">
                                                    <i
                                                        class="bi bi-rulers me-1"></i>{{ ucfirst($item->estimated_size) }}
                                                </p>
                                                @if ($item->item_description)
                                                    <p class="text-muted small mb-0">
                                                        {{ Str::limit($item->item_description, 60) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                @if ($application->storage_application_status === 'pending')
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button wire:click="goToEdit({{ $application->id }})" class="btn btn-primary px-4">
                            <i class="bi bi-pencil me-2"></i>Edit Application
                        </button>
                        <button wire:click="cancelApplication({{ $application->id }})"
                            wire:confirm="Are you sure you want to cancel this application?"
                            class="btn btn-outline-danger px-4">
                            <i class="bi bi-x-circle me-2"></i>Cancel Application
                        </button>
                    </div>
                @endif
            </div>
        </div>

    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="mb-0" style="font-size: 64px">📦</p>
                <h5 class="card-title h3">No Applications Found</h5>
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

@assets
    <style>
        .detail-item {
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            transform: translateX(2px);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
@endassets
