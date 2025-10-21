<div>
    @include('livewire.includes.header2', [
        'title' => 'Storage Application Management',
        'subtitle' => 'Approve or Review Storage Requests.',
    ])

    <div class="container-fluid">
        <div class="card text-center">
            <div class="card-header mb-3">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item" style="flex: 1;">
                        <button class="nav-link w-100 {{ $this->status == 'pending' ? 'active disabled' : '' }}"
                            wire:click="$set('status', 'pending')">Pending Application</button>
                    </li>
                    <li class="nav-item" style="flex: 1;">
                        <button class="nav-link w-100 {{ $this->status == 'active' ? 'active disabled' : '' }}"
                            wire:click="$set('status', 'active')">Approved Application</button>
                    </li>
                    <li class="nav-item" style="flex: 1;">
                        <button class="nav-link w-100 {{ $this->status == 'reject' ? 'active disabled' : '' }}"
                            wire:click="$set('status', 'reject')">Rejected Application</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                @if ($this->status === 'pending')
                    <livewire:storage-application-pending lazy="on-load" />
                @elseif($this->status === 'active')
                    <livewire:storage-application-active lazy="on-load" />
                @elseif($this->status === 'reject')
                    <livewire:storage-application-reject lazy="on-load" />
                @else
                    {{-- Error --}}
                    <div class="alert alert-danger d-flex align-items-start" role="alert">
                        <svg class="flex-shrink-0 me-3" width="24" height="24" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path
                                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </svg>
                        <div>
                            <h5 class="alert-heading mb-1">Invalid Application Status</h5>
                            <p class="mb-0">
                                The application status "{{ $this->status }}" is not recognized. Please contact support
                                if this
                                issue persists.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
