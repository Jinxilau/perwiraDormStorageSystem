<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    {{-- Header --}}
    @include('livewire.includes.header2', [
        'title' => 'User Account Management',
        'subtitle' => 'View and Manage User Accounts.',
    ])

    <div class="overflow-hidden container-fluid">

        {{-- User Accounts Metrics --}}
        <x-card-matrix :items="[
            [
                'title' => $this->users->where('role_id', 1)->count(),
                'subtitle' => 'Total Administrator',
                'icon' => 'bi-shield-fill-check',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->users->where('role_id', 2)->count(),
                'subtitle' => 'Total Staff',
                'icon' => 'bi-person-badge-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->users->where('role_id', 3)->count(),
                'subtitle' => 'Total Student',
                'icon' => 'bi-mortarboard-fill',
                'color' => 'text-info',
                'bg' => 'bg-info bg-opacity-10',
            ],
            [
                'title' => $this->users->where('application_status', 'approved')->count(),
                'subtitle' => 'Total Approved Accounts',
                'icon' => 'bi-check-circle-fill',
                'color' => 'text-success',
                'bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => $this->users->where('application_status', 'pending')->count(),
                'subtitle' => 'Total Pending Accounts',
                'icon' => 'bi-clock-history',
                'color' => 'text-warning',
                'bg' => 'bg-warning bg-opacity-10',
            ],
            [
                'title' => $this->users->where('application_status', 'rejected')->count(),
                'subtitle' => 'Total Rejected Accounts',
                'icon' => 'bi-x-circle-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => $this->users->count(),
                'subtitle' => 'Total User Accounts',
                'icon' => 'bi-people-fill',
                'color' => 'text-dark',
                'bg' => 'bg-dark bg-opacity-10',
            ],
            [
                'title' => $this->users->where('gender', 'male')->count(),
                'subtitle' => 'Total Male',
                'icon' => 'bi-gender-male',
                'color' => 'text-primary',
                'bg' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => $this->users->where('gender', 'female')->count(),
                'subtitle' => 'Total Female',
                'icon' => 'bi-gender-female',
                'color' => 'text-danger',
                'bg' => 'bg-danger bg-opacity-10',
            ],
        ]" />

        <x-filter-options>
            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    User Role
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="role">
                    <option value="">All Type</option>
                    <option value="1">Administrator</option>
                    <option value="2">Staff</option>
                    <option value="3">Student</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    User Status
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="accStatus">
                    <option value="">All Type</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Gender
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="userGender">
                    <option value="">All Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="col-md-6 col-lg-5">
                <label class="form-label small text-muted fw-bold">Date Range</label>

                <div class="input-group">
                    {{-- Start Date --}}
                    <input type="date" wire:model.live.debounce.300ms="fromRange" class="form-control bg-light border-0"
                        placeholder="Start Date" aria-label="Start Date">

                    {{-- Separator --}}
                    <span class="input-group-text bg-light border-0 text-secondary">to</span>

                    {{-- End Date --}}
                    <input type="date" wire:model.live.debounce.300ms="toRange" class="form-control bg-light border-0"
                        placeholder="End Date" aria-label="End Date">
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By Application Date
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortDate">
                    <option value="">Default (No Sorting)</option>
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Sort By User Name
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="sortName">
                    <option value="">Default (No Sorting)</option>
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>

            <div class="col-md-4 col-lg-3">
                <label class="form-label small text-muted fw-bold">
                    Application Per Page <span class="text-muted small">(Pagination)</span>
                </label>
                <select class="form-select bg-light border-0" wire:model.live.debounce.300ms="paginationInt">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </x-filter-options>

        {{-- Bulk Actions Bar --}}
        <div class="bg-primary-subtle border-bottom border-primary rounded-top px-3 py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-blue-700">
                        {{ count($this->selectedUsers) }} user(s) selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="bulkApprove"
                        @empty($this->selectedUsers) disabled @endempty
                        wire:confirm="Are you sure you want to approve the selected applications?"
                        class="btn btn-success">
                        Approve Selected
                    </button>
                    <button type="button" wire:click="bulkReject"
                        @empty($this->selectedUsers) disabled @endempty
                        wire:confirm="Are you sure you want to reject the selected applications?"
                        class="btn btn-danger">
                        Reject Selected
                    </button>
                </div>
            </div>
        </div>
        {{-- Table --}}
        <div class="table-responsive-xxl">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-uppercase align-middle">
                            <input type="checkbox" wire:model.live="selectAll">
                        </th>
                        <th class="align-middle py-2">
                            ID
                        </th>
                        <th class="align-middle py-2">
                            User Name
                        </th>
                        <th class="align-middle py-2">
                            Matric No
                        </th>
                        <th class="align-middle py-2">
                            Role
                        </th>
                        <th class="align-middle py-2">
                            Gender
                        </th>
                        <th class="align-middle py-2">
                            Contact
                        </th>
                        <th class="align-middle py-2">
                            Status
                        </th>
                        <th class="align-middle py-2">
                            Applied On
                        </th>
                        <th class="align-middle py-2">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse($this->users as $user)
                        <tr wire:key="row-{{ $user->id }}"
                            class="{{ in_array($user->id, $this->selectedUsers) ? 'table-active' : '' }}">
                            <td class="align-middle">
                                <input type="checkbox" value="{{ $user->id }}" wire:model.live="selectedUsers">
                            </td>
                            <td class="align-middle">
                                <span class="text-secondary small user-select-none">#</span>
                                <span class="fw-bold text-dark">{{ $user->id }}</span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 rounded-circle bg-primary-subtle text-primary fw-bold d-flex justify-content-center align-items-center text-uppercase me-1"
                                        style="height: 35px; width: 35px; font-size: 0.85rem;">
                                        {{ substr($user->name ?? 'NA', 0, 2) }}
                                    </div>

                                    <div class="fw-bold text-dark text-truncate" style="max-width: 150px;"
                                        title="{{ $user->name ?? 'N/A' }}">
                                        {{ $user->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            {{-- Matric No --}}
                            <td class="align-middle">
                                <span class="font-monospace text-secondary fw-medium">
                                    {{ $user->matric_no ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Role --}}
                            <td class="align-middle">
                                @php
                                    $roleColor = match (strtolower($user->userRole->role_name ?? '')) {
                                        'admin',
                                        'administrator'
                                            => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'staff' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                        default => 'bg-light text-secondary border',
                                    };
                                @endphp
                                <span class="badge {{ $roleColor }} border fw-normal">
                                    {{ ucfirst($user->userRole->role_name ?? 'Student') }}
                                </span>
                            </td>

                            {{-- Gender --}}
                            <td class="align-middle">
                                <span class="badge bg-light text-secondary border fw-normal">
                                    {{ ucfirst($user->gender ?? '-') }}
                                </span>
                            </td>

                            {{-- Contact Info (Email & Phone) --}}
                            <td class="align-middle">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center text-dark" style="font-size: 0.85rem;">
                                        <i class="bi bi-envelope text-muted me-2"></i>
                                        <span class="text-truncate" style="max-width: 180px;"
                                            title="{{ $user->email }}">
                                            {{ $user->email ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="bi bi-telephone me-2"></i>
                                        <span>{{ $user->phone_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="align-middle">
                                @php
                                    $statusClass = match ($user->application_status) {
                                        'approved' => 'bg-success-subtle text-success border-success-subtle',
                                        'rejected' => 'bg-danger-subtle text-danger border-danger-subtle',
                                        'pending' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} border rounded-pill fw-normal px-3">
                                    <span class="d-flex align-items-center gap-1">
                                        @if ($user->application_status === 'approved')
                                            <i class="bi bi-check-circle-fill"></i>
                                        @endif
                                        @if ($user->application_status === 'rejected')
                                            <i class="bi bi-x-circle-fill"></i>
                                        @endif
                                        @if ($user->application_status === 'pending')
                                            <i class="bi bi-clock-fill"></i>
                                        @endif
                                        {{ ucfirst($user->application_status) }}
                                    </span>
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                    <span class="small text-muted">
                                        {{ $user->created_at ? $user->created_at->format('h:i A') : '' }}
                                    </span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex align-items-center" style="white-space: nowrap">
                                    <a href="{{ route('user_details', ['user' => $user->id]) }}" title="View Details"
                                        class="btn btn-sm btn-outline-secondary" style="padding: 0;">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    <button wire:click="approveUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to approve this application?"
                                        class="btn btn-sm btn-outline-success" style="padding: 0;" title="Approve">
                                        <i class="bi bi-check"></i>
                                    </button>

                                    <button wire:click="rejectUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to reject this application?"
                                        class="btn btn-sm btn-outline-danger" style="padding: 0;" title="Reject">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="20" class="px-3 py-5 text-center">
                                <div>
                                    <i class="bi bi-folder2-open mx-auto" style="font-size: 60px"></i>
                                    <h3 class="mt-4">No User Found</h3>
                                    <p class="mt-2">There are no user found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $this->users->links('vendor.livewire.bootstrap') }}
        </div>
    </div>

    {{-- <x-custom-modal>
        <x-slot:title>
            <div class="d-flex justify-content-between align-items-center gap-1 w-100">
                <span>User Details</span>
                <span class="badge bg-secondary rounded-pill fs-6">#{{ $this->userId }}</span>
            </div>
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="userId" class="spinner-border my-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        @php
            $modalUser = App\Models\User::with(['userRole', 'applyStorage'])->find($this->userId);
            if ($modalUser) {
                $this->userStatus = $modalUser->application_status;
                $this->userRole = $modalUser->userRole->role_name;
            }
        @endphp

        <div wire:loading.remove="userId" class="text-start">
            @if ($modalUser)
                <form wire:submit="editUser">
                    <!-- User Status Section -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <h5 class="card-title mb-0">User Status</h5>
                            <span
                                class="badge px-3 py-2 {{ match ($this->userStatus) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'pending' => 'bg-warning text-dark',
                                } }}">
                                {{ ucfirst($this->userStatus) }}
                            </span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <div>
                                <input type="radio" class="btn-check" name="user_status" id="option1"
                                    autocomplete="off" wire:model.live="userStatus" value="approved"
                                    {{ $this->userStatus === 'approved' ? 'checked disabled' : '' }}>
                                <label class="btn btn-outline-success" for="option1">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Approve
                                </label>
                            </div>

                            <div>
                                <input type="radio" class="btn-check" name="user_status" id="option2"
                                    autocomplete="off" wire:model.live="userStatus" value="pending"
                                    {{ $this->userStatus === 'pending' ? 'checked disabled' : '' }}>
                                <label class="btn btn-outline-warning" for="option2">
                                    <i class="bi bi-slash-square-fill me-1"></i>
                                    Mark as Pending
                                </label>
                            </div>

                            <div>
                                <input type="radio" class="btn-check" name="user_status" id="option3"
                                    autocomplete="off" wire:model.live="userStatus" value="rejected"
                                    {{ $this->userStatus === 'rejected' ? 'checked disabled' : '' }}>
                                <label class="btn btn-outline-danger" for="option3">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Reject
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- User Role Section -->
                    <div class="mb-4">
                        <h5 class="card-title mb-3">User Role</h5>
                        <div class="dropdown w-100">
                            <button
                                class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>{{ ucfirst($this->userRole) }}</span>
                            </button>
                            <ul class="dropdown-menu w-100">
                                @foreach ($this->roles as $role)
                                    <li>
                                        <input type="radio" style="display:none" id="role-{{ $role->id }}"
                                            value="{{ $role->role_name }}" wire:model.live="userRole">
                                        <label for="role-{{ $role->id }}"
                                            class="dropdown-item {{ $role->role_name === $this->userRole ? 'active' : '' }}"
                                            style="cursor: pointer;">
                                            <i class="bi bi-person-badge me-2"></i>
                                            {{ ucfirst($role->role_name) }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if (!empty($modalUser->applyStorage))
                        <hr class="my-4">
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Storage Application</h5>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small d-block mb-1">ID:</label>
                                    <p class="mb-0 fw-medium">#{{ $modalUser->applyStorage->id }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small d-block mb-1">Status:</label>
                                    <span
                                        class="badge {{ $modalUser->applyStorage->storage_application_status === 'pending' ? 'text-bg-warning' : ($modalUser->applyStorage->storage_application_status === 'approved' ? 'text-bg-success' : 'text-bg-danger') }}">
                                        {{ ucfirst($modalUser->applyStorage->storage_application_status) }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small d-block mb-1">Applied At:</label>
                                    <p class="mb-0 fw-medium">
                                        {{ $modalUser->applyStorage->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small d-block mb-1">Updated At:</label>
                                    <p class="mb-0 fw-medium">
                                        {{ $modalUser->applyStorage->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Submit Button -->
                    <x-slot:footer>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </x-slot:footer>
                </form>
            @endif
        </div>
    </x-custom-modal> --}}


</div>
