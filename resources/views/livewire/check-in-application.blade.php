<div>
    {{-- Stop trying to control. --}}
    @php
        $id = $application->id;
    @endphp
    @include('livewire.includes.header2', [
        'title' => 'Check-In / Check-Out Application',
        'subtitle' => "Review and confirm the storage check-in or check-out for application #$id",
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
                    href="{{ route('manage_storage_application', ['status' => 'active']) }}">Applications</a>
            </li>
            <li class="breadcrumb-item active">Application #{{ $application->id }}</li>
        </ol>
    </nav>


    {{-- Session message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="container-fluid py-4">
        <div class="row">

            <!-- LEFT COLUMN: Applicant Information -->
            <div class="col-lg-4">
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
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-8">
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
                                            {{ $application->locker->storageRoom->room_name ?? 'N/A' }}
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
                                            {{ $application->openArea->storageRoom->room_name ?? 'N/A' }}</p>
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

                {{-- Stored Items with Edit Functionality --}}
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
                                            <th class="text-center">Actions</th>
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
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button wire:click="editItem({{ $item->id }})"
                                                            class="btn btn-outline-primary" title="Edit Item">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button wire:click="deleteItem({{ $item->id }})"
                                                            wire:confirm="Are you sure you want to delete this item? This action cannot be undone."
                                                            class="btn btn-outline-danger" title="Delete Item">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CHECK-IN SECTION --}}
            @if ($application->qrCode && $application->qrCode->status === 'not_scanned')
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-box-arrow-in-down"></i>
                            Check-In Details
                        </h5>
                    </div>

                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Storage Zone --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Storage Zone / Location
                            </label>
                            <input type="text" class="form-control" wire:model.defer="storage_zone"
                                placeholder="e.g. Rack A – Level 2 – Slot 5"
                                {{ $application->qrCode->status === 'checked_in' ? 'disabled' : '' }}>
                            @error('storage_zone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Check-in Photo --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Check-In Item Photo
                            </label>

                            <input type="file" class="form-control" wire:model="checkin_item_photo"
                                accept="image/*"
                                {{ $application->qrCode->status === 'checked_in' ? 'disabled' : '' }}>

                            @error('checkin_item_photo')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            {{-- Preview --}}
                            @if ($checkin_item_photo)
                                <div class="mt-2">
                                    <img src="{{ $checkin_item_photo->temporaryUrl() }}" class="img-thumbnail"
                                        style="max-height: 200px;">
                                </div>
                            @elseif ($application->qrCode->checkin_item_photo)
                                <div class="mt-2">
                                    <a href="{{ Storage::disk('public')->url($application->qrCode->checkin_item_photo) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-image"></i> View Uploaded Photo
                                    </a>
                                </div>
                            @endif
                            {{-- Loading State --}}
                            <div wire:loading wire:target="checkin_item_photo" class="mt-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="text-primary fw-medium">Uploading photo...</span>
                                </div>
                            </div>

                        </div>

                        {{-- Action --}}
                        @if ($application->qrCode->status !== 'checked_in')
                            <button wire:click="confirmCheckIn" wire:loading.attr="disabled" class="btn btn-success">
                                <i class="bi bi-check-circle"></i>
                                Confirm Check-In
                            </button>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i>
                                This item has already been checked in.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- CHECK-OUT SECTION (Already Checked In) --}}
            @if ($application->qrCode->status === 'checked_in' || $application->qrCode->status === 'checked_out')
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle-fill"></i>
                            Check-In Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-info-circle"></i>
                            This item has been successfully checked in.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Storage Zone</small>
                                <strong>{{ $application->qrCode->storage_zone ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6 mb-3 d-flex flex-column align-items-start">
                                <small class="text-muted d-block">Check-In Photo</small>
                                @if ($application->qrCode->checkin_item_photo)
                                    <img src="{{ Storage::disk('public')->url($application->qrCode->checkin_item_photo) }}"
                                        alt="Check-In Photo" class="img-thumbnail" style="max-height: 150px;">
                                    <a href="{{ Storage::disk('public')->url($application->qrCode->checkin_item_photo) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-image"></i> View Photo
                                    </a>
                                @else
                                    <span class="text-muted">No photo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHECK-OUT SECTION --}}
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-box-arrow-up"></i>
                            Check-Out
                        </h5>
                    </div>

                    <div class="card-body">
                        @if ($application->qrCode->status === 'checked_out')
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i>
                                This item has already been checked out.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Warning:</strong> Checking out will mark this item as collected and release the
                                storage space.
                            </div>

                            <p class="mb-3">
                                Are you sure the applicant has collected all their items? This action will:
                            </p>
                            <ul class="mb-4">
                                <li>Mark the QR code status as "Checked Out"</li>
                                <li>Release the assigned storage space (locker/open area)</li>
                                <li>Log the checkout event</li>
                            </ul>

                            <button wire:click="confirmCheckOut" wire:loading.attr="disabled" class="btn btn-primary"
                                onclick="return confirm('Are you sure you want to check out this application?')">
                                <i class="bi bi-box-arrow-up"></i>
                                <span wire:loading.remove>Confirm Check-Out</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>


    <!-- Edit Item Modal -->
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span><i class="bi bi-pencil-square me-2"></i>Edit Stored Item</span>
            </div>
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="editingItemId">
                <div class="spinner-border my-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <div wire:loading.remove="editingItemId" class="pb-3">
            <form wire:submit.prevent="updateItem">
                <div class="row">
                    <!-- Item Name -->
                    <div class="col-md-12 mb-3">
                        <label for="editItemName" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>Item Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" wire:model="editItemName" id="editItemName"
                            class="form-control @error('editItemName') is-invalid @enderror"
                            placeholder="e.g., Laptop, Study Desk">
                        @error('editItemName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Item Type -->
                    <div class="col-md-6 mb-3">
                        <label for="editItemType" class="form-label fw-semibold">
                            <i class="bi bi-grid-3x3 me-1"></i>Item Type <span class="text-danger">*</span>
                        </label>
                        <select wire:model="editItemType" id="editItemType"
                            class="form-select @error('editItemType') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="electronic">Electronic</option>
                            <option value="furniture">Furniture</option>
                            <option value="container">Container</option>
                            <option value="luggage">Luggage</option>
                            <option value="clothing">Clothing</option>
                            <option value="home_appliance">Home Appliance</option>
                            <option value="other">Other</option>
                        </select>
                        @error('editItemType')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estimated Size -->
                    <div class="col-md-6 mb-3">
                        <label for="editEstimatedSize" class="form-label fw-semibold">
                            <i class="bi bi-arrows-angle-expand me-1"></i>Size <span class="text-danger">*</span>
                        </label>
                        <select wire:model="editEstimatedSize" id="editEstimatedSize"
                            class="form-select @error('editEstimatedSize') is-invalid @enderror">
                            <option value="">Select Size</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                        @error('editEstimatedSize')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Item Condition -->
                    <div class="col-md-12 mb-3">
                        <label for="editItemCondition" class="form-label fw-semibold">
                            <i class="bi bi-clipboard-check me-1"></i>Condition <span class="text-danger">*</span>
                        </label>
                        <select wire:model="editItemCondition" id="editItemCondition"
                            class="form-select @error('editItemCondition') is-invalid @enderror">
                            <option value="">Select Condition</option>
                            <option value="fragile">Fragile - Requires special handling</option>
                            <option value="bulky">Bulky - Large volume or unusual shape</option>
                            <option value="boxed">Boxed - Easy to stack and store</option>
                            <option value="misc">Miscellaneous - Standard item</option>
                        </select>
                        @error('editItemCondition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label for="editItemDescription" class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1"></i>Description
                        </label>
                        <textarea wire:model="editItemDescription" id="editItemDescription"
                            class="form-control @error('editItemDescription') is-invalid @enderror" rows="3"
                            placeholder="Additional details about the item..."></textarea>
                        @error('editItemDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Photo Upload -->
                    <div class="col-md-12 mb-3">
                        <label for="editItemPhoto" class="form-label fw-semibold">
                            <i class="bi bi-camera me-1"></i>Item Photo
                        </label>

                        @if ($currentItemPhotoPath && !$editItemPhoto)
                            <div class="mb-2">
                                <img src="{{ Storage::disk('public')->url($currentItemPhotoPath) }}"
                                    alt="Current item photo" class="img-thumbnail" style="max-height: 150px;">
                                <p class="small text-muted mt-1">Current photo</p>
                            </div>
                        @endif

                        @if ($editItemPhoto)
                            <div class="mb-2">
                                <img src="{{ $editItemPhoto->temporaryUrl() }}" alt="New item photo"
                                    class="img-thumbnail" style="max-height: 150px;">
                                <p class="small text-success mt-1">New photo preview</p>
                            </div>
                        @endif

                        <input type="file" wire:model="editItemPhoto" id="editItemPhoto"
                            class="form-control @error('editItemPhoto') is-invalid @enderror" accept="image/*">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Max file size: 2MB. Leave empty to keep current photo.
                        </div>
                        @error('editItemPhoto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div wire:loading wire:target="editItemPhoto" class="text-primary mt-2">
                            <i class="bi bi-hourglass-split"></i> Uploading...
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="button" wire:click="cancelEdit" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <i class="bi bi-save me-1"></i>
                        <span wire:loading.remove wire:target="updateItem">Save Changes</span>
                        <span wire:loading wire:target="updateItem">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </x-custom-modal>

    @push('styles')
        <style>
            .btn-group-sm>.btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        </style>
    @endpush

</div>
