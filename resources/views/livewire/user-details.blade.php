<div>
    @php
        $id = $user->id;
    @endphp

    @include('livewire.includes.header2', [
        'title' => 'User Details',
        'subtitle' => "User #{$user->id} Details",
    ])

    @php
        $dashboard = Auth::user()->role_id === 1 ? 'admin_dashboard' : 'staff_dashboard';
    @endphp
    <div class="container-fluid mb-0 d-flex justify-content-between">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 ms-2">
                <li class="breadcrumb-item"><a href="{{ route($dashboard) }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manage_user') }}">User Accounts</a></li>
                <li class="breadcrumb-item active">User #{{ $user->id }}</li>
            </ol>
        </nav>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="p-4">

        <!-- Basic Information Section -->
        <div class="border-bottom pb-4 mb-4">
            <h3 class="h5 fw-semibold text-dark mb-3">Basic Information</h3>

            <div class="row g-3">
                <!-- Name -->
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" class="form-control">
                    @error('name')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email" id="email" wire:model="email" class="form-control">
                    @error('email')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Matric Number -->
                <div class="col-md-6">
                    <label for="matricNo" class="form-label">
                        Matric Number
                    </label>
                    <input type="text" id="matricNo" wire:model="matricNo" class="form-control">
                    @error('matricNo')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="col-md-6">
                    <label for="phoneNumber" class="form-label">
                        Phone Number
                    </label>
                    <input type="text" id="phoneNumber" wire:model="phoneNumber" class="form-control">
                    @error('phoneNumber')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Gender -->
                <div class="col-md-6">
                    <label for="gender" class="form-label">
                        Gender
                    </label>
                    <select id="gender" wire:model="gender" class="form-select">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Year of Study -->
                <div class="col-md-6">
                    <label for="yearOfStudy" class="form-label">
                        Year of Study
                    </label>
                    <input type="number" id="yearOfStudy" wire:model="yearOfStudy" min="1" max="7"
                        class="form-control">
                    @error('yearOfStudy')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Account Status Section -->
        <div class="border-bottom pb-4 mb-4">
            <h3 class="h5 fw-semibold text-dark mb-3">Account Status</h3>

            <div class="row g-3">
                <!-- Role -->
                <div class="col-md-6">
                    <label for="roleId" class="form-label">
                        User Role <span class="text-danger">*</span>
                    </label>
                    <select id="roleId" wire:model="roleId" class="form-select">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->role_name) }}</option>
                        @endforeach
                    </select>
                    @error('roleId')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Application Status -->
                <div class="col-md-6">
                    <label for="applicationStatus" class="form-label">
                        Application Status <span class="text-danger">*</span>
                    </label>
                    <select id="applicationStatus" wire:model="applicationStatus" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    @error('applicationStatus')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Account Metadata Section -->
        <div class="border-bottom pb-4 mb-4">
            <h3 class="h5 fw-semibold text-dark mb-3">Account Metadata</h3>

            <div class="row g-3 small">
                <div class="col-md-6">
                    <span class="fw-medium text-dark">Account Created:</span>
                    <span class="text-secondary">{{ $user->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="col-md-6">
                    <span class="fw-medium text-dark">Last Updated:</span>
                    <span class="text-secondary">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="col-md-6">
                    <span class="fw-medium text-dark">Email Verified:</span>
                    <span class="text-secondary">
                        @if ($user->email_verified_at)
                            <span class="text-success">✓ {{ $user->email_verified_at->format('M d, Y') }}</span>
                        @else
                            <span class="text-danger">✗ Not Verified</span>
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <span class="fw-medium text-dark">User ID:</span>
                    <span class="text-secondary">{{ $user->id }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center pt-3">
            <a href="{{ route('manage_user') }}" class="btn btn-secondary">
                ← Back to Users
            </a>

            <button type="submit" class="btn btn-primary px-4">
                Save Changes
            </button>
        </div>
    </form>
</div>
