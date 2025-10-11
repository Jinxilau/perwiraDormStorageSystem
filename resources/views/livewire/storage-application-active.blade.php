<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}

    {{-- Modal --}}
    <div tabindex="-1" :class="{ 'd-flex align-items-center justify-content-center': show }"
        style="display: none; z-index: 9000; inset: 0;" class="position-fixed" id="lockerModal" x-data="{ show: false }"
        @display_modal.window="show = true" x-show='show' x-effect="document.body.style.overflow = show ? 'hidden': ''">

        <div class="position-fixed" style="background-color: rgba(0, 0, 0, 0.5); inset:0;" @click="show = false"></div>

        @if ($this->application)
            <div class="card" x-transition>
                <div class="card-header">
                    Application - {{ $this->application->id }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">QR Code</h5>
                    {!! QrCode::size(200)->generate($this->application->qrCode->qr_token) !!}
                </div>
            </div>
        @endif
    </div>
    
    <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="align-middle">
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
                            Check-in Date
                        </th>
                        <th class="align-middle py-2">
                            Check-out Date
                        </th>
                        <th class="align-middle py-2">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse($this->approvedStorageApplications as $application)
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
                                @foreach ($application->items as $item)
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
                                <span class="badge text-bg-success">
                                    {{ ucfirst($application->storage_application_status) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    {{-- {{ $application->qrCode->scanLog-> }} --}}
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{-- {{ $application->qrCode->scanLog-> }} --}}
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center space-x-2" style="white-space: nowrap">
                                    <button @click="$dispatch('view_modal', {id: {{ $application->id }}})"
                                        title="View Details" class="btn btn-sm border border-secondary"
                                        style="padding: 0;" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    {{-- <button wire:click="approveApplication({{ $application->id }})"
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
                                    </button> --}}
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
            {{ $this->approvedStorageApplications->links('vendor.livewire.bootstrap') }}
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
