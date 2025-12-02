<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <x-custom-modal title="Edit Locker - {{ $this->locker?->id }}">
        @if ($this->locker)
            <form wire:submit="editLocker">

                <div class="d-flex gap-2">
                    <h5 class="card-title">
                        Locker Status
                        <span
                            class="badge px-3 py-1 {{ match ($this->locker->status) {'available' => 'bg-success','occupied' => 'bg-danger', 'reserved' => 'bg-warning',default => 'bg-secondary'} }}">
                            {{ ucfirst($this->locker->status) }}
                        </span>
                    </h5>
                </div>
                <div>
                    <input type="radio" class="btn-check" name="locker_status" id="option1" autocomplete="off"
                        wire:model="lockerStatus" value="available"
                        {{ $this->locker->status === 'available' ? 'checked disabled' : '' }}>
                    <label class="btn btn-success" for="option1">
                        <i class="bi bi-check-circle-fill"></i>
                        Mark as Available
                    </label>

                    <input type="radio" class="btn-check" name="locker_status" id="option2" autocomplete="off"
                        wire:model="lockerStatus" value="occupied"
                        {{ $this->locker->status === 'occupied' ? 'checked disabled' : '' }}>
                    <label class="btn btn-danger" for="option2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Mark as Occupied
                    </label>

                    <input type="radio" class="btn-check" name="locker_status" id="option4" autocomplete="off"
                        wire:model="lockerStatus" value="reserved"
                        {{ $this->locker->status === 'reserved' ? 'checked disabled' : '' }}>
                    <label class="btn btn-warning" for="option4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Mark as Reserved
                    </label>

                    <input type="radio" class="btn-check" name="locker_status" id="option3" autocomplete="off"
                        wire:model="lockerStatus" value="under_maintenance"
                        {{ $this->locker->status === 'under_maintenance' ? 'checked disabled' : '' }}>
                    <label class="btn btn-secondary" for="option3">
                        <i class="bi bi-wrench-adjustable"></i>
                        Mark as Under Maintenance
                    </label>
                </div>
                @error('lockerStatus')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <h5 class="card-title mt-2">Locker Size</h5>
                <div class="input-group mb-3">
                    <input type="text" class="form-control border border-dark"
                        placeholder="{{ $this->locker->size }}" wire:model="lockerSize">
                    <span class="input-group-text border border-dark">cm</span>
                </div>
                @error('lockerSize')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        @endif
    </x-custom-modal>

    @include('livewire.includes.header2', [
        'title' => 'Storage Room Management',
        'subtitle' => 'Oversee storage room details, capacity, and assignment.',
    ])


    <div class="container">
        {{-- Tabs --}}
        <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
            @foreach ($this->storageRooms as $room)
                <input type="radio" class="btn-check" name="btnradio" id="btnradio{{ $room->id }}"
                    autocomplete="off" {{ $roomId == $room->id ? 'checked' : '' }}
                    wire:key="btnradio{{ $room->id }}" wire:navigate
                    @click='window.location.href="{{ route('storage_room', ['room' => $room->id]) }}"'>
                <label class="btn btn-outline-primary" for="btnradio{{ $room->id }}"
                    wire:key="btnradio{{ $room->id }}">Room {{ $loop->iteration }}:
                    {{ $room->room_name }}</label>
            @endforeach
        </div>
        <button type="button" class="btn w-100 mt-4 {{ $this->roomStatus ? 'btn-secondary' : 'btn-success' }}"
            wire:click='toggleRoomStatus'>
            {{ $this->roomStatus ? 'Close' : 'Open' }} the Storage Room
        </button>
        {{-- Matrix Card --}}
        <section>
        </section>
        {{-- Open Area --}}
        <div class="row">
            <div class="col-md-8 mx-auto mt-3">
                <section
                    class="bg-white position-relative p-4 rounded-4 shadow-lg border border-3 {{ match ($this->storageRoom->openAreas->area_status) {'available' => 'border-success','occupied' => 'border-danger','under_maintenance' => 'border-secondary'} }}">
                    <span
                        class="badge position-absolute top-0 end-0 m-3 px-3 py-2 {{ match ($this->storageRoom->openAreas->area_status) {'available' => 'bg-success','occupied' => 'bg-danger','under_maintenance' => 'bg-secondary'} }}">
                        {{ ucfirst($this->storageRoom->openAreas->area_status) }}
                    </span>
                    <div class="d-flex flex-column justify-content-between align-items-center text-center pt-3">
                        <h3 class="fw-bold text-dark mb-3">Open Area</h3>
                        <p class="text-muted mb-4">Stored items:
                            <strong class="text-primary">{{ $this->openAreaStoredItems }}</strong> Items
                        </p>
                        <div>
                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off"
                                wire:confirm="Are you sure you want to update the status of open area?"
                                wire:click="toggleAreaStatus('available')"
                                {{ $this->storageRoom->openAreas->area_status === 'available' ? 'checked' : '' }}>
                            <label class="btn btn-success" for="option1">
                                <i class="bi bi-check-circle-fill"></i>
                                Mark as Available
                            </label>
                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off"
                                wire:confirm="Are you sure you want to update the status of open area?"
                                wire:click="toggleAreaStatus('occupied')"
                                {{ $this->storageRoom->openAreas->area_status === 'occupied' ? 'checked' : '' }}>
                            <label class="btn btn-danger" for="option2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Mark as Full
                            </label>
                            <input type="radio" class="btn-check" name="options" id="option3" autocomplete="off"
                                wire:confirm="Are you sure you want to update the status of open area?"
                                wire:click="toggleAreaStatus('under_maintenance')"
                                {{ $this->storageRoom->openAreas->area_status === 'under_maintenance' ? 'checked' : '' }}>
                            <label class="btn btn-secondary" for="option3">
                                <i class="bi bi-wrench-adjustable"></i>
                                Mark as Under Maintenance
                            </label>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        {{-- Locker --}}
        <div class="row">
            <div class="col-md-12 mx-auto mt-3">
                <section class="bg-white p-2 rounded-2 border border-dark border-3">
                    <div class="d-flex flex-column">
                        <h3 style="text-align: center">Locker</h3>
                        <div class="d-flex">
                            <div class="card text-bg-primary mb-3 mx-auto" style="width: clamp(10rem, 50%,15rem);">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Unit Available</h5>
                                    <p class="card-text text-center">
                                        {{ $this->availableLockers }}</p>
                                </div>
                            </div>
                            <div class="card text-bg-primary mb-3 mx-auto" style="width: clamp(10rem, 50%,15rem);">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Unit Occupied</h5>
                                    <p class="card-text text-center">
                                        {{ $this->occupiedLockers }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="progress width-100" role="progressbar" aria-label="Animated striped example"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            @if ($this->totalLockers !== 0)
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                    style="width: {{ ($this->occupiedLockers / $this->totalLockers) * 100 }}%">
                                </div>
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                    style="width: {{ (($this->reservedLockers) / $this->totalLockers) * 100 }}%">
                                </div>
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary"
                                    style="width: {{ (($this->unavailableLockers - $this->occupiedLockers - $this->reservedLockers) / $this->totalLockers) * 100 }}%">
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-body-tertiary text-opacity-75">Current Usage:
                                {{ $this->unavailableLockers }} Units</p>
                            <p class="text-body-tertiary text-opacity-75">Max Capacity:
                                {{ $this->totalLockers }} Units</p>
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
                                <button type="button"
                                    class="btn {{ match ($locker->status) {'available' => 'btn-success','reserved' => 'btn-warning','occupied' => 'btn-danger',default => 'btn-secondary'} }} d-flex justify-content-center align-items-center border border-dark border-3 text-white"
                                    style="border-style: inset;"
                                    @click="$dispatch('show-modal', { id: {{ $locker->id }} })">
                                    {{ $locker->id }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <x-toast-message />
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
