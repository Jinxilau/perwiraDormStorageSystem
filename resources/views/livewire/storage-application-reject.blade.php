<div>
    {{-- Modal --}}
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>Application Details</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->modalData }}</span>
            </div>
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="modalData" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        @php
            $modalApplication = App\Models\StorageApplication::with([
                'applicant',
                'approver',
                'storedItems',
                'openArea.storageRoom',
                'locker.storageRoom',
            ])->find($this->modalData);
        @endphp
        <div wire:loading.remove="modalData" class="text-start">
            @if ($modalApplication)
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="text-info mb-3"><i class="bi bi-person-circle me-2"></i>Applicant Information</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">ID</label>
                            <p class="mb-0 fw-medium">{{ $modalApplication->applicant->id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Matric No</label>
                            <p class="mb-0 fw-medium">{{ $modalApplication->applicant->matric_no ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Name</label>
                            <p class="mb-0 fw-medium">{{ $modalApplication->applicant->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Email</label>
                            <p class="mb-0 fw-medium text-break">{{ $modalApplication->applicant->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block mb-1">Phone</label>
                            <p class="mb-0 fw-medium text-break">
                                {{ $modalApplication->applicant->phone_number ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="text-info mb-3"><i class="bi bi-box-seam me-2"></i>Storage Details</h5>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Storage Room:</label>
                        <p class="mb-0 fw-medium">
                            @if (!empty($modalApplication->open_area_id) && !empty($modalApplication->locker_id))
                                {{ $modalApplication->openArea?->storageRoom?->room_name }}
                            @elseif (!empty($modalApplication->locker_id))
                                {{ $modalApplication->locker?->storageRoom?->room_name }}
                            @elseif (!empty($modalApplication->open_area_id))
                                {{ $modalApplication->openArea?->storageRoom?->room_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-muted small d-block mb-2">Storage Type:</span>

                        @if (!empty($modalApplication->open_area_id) && !empty($modalApplication->locker_id))
                            <div class="d-flex flex-wrap gap-2">
                                <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                    <i class="bi bi-lock me-2"></i>
                                    <span class="me-2">Locker</span>
                                    <span
                                        class="badge {{ match ($modalApplication->locker->status) {
                                            'available' => 'text-bg-success',
                                            'occupied' => 'text-bg-danger',
                                            default => 'text-bg-secondary',
                                        } }}">
                                        {{ ucfirst($modalApplication->locker->status) }}
                                    </span>
                                </div>
                                <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                    <i class="bi bi-grid-3x3 me-2"></i>
                                    <span class="me-2">Open Area</span>
                                    <span
                                        class="badge {{ match ($modalApplication->openArea->area_status) {
                                            'available' => 'text-bg-success',
                                            'occupied' => 'text-bg-danger',
                                            default => 'text-bg-secondary',
                                        } }}">
                                        {{ ucfirst($modalApplication->openArea->area_status) }}
                                    </span>
                                </div>
                            </div>
                        @elseif (!empty($modalApplication->locker_id))
                            <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                <i class="bi bi-lock me-2"></i>
                                <span class="me-2">Locker</span>
                                <span
                                    class="badge {{ match ($modalApplication->locker->status) {
                                        'available' => 'bg-success',
                                        'occupied' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ ucfirst($modalApplication->locker->status) }}
                                </span>
                            </div>
                        @elseif (!empty($modalApplication->open_area_id))
                            <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2">
                                <i class="bi bi-grid-3x3 me-2"></i>
                                <span class="me-2">Open Area</span>
                                <span
                                    class="badge {{ match ($modalApplication->openArea->area_status) {
                                        'available' => 'bg-success',
                                        'occupied' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ ucfirst($modalApplication->openArea->area_status) }}
                                </span>
                            </div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="text-info mb-3"><i class="bi bi-archive me-2"></i>Stored Items</h5>
                    @if ($modalApplication->storedItems->count() > 0)
                        <div class="row g-3">
                            @foreach ($modalApplication->storedItems as $item)
                                <div class="col-12">
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <img src="{{ \Storage::disk('public')->url($item->item_photo_path) }}" alt="Item Photo"
                                            class="rounded shadow-sm me-3"
                                            style="width: 80px; height: 80px; object-fit: cover;" />
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 fw-bold">{{ ucfirst($item->item_name) }}</h6>
                                            <div class="mb-2">
                                                <span
                                                    class="badge bg-primary me-1">{{ ucfirst($item->item_type) }}</span>
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($item->estimated_size) }}</span>
                                            </div>
                                            <p class="mb-0 small text-muted">{{ ucfirst($item->item_description) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0 py-3">No stored items found.</p>
                    @endif
                </div>
                @if ($modalApplication->approver)
                    <div class="mb-2">
                        <h5 class="text-info mb-3">Rejected by:</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">ID</label>
                                <p class="mb-0 fw-medium">#{{ $modalApplication->approver->id ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Name</label>
                                <p class="mb-0 fw-medium">{{ $modalApplication->approver->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Email</label>
                                <p class="mb-0 fw-medium text-break">
                                    {{ $modalApplication->approver->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-1">Phone</label>
                                <p class="mb-0 fw-medium text-break">
                                    {{ $modalApplication->approver->phone_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-2">No reviewer information available.</p>
                @endif
            @else
                <p>No application data found.</p>
            @endif
        </div>
    </x-custom-modal>

    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'storageDetails', 'item', 'applicationDate', 'rejectedDate', 'status'],
        toggleAll() {
            if (this.allCol) {
                this.cols = [...this.allOptions];
            } else {
                this.cols = [];
            }
        },
        checkIfAllSelected() {
            this.allCol = this.cols.length === this.cols.length && this.allOptions.every(e => this.cols.includes(e));
        },
    }" x-init="toggleAll()" class="bg-white shadow-sm rounded-lg overflow-hidden">
        <x-card-matrix :items="[
            ['title' => $this->countTotalRejected, 'subtitle' => 'Total Rejected Application'],
            ['title' => $this->countTotalRejectedToday, 'subtitle' => 'Total Rejected Today.'],
        ]" />

        <x-filter-options>
            {{-- Room --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Room</label>
                <select class="form-select bg-light border-0" wire:model="room">
                    <option value="">All Room</option>
                    @foreach ($this->rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Storage Type --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Type</label>
                <select class="form-select bg-light border-0" wire:model="storageType">
                    <option value="">All Types</option>
                    <option value="locker">Locker</option>
                    <option value="openArea">Open Area</option>
                    <option value="lockerOpenArea">Locker + Open Area</option>
                </select>
            </div>

            {{-- Sort By Date --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Application Date
                </label>
                <select class="form-select bg-light border-0" wire:model="sortDate">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>


            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Rejected Date
                </label>
                <select class="form-select bg-light border-0" wire:model="rejectedDate">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            {{-- Sort By Name --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Applicant Name
                </label>
                <select class="form-select bg-light border-0" wire:model="sortName">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3" x-data="{ show: false }">
                <label class="form-label small text-muted fw-bold">Columns</label>
                <div class="position-relative">
                    <button @click.prevent="show = !show"
                        class="form-select bg-light border-0 text-start d-flex justify-content-between align-items-center">
                        <span x-text="cols.length === 4 ? 'All Visible' : 'Custom'"></span>
                    </button>
                    {{-- Dropdown Menu --}}
                    <div x-show="show" @click.away="show = false" x-transition.opacity.duration.200ms
                        class="position-absolute bg-white border rounded-3 shadow p-3 mt-1 z-3"
                        style="display: none; min-width: 220px; z-index: 1050;">

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="allCol" x-model="allCol"
                                @change="toggleAll()">
                            <label class="form-check-label small" for="allCol">Select All</label>
                        </div>
                        <hr class="my-2">

                        @foreach (['studentInfo' => 'Student Info', 'storageDetails' => 'Storage Details', 'item' => 'Items', 'applicationDate' => 'Application Date', 'rejectedDate' => 'Rejected Date', 'status' => 'Status'] as $val => $label)
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" value="{{ $val }}"
                                    x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label small">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Per Page</label>
                <select class="form-select bg-light border-0" wire:model="paginationInt">
                    <option value="10">10 Rows</option>
                    <option value="25">25 Rows</option>
                    <option value="50">50 Rows</option>
                    <option value="100">100 Rows</option>
                </select>
            </div>
        </x-filter-options>

        <x-table>
            <x-slot:header>
                <th class="align-middle py-2">
                    Application ID
                </th>
                <th class="align-middle py-2" x-show="cols.includes('studentInfo')">
                    Student Info
                </th>
                <th class="align-middle py-2" x-show="cols.includes('storageDetails')">
                    Storage Details
                </th>
                <th class="align-middle py-2" x-show="cols.includes('item')">
                    Items
                </th>
                <th class="align-middle py-2" x-show="cols.includes('rejectedDate')">
                    Application Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('rejectedDate')">
                    Rejected Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Status
                </th>
                <th class="align-middle py-2">
                    Reason
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>

            @forelse($this->rejectedStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}">
                    <td>
                        #{{ $application->id }}
                    </td>
                    <td x-show="cols.includes('studentInfo')"> {{-- Student Info --}}
                        <div class="d-flex items-center gap-1">
                            <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                style="height: 30px; width:30px">
                                {{ substr($application->applicant->name ?? 'N/A', 0, 2) }}
                            </div>
                            <div>
                                <div>
                                    {{ $application->applicant->name ?? 'N/A' }}
                                </div>
                                <div>
                                    {{ $application->applicant->email ?? 'N/A' }}
                                </div>
                                <div>
                                    ID: {{ $application->applicant->matric_no ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td x-show="cols.includes('storageDetails')"> {{-- Storage Details --}}
                        <div>
                            @if (!empty($application->open_area_id) && !empty($application->locker_id))
                                {{ $application->openArea?->storageRoom?->room_name }} <div>Locker + Open Area
                                </div>
                            @elseif (!empty($application->locker_id))
                                {{ $application->locker?->storageRoom?->room_name }} <div>Locker</div>
                            @elseif (!empty($application->open_area_id))
                                {{ $application->openArea?->storageRoom?->room_name }} <div>Open Area</div>
                            @endif
                        </div>
                    </td>
                    <td x-show="cols.includes('item')">
                        @if ($application->storedItems->count() > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach ($application->storedItems as $item)
                                    <div wire:key="item-{{ $item->id }}" class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">{{ ucfirst($item->item_name) }}</div>
                                            <span class="badge bg-light text-dark border small">
                                                {{ ucfirst($item->item_type) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small">No items</span>
                        @endif
                    </td>
                    <td x-show="cols.includes('applicationDate')"> {{-- Application Date --}}
                        <div>
                            {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                        <div>
                            {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                        </div>
                    </td>
                    <td x-show="cols.includes('rejectedDate')"> {{-- Rejected Date --}}
                        <div>
                            {{ $application->updated_at->format('M d, Y') }}
                        </div>
                        <div>
                            {{ $application->updated_at->format('h:i A') }}
                        </div>
                    </td>
                    <td x-show="cols.includes('status')"> {{-- Status --}}
                        <span class="badge text-bg-danger">
                            {{ ucfirst($application->storage_application_status) }}
                        </span>
                    </td>
                    <td style="overflow-y: auto"> {{-- Reason --}}
                        {{ $application->note ?? 'N/A' }}
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <button wire:click="$set('modalData', {{ $application->id }}); $dispatch('display_modal')"
                                title="View Details" class="btn btn-sm border border-secondary"
                                style="padding: 0 2px;" type="button" data-bs-toggle="modal"
                                data-bs-target="#viewModal">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button wire:click="approveApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to approve this application?"
                                class="text-success btn btn-sm border border-success" style="padding: 0 2px;"
                                title="Reconsider & Approve">
                                <i class="bi bi-check"></i>
                            </button>

                            <button wire:click="undoApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to undo reject the application?"
                                class="text-warning btn btn-sm border border-warning" style="padding: 0 2px;"
                                title="Undo Reject">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Rejected Applications Found</h3>
                            <p class="mt-2 text-muted">There are no rejected storage applications to view.</p>
                        </div>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $this->rejectedStorageApplications->links('vendor.livewire.bootstrap') }}
            </x-slot:pagination>
        </x-table>
    </div>

</div>

@assets
    <style>
        .avatar {
            width: 2.5rem;
            height: 2.5rem;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
        }

        .avatar-initial {
            width: 100%;
            height: 100%;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        .fade-out {
            animation: fadeOut 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
    </style>
@endassets
