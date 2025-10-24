<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-custom-modal>
        <x-slot:title>
            User #<span>{{ $this->userId }}</span>
        </x-slot:title>

        <div class="d-flex justify-content-center">
            <div wire:loading="userId" class="spinner-border" role="status">
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
    </x-custom-modal>

    {{-- Header --}}
    @include('livewire.includes.header2', [
        'title' => 'User Account Management',
        'subtitle' => 'View and Manage User Accounts.',
    ])

    <div class="overflow-hidden container-fluid">

        <x-card-matrix :items="[
            ['title' => $this->users->where('role_id', 1)->count(), 'subtitle' => 'Total Administrator'],
            ['title' => $this->users->where('role_id', 2)->count(), 'subtitle' => 'Total Staff'],
            ['title' => $this->users->where('role_id', 3)->count(), 'subtitle' => 'Total Student'],
            [
                'title' => $this->users->where('application_status', 'approved')->count(),
                'subtitle' => 'Total Approved Accounts',
            ],
            [
                'title' => $this->users->where('application_status', 'pending')->count(),
                'subtitle' => 'Total Pending Accounts',
            ],
            [
                'title' => $this->users->where('application_status', 'rejected')->count(),
                'subtitle' => 'Total Rejected Accounts',
            ],
            [
                'title' => $this->users->count(),
                'subtitle' => 'Total User Accounts',
            ],
        ]" />

        {{-- Filter Options --}}
        <div class="bg-light row border-bottom border-top border-dark mb-3">
            {{-- Search --}}
            <div class="rounded p-3 d-flex gap-2 align-items-center">
                <div class="flex-grow-1 flex-shrink-1 position-relative">
                    <i class="bi bi-search fs-5 position-absolute"
                        style="left: 16px; top: 50%;transform: translateY(-50%);"></i>
                    <input wire:model.live="search" type="text" class="w-100" id="searchInput"
                        style="padding: 14px 16px 14px 48px; border: 2px solid #e5e7eb; border-radius: 8px;"
                        placeholder="Search applications by applicant name, matric no, or ID...">
                </div>
                <button type="button" class="btn btn-primary fw-bold">Search</button>
            </div>
            {{-- Dropdowns --}}
            <div class="border-rounded p-3 shadow-sm">
                <div class="row row-cols-3 mb-3">

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            User Role
                        </label>
                        <select class="form-select mb-3" wire:model.live="role">
                            <option value="">All Type</option>
                            <option value="1">Administrator</option>
                            <option value="2">Staff</option>
                            <option value="3">Student</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            User Status
                        </label>
                        <select class="form-select mb-3" wire:model.live="accStatus">
                            <option value="">All Type</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Gender
                        </label>
                        <select class="form-select mb-3" wire:model.live="userGender">
                            <option value="">All Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            From Application Date Range
                        </label>
                        <input type="date" wire:model.live="fromRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            To Application Date Range
                        </label>
                        <input type="date" wire:model.live="toRange" class="form-control">
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By Application Date
                        </label>
                        <select class="form-select mb-3 " wire:model.live="sortDate">
                            <option value="latest">Latest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Sort By User Name
                        </label>
                        <select class="form-select mb-3" wire:model.live="sortName">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>

                    <div>
                        <label class="small text-muted form-label d-inline-block w-100 text-start">
                            Application Per Page <span class="text-muted small">(Pagination)</span>
                        </label>
                        <select class="form-select mb-3" wire:model.live="paginationInt">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-3 pt-2 border-top">
                    <button class="btn btn-danger fw-bold" wire:click.prevent="clearFilters"
                        @click="allCol = true; toggleAll()">Clear All</button>
                </div>
            </div>

        </div>

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
                            <td>
                                #{{ $user->id }}
                            </td>
                            <td>
                                <div class="d-flex items-center gap-1">
                                    <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                        style="height: 30px; width:30px">
                                        {{ substr($user->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <div style="white-space: nowrap;">
                                        {{ $user->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $user->matric_no ?? 'N/A' }}
                            </td>
                            <td>
                                {{ ucfirst($user->userRole->role_name) }}
                            </td>
                            <td>
                                {{ ucfirst($user->gender) }}
                            </td>
                            <td>
                                <div style="white-space: nowrap;">
                                    Email: {{ $user->email ?? 'N/A' }}
                                </div>
                                <div>
                                    Phone: {{ $user->phone_number ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge {{ $user->application_status === 'pending' ? 'text-bg-warning' : ($user->application_status === 'approved' ? 'text-bg-success' : 'text-bg-danger') }}">
                                    {{ ucfirst($user->application_status) }}
                                </span>
                            </td>
                            <td>
                                <div style="white-space: nowrap;">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div>
                                    {{ $user->created_at ? $user->created_at->format('h:i A') : '' }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center space-x-2" style="white-space: nowrap">
                                    <button
                                        wire:click="$set('userId', {{ $user->id }}); $dispatch('display_modal');"
                                        title="View Details" class="btn btn-sm border border-secondary"
                                        style="padding: 0;" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button wire:click="approveUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to approve this application?"
                                        class="text-success btn btn-sm border border-success" style="padding: 0;"
                                        title="Approve">
                                        <i class="bi bi-check"></i>
                                    </button>

                                    <button wire:click="rejectUser({{ $user->id }})"
                                        wire:confirm="Are you sure you want to reject this application?"
                                        class="text-danger btn btn-sm border border-danger" style="padding: 0;"
                                        title="Reject">
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

    <!-- Toast Notifications -->
    <x-toast-message />


</div>
