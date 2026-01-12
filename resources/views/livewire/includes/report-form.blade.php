<form wire:submit.prevent="" class="card bg-white shadow px-5 py-4 mb-4">

    {{-- LOADING --}}
    <div wire:loading wire:target="nextStep, submitForm, previousStep, switchToItem">
        <div
            class="position-absolute top-50 start-50 translate-middle z-3 w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="bg-white p-4 rounded shadow-lg text-center border">
                <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="fw-bold text-muted">Processing...</div>
            </div>
        </div>
    </div>

    {{-- Form Errors --}}
    @for ($i = 0; $i < count($this->form->items); $i++)
        @error('form.items.' . $i . '.id')
            <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>{{ $message }}</span>
            </div>
        @enderror
    @endfor
    {{-- Form Content --}}
    <fieldset wire:loading.attr="disabled" wire:target='nextStep, submitForm, previousStep, switchToItem'>
        @if ($this->currentStep === 1)
            {{-- Select Existing Storage --}}
            <section x-data="{ ok: $wire.entangle('form.storageApplicationId') }" class="py-4">
                <div class="mb-4">
                    <h4 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="bi bi-box-seam me-2 text-primary"></i>
                        My Storage
                    </h4>
                    <p class="text-muted small mb-0">Select an storage to proceed</p>
                </div>

                <div class="mb-3">
                    <label for="storageSelect" class="form-label fw-semibold">Storage</label>
                    <select id="storageSelect" wire:model.live="form.storageApplicationId"
                        class="form-select form-select-lg shadow-sm">
                        <option value="">-- Choose a storage --</option>
                        @forelse ($this->studentApplications as $application)
                            <option value="{{ $application->id }}">Storage
                                {{ $application->semester->academic_year . ' - ' . $application->semester->semester_no }}
                            </option>
                        @empty
                            <option disabled>No storage</option>
                        @endforelse
                    </select>
                </div>

                <span wire:loading wire:target='form.storageApplicationId'>
                    <div class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </span>

                {{-- Show Detail of Selected Storage --}}
                @if ($this->form->storageApplicationId)
                    <div class="card border-primary shadow-sm mt-4 animate-fade-in">
                        <div class="card-header bg-primary bg-opacity-10 border-primary">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="bi bi-info-circle me-2"></i>Storage Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-door-open text-primary me-3 fs-5"></i>
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1">Storage Room</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $this->selectedApplication->storageRoom->room_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-lock text-primary me-3 fs-5"></i>
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1">Locker</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $this->selectedApplication->locker->code ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-grid-3x3 text-primary me-3 fs-5"></i>
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1">Open Area</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-5 pt-3 border-top">
                    <button :disabled="!ok"
                        class="btn btn-primary btn-lg w-100 py-3 shadow-sm d-flex align-items-center justify-content-center position-relative overflow-hidden"
                        wire:loading.attr="disabled" wire:click.prevent="nextStep" style="transition: all 0.3s ease;">
                        <span class="d-flex align-items-center">
                            <i class="bi bi-check-lg me-2 fs-5" wire:loading.remove wire:target='nextStep'></i>
                            <span wire:loading wire:target='nextStep'>
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                            <span class="fw-semibold">Proceed to Next Step</span>
                        </span>
                    </button>
                </div>
            </section>
        @elseif ($this->currentStep === 2)
            {{-- Item Navigation --}}
            <section class="py-2">
                @if (count($this->form->items) > 1)
                    <div class="mb-4">
                        <ul class="nav nav-pills nav-fill gap-2" role="tablist" x-data="{ currentIdx: $wire.entangle('form.itemIdx') }">
                            @foreach ($this->form->items as $index => $item)
                                <li class="nav-item" role="presentation">
                                    <span wire:click.prevent="switchToItem({{ $index }})"
                                        class="nav-link position-relative d-flex align-items-center justify-content-center py-3 shadow-sm"
                                        :class="{ 'active': currentIdx == {{ $index }} }"
                                        style="cursor: pointer; transition: all 0.2s ease;">
                                        <i class="bi bi-box-seam me-2"></i>
                                        <span class="fw-semibold">Item {{ $index + 1 }}</span>
                                        <button type="button" wire:confirm="Are you sure you want to delete this item?"
                                            wire:click.stop.prevent="removeItem({{ $index }})"
                                            class="btn-close btn-close-sm ms-2 position-absolute end-0 me-2"
                                            style="font-size: 0.7rem;"></button>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div
                        class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-tag text-primary me-2"></i>
                            Item {{ count($this->form->items) > 1 ? $this->form->itemIdx + 1 : '' }} Details
                        </h5>

                        {{-- Add/Remove Item --}}
                        <div class="d-flex gap-2">
                            @if (count($this->form->items) > 1)
                                <button type="button" wire:confirm="Are you sure you want to delete this item?"
                                    wire:click="removeItem({{ $this->form->itemIdx }})"
                                    class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2 px-3 py-2">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-md-inline">Remove</span>
                                </button>
                            @endif
                            @if (count($this->form->items) < $this->selectedApplication->storedItems->count())
                                <button type="button" wire:click="addItem"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 px-3 py-2"
                                    style="border-style: dashed; border-width: 2px;">
                                    <i class="bi bi-plus-lg"></i>
                                    <span class="d-none d-md-inline">Add Item</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- Dropdown: Select Item --}}
                        <div class="mb-4">
                            <label for="itemSelect" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Select an Item
                            </label>
                            <select id="itemSelect" wire:model.live="form.itemId"
                                class="form-select form-select-lg shadow-sm">
                                <option value="">-- Choose an item --</option>

                                @php
                                    // We use collect() to make it easy to pluck the 'id' column
                                    $takenIds = collect($this->form->items)
                                        ->pluck('id')
                                        ->filter() // removes null values
                                        ->toArray();
                                @endphp

                                @foreach ($this->selectedApplication->storedItems as $item)
                                    @php
                                        //    - It is in the $takenIds list
                                        //    - AND it is NOT the one currently selected in this specific dropdown
                                        //      (This prevents the current selection from disabling itself)
                                        $isDisabled =
                                            (in_array($item->id, $takenIds) && $item->id != $this->form->itemId) ||
                                            $item->missing_report_id !== null;
                                    @endphp

                                    <option value="{{ $item->id }}" {{ $isDisabled ? 'disabled' : '' }}>
                                        {{ ucfirst($item->item_name) }}
                                        {{-- Optional: Add text to show why it's disabled --}}
                                        {{ $isDisabled ? '(Already Selected)' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('form.itemId')
                                <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                    role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <span wire:loading wire:target='form.itemId'>
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                        {{-- Show Detail of Selected Item --}}
                        @if (!empty($this->form->itemId))
                            <div class="card border-primary shadow-sm animate-fade-in" wire:loading.remove
                                wire:target="form.itemId">
                                <div class="card-header bg-primary bg-opacity-10 border-primary">
                                    <h6 class="mb-0 fw-semibold text-primary">
                                        <i class="bi bi-info-circle me-2"></i>Item Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        {{-- Category --}}
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-grid text-primary me-3 fs-5 mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Category</p>
                                                    <p class="fw-semibold mb-0">
                                                        {{ ucfirst(str_replace('_', ' ', $this->selectedItem->item_type)) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Size --}}
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-rulers text-primary me-3 fs-5 mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Size</p>
                                                    <p class="fw-semibold mb-0">
                                                        {{ ucfirst($this->selectedItem->estimated_size) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Description --}}
                                        <div class="col-12">
                                            <div class="d-flex align-items-start">
                                                <i class="bi bi-text-left text-primary me-3 fs-5 mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Description</p>
                                                    <p class="fw-semibold mb-0">{{ $this->selectedItem->item_name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Photo --}}
                                        <div class="col-12">
                                            <div class="border-top pt-3 mt-2">
                                                <div class="d-flex align-items-start">
                                                    <i class="bi bi-image text-primary me-3 fs-5 mt-1"></i>
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted small mb-2">Item Photo</p>

                                                        @if ($this->selectedItem->item_photo_path)
                                                            <div class="text-center bg-light rounded p-3">
                                                                <img class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 500px; object-fit: contain;"
                                                                    src="{{ \Storage::disk('public')->url($this->selectedItem->item_photo_path) }}"
                                                                    alt="Item Photo">
                                                            </div>
                                                        @else
                                                            {{-- Option to upload item Photo --}}
                                                            <div
                                                                class="upload-area border border-2 border-dashed rounded p-4 text-center bg-light">
                                                                <i
                                                                    class="bi bi-cloud-arrow-up text-muted fs-1 mb-3 d-block"></i>

                                                                <input type="file" id="item_photo"
                                                                    accept=".jpg,.jpeg,.png,.svg" class="d-none"
                                                                    wire:model="form.photo">

                                                                <label for="item_photo" class="mb-0"
                                                                    style="cursor: pointer;">
                                                                    <span
                                                                        class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                                                        <i class="bi bi-upload me-2"></i>
                                                                        Item Photo <span
                                                                            class="fw-normal">(Optional)</span>
                                                                    </span>
                                                                </label>

                                                                <p class="small text-muted mt-3 mb-0">
                                                                    <i class="bi bi-info-circle me-1"></i>
                                                                    Supports .jpg, .png and .svg files (Max: 2MB)
                                                                </p>

                                                                @error('form.photo')
                                                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-3 mb-0"
                                                                        role="alert">
                                                                        <i
                                                                            class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                        <span>{{ $message }}</span>
                                                                    </div>
                                                                @enderror

                                                                {{-- Loading State --}}
                                                                <div wire:loading wire:target="form.photo"
                                                                    class="mt-3">
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-center">
                                                                        <div class="spinner-border spinner-border-sm text-primary me-2"
                                                                            role="status">
                                                                            <span
                                                                                class="visually-hidden">Loading...</span>
                                                                        </div>
                                                                        <span
                                                                            class="text-primary small fw-medium">Uploading
                                                                            photo...</span>
                                                                    </div>
                                                                </div>

                                                                {{-- Photo Preview --}}
                                                                @if ($this->form->photo)
                                                                    <div class="mt-4">
                                                                        <div class="d-inline-block position-relative">
                                                                            <img src="{{ $this->form->photo->temporaryUrl() }}"
                                                                                alt="Preview"
                                                                                class="img-thumbnail shadow-sm"
                                                                                style="width: 150px; height: 150px; object-fit: cover;">
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow"
                                                                                style="width: 30px; height: 30px; padding: 0; margin: -8px;"
                                                                                wire:click="removePhoto">
                                                                                <i class="bi bi-x-lg"></i>
                                                                            </button>
                                                                        </div>
                                                                        <p
                                                                            class="text-success small fw-medium mt-2 mb-0">
                                                                            <i
                                                                                class="bi bi-check-circle-fill me-1"></i>
                                                                            {{ $this->form->photo->getClientOriginalName() }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif {{-- End Selected Item --}}
                    </div>
                </div>

                {{-- Back/Next Button --}}
                <div x-data="{ ok: $wire.entangle('form.itemId') }" class="d-flex justify-content-between gap-3 pt-3 border-top">
                    <button wire:click.prevent="previousStep" type="button"
                        class="btn btn-outline-secondary btn-lg px-5 py-3 d-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        <span>Back</span>
                    </button>

                    <button type="button" wire:click.prevent="nextStep" wire:loading.attr="disabled"
                        class="btn btn-primary btn-lg px-5 py-3 shadow-sm d-flex align-items-center"
                        :disabled="!ok" style="transition: all 0.3s ease;">
                        <i class="bi bi-check-lg me-2 fs-5" wire:loading.remove wire:target='nextStep'></i>
                        <span wire:loading wire:target='nextStep'>
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                        <span>Confirm Selection</span>
                    </button>
                </div>
            </section>

            <style>
                .nav-pills .nav-link {
                    background-color: #f8f9fa;
                    color: #6c757d;
                    border: 1px solid #dee2e6;
                }

                .nav-pills .nav-link:hover {
                    background-color: #e9ecef;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .nav-pills .nav-link.active {
                    background-color: var(--bs-primary);
                    color: white;
                    border-color: var(--bs-primary);
                }

                .form-select:focus {
                    border-color: var(--bs-primary);
                    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
                }

                .btn-outline-primary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
                }

                .btn-outline-secondary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .btn:disabled {
                    cursor: not-allowed;
                    opacity: 0.6;
                }

                .upload-area {
                    transition: all 0.3s ease;
                }

                .upload-area:hover {
                    background-color: #f0f7ff !important;
                    border-color: var(--bs-primary) !important;
                }

                .card {
                    transition: all 0.3s ease;
                }
            </style>
        @elseif ($this->currentStep === 3)
            {{-- Missing Item Details Form --}}
            <section class="py-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            Incident Details
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        {{-- Last Seen Time --}}
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-eye text-primary me-2"></i>
                                When did you last see these items? <span class="text-danger">*</span>
                            </h6>

                            <div x-data="{ option: @entangle('form.seeTime') }" class="ps-3">
                                {{-- Specific Date Option --}}
                                <div class="form-check form-check-custom mb-3">
                                    <input x-model="option" name="seeTime" value="specific_date" id="seetime1"
                                        type="radio" class="form-check-input" style="cursor: pointer;"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                    <label for="seetime1" class="form-check-label fw-medium"
                                        style="cursor: pointer;">
                                        <i class="bi bi-calendar-date me-2"></i>On a specific date
                                    </label>
                                </div>

                                <div class="ms-4 mb-3" x-show="option === 'specific_date'" x-transition>
                                    <input type="date" wire:model="form.seeDate"
                                        :disabled="option !== 'specific_date'"
                                        class="form-control form-control-lg shadow-sm"
                                        :class="{ 'bg-light': option !== 'specific_date' }"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                    @error('form.seeDate')
                                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                            role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                {{-- Time Range Option --}}
                                <div class="form-check form-check-custom mb-3">
                                    <input x-model="option" name="seeTime" value="range" id="seetime2"
                                        type="radio" class="form-check-input" style="cursor: pointer;"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                    <label for="seetime2" class="form-check-label fw-medium"
                                        style="cursor: pointer;">
                                        <i class="bi bi-clock-history me-2"></i>Around this time
                                    </label>
                                </div>

                                <div class="ms-4 mb-3" x-show="option === 'range'" x-transition>
                                    <select wire:model="form.seeRange" :disabled="option !== 'range'"
                                        class="form-select form-select-lg shadow-sm"
                                        :class="{ 'bg-light': option !== 'range' }" wire:loading.attr="disabled"
                                        wire:target="nextStep">
                                        <option value="">-- Select a timeframe --</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This week</option>
                                        <option value="last_week">Last week</option>
                                        <option value="last_month">Last month</option>
                                        <option value="longer">More than a month ago</option>
                                    </select>
                                    @error('form.seeRange')
                                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                            role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                {{-- Don't Remember Option --}}
                                <div class="form-check form-check-custom">
                                    <input x-model="option" name="seeTime" value="dont_remember" id="seetime3"
                                        type="radio" class="form-check-input" style="cursor: pointer;">
                                    <label for="seetime3" class="form-check-label fw-medium" style="cursor: pointer;"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                        <i class="bi bi-question-circle me-2"></i>I don't remember
                                    </label>
                                </div>

                                @error('form.seeTime')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-3 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Last Seen Location --}}
                        <div class="mb-5 pb-4 border-bottom">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                Last Seen Location <span class="text-danger">*</span>
                            </h6>

                            <div class="ps-3">
                                <input wire:model="form.last_seen_location" type="text" id="last_seen_location"
                                    class="form-control form-control-lg shadow-sm"
                                    placeholder="e.g., Storage Room E1-03, E1-06, Locker 12" required
                                    wire:loading.attr="disabled" wire:target="nextStep">
                                <small class="form-text text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Be as specific as possible to help with the investigation
                                </small>
                                @error('form.last_seen_location')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Discovery Time --}}
                        <div class="mb-5 pb-4 border-bottom">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle text-primary me-2"></i>
                                When did you discover the items were missing? <span class="text-danger">*</span>
                            </h6>

                            <div x-data="{ option: @entangle('form.discoverTime') }" class="ps-3">
                                {{-- Specific Date Option --}}
                                <div class="form-check form-check-custom mb-3">
                                    <input x-model="option" name="discoverTime" value="specific_date"
                                        id="discoverTime1" type="radio" class="form-check-input"
                                        style="cursor: pointer;" wire:loading.attr="disabled" wire:target="nextStep">
                                    <label for="discoverTime1" class="form-check-label fw-medium"
                                        style="cursor: pointer;">
                                        <i class="bi bi-calendar-date me-2"></i>On a specific date
                                    </label>
                                </div>

                                <div class="ms-4 mb-3" x-show="option === 'specific_date'" x-transition>
                                    <input type="date" wire:model="form.discoverDate"
                                        :disabled="option !== 'specific_date'"
                                        class="form-control form-control-lg shadow-sm"
                                        :class="{ 'bg-light': option !== 'specific_date' }"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                    @error('form.discoverDate')
                                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                            role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                {{-- Time Range Option --}}
                                <div class="form-check form-check-custom mb-3">
                                    <input x-model="option" name="discoverTime" value="range" id="discoverTime2"
                                        type="radio" class="form-check-input" style="cursor: pointer;"
                                        wire:loading.attr="disabled" wire:target="nextStep">
                                    <label for="discoverTime2" class="form-check-label fw-medium"
                                        style="cursor: pointer;">
                                        <i class="bi bi-clock-history me-2"></i>Around this time
                                    </label>
                                </div>

                                <div class="ms-4 mb-3" x-show="option === 'range'" x-transition>
                                    <select wire:model="form.discoverRange" :disabled="option !== 'range'"
                                        class="form-select form-select-lg shadow-sm"
                                        :class="{ 'bg-light': option !== 'range' }" wire:loading.attr="disabled"
                                        wire:target="nextStep">
                                        <option value="">-- Select a timeframe --</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This week</option>
                                        <option value="last_week">Last week</option>
                                        <option value="last_month">Last month</option>
                                        <option value="longer">More than a month ago</option>
                                    </select>
                                    @error('form.discoverRange')
                                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                            role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                {{-- Don't Remember Option --}}
                                <div class="form-check form-check-custom">
                                    <input x-model="option" name="discoverTime" value="dont_remember"
                                        id="discoverTime3" type="radio" class="form-check-input"
                                        style="cursor: pointer;" wire:loading.attr="disabled" wire:target="nextStep">
                                    <label for="discoverTime3" class="form-check-label fw-medium"
                                        style="cursor: pointer;">
                                        <i class="bi bi-question-circle me-2"></i>I don't remember
                                    </label>
                                </div>

                                @error('form.discoverTime')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-3 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Additional Information --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-chat-left-text text-primary me-2"></i>
                                Additional Information or Witnesses
                            </h6>

                            <div class="ps-3">
                                <textarea wire:model="form.addInfo" name="addtionalinformation" id="additionalinformation" cols="30"
                                    rows="5" wire:loading.attr="disabled" wire:target="nextStep"
                                    placeholder="Helpful details to include:&#10;• Names of potential witnesses&#10;• Suspicious behavior&#10;• Timeline of events"
                                    class="form-control form-control-lg shadow-sm"></textarea>
                                <small class="form-text text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Optional: Include any details that might help locate your items (Max 2000
                                    characters)
                                </small>
                                @error('form.addInfo')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Back/Next Button --}}
                <div class="d-flex justify-content-between gap-3 pt-3 border-top">
                    <button wire:click.prevent="previousStep" type="button"
                        class="btn btn-outline-secondary btn-lg px-5 py-3 d-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        <span>Back</span>
                    </button>

                    <button type="button" wire:click.prevent="nextStep" wire:loading.attr="disabled"
                        class="btn btn-primary btn-lg px-5 py-3 shadow-sm d-flex align-items-center"
                        style="transition: all 0.3s ease;">
                        <i class="bi bi-check-lg me-2 fs-5" wire:loading.remove wire:target='nextStep'></i>
                        <span wire:loading wire:target='nextStep'>
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                        <span>Continue</span>
                    </button>
                </div>
            </section>

            <style>
                .form-check-custom {
                    padding: 0.75rem;
                    border-radius: 0.5rem;
                    transition: all 0.2s ease;
                }

                .form-check-custom:hover {
                    background-color: #f8f9fa;
                }

                .form-check-input {
                    width: 1.25rem;
                    height: 1.25rem;
                    margin-top: 0.125rem;
                }

                .form-check-input:checked {
                    background-color: var(--bs-primary);
                    border-color: var(--bs-primary);
                }

                .form-check-label {
                    margin-left: 0.5rem;
                    user-select: none;
                }

                textarea.form-control {
                    resize: vertical;
                    min-height: 120px;
                }

                .btn-outline-secondary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                [x-cloak] {
                    display: none !important;
                }
            </style>
        @elseif ($this->currentStep === 4)
            {{-- Contact Verification Form --}}
            <section class="py-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="bi bi-person-check text-primary me-2"></i>
                            Verify Your Contact Information
                        </h5>
                        <p class="small text-muted mb-0 mt-1">
                            We'll use this information to contact you about your missing items
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-3 mt-1 fs-5"></i>
                            <div>
                                <strong>Important:</strong> Please ensure your contact information is accurate.
                                We will use these details to notify you of any updates regarding your missing items.
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Email Address --}}
                            <div class="col-md-6">
                                <label for="useremail" class="form-label fw-semibold d-flex align-items-center">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    Email Address
                                </label>
                                <input wire:model="form.userEmail" type="email"
                                    class="form-control form-control-lg shadow-sm" id="useremail"
                                    placeholder="name@example.com" wire:loading.attr="disabled"
                                    wire:target="nextStep">
                                <small class="form-text text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-shield-check me-1"></i>
                                    We'll send updates to this email
                                </small>
                                @error('form.userEmail')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="col-md-6">
                                <label for="userphone" class="form-label fw-semibold d-flex align-items-center">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    Phone Number
                                </label>
                                <input wire:model="form.userPhone" type="tel"
                                    class="form-control form-control-lg shadow-sm" id="userphone"
                                    placeholder="012-3456789" wire:loading.attr="disabled" wire:target="nextStep">
                                <small class="form-text text-muted d-flex align-items-center mt-2">
                                    <i class="bi bi-shield-check me-1"></i>
                                    For urgent notifications
                                </small>
                                @error('form.userPhone')
                                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-2 mb-0"
                                        role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirmation Checkbox --}}
                        <div class="mt-4 pt-4 border-top">
                            <div class="card bg-light border-0">
                                <div class="card-body p-4">
                                    <div class="form-check form-check-custom d-flex align-items-start">
                                        <input wire:model="form.confirmed" class="form-check-input mt-1"
                                            type="checkbox" name="contactinfo" id="contactinfo"
                                            style="cursor: pointer;" wire:loading.attr="disabled"
                                            wire:target="nextStep">
                                        <label class="form-check-label ms-2 fw-medium" for="contactinfo"
                                            style="cursor: pointer;">
                                            I confirm that all information provided is accurate and complete.
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2 ms-4 ps-3">
                                        By checking this box, you verify that the details you've provided in this report
                                        are
                                        truthful
                                        and accurate to the best of your knowledge.
                                    </small>
                                    @error('form.confirmed')
                                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-3 mb-0"
                                            role="alert">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit/Back Buttons --}}
                <div class="d-flex justify-content-between gap-3 pt-3 border-top">
                    <button wire:click.prevent="previousStep" type="button"
                        class="btn btn-outline-secondary btn-lg px-5 py-3 d-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        <span>Back</span>
                    </button>

                    <button wire:click.prevent="nextStep" type="button"
                        class="btn btn-success btn-lg px-5 py-3 d-flex align-items-center shadow-sm position-relative"
                        wire:loading.attr="disabled" wire:target="nextStep"
                        style="min-width: 220px; transition: all 0.3s ease;">
                        <span wire:loading.remove wire:target="nextStep" class="d-flex align-items-center">
                            <i class="bi bi-send-check me-2"></i>
                            Submit Application
                        </span>
                        <span wire:loading wire:target="nextStep">
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            Submitting...
                        </span>
                    </button>
                </div>
            </section>

            <style>
                .form-control,
                .form-select {
                    border: 2px solid #dee2e6;
                    border-radius: 8px;
                    padding: 12px 16px;
                    transition: all 0.3s ease;
                }

                .form-control:focus,
                .form-select:focus {
                    border-color: #667eea;
                    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
                }

                .form-check-custom .form-check-input {
                    width: 1.5rem;
                    height: 1.5rem;
                    border-width: 2px;
                }

                .form-check-custom .form-check-input:checked {
                    background-color: var(--bs-success);
                    border-color: var(--bs-success);
                }

                .form-check-custom:hover {
                    background-color: rgba(13, 110, 253, 0.05);
                    border-radius: 0.5rem;
                    transition: all 0.2s ease;
                }

                .btn-success:not(:disabled):hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px rgba(25, 135, 84, 0.3);
                }

                .btn-outline-secondary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .btn:disabled {
                    cursor: not-allowed;
                    opacity: 0.7;
                }

                .alert {
                    border-left: 4px solid;
                }

                .alert-info {
                    border-left-color: var(--bs-info);
                }

                .alert-danger {
                    border-left-color: var(--bs-danger);
                }

                @keyframes pulse {

                    0%,
                    100% {
                        opacity: 1;
                    }

                    50% {
                        opacity: 0.7;
                    }
                }

                [wire\:loading] {
                    animation: pulse 1.5s ease-in-out infinite;
                }
            </style>
        @endif {{-- End Step 4 --}}
    </fieldset>
    <style>
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

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        .btn-primary:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3) !important;
        }

        .btn-primary:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .card {
            transition: all 0.3s ease;
        }
    </style>
</form>
