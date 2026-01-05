<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                <!-- Header Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-4">
                        <img src="{{ Vite::asset('resources/images/icons/perwira_logo.png') }}" alt="Logo"
                            class="img-fluid" style="width: 300px; object-fit: cover; margin-top: -30px; margin-bottom: -30px;">
                        <h1 class="fw-bold text-dark mb-2 h2">Account Application</h1>
                        <p class="text-muted mb-0">Perwira Dorm Storage System</p>
                    </div>
                </div>

                <!-- Main Form Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-lg-5 p-4">

                        <!-- Success/Error Messages -->
                        @if (session()->has('success'))
                            <div class="alert alert-success d-flex align-items-start mb-4" role="alert">
                                <svg class="me-2 flex-shrink-0" width="20" height="20" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger d-flex align-items-start mb-4" role="alert">
                                <svg class="me-2 flex-shrink-0" width="20" height="20" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif

                        @if (session()->has('message'))
                            <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                                <svg class="me-2 flex-shrink-0" width="20" height="20" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>{{ session('message') }}</div>
                            </div>
                        @endif

                        <form wire:submit.prevent="submit">

                            <fieldset wire:loading.attr="disabled" wire:target="submit">
                                <!-- Personal Information Section -->
                                <div class="mb-5">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge bg-primary me-2">1</div>
                                        <h3 class="h4 mb-0 fw-semibold text-dark">Personal Information</h3>
                                    </div>
                                    <hr class="mb-4">
    
                                    <div class="row g-3">
                                        <!-- First Name -->
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-semibold">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="name" wire:model="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="e.g., Lau Jin Xi">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Last Name -->
                                        {{-- <div class="col-md-6 col-lg-4">
                                            <label for="last_name" class="form-label fw-semibold">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="last_name" wire:model="last_name"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                placeholder="e.g., Jin Xi">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div> --}}
    
                                        <!-- Gender -->
                                        <div class="col-md-6">
                                            <label for="gender" class="form-label fw-semibold">Gender <span
                                                    class="text-danger">*</span></label>
                                            <select id="gender" wire:model="gender"
                                                class="form-select @error('gender') is-invalid @enderror">
                                                <option value="">Select your gender</option>
                                                @foreach ($genders as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Phone Number -->
                                        <div class="col-md-6">
                                            <label for="phone_number" class="form-label fw-semibold">Phone Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" id="phone_number" wire:model="phone_number"
                                                class="form-control @error('phone_number') is-invalid @enderror"
                                                placeholder="e.g., 012-3456789">
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Email Address -->
                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-semibold">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email" wire:model="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="student@uthm.edu.my">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Academic Details Section -->
                                <div class="mb-5">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge bg-primary me-2">2</div>
                                        <h3 class="h4 mb-0 fw-semibold text-dark">Academic Details</h3>
                                    </div>
                                    <hr class="mb-4">
    
                                    <div class="row g-3">
                                        <!-- Matric No -->
                                        <div class="col-md-6 col-lg-4">
                                            <label for="matric_no" class="form-label fw-semibold">Matric No <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="matric_no" wire:model="matric_no"
                                                class="form-control @error('matric_no') is-invalid @enderror"
                                                placeholder="e.g., AI220123">
                                            @error('matric_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Year of Study -->
                                        <div class="col-md-6 col-lg-4">
                                            <label for="year_of_study" class="form-label fw-semibold">Year of Study <span
                                                    class="text-danger">*</span></label>
                                            <select id="year_of_study" wire:model="year_of_study"
                                                class="form-select @error('year_of_study') is-invalid @enderror">
                                                <option value="">Select year</option>
                                                @foreach ($years as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('year_of_study')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Semester -->
                                        {{-- <div class="col-md-6 col-lg-4">
                                            <label for="semester" class="form-label fw-semibold">Semester <span
                                                    class="text-danger">*</span></label>
                                            <select id="semester" wire:model="semester"
                                                class="form-select @error('semester') is-invalid @enderror">
                                                <option value="">Select semester</option>
                                                @foreach ($this->semesters as $eachsemester)
                                                    <option value="{{ $eachsemester->id }}">
                                                        {{ $eachsemester->academic_year }} – Semester {{ $eachsemester->semester_no }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('semester')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div> --}}
                                    </div>
                                </div>
    
                                <!-- Account Security Section -->
                                <div class="mb-5">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="badge bg-primary me-2">3</div>
                                        <h3 class="h4 mb-0 fw-semibold text-dark">Account Security</h3>
                                    </div>
                                    <hr class="mb-4">
    
                                    <div class="alert alert-info d-flex align-items-start" role="alert">
                                        <svg class="me-2 flex-shrink-0 mt-1" width="18" height="18"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="small">
                                            <strong>Password Requirements:</strong> Your password must be at least 8
                                            characters long and should include a mix of letters, numbers, and special
                                            characters.
                                        </div>
                                    </div>
    
                                    <div class="row g-3">
                                        <!-- Password -->
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-semibold">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" id="password" wire:model="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Enter password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
    
                                        <!-- Confirm Password -->
                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label fw-semibold">Confirm
                                                Password <span class="text-danger">*</span></label>
                                            <input type="password" id="password_confirmation"
                                                wire:model="password_confirmation" class="form-control"
                                                placeholder="Re-enter password">
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Login Link -->
                                <div class="text-center mb-4 pb-3 border-top pt-4">
                                    <p class="text-muted mb-0">
                                        {{ __('Already have an account?') }}
                                        <a href="{{ route('login') }}" wire:navigate
                                            class="text-primary fw-semibold text-decoration-none">{{ __('Log in') }}</a>
                                    </p>
                                </div>
    
                                <!-- Form Buttons -->
                                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                    <button type="button" wire:click="clearForm"
                                        class="btn btn-outline-secondary btn-lg px-5" wire:loading.attr="disabled"
                                        wire:loading.class="disabled">
                                        <span wire:loading.remove wire:target="clearForm">Clear Form</span>
                                        <span wire:loading wire:target="clearForm">Clearing...</span>
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg px-5"
                                        wire:loading.attr="disabled" wire:loading.class="disabled">
                                        <span wire:loading.remove wire:target="submit">Apply Account</span>
                                        <span wire:loading wire:target="submit">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                                    aria-hidden="true"></span>
                                                Submitting...
                                            </div>
                                        </span>
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
