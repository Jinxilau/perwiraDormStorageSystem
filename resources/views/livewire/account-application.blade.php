<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <!-- Main Content -->
    <div class="mx-auto">
        <h2 class="text-center display-4 fw-bold text-dark mb-2 card-header">Account Application</h2>
        <div class="card-body">
            <!-- Success/Error Messages -->
            @if (session()->has('success'))
            <div class="mb-4 p-3 bg-success bg-opacity-10 border border-success text-success rounded">
                {{ session('success') }}
            </div>
            @endif
    
            @if (session()->has('error'))
            <div class="mb-4 p-3 bg-danger bg-opacity-10 border border-danger text-danger rounded">
                {{ session('error') }}
            </div>
            @endif
    
            @if (session()->has('message'))
            <div class="mb-4 p-3 bg-info bg-opacity-10 border border-info text-info rounded">
                {{ session('message') }}
            </div>
            @endif
    
            <form wire:submit.prevent="submit" class="mb-3">
                <!-- Personal Information Section -->
                <div class="mb-3">
                    
                    <div class="row">
                        <h3 class="font-weight-semibold text-dark mb-4">Personal Information</h3>
                        <hr>
                        <!-- First Name -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="first_name" class="form-label ">First Name</label>
                            <input type="text" id="first_name" wire:model="first_name" class="border border-dark form-control  @error('first_name') is-invalid @enderror" placeholder="eg: Lau">
                            @error('first_name') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="last_name" class="form-label ">Last Name</label>
                            <input type="text" id="last_name" wire:model="last_name" class="border border-dark form-control  @error('last_name') is-invalid @enderror" placeholder="eg: Jin Xi">
                            @error('last_name') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="gender" class="form-label ">Gender</label>
                            <select id="gender" wire:model="gender" class="border border-dark form-select @error('gender') is-invalid @enderror">
                                <option value="">Please select your gender</option>
                                @foreach($genders as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('gender') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <hr>
                        <h3 class="font-weight-semibold text-dark mb-4">Academic Details</h3>
                        <hr>

                        <!-- Matric No -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="matric_no" class="form-label ">Matric No</label>
                            <input type="text" id="matric_no" wire:model="matric_no" class="border border-dark form-control  @error('matric_no') is-invalid @enderror">
                            @error('matric_no') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Year of Study -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="year_of_study" class="form-label ">Year of Study</label>
                            <select id="year_of_study" wire:model="year_of_study" class="border border-dark form-select @error('year_of_study') is-invalid @enderror">
                                <option value="">Please select a year</option>
                                @foreach($years as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('year_of_study') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <label for="semester" class="form-label ">Semester</label>
                            <select id="semester" wire:model="semester" class="border border-dark form-select @error('semester') is-invalid @enderror">
                                <option value="">Please select a year</option>
                                @foreach($semesters as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('semester') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6 mb-4">
                            <label for="phone_number" class="form-label ">Phone Number</label>
                            <input type="tel" id="phone_number" wire:model="phone_number" class="border border-dark form-control  @error('phone_number') is-invalid @enderror">
                            @error('phone_number') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" wire:model="email" class="border border-dark form-control  @error('email') is-invalid @enderror">
                            @error('email') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" wire:model="password" class="border border-dark form-control  @error('password') is-invalid @enderror">
                            @error('password') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label ">Confirm Password</label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" class="border border-dark form-control ">
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="form-check mb-4">
                    <input type="checkbox" id="agree_terms" wire:model="agree_terms" class="border border-dark form-check-input @error('agree_terms') is-invalid @enderror">
                    <label for="agree_terms" class="form-check-label">
                        I agree to the 
                        <a href="#" class="text-primary">Terms of Service</a> 
                        and 
                        <a href="#" class="text-primary">Privacy Policy</a>
                    </label>
                    @error('agree_terms') 
                        <div class="invalid-feedback d-block">{{ $message }}</div> 
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="d-flex justify-content-center gap-3 pt-4">
                    <button type="button" wire:click="clearForm" class="btn btn-outline-secondary btn-lg px-4" wire:loading.attr="disabled" wire:loading.class="disabled">Clear Form</button>
                    <button type="submit" class="btn btn-dark btn-lg px-4" wire:loading.attr="disabled" wire:loading.class="disabled">
                        <span wire:loading.remove>Apply Account</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
