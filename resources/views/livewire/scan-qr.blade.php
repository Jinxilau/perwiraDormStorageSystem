<div>
    {{-- Stop trying to control. --}}
    {{-- READER --}}
    <section class="card w-100 d-flex flex-column justify-content-center p-2 border border-secondary-subtle">
        <div class="mx-auto">
            <div id="reader"
                style="width: 500px; height:375px; background-color: rgb(113, 113, 113); border: 2px solid rgb(0, 0, 0)"
                wire:ignore></div>
        </div>
        <div class="card-body align-self-center d-flex gap-4" x-data="{ status: true }">
            <button id="startBtn" type="button" class="card-text btn btn-success" :disabled="status"
                @click="$dispatch('start_scanner'); status=true">
                Start Scanning
            </button>
            <button id="stopBtn" type="button" class="card-text btn btn-danger" :disabled="!status"
                @click="$dispatch('stop_scanner'); status=false">
                Stop Scanning
            </button>
        </div>
        <form class="mx-auto" wire:submit.prevent="validateQR">
            <label for="qr_token" class="form-label ">QR Token:</label>
            <div class="input-group">
                <input type="text" id="qr_token" class="form-control border border-dark" style="width: 300px"
                    placeholder="xxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" wire:model="scannedToken">
                <button type="submit" class="btn btn-primary border border-dark">Submit</button>
            </div>
            @error('scannedToken')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </form>
    </section>

    {{-- Content after READ --}}
    @if ($this->storageApp)
        <section class="card w-100 mt-3 bg-secondary">
            <h4 class="p-2 mb-0 border border-bottom">Details</h4>
            <div class="bg-secondary-subtle p-3">
                <div class="row bg-white mx-auto border border-dark">
                    <div class="col-6">Student Name:</div>
                    {{-- This is eager loaded means that it is not querying the database --}}
                    <div class="col-6">{{ $this->storageApp->applicant->name }}</div>
                    <div class="col-6">Matric No:</div>
                    <div class="col-6">{{ $this->storageApp->applicant->matric_no }}</div>
                    <div class="col-6">Storage Application:</div>
                    <div class="col-6">#{{ $this->storageApp->id }}</div>
                    <div class="col-6">Storage Room:</div>
                    <div class="col-6">
                        {{ $this->storageApp->openArea->storageRoom->id ?? $this->storageApp->locker->storageRoom->id }}
                    </div>
                    @if ($this->storageApp->locker)
                        <div class="col-6">Locker:</div>
                        <div class="col-6">#{{ $this->storageApp->locker->id }}</div>
                    @endif
                    @if ($this->storageApp->openArea)
                        <div class="col-6">Open Area:</div>
                        <div class="col-6">True</div>
                    @endif
                </div>

                <div class="mt-2 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-primary" wire:click="checkin">Complete
                        Check-in</button>
                    <button type="button" class="btn btn-outline-primary" wire:click="checkout">Complete
                        Check-out</button>
                </div>
            </div>
        </section>
    @endif

    {{-- Recent Logs --}}
    <section class="card mt-2 border border-dark">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Recent Scan Logs
            </h5>
            <span class="badge bg-secondary">{{ $this->recentLogs->count() }} logs</span>
        </div>
        <div class="card-body p-0">
            @forelse ($this->recentLogs as $log)
                <div class="log-item border-bottom p-3 hover-bg-light">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <span class="badge bg-primary">#{{ $log->id }}</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ $log->created_at->format('M j, Y') }}
                            </small>
                            <br>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </div>
                        <div class="col-4">
                            @php
                                $statusClass = match (strtolower($log->event_type)) {
                                    'check_in' => 'bg-success',
                                    'check_out' => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($log->event_type) }}
                            </span>
                        </div>
                        <div class="col-2 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>ViewDetails</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"><i
                                                class="fas fa-download me-2"></i>Download</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#">
                                            <iclass="fas fa-trash me-2"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h6 class="text-muted">No recent logs found</h6>
                    <p class="text-muted mb-0">Scan logs will appear here once you start scanning.</p>
                </div>
            @endforelse
        </div>

        @if ($this->recentLogs->count() > 0)
            <div class="card-footer text-center">
                <a href="">
                    <button class="btn btn-outline-primary btn-sm" wire:click="viewAllLogs">
                        <i class="fas fa-list me-2"></i>View All Logs
                    </button>
                </a>
            </div>
        @endif
    </section>

    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }"
        @scanned.window="
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

{{-- HTML5QRCODE --}}
@push('styles')
    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
            transition: background-color 0.2s ease;
        }

        .log-item:last-child {
            border-bottom: none !important;
        }

        .card-title i {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .log-item .row {
                text-align: center;
            }

            .log-item .col-2,
            .log-item .col-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
@endpush
@push('scripts')
    <script>
        const scanner = new Html5Qrcode("reader"); // Instantiate Scanner

        // Scanner function
        const startScanner = async () => {
            try {
                await scanner.start({
                        facingMode: "environment"
                    }, // Camera
                    {
                        fps: 20,
                        qrbox: 250
                    },
                    (decodedText) => {
                        $wire.call('validateQR', decodedText)
                    }, // Success Callback
                    (errorMessage) => {
                        console.log("Unable to detect a QR Code.", errorMessage)
                    } // Error Callback
                )
            } catch (e) {
                console.error(e);
            }
        };

        const stopScanner = async () => {
            try {
                await scanner.stop()
            } catch (err) {
                console.error("Unable to stop scanning.", err);
            }
        };

        // document.addEventListener('livewire:init', startScanner);
        document.addEventListener('start_scanner', startScanner);
        document.addEventListener('stop_scanner', stopScanner);
        document.addEventListener('livewire:navigate', stopScanner);
    </script>
@endpush
