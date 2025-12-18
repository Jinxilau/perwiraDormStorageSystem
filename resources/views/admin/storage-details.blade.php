<x-layouts.admin title="Scan QR">
    @php
        $id = $application->id;
    @endphp
    @include('livewire.includes.header2', [
        'title' => 'Storage Details',
        'subtitle' => "Application #{$id} Details",
    ])

    @php
        $dashboard = Auth::user()->role_id === 1 ? 'admin_dashboard' : 'staff_dashboard';
    @endphp
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 ms-2">
            <li class="breadcrumb-item"><a href="{{ route($dashboard) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manage_storage_application') }}">Applications</a></li>
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
                                <h6 class="alert-heading"><i class="bi bi-lock"></i> Locker Assigned</h6>
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
                                    <i class="bi bi-person-standing-dress text-success" style="font-size: 2.5rem;"></i>
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
                            <form action="route('applications.approve', $application->id)" method="POST"
                                class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle"></i> Approve Application
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject Application
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="route('applications.reject', $application->id)" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="note" class="form-label">Reason for Rejection</label>
                            <textarea class="form-control" id="note" name="note" rows="4" required
                                placeholder="Please provide a reason for rejecting this application..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>

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
