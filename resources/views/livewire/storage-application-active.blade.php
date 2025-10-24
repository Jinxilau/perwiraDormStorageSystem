<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-custom-modal>
        <x-slot:title>
            Application #{{ $this->modalData }}
        </x-slot:title>

        <div wire:loading="modalData" class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        @php
            $modalApplication = App\Models\StorageApplication::with(['applicant', 'approver', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom'])->find($this->modalData);
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
                <div class="text-center pb-4 border-bottom mb-4">
                    <h5 class="text-info mb-3"><i class="bi bi-qr-code me-2"></i>QR Code</h5>
                    <p class="fw-bold mt-3 mb-0">{{ $modalApplication->qrCode->qr_token }}</p>
                    <div class="d-inline-block p-3 bg-white rounded shadow-sm border">
                        {!! QrCode::size(200)->generate($modalApplication->qrCode->qr_token) !!}
                    </div>
                    <p class="text-muted small mt-3 mb-0">Scan this code for quick check-in and check-out.</p>
                </div>
                <div class="mb-4 pb-3 border-bottom">
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
                @if ($modalApplication->approver)
                    <div class="mb-2">
                        <h5 class="text-info mb-3">Approver</h5>
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
            @endif
        </div>
    </x-custom-modal>

    {{-- Main Content --}}
    <div x-data="{
        allCol: true,
        cols: [],
        allOptions: ['studentInfo', 'storageDetails', 'item', 'applicationDate', 'status'],
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
            ['title' => $this->countTotalActive, 'subtitle' => 'Total Active Application'],
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
                    <input wire:model="search" type="text" class="w-100" id="searchInput"
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
                            Check In Date
                        </label>
                        <input type="date" max="{{ Date::now()->toDateString() }}" wire:model="checkinDate"
                            class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Check Out Date
                        </label>
                        <input type="date" max="{{ Date::now()->toDateString() }}" wire:model="checkoutDate"
                            class="form-control">
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
                            x-text="cols.length === 5 ? 'All Columns' : 'Custom Columns'"></button>
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
                                    value="storageDetails" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol3">Storage Details</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol4"
                                    value="item" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol4">Items</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol5"
                                    value="applicationDate" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol5">Application Date</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="displayCols" id="displayCol6"
                                    value="status" x-model="cols" @change="checkIfAllSelected()">
                                <label class="form-check-label" for="displayCol6">Status</label>
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
                <th class="align-middle py-2" x-show="cols.includes('applicationDate')">
                    Application Date
                </th>
                <th class="align-middle py-2" x-show="cols.includes('status')">
                    Status
                </th>
                <th class="align-middle py-2">
                    Check-in Date
                </th>
                <th class="align-middle py-2">
                    Check-out Date
                </th>
                <th class="align-middle py-2">
                    Actions
                </th>
            </x-slot:header>
            @forelse($this->approvedStorageApplications as $application)
                <tr wire:key="row-{{ $application->id }}">
                    <td> {{-- Application ID --}}
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
                        <div style="white-space: nowrap;">
                            {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                        <div>
                            {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                        </div>
                    </td>
                    <td x-show="cols.includes('status')"> {{-- Status --}}
                        @if ($application->qrCode->status === 'checked_in')
                            <span class="badge text-bg-warning">
                                {{ ucfirst($application->qrCode->status) }}
                            </span>
                        @elseif ($application->qrCode->status === 'checked_out')
                            <span class="badge text-bg-success">
                                {{ ucfirst($application->qrCode->status) }}
                            </span>
                        @else
                            <span class="badge text-bg-danger">
                                Not Checked In
                            </span>
                        @endif
                    </td>
                    <td> {{-- Check-in Date --}}
                        @php
                            $checkin = $application->qrCode->scanLogs->where('event_type', 'check_in')->first();
                        @endphp
                        @if ($checkin)
                            <div style="white-space: nowrap;">
                                {{ $checkin->created_at->format('M d, Y') }}
                            </div>
                            <div>
                                {{ $checkin->created_at->format('h:i A') }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td> {{-- Check-out Date --}}
                        @php
                            $checkout = $application->qrCode->scanLogs->where('event_type', 'check_out')->first();
                        @endphp
                        @if ($checkout)
                            <div style="white-space: nowrap;">
                                {{ $checkout->created_at->format('M d, Y') }}
                            </div>
                            <div>
                                {{ $checkout->created_at->format('h:i A') }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <button wire:click="$set('modalData', {{ $application->id }}); $dispatch('display_modal')"
                                title="View Details" class="btn btn-sm border border-secondary"
                                style="padding: 0 2px;" type="button">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button wire:click="checkin({{ $application }})"
                                wire:confirm="Are you sure you want to check in this storage?"
                                class="text-success btn btn-sm border border-success" style="padding: 0 2px;"
                                title="Check In">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </button>

                            <button wire:click="checkout({{ $application }})"
                                wire:confirm="Are you sure you want to check out this storage?"
                                class="text-danger btn btn-sm border border-danger" style="padding: 0 2px;"
                                title="Check Out">
                                <i class="bi bi-box-arrow-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="px-3 py-5 text-center">
                        <div>
                            <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                            <h3 class="mt-4">No Active Applications Found</h3>
                            <p class="mt-2 text-nuted">There are no active storage applications to view.</p>
                        </div>
                    </td>
                </tr>
            @endforelse

            <x-slot:pagination>
                {{ $this->approvedStorageApplications->links('vendor.livewire.bootstrap') }}
            </x-slot:pagination>
        </x-table>

    </div>

    <x-toast-message />
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
