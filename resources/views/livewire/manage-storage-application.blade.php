<div>
    {{-- Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Application Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">
        <!-- Bulk Actions Bar -->
        <div class="bg-primary-subtle border-bottom border-primary rounded-top px-3 py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-blue-700">
                        {{ count($this->selectedApplications) }} application(s) selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" wire:click="bulkApprove"
                        @empty($this->selectedApplications) disabled @endempty
                        wire:confirm="Are you sure you want to approve the selected applications?"
                        class="btn btn-success">
                        Approve Selected
                    </button>
                    <button type="button" wire:click="bulkReject"
                        @empty($this->selectedApplications) disabled @endempty
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
                            Application ID
                        </th>
                        <th class="align-middle py-2">
                            Student Info
                        </th>
                        <th class="align-middle py-2">
                            Storage Details
                        </th>
                        <th class="align-middle py-2">
                            Items
                        </th>
                        <th class="align-middle py-2">
                            Application Date
                        </th>
                        <th class="align-middle py-2">
                            Status
                        </th>
                        <th class="align-middle py-2">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse($this->pendingStorageApplications as $application)
                        <tr wire:key="row-{{ $application->id }}"
                            class="{{ in_array($application->id, $this->selectedApplications) ? 'table-active' : '' }}">
                            <td class="align-middle">
                                <input type="checkbox" value="{{ $application->id }}"
                                    wire:model.live="selectedApplications">
                            </td>
                            <td>
                                #{{ $application->id }}
                            </td>
                            <td>
                                <div class="d-flex items-center gap-1">
                                    <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                        style="height: 30px; width:30px">
                                        {{ substr($application->applicant->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <div>
                                        <div>
                                            {{ $application->applicant->name ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ $application->applicant->email ?? 'N/A' }}
                                        </div>
                                        <div>
                                            ID: {{ $application->applicant->matric_no ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if (!empty($application->open_area_id) && !empty($application->locker_id))
                                        {{ $application->openArea?->storageRoom?->room_name }} <div>Locker + Open Area
                                        </div>
                                    @elseif (!empty($application->locker_id))
                                        {{ $application->locker?->storageRoom?->room_name }} <div>Locker</div>
                                    @elseif (!empty($application->open_area_id))
                                        {{ $application->openArea?->storageRoom?->room_name }} <div>Open Area</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @foreach ($application->storedItems as $item)
                                    <div wire:key="item-{{ $item->id }}">
                                        {{ ucfirst($item->item_type) }}
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <div>
                                    {{ $application->created_at ? $application->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div>
                                    {{ $application->created_at ? $application->created_at->format('h:i A') : '' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge text-bg-warning">
                                    {{ ucfirst($application->storage_application_status) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center space-x-2" style="white-space: nowrap">
                                    <button @click="$dispatch('view-application', {{ $application->id }})"
                                        title="View Details" class="btn btn-sm border border-secondary"
                                        style="padding: 0;" type="button" data-bs-toggle="modal"
                                        data-bs-target="#viewModal">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button wire:click="approveApplication({{ $application->id }})"
                                        wire:confirm="Are you sure you want to approve this application?"
                                        class="text-success btn btn-sm border border-success" style="padding: 0;"
                                        title="Approve">
                                        <i class="bi bi-check"></i>
                                    </button>

                                    <button wire:click="rejectApplication({{ $application->id }})"
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
            {{ $this->pendingStorageApplications->links('vendor.livewire.bootstrap') }}
        </div>
    </div>


    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }"
        @application-updated.window="
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

    @push('styles')
        <style>
            .avatar {
                width: 2.5rem;
                height: 2.5rem;
            }

            .avatar-sm {
                width: 2rem;
                height: 2rem;
            }

            .avatar-initial {
                width: 100%;
                height: 100%;
            }

            .fade-in {
                animation: fadeIn 0.3s ease-in;
            }

            .fade-out {
                animation: fadeOut 0.3s ease-out;
            }

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

            @keyframes fadeOut {
                from {
                    opacity: 1;
                }

                to {
                    opacity: 0;
                }
            }
        </style>
    @endpush
</div>
