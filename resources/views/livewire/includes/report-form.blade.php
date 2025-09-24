<form wire:submit.prevent="" class="card bg-white shadow px-5 py-4 mb-4">
    @if ($this->currentStep === 1)
        <p class="h4 mt-2">My Storage</p>
        <div class="dropdown mb-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                @if (!empty($this->form->storageApplicationId))
                    Storage #{{ ucfirst($this->form->storageApplicationId) }}
                @else
                    Select Storage
                @endif
            </button>
            <ul class="dropdown-menu">
                @forelse ($this->studentApplications as $application)
                    <li>
                        <input type="radio" style="display:none" id="role-{{ $application->id }}"
                            value="{{ $application->id }}" wire:model.live="form.storageApplicationId">
                        <label for="role-{{ $application->id }}"
                            class="dropdown-item {{ $application->id === $this->form->storageApplicationId ? 'active' : '' }}">
                            Storage #{{ ucfirst($application->id) }}
                        </label>
                    </li>
                @empty
                    <p class="mb-0">
                        No Existing Storage
                    </p>
                @endforelse
            </ul>
        </div>
        @if ($this->form->storageApplicationId)
            <div class="border-top border-bottom border-primary bg-primary-subtle bg-opacity-10 p-2">
                <div class="row">
                    <p class="leading col-3">Storage Room:</p>
                    <p class="col-9">{{ $this->selectedApplication->storageRoom->id }}</p>
                    <p class="leading col-3">Locker:</p>
                    <p class="col-9">{{ $this->selectedApplication->locker->id ?? "N/A" }}</p>
                    <p class="leading col-3">Open Area:</p>
                    <p class="col-9">{{ $this->selectedApplication->openArea->id ?? "N/A" }}</p>
                </div>
            </div>
        @endif

        <p class="h4 mt-2">Item Name</p>
        <div class="dropdown mb-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                @if (!empty($this->form->itemId))
                    {{ ucfirst($this->form->itemId) }}
                @else
                    Select Item
                @endif
            </button>
            <ul class="dropdown-menu">
                @forelse ($this->studentApplications->items as $item)
                    <li>
                        <input type="radio" style="display:none" id="role-{{ $item->id }}"
                            value="{{ $item->id }}" wire:model.live="form.itemId">
                        <label for="role-{{ $item->id }}"
                            class="dropdown-item {{ $item->id === $this->form->itemId ? 'active' : '' }}">
                            {{ ucfirst($item->id) }}
                        </label>
                    </li>
                @empty
                    <p class="mb-0">
                        No Existing Application
                    </p>
                @endforelse
            </ul>
        </div>
        @if ($this->form->storageApplicationId)
            <div class="border-top border-bottom border-primary bg-primary-subtle bg-opacity-10 p-2">
                <div class="row">
                    <p class="leading col-3">Storage Room:</p>
                    <p class="col-9">{{ $this->selectedApplication->storageRoom->id }}</p>
                    <p class="leading col-3">Locker:</p>
                    <p class="col-9">{{ $this->selectedApplication->locker->id }}</p>
                    <p class="leading col-3">Open Area:</p>
                    <p class="col-9">{{ $this->selectedApplication->openArea->id }}</p>
                </div>
            </div>
        @endif
    @elseif ($this->currentStep === 2)

    @elseif ($this->currentStep === 3)
    @endif
</form>
