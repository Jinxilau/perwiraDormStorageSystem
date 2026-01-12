<div wire:loading.class="opacity-50" wire:target="nextStep, submit, previousStep, switchToItem">
    {{-- Progress Indicator --}}
    <div class="container mb-2">
        <div class="row justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <ul x-data="{ current: $wire.entangle('currentStep') }"
                    class="steper-step list-unstyled p-0 mb-3 position-relative stepper-vertical d-inline-flex justify-content-center">
                    @for ($i = 1; $i <= $totalSteps; $i++)
                        <li class="stepper position-relative pb-1 d-inline-flex"
                            :class="{
                                'actived': current == {{ $i }},
                                'completed': current > {{ $i }},
                            }">
                            {{-- Stepper Head --}}
                            <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center"
                                style="width: 16vw; min-width: 70px;">
                                {{-- Stepper Icon --}}
                                <span
                                    class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                    @if ($this->currentStep <= $i)
                                        {{ $i }}
                                    @endif
                                </span>
                                <div class="stepper-content py-0">
                                    <p class="text-muted fw-bold mb-0">
                                        @if ($i == 1)
                                            Item Details
                                        @elseif($i == 2)
                                            Room Selection
                                        @else
                                            Review & Submit
                                        @endif
                                    </p>
                                </div>
                            </div>
                            {{-- Stepper Line --}}
                            @if ($i < $totalSteps)
                                <span class="stepper-head-line"></span>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-success bg-opacity-10 border border-success text-success rounded">
            {{ session('success') }}
        </div>
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="alert alert-success mt-4">
                {{ session('message') }}
            </div>
        @endif
    @endif

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- LOADING --}}
    <div wire:loading wire:target="nextStep, submit, previousStep, switchToItem">
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

    <!-- Main Form -->
    <form wire:submit.prevent="submit" class="mb-4">
        <fieldset wire:loading.attr="disabled" wire:target='nextStep, submit, previousStep'>
            <div class="card bg-white shadow px-5 py-4">
                <!-- Step 1: Item Details -->
                @if ($currentStep == 1)
                    <section class="p-4">
                        <div class="item-form-section">
                            {{-- Item Navigation --}}
                            @if (count($this->itemForm->items) > 1)
                                <div class="mb-4">
                                    <ul class="nav nav-pills nav-fill gap-2" role="tablist">
                                        @foreach ($this->itemForm->items as $index => $item)
                                            <li class="nav-item" role="presentation">
                                                <span wire:click.prevent="switchToItem({{ $index }})"
                                                    class="nav-link position-relative d-flex align-items-center justify-content-center py-3 shadow-sm {{ $this->itemForm->currentItemIndex == $index ? 'active' : '' }}"
                                                    style="cursor: pointer; transition: all 0.2s ease;">
                                                    <i class="bi bi-box-seam me-2"></i>
                                                    <span class="fw-semibold">Item {{ $index + 1 }}</span>
                                                    <button type="button"
                                                        wire:confirm="Are you sure you want to delete this item?"
                                                        wire:click.stop.prevent="removeItem({{ $index }})"
                                                        class="btn-close btn-close-sm ms-2 position-absolute end-0 me-2"
                                                        style="font-size: 0.7rem;"></button>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{-- Section Header --}}
                            <div class="section-header bg-light bg-gradient">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="mb-1">
                                            <i class="bi bi-tag text-primary me-2 fw-bold"></i>
                                            Item Details
                                        </h2>
                                        @if (count($this->itemForm->items) > 1)
                                            <p class="mb-0 opacity-90">Item {{ $this->itemForm->currentItemIndex + 1 }}
                                                of
                                                {{ count($this->itemForm->items) }}</p>
                                        @endif
                                    </div>
                                    {{-- Add/Remove Item --}}
                                    <div class="d-flex gap-2">
                                        @if (count($this->itemForm->items) > 1)
                                            <button type="button"
                                                wire:confirm="Are you sure you want to delete this item?"
                                                class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2 px-3 py-2"
                                                wire:click="removeItem({{ $this->itemForm->currentItemIndex }})">
                                                <i class="bi bi-trash"></i>
                                                <span class="d-none d-md-inline">Remove</span>
                                            </button>
                                        @endif
                                        <button type="button" wire:click="addItem"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2 px-3 py-2"
                                            style="border-style: dashed; border-width: 2px;">
                                            <i class="bi bi-plus-lg"></i>
                                            <span class="d-none d-md-inline">Add Item</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- Current Item Form --}}
                            <div class="row mb-4">
                                <!-- Item Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="item_name" class="form-label">Item Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="item_name"
                                        class="form-control @error('itemForm.item_name') is-invalid @enderror"
                                        wire:model="itemForm.item_name" placeholder="Enter item name">
                                    @error('itemForm.item_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Item Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select id="category"
                                        class="form-select @error('itemForm.category') is-invalid @enderror"
                                        wire:model="itemForm.category">
                                        <option value="">Select Category</option>
                                        @foreach ($this->categories as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('itemForm.category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Item Description -->
                            <div class="mb-4">
                                <label for="item_description" class="form-label">
                                    Item Description <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <textarea id="item_description" rows="4"
                                    class="form-control @error('itemForm.item_description') is-invalid @enderror"
                                    placeholder="Provide additional details, brand, model, condition, etc..." wire:model="itemForm.item_description"></textarea>
                                @error('itemForm.item_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Estimated Size -->
                            <div class="mb-4">
                                <label class="form-label mb-3">Estimated Size <span
                                        class="text-danger">*</span></label>
                                <div class="row g-3">
                                    @foreach ($sizes as $size_key => $size_info)
                                        <div class="col-md-4">
                                            <div class="form-check m-0">
                                                <input type="radio" id="{{ $size_key }}" name="estimated_size"
                                                    value="{{ $size_key }}"
                                                    class="form-check-input position-absolute opacity-0"
                                                    wire:model.live="itemForm.estimated_size">
                                                <label for="{{ $size_key }}"
                                                    class="size-card d-block p-4 rounded-3">
                                                    <div class="text-center">
                                                        <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center fs-2"
                                                            style="width: 60px; height: 60px; background-color: var(--bs-{{ $size_info['color'] }}-bg-subtle);">
                                                            {{ $size_info['icon'] }}
                                                        </div>
                                                        <h3 class="h5 mb-1 fw-bold text-dark">
                                                            {{ $size_info['name'] }}
                                                        </h3>
                                                        <div
                                                            class="badge bg-{{ $size_info['color'] }} bg-opacity-10 text-{{ $size_info['color'] }} mb-3">
                                                            {{ $size_info['context'] }}
                                                        </div>
                                                        <div class="text-muted small border-top pt-3 mt-2">
                                                            <span class="fw-semibold text-dark">Common Examples:</span>
                                                            <ul class="list-unstyled mb-0 mt-1">
                                                                @foreach ($size_info['examples'] as $ex)
                                                                    <li class="mb-1">
                                                                        <i
                                                                            class="bi bi-check2 text-{{ $size_info['color'] }}"></i>
                                                                        {{ $ex }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('itemForm.estimated_size')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Item Photo -->
                            <div class="mb-4">
                                <label class="form-label mb-3">
                                    Item Photo <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                @if (
                                    !empty($this->itemForm->item_photo) ||
                                        !empty($this->itemForm->items[$this->itemForm->currentItemIndex]['temp_photo_url']))
                                    <div class="text-center mb-3">
                                        <div class="photo-preview">
                                            <img src="{{ $this->itemForm->item_photo ? $this->itemForm->item_photo->temporaryUrl() : \Storage::disk('public')->url($this->itemForm->items[$this->itemForm->currentItemIndex]['temp_photo_url']) }}"
                                                alt="Preview"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                            <button type="button"
                                                class="remove-photo-btn position-absolute top-0 end-0 bg-danger text-white rounded-circle"
                                                wire:click="removePhoto">
                                                <i class="bi bi-x fs-5"></i>
                                            </button>
                                        </div>
                                        <p class="text-success small fw-medium mt-2 mb-0">
                                            {{ $this->itemForm->item_photo?->getClientOriginalName() ?? 'Uploaded Photo' }}
                                        </p>
                                    </div>
                                @endif
                                <label for="item_photo"
                                    class="upload-zone d-flex flex-column align-items-center justify-content-center rounded-3"
                                    style="cursor: pointer;">
                                    <input type="file" id="item_photo" accept=".jpg,.jpeg,.png,.svg"
                                        class="d-none" wire:model="itemForm.item_photo">
                                    <i class="bi bi-cloud-arrow-up text-primary fs-1 mb-2"></i>
                                    <p class="text-dark mb-1 fw-semibold">Drag your file(s) to start uploading</p>
                                    <p class="text-muted mb-3">OR</p>
                                    <span class="btn btn-primary px-5 py-2 rounded-pill">Browse files</span>
                                    <p class="small text-muted mt-3 mb-0">Only support .jpg, .png and .svg files (Max:
                                        2MB)
                                    </p>
                                </label>
                                @error('itemForm.item_photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div wire:loading wire:target="itemForm.item_photo" class="mt-3">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="spinner-border spinner-border-sm text-primary me-2"
                                            role="status">
                                        </div>
                                        <span class="text-primary small fw-medium">Uploading photo...</span>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <button class="btn btn-primary w-100" wire:loading.attr="disabled"
                                wire:click.prevent="nextStep"
                                x-on:click="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))">
                                <i class="bi bi-check-lg me-2" wire:loading.remove wire:target='nextStep'></i>
                                <span wire:loading wire:target='nextStep'>
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                </span>
                                Proceed with {{ count($this->itemForm->items) }}
                                {{ count($this->itemForm->items) === 1 ? 'Item' : 'Items' }}
                            </button>
                        </div>
                        <style>
                            .item-form-section {
                                margin: 0 auto;
                            }

                            .nav-tabs {
                                border-bottom: 2px solid #e9ecef;
                            }

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

                            .section-header {
                                padding: 24px;
                                border-radius: 12px;
                                margin-bottom: 24px;
                                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            }

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

                            .form-label {
                                font-weight: 600;
                                color: #2d3748;
                                margin-bottom: 8px;
                            }

                            .size-card {
                                cursor: pointer;
                                transition: all 0.3s ease;
                                border: 2px solid #dee2e6 !important;
                                background: white;
                            }

                            .size-card:hover {
                                border-color: #667eea !important;
                                transform: translateY(-4px);
                                box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
                            }

                            .form-check-input:checked~.size-card {
                                border-color: #667eea !important;
                                background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
                                box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
                            }

                            .size-icon {
                                width: 64px;
                                height: 64px;
                                color: white;
                                margin: 0 auto 16px;
                            }

                            .upload-zone {
                                border: 2px dashed #cbd5e0;
                                background: #f8f9fa;
                                padding: 48px 24px;
                                transition: all 0.3s ease;
                            }

                            .upload-zone:hover {
                                border-color: #667eea;
                                background: #f0f2ff;
                            }

                            .photo-preview {
                                position: relative;
                                display: inline-block;
                            }

                            .photo-preview img {
                                border-radius: 12px;
                                border: 3px solid #e9ecef;
                            }

                            .remove-photo-btn {
                                width: 32px;
                                height: 32px;
                                border: none;
                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                                transition: all 0.2s ease;
                            }

                            .remove-photo-btn:hover {
                                transform: scale(1.1);
                            }

                            .btn-primary {
                                /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
                                border: none;
                                border-radius: 12px;
                                padding: 16px 32px;
                                font-weight: 600;
                                font-size: 1.1rem;
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                            }

                            .btn-primary:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
                            }

                            .btn-outline-danger {
                                border-radius: 8px;
                                margin-right: 12px;
                                transition: all 0.3s ease;
                            }

                            .btn-outline-danger:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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
                        </style>
                    </section>
                @endif {{-- End Step 1 --}}
                @if ($currentStep == 2)
                    <section class="p-4">
                        <div class="storage-selection-section">
                            <h2 class="section-title">Storage Selection</h2>
                            <!-- Room Selection -->
                            <div class="mb-5">
                                <h4 class="subsection-title">Choose Your Storage Room</h4>
                                <div class="row g-4">
                                    @forelse ($this->storageRooms as $room)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="room-card {{ $this->roomForm->selectedRoom == $room['id'] ? 'selected' : '' }}"
                                                wire:click="selectRoom({{ $room['id'] }})"
                                                style="cursor: pointer;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <h5 class="card-title mb-0">
                                                                <i class="bi bi-geo-alt-fill me-1 text-muted"></i>
                                                                {{ $room['room_name'] }}
                                                            </h5>
                                                        </div>
                                                        <span
                                                            class="badge {{ $room['is_full'] ? 'bg-danger' : 'bg-success' }}">
                                                            {{ $room['is_full'] ? 'Full' : 'Available' }}
                                                        </span>
                                                    </div>
                                                    <hr class="my-3">
                                                    <p class="mb-2">
                                                        <strong class="text-dark">Lockers Available:</strong>
                                                        <span class="text-muted">
                                                            {{ $room['available_lockers'] }}
                                                        </span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <strong class="text-dark">Open Area:</strong>
                                                        <span class="text-muted">
                                                            {{ $room['hasOpenAreaSpace'] ? 'Available' : 'Full' }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="no-rooms-message">
                                                <i class="bi bi-x-octagon-fill d-block"></i>
                                                <h4>No Storage Rooms Available</h4>
                                                <p class="text-muted">Please check back later or contact support.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @if ($this->roomForm->selectedRoom)
                                <hr class="my-5">
                                <!-- Storage Type Selection -->
                                <div class="mb-4">
                                    <h4 class="subsection-title">
                                        {{ collect($this->storageRooms)->firstWhere('id', $this->roomForm->selectedRoom)['room_name'] ?? 'Selected Room' }}
                                    </h4>
                                    <div class="alert-info-custom mb-4">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                                            <div>
                                                <strong>Important:</strong> Lockers are recommended for electronics,
                                                documents,
                                                and valuable items.
                                                <br>
                                                <strong>Locker Dimensions:</strong> {{ $this->lockerSize }}
                                            </div>
                                        </div>
                                    </div>
                                    <fieldset>
                                        <legend class="h5 fw-bold text-dark mb-4">Select Storage Type</legend>
                                        {{-- Open Area --}}
                                        <div class="storage-type-box">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center flex-grow-1">
                                                    <i class="bi bi-grid-3x3-gap fs-4 text-primary me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2">Open Area Storage</h5>
                                                        <div class="form-check">
                                                            <input id="openAreaOption" class="form-check-input"
                                                                type="radio" name="storageType" value="open_area"
                                                                wire:model.live="roomForm.selectedStorageType">
                                                            <label for="openAreaOption" class="form-check-label">
                                                                Use the floor space for general storage.
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Locker --}}
                                        <div class="storage-type-box">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center flex-grow-1">
                                                    <i class="bi bi-lock-fill fs-4 text-success me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2">Secure Locker</h5>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="storageType" value="locker" id="lockerOption"
                                                                wire:model.live="roomForm.selectedStorageType">
                                                            <label class="form-check-label" for="lockerOption">
                                                                I want to use a secure locker for valuable items
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($this->roomForm->selectedStorageType === 'locker')
                                                <!-- Legend -->
                                                <div class="d-flex gap-4 mb-3 flex-wrap">
                                                    <div class="legend-item">
                                                        <div class="legend-dot"
                                                            style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                                                        </div>
                                                        Available
                                                    </div>
                                                    <div class="legend-item">
                                                        <div class="legend-dot"
                                                            style="background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);">
                                                        </div>
                                                        In Maintenance
                                                    </div>
                                                    <div class="legend-item">
                                                        <div class="legend-dot"
                                                            style="background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);">
                                                        </div>
                                                        Not Available
                                                    </div>
                                                </div>
                                                <!-- Locker Grid -->
                                                <div class="locker-grid">
                                                    @foreach ($this->roomForm->availableLockers as $locker)
                                                        <div class="locker-item
                                                            {{ $locker['status'] === 'available' ? 'available' : '' }}
                                                            {{ $locker['status'] === 'under_maintenance' ? 'maintenance' : '' }}
                                                            {{ $locker['status'] === 'occupied' ? 'occupied' : '' }}
                                                            {{ $locker['status'] === 'reserved' ? 'occupied' : '' }}
                                                            {{ $this->roomForm->selectedLockerId == $locker['id'] ? 'selected' : '' }}"
                                                            @if ($locker['status'] === 'available') wire:click="selectLocker({{ $locker['id'] }})" @endif>
                                                            {{ $locker['locker_number'] }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </fieldset>
                                </div>
                                {{-- Navigation Buttons --}}
                                <div class="d-flex justify-content-between pt-4 border-top mt-5">
                                    <button wire:click.prevent="previousStep" type="button"
                                        class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back
                                    </button>
                                    <button type="button" wire:click.prevent="nextStep" class="btn btn-primary"
                                        {{ !$this->roomForm->selectedStorageType || ($this->roomForm->selectedStorageType === 'locker' && !$this->roomForm->selectedLockerId) ? 'disabled' : '' }}>
                                        Confirm Selection<i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <style>
                            .storage-selection-section {
                                margin: 0 auto;
                            }

                            .section-title {
                                font-size: 2rem;
                                font-weight: 700;
                                color: #1a202c;
                                margin-bottom: 2rem;
                                text-align: center;
                            }

                            .subsection-title {
                                font-size: 1.5rem;
                                font-weight: 600;
                                color: #2d3748;
                                margin-bottom: 1.5rem;
                                text-align: center;
                            }

                            .room-card {
                                border-radius: 16px;
                                transition: all 0.3s ease;
                                border: 3px solid #e2e8f0;
                                background: white;
                                overflow: hidden;
                            }

                            .room-card:hover {
                                transform: translateY(-8px);
                                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
                                border-color: #cbd5e0;
                            }

                            .room-card.selected {
                                border-color: #667eea;
                                background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
                                box-shadow: 0 12px 24px rgba(102, 126, 234, 0.25);
                            }

                            .room-card .card-body {
                                padding: 1.5rem;
                            }

                            .room-card .card-title {
                                font-size: 1.25rem;
                                font-weight: 700;
                                color: #1a202c;
                                margin-bottom: 0.5rem;
                            }

                            .badge {
                                font-size: 0.75rem;
                                font-weight: 600;
                                padding: 0.4rem 0.8rem;
                                border-radius: 20px;
                            }

                            .badge.bg-success {
                                background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
                            }

                            .badge.bg-danger {
                                background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
                            }

                            .storage-type-box {
                                border: 2px solid #cbd5e0;
                                border-radius: 12px;
                                padding: 1.5rem;
                                margin-bottom: 1rem;
                                background: white;
                                transition: all 0.3s ease;
                            }

                            .storage-type-box:hover {
                                border-color: #667eea;
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
                            }

                            .storage-type-box h5 {
                                font-size: 1.25rem;
                                font-weight: 700;
                                color: #2d3748;
                                margin-bottom: 1rem;
                            }

                            .form-check-input {
                                width: 1.25rem;
                                height: 1.25rem;
                                border: 2px solid #cbd5e0;
                                cursor: pointer;
                            }

                            .form-check-input:checked {
                                background-color: #667eea;
                                border-color: #667eea;
                            }

                            .form-check-label {
                                font-size: 1rem;
                                color: #4a5568;
                                cursor: pointer;
                                margin-left: 0.5rem;
                            }

                            .alert-info-custom {
                                background: linear-gradient(135deg, #e6fffa 0%, #b2f5ea 100%);
                                border: 2px solid #81e6d9;
                                border-radius: 12px;
                                padding: 1rem 1.5rem;
                                color: #234e52;
                            }

                            .legend-item {
                                display: inline-flex;
                                align-items: center;
                                font-size: 0.9rem;
                                font-weight: 500;
                                color: #4a5568;
                            }

                            .legend-dot {
                                width: 18px;
                                height: 18px;
                                border-radius: 50%;
                                margin-right: 0.5rem;
                                border: 2px solid rgba(255, 255, 255, 0.3);
                            }

                            .locker-grid {
                                display: grid;
                                grid-template-columns: repeat(10, 1fr);
                                gap: 0.5rem;
                                margin-top: 1rem;
                            }

                            .locker-item {
                                aspect-ratio: 1 / 1;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                border-radius: 8px;
                                font-weight: 700;
                                font-size: 0.85rem;
                                color: white;
                                transition: all 0.2s ease;
                                border: 2px solid transparent;
                            }

                            .locker-item.available {
                                background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
                                cursor: pointer;
                            }

                            .locker-item.available:hover {
                                transform: scale(1.1);
                                box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
                            }

                            .locker-item.selected {
                                border: 3px solid #667eea;
                                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
                                transform: scale(1.05);
                            }

                            .locker-item.maintenance {
                                background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
                                opacity: 0.7;
                                cursor: not-allowed;
                            }

                            .locker-item.occupied {
                                background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
                                opacity: 0.7;
                                cursor: not-allowed;
                            }

                            .btn-primary {
                                border: none;
                                border-radius: 10px;
                                padding: 12px 32px;
                                font-weight: 600;
                                font-size: 1rem;
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
                            }

                            .btn-primary:hover:not(:disabled) {
                                transform: translateY(-2px);
                                box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
                            }

                            .btn-primary:disabled {
                                opacity: 0.5;
                                cursor: not-allowed;
                            }

                            .btn-outline-secondary {
                                border: 2px solid #cbd5e0;
                                border-radius: 10px;
                                padding: 12px 32px;
                                font-weight: 600;
                                color: #4a5568;
                                transition: all 0.3s ease;
                            }

                            .btn-outline-secondary:hover {
                                background: #f7fafc;
                                border-color: #a0aec0;
                                transform: translateY(-2px);
                            }

                            .no-rooms-message {
                                text-align: center;
                                padding: 3rem 1rem;
                            }

                            .no-rooms-message i {
                                font-size: 4rem;
                                color: #fc8181;
                                margin-bottom: 1rem;
                            }

                            .no-rooms-message h4 {
                                font-size: 1.5rem;
                                color: #4a5568;
                                font-weight: 600;
                            }
                        </style>
                    </section>
                @endif {{-- Step 2 End --}}
                @if ($currentStep == 3)
                    <section class="container py-3">
                        <!-- Header -->
                        <div class="border-bottom pb-4 mb-5">
                            <h1 class="h3 fw-bold text-dark mb-2">Review & Submit Application</h1>
                            <p class="text-muted mb-0">Please review all information before submitting your application
                            </p>
                        </div>
                        <!-- Student Information -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z" />
                                        </svg>
                                    </div>
                                    <h2 class="h5 fw-semibold text-dark mb-0">Student Information</h2>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Name</small>
                                            <span class="fw-medium">{{ $applicantData['name'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Matric Card</small>
                                            <span class="fw-medium">{{ $applicantData['matric_card'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Study Year &
                                                Semester</small>
                                            <span class="fw-medium">{{ $applicantData['study_year_semester'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Gender</small>
                                            <span class="fw-medium">{{ ucfirst($applicantData['gender']) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Phone Number</small>
                                            <span class="fw-medium">{{ $applicantData['phone'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Email</small>
                                            <span class="fw-medium">{{ $applicantData['email'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Registered Items -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="text-success" viewBox="0 0 16 16">
                                            <path
                                                d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                        </svg>
                                    </div>
                                    <h2 class="h5 fw-semibold text-dark mb-0">Registered Items</h2>
                                </div>
                                <div class="table-responsive border rounded shadow-sm">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4"
                                                    style="font-size: 0.75rem;">
                                                    Item Name
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="font-size: 0.75rem;">
                                                    Category
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="font-size: 0.75rem;">
                                                    Size
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="font-size: 0.75rem;">
                                                    Description
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                    style="width: 100px; font-size: 0.75rem;">
                                                    Photo
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            {{-- Item Details --}}
                                            @forelse ($this->itemForm->items as $item)
                                                <tr>
                                                    {{-- Item Name --}}
                                                    <td class="ps-4">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm fw-bold text-dark">
                                                                {{ $item['item_name'] }}</h6>
                                                        </div>
                                                    </td>

                                                    {{-- Category --}}
                                                    <td>
                                                        <span class="badge bg-light text-dark border fw-normal">
                                                            {{ $item['category'] }}
                                                        </span>
                                                    </td>

                                                    {{-- Size --}}
                                                    <td>
                                                        <span class="text-secondary text-sm font-weight-bold">
                                                            {{ $item['estimated_size'] }}
                                                        </span>
                                                    </td>

                                                    {{-- Description --}}
                                                    <td>
                                                        <span
                                                            class="text-secondary text-sm d-inline-block text-truncate"
                                                            style="max-width: 150px;">
                                                            {{ $item['item_description'] ?: '-' }}
                                                        </span>
                                                    </td>

                                                    {{-- Photo --}}
                                                    <td>
                                                        @if (!empty($item['item_photo']) || !empty($item['temp_photo_url']))
                                                            {{-- LOGIC: Check New Upload vs Saved DB Image --}}
                                                            @php
                                                                $src = '';
                                                                if (
                                                                    is_object($item['item_photo']) &&
                                                                    method_exists($item['item_photo'], 'temporaryUrl')
                                                                ) {
                                                                    $src = $item['item_photo']->temporaryUrl();
                                                                } else {
                                                                    $src =
                                                                        \Storage::disk('public')->url(
                                                                            $item['temp_photo_url'],
                                                                        ) ?? null;
                                                                }
                                                            @endphp

                                                            @if ($src)
                                                                <img src="{{ $src }}"
                                                                    class="avatar avatar-sm rounded border"
                                                                    alt="Item"
                                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                            @endif
                                                        @else
                                                            {{-- Placeholder for No Image --}}
                                                            <div class="d-flex align-items-center justify-content-center bg-light rounded border text-secondary"
                                                                style="width: 60px; height: 60px;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" fill="currentColor"
                                                                    class="bi bi-image" viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                                    <path
                                                                        d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        No items added yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Storage Selection -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" class="text-warning" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                                        </svg>
                                    </div>
                                    <h2 class="h5 fw-semibold text-dark mb-0">Storage Selection</h2>
                                </div>
                                <div class="row g-4">
                                    <div class="d-flex flex-column mb-3 col-md-6">
                                        <small class="text-muted text-uppercase mb-1"
                                            style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            Selected Room
                                        </small>
                                        <span class="fw-medium">{{ $this->roomForm->room->room_name }}</span>
                                    </div>
                                    @if ('locker' === $this->roomForm->selectedStorageType)
                                        <div class="d-flex flex-column col-md-6">
                                            <small class="text-muted text-uppercase mb-1"
                                                style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                Selected Locker
                                            </small>
                                            <span class="fw-medium">{{ $this->roomForm->locker->code }}</span>
                                        </div>
                                    @endif
                                    @if ('open_area' === $this->roomForm->selectedStorageType)
                                        <div class="d-flex flex-column col-md-6">
                                            <span class="text-uppercase mb-1 fw-medium">
                                                Open Area
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (empty($this->application))
                            <!-- Terms and Conditions -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" class="text-info" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path
                                                    d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                            </svg>
                                        </div>
                                        <h2 class="h5 fw-semibold text-dark mb-0">Terms and Conditions</h2>
                                    </div>
                                    <div class="bg-light border rounded p-4 mb-3"
                                        style="max-height: 200px; overflow-y: auto;">
                                        <div class="term">
                                            <div class="term-title">
                                                1. Eligibility
                                            </div>
                                            <ul>
                                                <li>Storage service is available only for:</li>
                                                <ul>
                                                    <li>New students (February 2025 intake) residing in Perwira
                                                        Residential
                                                        College.
                                                    </li>
                                                    <li>Senior students who have received accommodation approval and
                                                        completed
                                                        pre-registration for Semester 1, Session 2025/2026 via HOMS.
                                                    </li>
                                                </ul>
                                                <li>Storage under another person’s name is strictly prohibited.</li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                2. Storage Rules
                                            </div>
                                            <ul>
                                                <li>Each student may store up to three (3) boxes or bags only.</li>
                                                <li>All items must be recorded in the official Storage Record Book for
                                                    Semester
                                                    II,
                                                    Session 2024/2025.</li>
                                                <li>Items must not include:</li>
                                                <ul>
                                                    <li>Food or perishable goods</li>
                                                    <li>Illegal or dangerous materials</li>
                                                    <li>Flammable, explosive, or hazardous materials</li>
                                                    <li>Valuable items (cash, jewelry, electronics) — storage of such
                                                        items
                                                        is
                                                        at
                                                        your own risk.</li>
                                                </ul>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                3. Storage and Retrieval Schedule
                                            </div>
                                            <ul>
                                                <li><strong>Storage Period:</strong> 21 – 25 July 2025 (9:00 a.m.–11:00
                                                    a.m.
                                                    &
                                                    2:30
                                                    p.m.–4:00 p.m.)</li>
                                                <li><strong>Retrieval Period:</strong> 4 – 10 October 2025 (refer to
                                                    detailed
                                                    schedule on noticeboard or PDSS system).</li>
                                                <li>Storage outside these periods requires prior arrangement with KKLK
                                                    staff
                                                    or
                                                    the
                                                    Student Leadership Council (MKP).</li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                4. Liability
                                            </div>
                                            <ul>
                                                <li>The management will not be held responsible for any damage or loss
                                                    of
                                                    items
                                                    during the storage period.</li>
                                                <li>Storage is <strong>at the student’s own risk.</strong></li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                5. Storage Allocation
                                            </div>
                                            <ul>
                                                <li>Storage space will be assigned based on availability and item size
                                                    category
                                                    (Small / Medium / Large).</li>
                                                <li>Students must follow staff instructions during check-in and
                                                    check-out.
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                6. Unclaimed Items
                                            </div>
                                            <ul>
                                                <li>Any items not collected within one (1) week after Semester 1,
                                                    Session
                                                    2025/2026
                                                    begins will be disposed of by the management.</li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                7. QR Code and Check-In Procedure
                                            </div>
                                            <ul>
                                                <li>A QR code will be generated after approval and must be presented
                                                    during
                                                    check-in.</li>
                                                <li>The QR code is non-transferable and tied to your registration
                                                    record.
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                9. Violation of Terms
                                            </div>
                                            <ul>
                                                <li>Violation of any term may result in cancellation of storage
                                                    privileges
                                                    or
                                                    disciplinary action as per college regulations.</li>
                                            </ul>
                                        </div>
                                        <div class="term">
                                            <div class="term-title">
                                                8. Acknowledgement
                                            </div>
                                            <ul>
                                                <li>By checking the agreement box and submitting this form, you confirm
                                                    that
                                                    you
                                                    have read, understood, and agreed to these terms.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-check p-3 bg-light rounded">
                                        <input class="form-check-input" type="checkbox" id="terms"
                                            wire:model="termsAccepted" style="width: 1.25rem; height: 1.25rem;">
                                        <label class="form-check-label ms-2 fw-medium" for="terms"
                                            style="cursor: pointer;">
                                            I have read and agree to all terms and conditions
                                        </label>
                                    </div>
                                    @error('termsAccepted')
                                        <div class="alert alert-danger mt-3 mb-0 d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                                <path
                                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <!-- Important Notes -->
                        <div class="alert alert-info border-0 shadow-sm d-flex">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="text-info" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="fw-bold text-info mb-2">
                                    📧 You will receive a notification via email and in the system within 24–48 hours
                                    regarding
                                    approval status. If approved, a QR code will be generated and sent to you.
                                </div>
                                <div class="text-muted small">
                                    <strong>Note:</strong> Please store your items before the specified date.
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-4 border-top mt-4">
                            <!-- Back Button -->
                            <button wire:click.prevent="previousStep" type="button"
                                class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                                </svg>
                                Back
                            </button>
                            <!-- Submit Button -->
                            @if (empty($this->application))
                                <button wire:click.prevent="submit" type="button"
                                    class="btn btn-primary px-5 py-2 d-flex align-items-center shadow-sm"
                                    wire:loading.attr="disabled" wire:target="submit">
                                    <span wire:loading.remove wire:target="submit">
                                        <div class="d-flex align-items-center">
                                            Submit Application
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                                            </svg>
                                        </div>
                                    </span>
                                    <span wire:loading wire:target="submit">
                                        <div class="d-flex align-items-center">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                                aria-hidden="true"></span>
                                            Submitting...
                                        </div>
                                    </span>
                                </button>
                            @else
                                <button wire:click.prevent="update" type="button"
                                    class="btn btn-primary px-5 py-2 d-flex align-items-center shadow-sm"
                                    wire:loading.attr="disabled" wire:target="update">

                                    <span wire:loading.remove wire:target="update">
                                        <div class="d-flex align-items-center">
                                            Edit Application
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square ms-2"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                            </svg>
                                        </div>
                                    </span>

                                    <span wire:loading wire:target="update">
                                        <div class="d-flex align-items-center">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                                aria-hidden="true"></span>
                                            Loading...
                                        </div>
                                    </span>
                                </button>
                            @endif
                        </div>
                    </section>
                @endif
            </div>
        </fieldset>
    </form>
</div>

@assets
    <style>
        .stepper-head-icon {
            width: 2rem;
            height: 2rem;
            background-color: #dee2e6;
            color: rgb(255, 255, 255);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .stepper.actived .stepper-head-icon {
            background-color: #0d6efd;
            color: white;
            transform: scale(1.15) rotate(-2deg);
        }

        .stepper.completed .stepper-head-icon {
            background-color: #1c9a00 !important;
            color: white;
        }

        .stepper.completed .stepper-head-icon::after {
            content: '✓';
            font-size: 20px;
        }

        .stepper-content {
            /* margin-left: 3rem; */
            /* margin-top: -0.2rem; */
            width: 100%;
            height: 1rem;
        }

        .stepper-head-line {
            position: absolute;
            top: 1rem;
            left: 50%;
            width: 100%;
            height: 2px;
            z-index: 1;
            background-color: #dee2e6;
            /* margin-right: 1rem; */
        }

        /* .stepper.actived .stepper-head-line:not(:last-child) {
                                background-color: #0d6efd;
                            } */
        .stepper.completed .stepper-head-line {
            background-image: linear-gradient(90deg, #1c9a00 20%, #5675ff 100%);
        }

        /* SIZE INPUT */
        .hover-border-primary:hover {
            border-color: var(--bs-primary) !important;
            cursor: pointer;
        }

        .transition-all {
            transition: all 0.2s ease;
        }
    </style>
@endassets
