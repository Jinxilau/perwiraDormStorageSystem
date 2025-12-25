<div>
    {{-- Do your work, then step back. --}}
    @php
        $id = $application->id;
    @endphp
    @include('livewire.includes.header2', [
        'title' => 'Storage Details',
        'subtitle' => "Application #{$id} Details",
    ])

    @php
        $dashboard = Auth::user()->role_id === 1 ? 'admin_dashboard' : 'staff_dashboard';
        $appStatus = $application->storage_application_status;
    @endphp
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 ms-2">
            <li class="breadcrumb-item"><a href="{{ route($dashboard) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('manage_storage_application', ['status' => $appStatus == 'approved' ? 'active' : ($appStatus == 'rejected' ? 'reject' : $appStatus)]) }}">Applications</a>
            </li>
            <li class="breadcrumb-item active">Application #{{ $application->id }}</li>
        </ol>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Main Application Info -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text"></i> Application Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Application ID</label>
                                <p class="fw-bold">#{{ $application->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Status</label>
                                <p>
                                    @if ($application->storage_application_status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($application->storage_application_status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Applied Date</label>
                                <p class="fw-bold">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Last Updated</label>
                                <p class="fw-bold">{{ $application->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if ($application->semester)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Semester</label>
                                    <p class="fw-bold">{{ $application->semester->academic_year ?? 'N/A' }} -
                                        {{ $application->semester->semester_no ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($application->note)
                            <div class="row">
                                <div class="col-12">
                                    <label class="text-muted small mb-1">Admin's Rejection Note</label>
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> {{ $application->note }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Storage Allocation -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-box"></i> Storage Allocation</h5>
                    </div>
                    <div class="card-body">
                        @if ($application->locker_id)
                            {{-- Locker --}}
                            <div class="alert alert-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="alert-heading"><i class="bi bi-lock"></i> Locker Assigned</h6>
                                    <span
                                        class="badge {{ match ($this->application->locker->status) {
                                            'available' => 'bg-success',
                                            'occupied' => 'bg-danger',
                                            'reserved' => 'bg-warning text-dark',
                                            default => 'bg-secondary',
                                        } }}">
                                        {{ ucfirst($this->application->locker->status) }}
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
                            @php
                                $competingApplications = $this->getCompetingApplications();
                            @endphp
                            @if ($competingApplications->count() > 1 && $application->storage_application_status === 'pending')
                                <div class="card border-warning shadow-sm mb-4">
                                    <div class="card-header bg-warning bg-opacity-10 border-warning">
                                        <h5 class="card-title mb-0 text-warning-emphasis">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            Conflict Detected: {{ $competingApplications->count() }} Applicants
                                            for one locker
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Priority</th>
                                                        <th>Applicant Details</th>
                                                        <th>Matric No</th>
                                                        <th>Applied At</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($competingApplications as $index => $competing)
                                                        <tr
                                                            class="{{ $competing->id === $application->id ? 'table-primary' : '' }}">
                                                            <td>
                                                                @if ($index === 0)
                                                                    <span class="badge bg-success rounded-pill">1st
                                                                        Priority</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-light text-dark border rounded-pill">{{ $index + 1 }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="fw-bold">{{ $competing->applicant->name }}
                                                                </div>
                                                                <small class="text-muted">App ID:
                                                                    #{{ $competing->id }}</small>
                                                                @if ($competing->id === $application->id)
                                                                    <span class="badge bg-primary ms-1">Currently
                                                                        Viewing</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $competing->applicant->matric_no }}</td>
                                                            <td>
                                                                {{ $competing->created_at->format('d M Y, h:i A') }}
                                                                <div class="small text-muted text-nowrap">
                                                                    ({{ $competing->created_at->diffForHumans() }})
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <button wire:click="approve({{ $competing->id }})"
                                                                    wire:confirm="Approving this will make the locker occupied. Proceed?"
                                                                    class="btn btn-sm btn-primary">
                                                                    Approve
                                                                </button>
                                                                <button
                                                                    class="btn btn-sm btn-outline-danger">Reject</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> No competing applications found for this
                                    locker.
                                </div>
                            @endif
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

                {{-- Stored Items --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-box-seam"></i> Stored Items
                            <span class="badge bg-light text-dark ms-2">
                                {{ $application->storedItems->count() }}
                            </span>
                        </h5>
                    </div>

                    <div class="card-body">
                        @if ($application->storedItems->isEmpty())
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                No items were submitted for this application.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Condition</th>
                                            <th>Description</th>
                                            <th>Photos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($application->storedItems as $item)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $item->item_name }}
                                                </td>

                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($item->estimated_size) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @php
                                                        $conditionClasses = match ($item->item_condition) {
                                                            'fragile' => 'bg-danger',
                                                            'bulky' => 'bg-warning text-dark',
                                                            'boxed' => 'bg-success',
                                                            default => 'bg-light text-dark border',
                                                        };

                                                        $conditionIcon = match ($item->item_condition) {
                                                            'fragile' => 'bi-exclamation-triangle-fill',
                                                            'bulky' => 'bi-box-fill',
                                                            'boxed' => 'bi-check2-square',
                                                            default => 'bi-tag',
                                                        };
                                                    @endphp

                                                    <span class="badge {{ $conditionClasses }}">
                                                        <i class="bi {{ $conditionIcon }} me-1"></i>
                                                        {{ ucfirst($item->item_condition) }}
                                                    </span>
                                                </td>

                                                <td class="text-muted small">
                                                    {{ $item->item_description ?? '—' }}
                                                </td>

                                                <td>
                                                    @if ($item->item_photo_path)
                                                        <a href="{{ Storage::disk('public')->url($item->item_photo_path) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-image"></i>
                                                        </a>
                                                    @endif

                                                    @if ($item->item_location_photo)
                                                        <a href="{{ Storage::disk('public')->url($item->item_location_photo) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-secondary ms-1">
                                                            <i class="bi bi-geo-alt"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code Information -->
                @if ($application->qrCode)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-qr-code"></i> QR Code Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Scanned Count</th>
                                            <th>Expires At</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                @if ($application->qrCode->status === 'not_scanned')
                                                    <span class="badge bg-secondary">Not Scanned</span>
                                                @elseif($application->qrCode->status === 'checked_in')
                                                    <span class="badge bg-success">Checked In</span>
                                                @elseif($application->qrCode->status === 'checked_out')
                                                    <span class="badge bg-primary">Checked Out</span>
                                                @else
                                                    <span class="badge bg-danger">Expired</span>
                                                @endif
                                            </td>
                                            <td>{{ $application->qrCode->scanned_count }}</td>
                                            <td>{{ $application->qrCode->qr_expires_at ? $application->qrCode->qr_expires_at->format('M d, Y h:i A') : 'N/A' }}
                                            </td>
                                            <td>{{ $application->qrCode->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Applicant Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person"></i> Applicant</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                @if ($application->applicant->gender === 'male')
                                    <i class="bi bi-person-standing text-success" style="font-size: 2.5rem;"></i>
                                @elseif($application->applicant->gender === 'female')
                                    <i class="bi bi-person-standing-dress text-success"
                                        style="font-size: 2.5rem;"></i>
                                @else
                                    <i class="bi bi-person-standing text-success" style="font-size: 2.5rem;"></i>
                                @endif
                            </div>
                        </div>
                        <h6 class="text-center mb-3">{{ $application->applicant->name }}</h6>

                        @if ($application->applicant->matric_no)
                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-card-text"></i> Matric No.</small>
                                <p class="mb-0 fw-bold">{{ $application->applicant->matric_no }}</p>
                            </div>
                        @endif

                        <div class="mb-2">
                            <small class="text-muted"><i class="bi bi-envelope"></i> Email</small>
                            <p class="mb-0">{{ $application->applicant->email }}</p>
                        </div>

                        @if ($application->applicant->phone_number)
                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-telephone"></i> Phone</small>
                                <p class="mb-0">{{ $application->applicant->phone_number }}</p>
                            </div>
                        @endif

                        @if ($application->applicant->gender)
                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-gender-ambiguous"></i> Gender</small>
                                <p class="mb-0 text-capitalize">{{ $application->applicant->gender }}</p>
                            </div>
                        @endif

                        @if ($application->applicant->year_of_study)
                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-mortarboard"></i> Year of Study</small>
                                <p class="mb-0">Year {{ $application->applicant->year_of_study }}</p>
                            </div>
                        @endif

                        <div class="mb-2">
                            <small class="text-muted"><i class="bi bi-hash"></i> User ID</small>
                            <p class="mb-0">#{{ $application->applicant->id }}</p>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted"><i class="bi bi-check-circle"></i> Account Status</small>
                            <p class="mb-0">
                                @if ($application->applicant->application_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($application->applicant->application_status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Approver Information -->
                @if ($application->approver)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-person-check"></i> Approver</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    @if ($application->approver->gender === 'male')
                                        <i class="bi bi-person-fill-check text-dark" style="font-size: 1.8rem;"></i>
                                    @elseif($application->approver->gender === 'female')
                                        <i class="bi bi-person-check text-dark" style="font-size: 1.8rem;"></i>
                                    @else
                                        <i class="bi bi-shield-check text-dark" style="font-size: 2rem;"></i>
                                    @endif
                                </div>
                            </div>
                            <h6 class="text-center mb-3">{{ $application->approver->name }}</h6>

                            @if ($application->approver->matric_no)
                                <div class="mb-2">
                                    <small class="text-muted"><i class="bi bi-card-text"></i> Matric No.</small>
                                    <p class="mb-0 fw-bold">{{ $application->approver->matric_no }}</p>
                                </div>
                            @endif

                            <div class="mb-2">
                                <small class="text-muted"><i class="bi bi-envelope"></i> Email</small>
                                <p class="mb-0">{{ $application->approver->email }}</p>
                            </div>

                            @if ($application->approver->phone_number)
                                <div class="mb-2">
                                    <small class="text-muted"><i class="bi bi-telephone"></i> Phone</small>
                                    <p class="mb-0">{{ $application->approver->phone_number }}</p>
                                </div>
                            @endif

                            @if ($application->approver->role)
                                <div class="mb-2">
                                    <small class="text-muted"><i class="bi bi-person-badge"></i> Role</small>
                                    <p class="mb-0">
                                        <span
                                            class="badge bg-dark">{{ $application->approver->role->name ?? 'Staff' }}</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                @if ($application->storage_application_status === 'pending')
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="bi bi-gear"></i> Actions</h5>
                        </div>
                        <div class="card-body">
                            <button wire:click="approveApplication()" type="button"
                                class="btn btn-success w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Approve Application
                            </button>
                            <button wire:click="$dispatch('display_modal2');" type="button"
                                class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Reject Application
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <x-custom-modal2>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Reject Application</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->application->id }}</span>
            </div>
        </x-slot:title>
        <div class="d-flex justify-content-center">
            <div wire:loading="modalData2" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div wire:loading.remove="modalData2" class="pb-3">
            <form wire:submit.prevent="rejectApplication()">
                <div class="mb-4">
                    <label for="rejectionNote" class="form-label fw-semibold">
                        <i class="bi bi-chat-left-text me-2"></i>Note for the Applicant
                    </label>
                    <textarea wire:model="rejectionNote" id="rejectionNote" class="form-control" rows="5"
                        placeholder="Provide a clear reason for the rejection..." required></textarea>
                    @error('rejectionNote')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        This message will be sent to the student.
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-octagon me-1"></i>Submit Rejection
                    </button>
                </div>
            </form>
        </div>
    </x-custom-modal2>

</div>
@push('styles')
    <style>
        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.25rem;
        }

        .badge {
            padding: 0.5em 1em;
            font-size: 0.875rem;
        }
    </style>
@endpush
