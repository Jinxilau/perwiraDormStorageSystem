<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    {{-- Modal --}}
    <div tabindex="-1" :class="{ 'd-flex align-items-center justify-content-center': show }"
        style="display: none; z-index: 9000; inset: 0;" class="position-fixed" id="lockerModal" x-data="{ show: false }"
        @display_modal.window="show = true" x-show='show' x-effect="document.body.style.overflow = show ? 'hidden': ''">

        <div class="position-fixed" style="background-color: rgba(0, 0, 0, 0.5); inset:0;" @click="show = false"></div>

        @if ($this->user)
            <div class="card" x-transition>
                <div class="card-header">
                    Edit User - {{ $this->user->id }}
                </div>
                <form wire:submit="editUser">
                    <div class="card-body">

                        <div class="d-flex gap-2">
                            <h5 class="card-title">User Status
                                <span
                                    class="badge px-3 py-1 {{ match ($this->userStatus) {'approved' => 'bg-success','rejected' => 'bg-danger','pending' => 'bg-secondary'} }}">
                                    {{ ucfirst($this->userStatus) }}
                                </span>
                            </h5>
                        </div>
                        <div>
                            <input type="radio" class="btn-check" name="user_status" id="option1" autocomplete="off"
                                wire:model.live="userStatus" value="approved"
                                {{ $this->userStatus === 'approved' ? 'checked disabled' : '' }}>
                            <label class="btn btn-success" for="option1">
                                <i class="bi bi-check-circle-fill"></i>
                                Approve
                            </label>

                            <input type="radio" class="btn-check" name="user_status" id="option2" autocomplete="off"
                                wire:model.live="userStatus" value="pending"
                                {{ $this->userStatus === 'pending' ? 'checked disabled' : '' }}>
                            <label class="btn btn-warning" for="option2">
                                <i class="bi bi-slash-square-fill"></i>
                                Mark as Pending
                            </label>

                            <input type="radio" class="btn-check" name="user_status" id="option3" autocomplete="off"
                                wire:model.live="userStatus" value="rejected"
                                {{ $this->userStatus === 'rejected' ? 'checked disabled' : '' }}>
                            <label class="btn btn-danger" for="option3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Reject
                            </label>
                        </div>

                        <h5 class="card-title mt-2">User Role</h5>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                {{ ucfirst($this->userRole) }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($this->roles as $role)
                                    <li>
                                        <input type="radio" style="display:none" id="role-{{ $role->id }}"
                                            value="{{ $role->role_name }}" wire:model.live="userRole">
                                        <label for="role-{{ $role->id }}" class="dropdown-item {{ $role->role_name === $this->userRole ? 'active' : ''}}">
                                            {{ ucfirst($role->role_name) }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">Submit</button>

                    </div>
                </form>
            </div>
        @endif
    </div>


    <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">

        <!-- Bulk Actions Bar -->
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

        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-uppercase align-middle">
                            <input type="checkbox" wire:model.live="selectAll">
                        </th>
                        <th class="align-middle py-2">
                            User Name
                        </th>
                        <th class="align-middle py-2">
                            User ID
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
                            Active Storage
                        </th>
                        <th class="align-middle py-2">
                            Status
                        </th>
                        <th class="align-middle py-2">
                            Created Timestamp
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
                                {{ $user->matric_no ?? $user->id }}
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
                                @if (!empty($user->applyStorage))
                                    <div>
                                        {{ $user->applyStorage->id }}
                                    </div>
                                    <div>
                                        {{ $user->applyStorage->status }}
                                    </div>
                                @else
                                    N/A
                                @endif
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
                                    <button @click="$dispatch('view-user', {id: {{ $user->id }} })"
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
                            <td colspan="8" class="px-3 py-5 text-center">
                                <div>
                                    <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                                    <h3 class="mt-4">No pending applications</h3>
                                    <p class="mt-2">There are no pending storage applications to review.</p>
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
    <div x-data="{ show: false, message: '', type: 'success' }"
        @user-updated.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type;
        setTimeout(() => show = false, 4000);
    "
        x-show="show" x-transition:enter="fade-in" x-transition:leave="fade-out"
        class="position-fixed bottom-0 start-0 p-3" style="z-index: 1050; display: none;">
        <div class="toast show" role="alert">
            <div class="toast-header"
                :class="{
                    'bg-success': type === 'success',
                    'bg-danger': type === 'fail'
                }">
                <!-- Change color based on type -->
                <div :class="{
                    'bg-success': type === 'success',
                    'bg-danger': type === 'fail'
                }"
                    class="rounded me-2" style="width: 20px; height: 20px;">
                </div>

                <!-- Change title based on type -->
                <strong class="me-auto" x-text="type === 'success' ? 'Success' : 'Error'"></strong>

                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
            <div class="toast-body" x-text="message"></div>
        </div>
    </div>


</div>
