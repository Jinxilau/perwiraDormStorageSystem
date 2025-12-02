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

            {{-- Check in --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Check In Date</label>
                <input type="date" max="{{ Date::now()->toDateString() }}" wire:model="checkinDate"
                    class="form-control bg-light border-0">
            </div>

            {{-- Check out --}}
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">Check Out Date</label>
                <input type="date" max="{{ Date::now()->toDateString() }}" wire:model="checkoutDate"
                    class="form-control bg-light border-0">
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

            {{-- Column Display --}}
            <div class="col-md-4 col-lg-3" x-data="{ show: false }">
                <label class="form-label small text-muted fw-bold">Columns</label>
                <div class="position-relative">
                    <button @click.prevent="show = !show"
                        class="form-select bg-light border-0 text-start d-flex justify-content-between align-items-center">
                        <span x-text="cols.length === 4 ? 'All Visible' : 'Custom'"></span>
                    </button>
                    {{-- Dropdown --}}
                    <div x-show="show" @click.away="show = false" x-transition.opacity.duration.200ms
                        class="position-absolute bg-white border rounded-3 shadow p-3 mt-1 z-3"
                        style="display: none; min-width: 220px; z-index: 1050;">

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="allCol" x-model="allCol"
                                @change="toggleAll()">
                            <label class="form-check-label small" for="allCol">Select All</label>
                        </div>
                        <hr class="my-2">

                        @foreach (['studentInfo' => 'Student Info', 'storageDetails' => 'Storage Details', 'item' => 'Items', 'applicationDate' => 'Application Date', 'status' => 'Status'] as $val => $label)
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

        {{-- Table --}}
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
