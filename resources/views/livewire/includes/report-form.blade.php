<form wire:submit.prevent="" class="card bg-white shadow px-5 py-4 mb-4">
    @for ($i = 0; $i < count($this->form->items); $i++)
        @error('form.items.' . $i . '.id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    @endfor
    @if ($this->currentStep === 1)
        {{-- Select Existing Storage --}}
        <section x-data="{ ok: $wire.entangle('form.storageApplicationId') }">
            <p class="h4 mt-2">My Storage</p>
            <select wire:model="form.storageApplicationId" class="form-select mb-2">
                <option value="">Select Storage</option>
                @forelse ($this->studentApplications as $application)
                <option value="{{ $application->id }}">Storage #{{ ucfirst($application->id) }}</option>
                @empty
                    <p class="mb-0">
                        No Existing Storage
                    </p>
                @endforelse
            </select>

            @if ($this->form->storageApplicationId)
                <div class="border-top border-bottom border-primary bg-primary-subtle bg-opacity-10 p-2">
                    <div class="row">
                        <p class="leading col-3 fw-bold">Storage Room:</p>
                        <p class="col-9">{{ $this->selectedApplication->storageRoom->id }}</p>
                        <p class="leading col-3 fw-bold">Locker:</p>
                        <p class="col-9">{{ $this->selectedApplication->locker->id ?? 'N/A' }}</p>
                        <p class="leading col-3 fw-bold">Open Area:</p>
                        <p class="col-9">{{ $this->selectedApplication->openArea->id ?? 'N/A' }}</p>
                    </div>
                </div>
            @endif

            <button :disabled="!ok" class="btn btn-primary flex-fill py-3 w-100 mt-5"
                wire:loading.attr="disabled" wire:click.prevent="nextStep">
                <span>
                    <i class="bi bi-check-lg me-2" wire:loading.remove wire:target='nextStep'></i>
                    <span wire:loading wire:target='nextStep'>
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    </span>
                    Proceed
                </span>
            </button>
        </section>
    @elseif ($this->currentStep === 2)
        {{-- Item Navigation --}}
        <section>
            @if (count($this->form->items) > 1)
                <div class="mb-4">
                    <ul class="nav nav-tabs" role="tablist" x-data="{ currentIdx: $wire.entangle('form.itemIdx') }">
                        @foreach ($this->form->items as $index => $item)
                            <li class="nav-item" role="presentation">
                                <span wire:click.prevent="switchToItem({{ $index }})" class="nav-link"
                                    :class="{ 'active': currentIdx == {{ $index }} }" style="cursor: pointer">
                                    Item {{ $index + 1 }}
                                    <button type="button" wire:confirm="Are you sure you want to delete this item?"
                                        wire:click.stop.prevent="removeItem({{ $index }})"
                                        class="btn-close btn-close-sm d-inline"></button>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="h4 mt-2">
                    Item
                    {{ count($this->form->items) > 1 ? $this->form->itemIdx + 1 : '' }}
                    Name
                </p>
                {{-- Add/Remove Item --}}
                <div class="d-flex">
                    @if (count($this->form->items) > 1)
                        <button type="button" wire:confirm="Are you sure you want to delete this item?"
                            wire:click="removeItem({{ $this->form->itemIdx }})"
                            class="btn btn-sm d-flex align-items-center justify-content-center btn-outline-danger py-2">
                            <i class="bi bi-trash"></i>
                            Remove This Item
                        </button>
                    @endif
                    <button type="button" wire:click="addItem" style="border: 1.5px rgb(108, 117, 125) dashed;"
                        class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center py-2">
                        <i class="bi bi-plus-lg"></i>
                        Add Another Item
                    </button>
                </div>
            </div>

            {{-- Dropdown: Select Item --}}
            <select wire:model.live="form.itemId" class="form-select mb-2">
                <option value="">Select Item</option>
                @foreach ($this->selectedApplication->storedItems as $item)
                    <option value="{{ $item->id }}">{{ ucfirst($item->item_name) }}</option>
                @endforeach
            </select>

            @error('form.itemId')
                <p class="text-danger mb-0">{{ $message }}</p>
            @enderror
            {{-- Show Detail of Selected Item --}}
            @if (!empty($this->form->itemId))
                <div class="border-top border-bottom border-primary bg-primary-subtle bg-opacity-10 p-2">
                    <div class="row">
                        <p class="leading col-3 fw-bold">Category:</p>
                        <p class="col-9">{{ $this->selectedItem->item_type }}</p>
                        <p class="leading col-3 fw-bold">Description:</p>
                        <p class="col-9">{{ $this->selectedItem->item_name }}</p>
                        <p class="leading col-3 fw-bold">Size:</p>
                        <p class="col-9">{{ $this->selectedItem->estimated_size }}</p>
                        <p class="leading col-3 fw-bold">Photo:</p>
                        @if ($this->selectedItem->item_photo_path)
                            <img class="col-9" style="height:500px; object-fit: contain"
                                src="{{ asset('storage/' . $this->selectedItem->item_photo_path) }}" alt="Item Photo">
                        @else
                            {{-- Option to uplaod item Photo --}}
                            <div class="col-9">
                                <p>N/A</p>
                                <div class="mb-4">
                                    <input type="file" id="item_photo" accept=".jpg,.jpeg,.png,.svg" class="d-none"
                                        wire:model="form.photo">
                                    <label for="item_photo" style="cursor: pointer;">
                                        <span
                                            class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center gap-2"
                                            style="width: fit-content;">
                                            <i class="bi bi-cloud-arrow-up text-light fs-5"
                                                style="vertical-align: middle"></i>
                                            Upload a Photo
                                        </span>
                                    </label>
                                    <p class="small text-muted mt-0">
                                        Only support .jpg, .png and .svg files (Max: 2MB)
                                    </p>
                                    @error('form.photo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div wire:loading wire:target="form.photo" class="mt-2">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm text-primary me-2"
                                                role="status">
                                            </div>
                                            <span class="text-primary small">Uploading photo...</span>
                                        </div>
                                    </div>
                                    {{-- Photo Preview --}}
                                    @if ($this->form->photo)
                                        <div class="mb-3">
                                            <div class="position-relative d-inline-block" style="width: 128px;">
                                                <div style="width: 128px; height: 128px;">
                                                    <img src="{{ $this->form->photo->temporaryUrl() }}"
                                                        alt="Preview" class="img-thumbnail"
                                                        style="width: 128px; height: 128px;">
                                                    <button type="button"
                                                        class="position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fs-6"
                                                        style="width: 23px; height: 23px" wire:click="removePhoto">
                                                        X
                                                    </button>
                                                </div>
                                                <p class="text-success small fw-medium mb-1 text-center"
                                                    style="white-space: nowrap; overflow:visible;">
                                                    {{ $this->form->photo->getClientOriginalName() }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif {{-- End Selected Item --}}

            {{-- Back/Next Button --}}
            <div x-data="{ ok: $wire.entangle('form.itemId') }" class="d-flex justify-content-between pt-4 border-top mt-4">
                <button wire:click.prevent="previousStep" type="button" class="btn btn-outline-secondary px-4">
                    Back
                </button>

                <button type="button" wire:click.prevent="nextStep" class="btn btn-primary px-4 py-2"
                    :disabled="!ok">
                    Confirm Selection
                </button>
            </div>
        </section>
    @elseif ($this->currentStep === 3)
        {{-- Radio 1 --}}
        <section>
            <p class="h4 mt-2">When did you last see these items?</p>
            <div x-data="{ option: @entangle('form.seeTime') }">

                <div class="form-check">
                    <input x-model="option" name="seeTime" value="specific_date" id="seetime1" type="radio"
                        class="form-check-input">
                    <label for="seetime1" class="form-check-label">On a specific date</label>
                </div>
                <!-- Specific Date -->
                <input type="date" wire:model="form.seeDate" :disabled="option !== 'specific_date'"
                    class="form-control mt-2">
                @error('form.seeDate')
                    <div class="text-danger mb-0">{{ $message }}</div>
                @enderror

                <div class="form-check mt-3">
                    <input x-model="option" name="seeTime" value="range" id="seetime2" type="radio"
                        class="form-check-input">
                    <label for="seetime2" class="form-check-label">Around this time</label>
                </div>
                <!-- Approximate range -->
                <select wire:model="form.seeRange" :disabled="option !== 'range'" class="form-control mt-2">
                    <option value="">-- Select one --</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This week</option>
                    <option value="last_week">Last week</option>
                    <option value="last_month">Last month</option>
                    <option value="longer">More than a month ago</option>
                </select>
                @error('form.seeRange')
                    <div class="text-danger mb-0">{{ $message }}</div>
                @enderror

                <!-- I don't remember -->
                <div class="form-check mt-3">
                    <input x-model="option" name="seeTime" value="dont_remember" id="seetime3" type="radio"
                        class="form-check-input">
                    <label for="seetime3" class="form-check-label">I don’t remember</label>
                </div>
            </div>
            @error('form.seeTime')
                <div class="text-danger mb-0">{{ $message }}</div>
            @enderror

            <hr>
            <div class="my-2">
                <p for="last_seen_location" class="h4">Last Seen Location?</p>
                <input wire:model="form.last_seen_location" type="text" id="last_seen_location"
                    class="form-control" placeholder="Enter where the item was last seen..." required>
                <small class="form-text text-muted">
                    Example: Locker A-12, Main Hall, or Reception Counter
                </small>
                @error('form.last_seen_location')
                    <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
            </div>

            <hr>
            {{-- Radio 2 --}}
            <p class="h4 mt-2">When did you discover items was missing?</p>
            <div x-data="{ option: @entangle('form.discoverTime') }">

                <div class="form-check">
                    <input x-model="option" name="discoverTime" value="specific_date" id="discoverTime1"
                        type="radio" class="form-check-input">
                    <label for="discoverTime1" class="form-check-label">On a specific date</label>
                </div>
                <!-- Specific Date -->
                <input type="date" wire:model="form.discoverDate" :disabled="option !== 'specific_date'"
                    class="form-control mt-2">
                @error('form.discoverDate')
                    <div class="text-danger mb-0">{{ $message }}</div>
                @enderror

                <div class="form-check mt-3">
                    <input x-model="option" name="discoverTime" value="range" id="discoverTime2" type="radio"
                        class="form-check-input">
                    <label for="discoverTime2" class="form-check-label">Around this time</label>
                </div>
                <!-- Approximate range -->
                <select wire:model="form.discoverRange" :disabled="option !== 'range'" class="form-control mt-2">
                    <option value="">-- Select one --</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This week</option>
                    <option value="last_week">Last week</option>
                    <option value="last_month">Last month</option>
                    <option value="longer">More than a month ago</option>
                </select>
                @error('form.discoverRange')
                    <div class="text-danger mb-0">{{ $message }}</div>
                @enderror

                <!-- I don't remember -->
                <div class="form-check mt-3">
                    <input x-model="option" name="discoverTime" value="dont_remember" id="discoverTime3"
                        type="radio" class="form-check-input">
                    <label for="discoverTime3" class="form-check-label">I don’t remember</label>
                </div>
            </div>
            @error('form.discoverTime')
                <div class="text-danger mb-0">{{ $message }}</div>
            @enderror

            <hr>

            <p class="h4 mt-2">Relevant Information or Witnesses</p>
            <textarea wire:model="form.addInfo" name="addtionalinformation" id="additionalinformation" cols="30"
                rows="5" placeholder="Information to help with further investigation." class="form-control"></textarea>
            @error('form.addInfo')
                <div class="text-danger mb-0">{{ $message }}</div>
            @enderror

            {{-- Back/Next Button --}}
            <div class="d-flex justify-content-between pt-4 border-top mt-4">
                <button wire:click.prevent="previousStep" type="button" class="btn btn-outline-secondary px-4">
                    Back
                </button>

                <button type="button" wire:click.prevent="nextStep" class="btn btn-primary px-4 py-2">
                    Next
                </button>
            </div>
        </section>
    @elseif ($this->currentStep === 4)
        <section>
            <p class="h4 mt-2">Verify or Update Your Contact Information</p>

            <div class="row">
                <div class="col-6 mb-3">
                    <label for="useremail" class="form-label">Email address</label>
                    <input wire:model="form.userEmail" type="email" class="form-control" id="useremail"
                        placeholder="name@example.com">
                    @error('form.userEmail')
                        <p class="text-danger mb-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-6 mb-3">
                    <label for="userphone" class="form-label">Phone Number:</label>
                    <input wire:model="form.userPhone" type="tel" class="form-control" id="userphone"
                        placeholder="012-1234567">
                    @error('form.userPhone')
                        <p class="text-danger mb-0">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-check">
                <input wire:model="form.confirmed" class="form-check-input" type="checkbox" name="contactinfo"
                    id="contactinfo">
                <label class="form-check-label" for="contactinfo">
                    I confirm that all information provided is accurate.
                </label>
                @error('form.confirmed')
                    <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit/Back Buttons -->
            <div class="d-flex justify-content-between pt-4 border-top mt-4">
                <button wire:click.prevent="previousStep" type="button" class="btn btn-outline-secondary px-4">
                    Back
                </button>

                <button wire:click.prevent="nextStep" type="button"
                    class="btn btn-primary px-5 d-flex align-items-center" wire:loading.attr="disabled"
                    wire:target="submitApplication">
                    <span wire:loading.remove wire:target="submit">Submit Application</span>
                    <span wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submitting...
                    </span>
                </button>
            </div>
        </section>
    @endif {{-- End Step 4 --}}

</form>
