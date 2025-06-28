<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @if($showSuccessMessage)
    <div class="text-center py-5">
        <div class="mx-auto mb-4 bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="fas fa-check text-success fs-3"></i>
        </div>
        <h2 class="h3 fw-bold mb-3">Booking Submitted Successfully!</h2>
        <p class="text-muted mb-4">Thank you for choosing Calystra Studio. We'll contact you within 24 hours to confirm your booking.</p>
        <button wire:click="resetForm" class="btn px-4 text-light" style="background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">Make Another Booking</button>
    </div>
    @else
    {{-- Progress Indicator --}}
    <div class="container mb-2">
        <div class="row justify-content-center">
            <div class="col-lg-8 d-flex justify-content-center">
                <ul class="steper-step list-unstyled p-0 m-0 position-relative stepper-vertical d-inline-flex justify-content-center">
                    @for($i = 1; $i <= $totalSteps; $i++)
                    <li class="stepper position-relative pb-1 {{ $currentStep >= $i ? 'actived' : '' }} {{ $i < $currentStep ? 'completed' : '' }} d-inline-flex">
                        {{-- Stepper Head --}}
                        <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center" style="width: 12vw; min-width: 70px;">
                            {{-- Stepper Icon --}}
                            <span class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                {{ $i }}
                            </span>
                            <div class="stepper-content py-0">
                                <p class="text-muted small mb-0">
                                    @if($i == 1) Item Details
                                    @elseif($i == 2) Room Selection
                                    @else Review & Submit
                                    @endif
                                </p>
                            </div>
                        </div>
                        {{-- Stepper Line --}}
                        @if($i < $totalSteps)
                        <span class="stepper-head-line"></span>
                        @endif
                    </li>
                    @endfor
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-3">
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

        <form wire:submit.prevent="submit" class="mb-4">
            <div class="card bg-white shadow px-5 py-4">
            <!-- Step 1: Item Details -->
                @if($currentStep == 1)
                {{-- Item Tabs (if more than one item) --}}
                @if(count($items) > 1)
                <div class="mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($items as $index => $item)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex {{ $currentItemIndex == $index ? 'active' : '' }}" wire:click="switchToItem({{ $index }})" type="button">
                                <span>
                                    Item {{ $index + 1 }}
                                    @if(count($items) > 1)
                                    <button class="btn-close btn-close-sm d-inline" onclick="event.stopPropagation();" type="button" wire:click="removeItem({{ $index }})"></button>
                                    @endif
                                </span>

                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Current Item Form --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="text-dark mb-0">
                        Item Details {{ count($items) > 1 ? '(' . ($currentItemIndex + 1) . ' of ' . count($items) . ')' : '' }}
                    </h2>
                    <div class="d-flex">
                        @if(count($items) > 1)
                        <button type="button" wire:click="removeItem({{ $currentItemIndex }})" class="btn btn-sm d-flex align-items-center justify-content-center btn-outline-danger py-2">
                            <i class="bi bi-trash"></i> 
                            Remove This Item
                        </button>
                        @endif
                        <!-- Add Item Button -->
                        <button type="button" wire:click="addItem" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center py-2" style="border: 1.5px rgb(108, 117, 125) dashed;">
                            <i class="bi bi-plus-lg"></i>
                            Add Another Item
                        </button>
                    </div>
                </div>
            
                <div class="row mb-4">
                    <!-- Item Name -->
                    <div class="col-md-6 mb-3">
                        <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" id="item_name" wire:model.live="item_name" class="border border-dark form-control @error('item_name') is-invalid @enderror">
                        @error('item_name') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <!-- Item Category -->
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select id="category" wire:model.live="category" class="border border-dark form-select @error('category') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                <!-- Item Description -->
                <div class="mb-4">
                    <label for="item_description" class="form-label">Item Description <span class="text-muted">(Optional)</span></label>
                    <textarea id="item_description" wire:model.live="item_description" rows="3" class="border border-dark form-control @error('item_description') is-invalid @enderror" placeholder="Provide additional details, brand, model, condition, etc..."></textarea>
                    @error('item_description') 
                    <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                </div>

                <!-- Estimated Size -->
                <div class="mb-1">
                    <label class="form-label mb-3">Estimated Size <span class="text-danger">*</span></label>
                    <div class="row g-4">
                        @foreach($sizes as $size_key => $size_info)
                        <div class="col-md-4">
                            <div class="form-check m-0">
                                <input type="radio" id="{{ $size_key }}" name="estimated_size" value="{{ $size_key }}" wire:model.live="estimated_size" class="form-check-input position-absolute opacity-0">
                                <label for="{{ $size_key }}" class="border border-dark d-block p-4 h-100 border rounded-3 transition-all border-light hover-border-primary @if($estimated_size == $size_key) 'border-primary bg-primary bg-opacity-10 shadow-sm' @else '' @endif">
                                    <div class="text-center">
                                        <div class="w-16 h-16 rounded-circle mb-3 d-flex align-items-center justify-content-center fs-3 bg-secondary @if($estimated_size == $size_key) 'bg-primary bg-opacity-25' @else ''@endif">
                                            {{ $size_info['icon'] }}
                                        </div>
                                        <h3 class="h4 mb-2 {{ $estimated_size == $size_key ? 'text-primary' : 'text-dark' }}">
                                            {{ $size_info['name'] }}
                                        </h3>
                                        <p class="small {{ $estimated_size == $size_key ? 'text-primary' : 'text-muted' }}">
                                            {{ $size_info['description'] }}
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('estimated_size') 
                        <div class="invalid-feedback d-block">{{ $message }}</div> 
                    @enderror
                </div>

                <!-- Item Photo -->
                <div class="mb-4">
                    <p class="form-label mb-2">Item Photo <span class="text-muted">(Optional)</span></p>
                    @if($item_photo)
                    <div class="d-flex flex-column align-items-center justify-content-center mb-3">
                        <div class="position-relative d-inline-block">
                            <div style="width: 128px; height: 128px;">
                                <img src="{{ $item_photo->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="width: 128px; height: 128px;">
                                <button type="button" wire:click="removePhoto" class="position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fs-6" style="width: 23px; height: 23px">
                                    X
                                </button>
                            </div>
                            <p class="text-success small fw-medium mb-1">{{ $item_photo->getClientOriginalName() }}</p>
                        </div>
                    </div>
                    @endif
                    <label for="item_photo" class="d-flex flex-column align-items-center justify-content-center w-100 rounded-3 bg-light hover-bg-light p-0" style="cursor: pointer; border: 1.5px rgb(52, 63, 75) dashed;">
                        <input type="file" id="item_photo" wire:model="item_photo" accept=".jpg,.jpeg,.png,.svg" class="d-none">
                        <i class="bi bi-cloud-arrow-up text-muted fs-3"></i>
                        <p class="text-muted mb-0">Drag your file(s) to start uploading</p>
                        <p class="text-muted mb-0">OR</p>
                        <span class="btn btn-primary px-4 py-2 rounded-pill">Browse files</span>
                        <p class="small text-muted mt-0">Only support .jpg, .png and .svg files (Max: 2MB)</p>
                    </label>
                    @error('item_photo') 
                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                    @enderror
                    <div wire:loading wire:target="item_photo" class="mt-2">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span class="text-primary small">Uploading photo...</span>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <button class="btn btn-primary flex-fill py-3 w-100" wire:loading.attr="disabled" wire:click.prevent="nextStep">
                    <span>
                        <i class="bi bi-check-lg me-2" wire:loading.remove wire:target='nextStep'></i>
                        <span wire:loading wire:target='nextStep'>
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        </span>
                        Proceed {{ count($items) > 1 ? 'with ' . count($items) . ' Items' : 'with ' . count($items) .' '. 'Item' }}
                    </span>
                </button>
                @endif
                
                @if($currentStep == 2)
                <h2 class="mb-3 text-dark">Storage Selection</h2>

                <!-- Room Selection -->
                <div class="mb-5">
                    <h4 class="fw-semibold text-dark mb-4 text-center">Room Selection</h4>

                    {{-- {{ dd($this->storageRooms) }} --}}
                    <div class="row g-4 justify-content-center align-items-center gap-5">
                        @foreach($storageRooms as $room)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-2 {{ $selectedRoom == $room['id'] ? 'border-primary bg-light' : 'border-secondary' }} cursor-pointer" wire:click="selectRoom({{ $room['id'] }})" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title">{{ $room['room_name'] }}</h5>
                                        <span class="badge {{ $room['is_full'] ? 'bg-danger' : 'bg-success' }}">
                                            {{ $room['is_full'] ? 'Full' : 'Available' }}
                                        </span>
                                    </div>
                                    <p class="card-text text-muted">{{ $room['storage_location'] }}</p>
                                    <hr>
                                    <p class="mb-2 text-muted"><strong>Locker:</strong> {{ $room['available_lockers'] }} Available</p>
                                    <p class="mb-0 text-muted"><strong>Operating Hours:</strong></p>
                                    <p class="mb-0 text-muted">Morning: 9:00 AM - 11:00 AM</p>
                                    <p class="mb-0 text-muted">Evening: 9:00 PM - 11:00 PM</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($selectedRoom)
                <hr>

                <!-- Storage Type Selection -->
                <div class="mb-4">
                
                    <h4 class="mb-4 text-center">
                        {{ collect($storageRooms)->firstWhere('id', $selectedRoom)['room_name'] ?? 'Selected Room' }}
                    </h4>

                    <div class="alert alert-secondary mb-4">
                        <small>
                            <strong>Note:</strong> Lockers are recommended for electronics, documents, and valuable items.<br>
                            <strong>Size of Locker:</strong> 45×90×55cm (120-150 liters)
                        </small>
                    </div>

                    <fieldset>
                        <legend>Select Storage Type (Multiple)</legend>
                        <div class="border border-dark p-2">
                            <h5>Open Area</h5>
                            <div class="form-check mb-3 ms-4">
                                <input class="form-check-input" type="checkbox" wire:model.live="selectedStorageType" value="open_area" id="openAreaOption">
                                <label class="form-check-label" for="openAreaOption">Use open space for storage (non-locker)</label>
                            </div>
                        </div>

                        <div class="border border-dark p-2">
                            <h5>Locker</h5>
                            <div class="form-check mb-3 ms-4">
                                <input class="form-check-input" type="checkbox" wire:model.live="selectedStorageType" value="locker" id="lockerOption">
                                <label class="form-check-label" for="lockerOption">I want to use a secure locker for valuable items</label>
                            </div>
                            @if(in_array('locker', $selectedStorageType ?? []))
                            <!-- Locker Status Legend -->
                            <div class="mb-3 small d-flex gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 rounded-circle bg-success" style="width: 15px; height: 15px;"></div>
                                    Available
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-2 rounded-circle bg-secondary" style="width: 15px; height: 15px;"></div>
                                    In Maintenance
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="me-2 rounded-circle bg-danger" style="width: 15px; height: 15px;"></div>
                                    Not Available
                                </div>
                            </div>
        
                            <!-- Locker Grid -->
                            <div class="row row-cols-10 g-2">
                                @foreach(collect($availableLockers)->chunk(10) as $row)
                                    @foreach($row as $locker)
                                    <div class="col-1">
                                        <div class="d-flex align-items-center justify-content-center border rounded text-white text-center fw-bold" style="aspect-ratio: 1 / 1; background-color:{{ $locker['status'] === 'available' ? '#198754' : ($locker['status'] === 'under_maintenance' ? '#6c757d' : '#dc3545') }}; {{ $locker['status'] === 'available' ? 'cursor: pointer;' : 'pointer-events: none; opacity: 0.7;' }} {{ $selectedLockerId == $locker['id'] ? 'outline: 3px solid #0d6efd;' : '' }}" @if($locker['status'] === 'available') wire:click="selectLocker({{ $locker['id'] }})" @endif>
                                            {{ $locker['locker_number'] }}
                                        </div>
                                    </div>
                                    @endforeach
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </fieldset>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-between pt-4 border-top mt-4">
                    <!-- Back Button -->
                    <button wire:click.prevent="previousStep" type="button" class="btn btn-outline-secondary px-4">
                        Back
                    </button>

                    <button type="button" wire:click.prevent="nextStep" class="btn btn-primary px-4 py-2" {{ (!$selectedStorageType || ($selectedStorageType === 'locker' && !$selectedLockerId)) ? 'disabled' : '' }}>
                        Confirm Selection
                    </button>
                </div>
                @endif
                @endif
                
                @if($currentStep == 3)
                <div class="container py-4">
                    <!-- Header -->
                    <div class="border-bottom pb-3 mb-4">
                        <h1 class="h4 fw-bold text-dark">Review & Submit Application</h1>
                    </div>

                    <!-- Student Information -->
                    <div class="bg-light p-4 rounded mb-4">
                        <h2 class="h5 fw-semibold text-dark mb-3">Student Information</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Name:</strong> <span class="ms-2">{{ $applicantData['name'] }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Matric Card:</strong> <span class="ms-2">{{ $applicantData['matric_card'] }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Study Year & Semester:</strong> <span class="ms-2">{{ $applicantData['study_year_semester'] }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Gender:</strong> <span class="ms-2">{{ $applicantData['gender'] }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone Number:</strong> <span class="ms-2">{{ $applicantData['phone'] }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> <span class="ms-2">{{ $applicantData['email'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Registered Items -->
                    <div class="mb-4">
                        <h2 class="h5 fw-semibold text-dark mb-3">Registered Items</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td>{{ $item['item_name'] }}</td>
                                        <td>{{ $item['category'] }}</td>
                                        <td>{{ $item['estimated_size'] }}</td>
                                        <td>{{ $item['item_description'] == '' ? '-' : $item['item_description'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Storage Selection -->
                    <div class="bg-light p-4 rounded mb-4">
                        <h2 class="h5 fw-semibold text-dark mb-3">Storage Selection</h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div>
                                    <strong>Selected Room:</strong>
                                    <span class="ms-2">{{ $selectedRoom }}</span>
                                </div>
                                @if(in_array('locker', $selectedStorageType))
                                <div>
                                    <strong>Selected Locker:</strong>
                                    <span class="ms-2">{{ $selectedLockerId }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Operating Hours for Access:</strong>
                                <div class="text-muted small mt-1">
                                    <div>Morning: 9:00 AM - 11:00 AM</div>
                                    <div>Evening: 9:00 PM - 11:00 PM</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-4">
                        <h2 class="h5 fw-semibold text-dark mb-3">Terms and Conditions</h2>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded mb-3">
                            <div class="placeholder- mb-2">
                                <span class="placeholder col-12"></span>
                                <span class="placeholder col-10"></span>
                                <span class="placeholder col-11"></span>
                                <span class="placeholder col-8"></span>
                            </div>
                            <div class="pt-2 placeholder-">
                                <span class="placeholder col-12"></span>
                                <span class="placeholder col-10"></span>
                                <span class="placeholder col-11"></span>
                                <span class="placeholder col-8"></span>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" wire:model="termsAccepted">
                            <label class="form-check-label" for="terms">
                                I have read and agree to all terms and conditions
                            </label>
                        </div>
                        @error('termsAccepted')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Important Notes -->
                    <div class="alert alert-info">
                        <div class="mb-1 fw-bold text-primary">
                            You will receive a notification via email and in the system within 24–48 hours regarding approval status. If approved, a QR code will be generated and sent to you.
                        </div>
                        <div class="text-muted">
                            <strong>Note:</strong> Please store your items before the specified date.
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between pt-4 border-top mt-4">
                    <!-- Back Button -->
                    <button wire:click.prevent="previousStep" type="button" class="btn btn-outline-secondary px-4">
                        Back
                    </button>

                    <!-- Submit Button -->
                    <button wire:click.prevent="submit" type="button" class="btn btn-primary px-5 d-flex align-items-center" wire:loading.attr="disabled" wire:target="submitApplication">
                        <span wire:loading.remove wire:target="submit">Submit Application</span>
                        <span wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Submitting...
                        </span>
                    </button>
                </div>
                @endif
                @if (session()->has('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

            </div>
        </form>
    </div>
    @endif
</div>
