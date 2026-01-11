<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    @include('livewire.includes.header2', [
        'title' => 'Storage Room Management',
        'subtitle' => 'Oversee storage room details, capacity, and assignment.',
    ])

    <div class="container">
        {{-- Tabs --}}
        <div class="d-flex gap-2 align-items-center mb-3">
            <div class="btn-group flex-grow-1" role="group" aria-label="Basic radio toggle button group">
                @foreach ($this->rooms as $room)
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio{{ $room->id }}"
                        autocomplete="off" {{ $roomId == $room->id ? 'checked' : '' }}
                        wire:key="btnradio{{ $room->id }}" wire:model.live="roomId" value="{{ $room->id }}"
                        @click='window.location.href="{{ route('storage_room', ['room' => $room->id]) }}"'>
                    <label class="btn btn-outline-primary d-flex align-items-center justify-content-center p-2"
                        for="btnradio{{ $room->id }}" wire:key="btnradio{{ $room->id }}">

                        <span>Room {{ $loop->iteration }}: {{ $room->room_name }}</span>

                        <span
                            class="badge rounded-pill m-0 ms-2
                                {{ $room->room_status === 'open' ? 'bg-success' : '' }}
                                {{ $room->room_status === 'closed' ? 'bg-danger' : '' }}
                                {{ $room->room_status === 'under_maintenance' ? 'bg-warning text-dark' : '' }}">

                            {{ str_replace('_', ' ', ucfirst($room->room_status)) }}
                        </span>
                    </label>
                @endforeach
            </div>

            {{-- Add Room Button --}}
            @if (auth()->user()->role_id === 1)
                <button type="button" class="btn btn-outline-success" wire:click="$dispatch('display_modal3');"
                    title="Add New Storage Room">
                    <i class="bi bi-plus-circle"></i> Add Room
                </button>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
            <button type="button" class="btn w-100  {{ $this->roomStatus ? 'btn-secondary' : 'btn-success' }}"
                wire:click='toggleRoomStatus' wire:loading.attr="disabled">
                {{ $this->roomStatus ? 'Close' : 'Open' }} the Storage Room
            </button>
            @if (auth()->user()->role_id === 1)
                <button type="button" class="btn w-100 btn-outline-danger"
                    wire:click="$dispatch('display_delete_room_modal')" title="Delete Current Storage Room">
                    <i class="bi bi-trash"></i> Delete Room
                </button>
            @endif
        </div>

        {{-- Open Area Section --}}
        @php
            $status = $this->storageRoom->openAreas->area_status;
            $statusClasses = match ($status) {
                'available' => ['border' => 'border-success', 'bg' => 'bg-success', 'icon' => 'bi-check-circle-fill'],
                'occupied' => [
                    'border' => 'border-danger',
                    'bg' => 'bg-danger',
                    'icon' => 'bi-exclamation-triangle-fill',
                ],
                'under_maintenance' => [
                    'border' => 'border-secondary',
                    'bg' => 'bg-secondary',
                    'icon' => 'bi-wrench-adjustable',
                ],
                default => ['border' => 'border-dark', 'bg' => 'bg-dark', 'icon' => 'bi-question-circle'],
            };
        @endphp

        <div class="row">
            <div class="col-md-8 mx-auto mt-3">
                <section
                    class="bg-white position-relative p-4 rounded-4 shadow-lg border border-3 {{ $statusClasses['border'] }}">

                    {{-- Status Badge --}}
                    <span class="badge position-absolute top-0 end-0 m-3 px-3 py-2 {{ $statusClasses['bg'] }}">
                        <i class="bi {{ $statusClasses['icon'] }} me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </span>

                    <div class="d-flex flex-column justify-content-between align-items-center text-center pt-3">
                        <h3 class="fw-bold text-dark mb-1">Open Area</h3>
                        <p class="text-muted mb-4">
                            Stored items: <strong class="text-primary">{{ $this->openAreaStoredItems }}</strong> Items
                        </p>

                        {{-- Status Toggle Buttons --}}
                        <div class="btn-group shadow-sm" role="group" aria-label="Area status toggle">
                            {{-- Available --}}
                            <input type="radio" class="btn-check" id="status_available"
                                wire:click="toggleAreaStatus('available')"
                                {{ $status === 'available' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success px-4" for="status_available">
                                <i class="bi bi-check-circle"></i> Available
                            </label>

                            {{-- Occupied (Full) --}}
                            <input type="radio" class="btn-check" id="status_occupied"
                                wire:click="toggleAreaStatus('occupied')"
                                {{ $status === 'occupied' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger px-4" for="status_occupied">
                                <i class="bi bi-dash-circle"></i> Full
                            </label>

                            {{-- Maintenance --}}
                            <input type="radio" class="btn-check" id="status_maintenance"
                                wire:click="toggleAreaStatus('under_maintenance')"
                                {{ $status === 'under_maintenance' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary px-4" for="status_maintenance">
                                <i class="bi bi-tools"></i> Maintenance
                            </label>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- Locker --}}
        <div class="row">
            <div class="col-md-12 mx-auto mt-3">
                <section class="bg-white p-2 rounded-2 border border-dark border-3 position-relative">
                    {{-- Add Locker --}}
                    @if (auth()->user()->role_id === 1)
                        <button type="button" class="btn btn-success btn-sm ms-auto position-absolute end-0 me-3 mt-2"
                            wire:click="$dispatch('display_modal2');" title="Add New Locker">
                            <i class="bi bi-plus-circle"></i> Add Locker
                        </button>
                    @endif

                    {{-- Title --}}
                    <div class="d-flex flex-column">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <h3 class="m-0">Locker</h3>
                        </div>

                        <div class="d-flex">
                            <div class="card text-bg-primary mb-3 mx-auto" style="width: clamp(10rem, 50%,15rem);">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Unit Available</h5>
                                    <p class="card-text text-center">
                                        {{ $this->lockerStats['available'] }}</p>
                                </div>
                            </div>
                            <div class="card text-bg-primary mb-3 mx-auto" style="width: clamp(10rem, 50%,15rem);">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Unit Occupied</h5>
                                    <p class="card-text text-center">
                                        {{ $this->lockerStats['occupied'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="progress width-100" role="progressbar" aria-label="Animated striped example"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            @if ($this->lockerStats['total'] !== 0)
                                {{-- Occupied --}}
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                    style="width: {{ ($this->lockerStats['occupied'] / $this->lockerStats['total']) * 100 }}%">
                                </div>
                                {{-- Reserved --}}
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                    style="width: {{ ($this->lockerStats['reserved'] / $this->lockerStats['total']) * 100 }}%">
                                </div>
                                {{-- In Maintenance --}}
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary"
                                    style="width: {{ (($this->lockerStats['total'] - $this->lockerStats['available'] - $this->lockerStats['occupied'] - $this->lockerStats['reserved']) / $this->lockerStats['total']) * 100 }}%">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body-tertiary text-opacity-75">Current Usage:
                                {{ $this->lockerStats['occupied'] + $this->lockerStats['reserved'] }} Units</p>
                            <p class="text-body-tertiary text-opacity-75">Max Capacity:
                                {{ $this->lockerStats['total'] }} Units</p>
                        </div>

                        <div class="d-flex gap-3">
                            <div>
                                <div class="rounded-circle bg-success d-inline-block"
                                    style="width: 10px; height: 10px;">
                                </div>
                                <span>Available</span>
                            </div>
                            <div>
                                <div class="rounded-circle bg-warning d-inline-block"
                                    style="width: 10px; height: 10px;">
                                </div>
                                <span>Reserved</span>
                            </div>
                            <div>
                                <div class="rounded-circle bg-danger d-inline-block"
                                    style="width: 10px; height: 10px;">
                                </div>
                                <span>Occupied</span>
                            </div>
                            <div>
                                <div class="rounded-circle bg-secondary d-inline-block"
                                    style="width: 10px; height: 10px;">
                                </div>
                                <span>In Maintenance</span>
                            </div>
                        </div>

                        <div id='grid'>
                            @foreach ($this->storageRoom->lockers as $locker)
                                @php
                                    $isOccupiedButClear =
                                        $locker->status === 'occupied' && $locker->canBeMarkedAvailable();
                                    $statusColor = match ($locker->status) {
                                        'available' => 'btn-success',
                                        'reserved' => 'btn-warning text-dark',
                                        'occupied' => 'btn-danger',
                                        'under_maintenance' => 'btn-secondary',
                                        default => 'btn-dark',
                                    };
                                @endphp
                                <button type="button" wire:key="locker-{{ $locker->id }}"
                                    class="btn {{ $statusColor }} d-flex justify-content-center align-items-center border border-dark border-2 shadow-sm py-3"
                                    style="border-style: inset; aspect-ratio: 1/1; max-height: 100px;"
                                    wire:click="$set('modalData', {{ $locker->id }}); $dispatch('display_modal');"
                                    @disabled($locker->status === 'occupied' && $locker->canBeMarkedAvailable() === true)>

                                    <span class="fw-bold fs-5">{{ $locker->code }}</span>
                                    {{-- Small Indicator Icon --}}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- Edit Locker Modal --}}
    <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <strong>Manage Locker</strong>
                    <div class="small text-muted">
                        Room: {{ $this->locker?->storageRoom?->room_name }}
                    </div>
                </div>

                <span class="badge bg-dark fs-6">
                    {{ $this->locker?->code }}
                </span>
            </div>
        </x-slot:title>

        {{-- Loading --}}
        <div wire:loading="modalData" class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Content --}}
        <div wire:loading.remove="modalData">
            @if ($this->locker)
                {{-- 🔹 Locker Summary --}}
                <div class="border rounded-3 p-3 mb-3 bg-light">
                    <div class="row text-center">
                        <div class="col">
                            <div class="fw-bold">Locker ID</div>
                            <div>#{{ $this->locker->id }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold">Size</div>
                            <div>{{ $this->locker->size }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold">Status</div>
                            <span
                                class="badge
                            {{ match ($this->locker->status) {
                                'available' => 'bg-success',
                                'reserved' => 'bg-warning',
                                'occupied' => 'bg-danger',
                                default => 'bg-secondary',
                            } }}">
                                {{ ucfirst($this->locker->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🔹 Application Info --}}
                <div class="mb-3">
                    <h6 class="fw-bold">Current Assignment</h6>

                    @if ($this->lockerApplication)
                        <div class="alert alert-warning mb-2">
                            <strong>In Use</strong><br>
                            Applicant:
                            <strong>{{ $this->lockerApplication->applicant->name }}</strong><br>
                            Status:
                            <span class="badge bg-info">
                                {{ ucfirst($this->lockerApplication->storage_application_status) }}
                            </span>
                        </div>
                    @else
                        <div class="alert alert-success mb-2">
                            This locker is not assigned to any storage application.
                        </div>
                    @endif
                </div>

                {{-- 🔹 Edit Form --}}
                <form wire:submit="editLocker">
                    <h6 class="fw-bold">Update Locker Status</h6>

                    <div class="mb-2 d-flex flex-wrap gap-2">
                        @foreach (['available', 'occupied', 'reserved', 'under_maintenance'] as $status)
                            <input type="radio" class="btn-check" id="status-{{ $status }}"
                                wire:model="lockerStatus" value="{{ $status }}" @disabled($this->locker->status === $status || ($status === 'available' && $this->locker->canBeMarkedAvailable() === true))>
                            <label class="btn btn-outline-secondary" for="status-{{ $status }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </label>
                        @endforeach
                    </div>

                    @if ($this->locker->canBeMarkedAvailable() === true)
                        <small class="text-danger">
                            This locker cannot be marked as <strong>available</strong> while assigned.
                        </small>
                    @endif

                    @error('lockerStatus')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    {{-- Size --}}
                    <h6 class="fw-bold mt-3">Locker Size</h6>
                    <input type="text" class="form-control" wire:model="lockerSize" placeholder="e.g. 30x40x50">

                    @error('lockerSize')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <button class="btn btn-primary w-100 mt-3">
                        Save Changes
                    </button>
                </form>

                {{-- Delete Locker Section --}}
                @if (auth()->user()->role_id === 1)
                    <hr class="my-4">
                    <div class="mt-3">
                        <h6 class="fw-bold text-danger">Danger Zone</h6>
                        <p class="text-muted small">Delete this locker permanently. This action cannot be undone.</p>

                        @if ($this->locker->canBeDeleted())
                            <button type="button" class="btn btn-outline-danger w-100" wire:click="deleteLocker"
                                wire:confirm="Are you sure you want to delete locker {{ $this->locker->code }}? This action cannot be undone.">
                                <i class="bi bi-trash"></i> Delete Locker
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                <i class="bi bi-lock"></i> Cannot Delete (Locker In Use)
                            </button>
                            <small class="text-danger d-block mt-2">
                                This locker cannot be deleted because it has active storage applications.
                            </small>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </x-custom-modal>

    {{-- Add Room Modal --}}
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
    }" @display_modal3.window="openModal();" @close-modal.window="closeModal()"
        @keydown.escape.window="closeModal()" x-show='show' tabindex="-1" id="modal2"
        :class="{ 'display': show }" style="display: none;" x-transition.scale>

        {{-- Background Overlay --}}
        <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

        {{-- Modal --}}
        <div id="modal-content2" class="card" x-transition>
            {{-- Title --}}
            <div class="card-header d-flex justify-content-between align-items-center ps-3 pe-3">
                <h5 class="modal-title mb-0">Add New Storage Room</h5>
                {{-- Bootstrap Native Close Button --}}
                <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">

                <form wire:submit="addRoom">
                    <div class="mb-3">
                        <label for="newRoomName" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="newRoomName" wire:model="newRoomName"
                            placeholder="e.g. E1-09" required>
                        @error('newRoomName')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Status</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" id="room-status-open" wire:model="newRoomStatus"
                                value="open" checked>
                            <label class="btn btn-outline-success" for="room-status-open">
                                Open
                            </label>

                            <input type="radio" class="btn-check" id="room-status-closed"
                                wire:model="newRoomStatus" value="closed">
                            <label class="btn btn-outline-secondary" for="room-status-closed">
                                Closed
                            </label>

                            <input type="radio" class="btn-check" id="room-status-maintenance"
                                wire:model="newRoomStatus" value="under_maintenance">
                            <label class="btn btn-outline-warning" for="room-status-maintenance">
                                Under Maintenance
                            </label>
                        </div>
                        @error('newRoomStatus')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Create Room
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Locker Modal --}}
    <x-custom-modal2 modal-id="addLockerModal" event-name="display_add_locker_modal">
        <x-slot:title>
            <strong>Add New Locker</strong>
            <div class="small text-muted">Room: {{ $this->storageRoom->room_name }}</div>
        </x-slot:title>

        <form wire:submit="addLocker">
            <div class="mb-3">
                <label for="newLockerCode" class="form-label">Locker Code</label>
                <input type="text" class="form-control" id="newLockerCode" wire:model="newLockerCode"
                    placeholder="e.g. A01" required>
                <small class="text-muted">Must be unique within this room</small>
                @error('newLockerCode')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="newLockerSize" class="form-label">Locker Size</label>
                <input type="text" class="form-control" id="newLockerSize" wire:model="newLockerSize"
                    placeholder="e.g. 30x40x50" required>
                @error('newLockerSize')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Initial Status</label>
                <div class="d-flex flex-wrap gap-2">
                    <input type="radio" class="btn-check" id="locker-status-available"
                        wire:model="newLockerStatus" value="available" checked>
                    <label class="btn btn-outline-success" for="locker-status-available">
                        Available
                    </label>

                    <input type="radio" class="btn-check" id="locker-status-maintenance"
                        wire:model="newLockerStatus" value="under_maintenance">
                    <label class="btn btn-outline-secondary" for="locker-status-maintenance">
                        Under Maintenance
                    </label>
                </div>
                @error('newLockerStatus')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-plus-circle"></i> Create Locker
            </button>
        </form>
    </x-custom-modal2>

    {{-- Delete Room Modal --}}
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
    }" @display_delete_room_modal.window="openModal();" @close-modal.window="closeModal()"
        @keydown.escape.window="closeModal()" x-show='show' tabindex="-1" id="modal2"
        :class="{ 'display': show }" style="display: none;" x-transition.scale>

        {{-- Background Overlay --}}
        <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

        {{-- Modal --}}
        <div id="modal-content2" class="card" x-transition>
            {{-- Title --}}
            <div class="card-header d-flex justify-content-between align-items-center ps-3 pe-3">
                <h5>
                    <strong class="text-danger">Delete Storage Room</strong>
                </h5>
                <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>

            </div>

            {{-- Body --}}
            <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">

                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>

                <div class="mb-3">
                    <h6>Room Details:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Name:</strong> {{ $this->storageRoom->room_name }}</li>
                        <li><strong>Total Lockers:</strong> {{ $this->storageRoom->lockers->count() }}</li>
                        <li><strong>Open Area Status:</strong>
                            {{ ucfirst($this->storageRoom->openAreas->area_status ?? 'N/A') }}</li>
                    </ul>
                </div>

                @if ($this->storageRoom->canDelete())
                    <p class="text-muted">
                        This room has no active storage applications and can be safely deleted.
                    </p>
                    <button type="button" class="btn btn-danger w-100" wire:click="deleteRoom"
                        wire:confirm="Are you absolutely sure? This will permanently delete '{{ $this->storageRoom->room_name }}' and all its lockers.">
                        <i class="bi bi-trash"></i> Yes, Delete This Room
                    </button>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-lock-fill me-2"></i>
                        <strong>Cannot Delete Room</strong>
                        <p class="mb-0 mt-2">This storage room cannot be deleted because it contains:</p>
                        <ul class="mb-0">
                            @if (
                                $this->storageRoom->lockers()->whereHas('storageApplications', function ($q) {
                                        $q->whereIn('storage_application_status', ['pending', 'approved']);
                                    })->exists())
                                <li>Lockers with active storage applications</li>
                            @endif
                            @if (
                                $this->storageRoom->openAreas()->whereHas('storageApplications', function ($q) {
                                        $q->whereIn('storage_application_status', ['pending', 'approved']);
                                    })->exists())
                                <li>Open area with active storage applications</li>
                            @endif
                        </ul>
                    </div>
                    <button type="button" class="btn btn-secondary w-100" disabled>
                        <i class="bi bi-lock"></i> Room Cannot Be Deleted
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@assets
    <style>
        #grid {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
            grid-template-rows: repeat(auto-fit, minmax(50px, 1fr));
            grid-auto-flow: row;
            grid-auto-rows: minmax(50px, 1fr);
            gap: 10px
        }
    </style>
@endassets
