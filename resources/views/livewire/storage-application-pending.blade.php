<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <x-custom-modal>
        <x-slot:title>
            Application #<span>{{ $this->modalData }}</span>
        </x-slot:title>

        <div wire:loading="modalData" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        @php
            $modalApplication = App\Models\StorageApplication::find($this->modalData);
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

                <div>
                    <h5 class="text-info mb-3"><i class="bi bi-archive me-2"></i>Stored Items</h5>
                    @if ($modalApplication->storedItems->count() > 0)
                        <div class="row g-3">
                            @foreach ($modalApplication->storedItems as $item)
                                <div class="col-12">
                                    <div class="d-flex align-items-start p-3 bg-light rounded">
                                        <img src="{{ asset('storage/' . $item->item_photo_path) }}" alt="Item Photo"
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
            @else
                <p>No application data found.</p>
            @endif
        </div>
    </x-custom-modal>

    <x-custom-modal2>
        <x-slot:title>
            Reject Application #{{ $this->modalData2 }}
        </x-slot:title>
        <div wire:loading="modalData2" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div wire:loading.remove="modalData2" class="pb-3">
            <form wire:submit.prevent="rejectApplication({{ $this->modalData2 }})">
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

    <div x-data="{
        show: false,
        openModal() {
            this.show = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.show = false;
            document.body.style.overflow = '';
        },
    }" @display_modal3.window="openModal();" x-show='show' tabindex="-1" id="modal2"
        :class="{ 'display': show }" style="display: none;" x-transition.scale>

        {{-- Background Overlay --}}
        <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

        {{-- Modal --}}
        <div id="modal-content2" class="card" x-transition>
            {{-- Title --}}
            <div class="card-header">
                Bulk Reject Application
            </div>
            <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">
                <div wire:loading="modalData2" class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div wire:loading.remove="modalData2" class="pb-3">
                    <form wire:submit.prevent="bulkReject">
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
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'items', 'storageDetails', 'status'],
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
            ['title' => $this->countTotalPending, 'subtitle' => 'Total Pending Application'],
            ['title' => $this->countLocker, 'subtitle' => 'Total Locker Application'],
            ['title' => $this->countOpenArea, 'subtitle' => 'Total Open Area Application'],
        ]" />

        {{-- Filtering Options --}}
        <div class="row border-bottom border-top border-dark bg-white mb-3">
            {{-- Search --}}
            <div class="bg-white rounded p-3 d-flex gap-2 align-items-center">
                <div class="flex-grow-1 flex-shrink-1 position-relative">
                    <i class="bi bi-search fs-5 position-absolute"
                        style="left: 16px; top: 50%;transform: translateY(-50%);"></i>
                    <input wire:model.live="search" type="text" class="w-100" id="searchInput"
                        style="padding: 14px 16px 14px 48px; border: 2px solid #e5e7eb; border-radius: 8px;"
                        placeholder="Search applications by applicant name, matric no, or ID...">
                </div>
                <button type="button" class="btn btn-primary fw-bold">Search</button>
            </div>
            {{-- Dropdowns --}}
            <div class="bg-white border-rounded p-3 shadow-sm">
                <div class="row row-cols-3 mb-3">
                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">Room</label>
                        <select class="form-select mb-3" wire:model="room">
                            <option value="">All Room</option>
                            @foreach ($this->rooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Storage Type
                        </label>
                        <select class="form-select mb-3" wire:model="storageType">
                            <option value="">All Type</option>
                            <option value="locker">Locker</option>
                            <option value="openArea">Open Area</option>
                            <option value="lockerOpenArea">Locker + Open Area</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            From Application Date Range
                        </label>
                        <input type="date" wire:model="fromRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            To Application Date Range
                        </label>
                        <input type="date" wire:model="toRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By Application Date
                        </label>
                        <select class="form-select mb-3 " wire:model="sortDate">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By Applicant Name
                        </label>
                        <select class="form-select mb-3" wire:model="sortName">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>

                    <div x-data="{ show: false }" class="position-relative">
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Show/Hide Columns
                        </label>
                        <button @click.prevent="show = !show" class="form-select text-start"
                            x-text="cols.length === 4 ? 'All Columns' : 'Custom Columns'"></button>
                        <div x-show="show" @click.away="show = false"
                            class="position-absolute bg-white border rounded shadow-sm p-2 mt-1 z-50"
                            style="display: none; min-width: 200px; left: 0;">

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="allCol" id="displayCol1"
                                    x-model="allCol" @change="toggleAll()">
                                <label class="form-check-label" for="displayCol1">All Columns</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol2"
                                    value="studentInfo" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol2">Student Info</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol3"
                                    value="items" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol3">Items</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol4"
                                    value="storageDetails" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol4">Storage Details</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol5"
                                    value="status" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol5">Status</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Application Per Page <span class="text-muted small">(Pagination)</span>
                        </label>
                        <select class="form-select mb-3" wire:model="paginationInt">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-3 pt-2 border-top">
                    <button class="btn btn-danger fw-bold" wire:click.prevent="clearFilters"
                        @click="allCol = true; toggleAll()">Clear All</button>
                </div>
            </div>

        </div>

        <!-- Bulk Actions Bar -->
        <div class="bg-primary-subtle border-bottom border-primary rounded-top px-3 py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-blue-700">
                        {{ count($this->selectedApplications) }} application(s) selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="bulkApprove"
                        @empty($this->selectedApplications) disabled @endempty
                        wire:confirm="Are you sure you want to approve the selected applications?"
                        class="btn btn-success">
                        Approve Selected
                    </button>
                    <button type="button" wire:click="$dispatch('display_modal3');"
                        @empty($this->selectedApplications) disabled @endempty class="btn btn-danger">
                        Reject Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <x-table>
            <x-slot:header>
                <th class="text-uppercase align-middle">
                    <input type="checkbox" wire:model.live="selectAll">
                </th>
                <th class="align-middle py-2">
                    Application ID
                </th>
                <th class="align-middle py-2" x-show="cols.includes('studentInfo')">
                    Student Info
                </th>
                <th class="align-middle py-2" x-show="cols.includes('storageDetails')">
                    Storage Details
                </th>
                <th class="align-middle py-2" x-show="cols.includes('items')">
                    Items
                </th>
                <th class="align-middle py-2">
                    Application Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Status
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>
            @forelse($this->pendingStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}"
                    class="{{ in_array($application->id, $this->selectedApplications) ? 'table-active' : '' }}">
                    <td class="align-middle"> {{-- Select --}}
                        <input type="checkbox" value="{{ $application->id }}"
                            wire:model.live="selectedApplications">
                    </td>
                    <td> {{-- ID --}}
                        #{{ $application->id }}
                    </td> {{-- Student Info --}}
                    <td x-show="cols.includes('studentInfo')">
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
                                {{ $application->openArea?->storageRoom?->room_name }} <div>Locker + Open
                                    Area
                                </div>
                            @elseif (!empty($application->locker_id))
                                {{ $application->locker?->storageRoom?->room_name }} <div>Locker</div>
                            @elseif (!empty($application->open_area_id))
                                {{ $application->openArea?->storageRoom?->room_name }} <div>Open Area</div>
                            @endif
                        </div>
                    </td>
                    <td x-show="cols.includes('items')"> {{-- Items --}}
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
                    <td> {{-- Application Date --}}
                        <div>
                            {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                        <div>
                            {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                        </div>
                    </td>
                    <td x-show="cols.includes('status')"> {{-- Status --}}
                        <span class="badge text-bg-warning">
                            {{ ucfirst($application->storage_application_status) }}
                        </span>
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <button
                                wire:click="$set('modalData', {{ $application->id }}); $dispatch('display_modal');"
                                title="View Details" class="btn btn-sm border border-secondary"
                                style="padding: 0 2px;" type="button">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button wire:click="approveApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to approve this application?"
                                class="text-success btn btn-sm border border-success" style="padding: 0 2px;"
                                title="Approve">
                                <i class="bi bi-check"></i>
                            </button>

                            <button
                                wire:click="$set('modalData2', {{ $application->id }}); $dispatch('display_modal2');"
                                class="text-danger btn btn-sm border border-danger" style="padding: 0 2px;"
                                title="Reject">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-folder2-open mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Pending Applications Found</h3>
                            <p class="mt-2 text-muted">There are no pending storage applications to review.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            <x-slot:pagination>{{ $this->pendingStorageApplications->links('vendor.livewire.bootstrap') }}</x-slot:pagination>
        </x-table>
    </div>

    <x-toast-message />

</div>
@push('styles')
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
@endpush
