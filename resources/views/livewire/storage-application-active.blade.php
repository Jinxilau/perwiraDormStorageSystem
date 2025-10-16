<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
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
        <x-custom-modal>
            @if ($this->application)
                <div class="card">
                    <div class="card-header">
                        Application - {{ $this->application->id }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">QR Code</h5>
                        {!! QrCode::size(200)->generate($this->application->qrCode->qr_token) !!}
                    </div>
                </div>
            @endif
        </x-custom-modal>

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
                        placeholder="Search applications...">
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
                <tr wire:key="row-{{ $application->id }}"
                    class="{{ in_array($application->id, $this->selectedApplications) ? 'table-active' : '' }}">
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
                    <td x-show="cols.includes('item')"> {{-- Items --}}
                        @foreach ($application->items as $item)
                            <div wire:key="item-{{ $item->id }}">
                                {{ ucfirst($item->item_type) }}
                            </div>
                        @endforeach
                    </td>
                    <td x-show="cols.includes('applicationDate')"> {{-- Application Date --}}
                        <div>
                            {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                        </div>
                        <div>
                            {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                        </div>
                    </td>
                    <td x-show="cols.includes('status')"> {{-- Status --}}
                        <span class="badge text-bg-success">
                            {{ ucfirst($application->storage_application_status) }}
                        </span>
                    </td>
                    <td> {{-- Check-in Date --}}
                        @php
                            $checkin = $application->qrCode->scanLogs->where('event_type', 'check_in')->first();
                        @endphp
                        @if ($checkin)
                            <div>
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
                            <div>
                                {{ $checkout->created_at->format('M d, Y') }}
                            </div>
                            <div>
                                {{ $checkout->created_at->format('h:i A') }}
                            </div>
                        @endif
                    </td>
                    <td class="align-middle"> {{-- Actions --}}
                        <div class="flex items-center space-x-2" style="white-space: nowrap">
                            <button @click="$dispatch('view_modal', {id: {{ $application->id }}})"
                                title="View Details" class="btn btn-sm border border-secondary" style="padding: 0;"
                                type="button">
                                <i class="bi bi-eye"></i>
                            </button>

                            <button wire:click="approveApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to check in this storage?"
                                class="text-success btn btn-sm border border-success" style="padding: 0;"
                                title="Check In">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </button>

                            <button wire:click="rejectApplication({{ $application->id }})"
                                wire:confirm="Are you sure you want to check out this storage?"
                                class="text-danger btn btn-sm border border-danger" style="padding: 0;"
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
