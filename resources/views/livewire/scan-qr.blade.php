<div>
    {{-- Stop trying to control. --}}
    @include('livewire.includes.header2', [
        'title' => 'Scan QR Code',
        'subtitle' => 'Point your device camera at a QR code to begin scanning.',
    ])
    {{-- READER --}}
    <div class="container mb-4">
        {{-- Scanner Section --}}
        <section class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark mb-2">
                        <i class="bi bi-qr-code-scan me-2 text-primary"></i>QR Code Scanner
                    </h4>
                    <p class="text-muted small mb-0">Scan or enter QR token to validate</p>
                </div>

                {{-- Scanner Display --}}
                <div class="d-flex justify-content-center mb-4">
                    <div id="reader" wire:ignore>
                    </div>
                </div>

                {{-- Control Buttons --}}
                <div class="d-flex justify-content-center gap-3 mb-4">
                    <button id="startBtn" type="button" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-play me-2"></i>Start Scanning
                    </button>
                    <button id="stopBtn" type="button" class="btn btn-danger px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-stop me-2"></i>Stop Scanning
                    </button>
                </div>

                {{-- Manual Input Form --}}
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <form wire:submit.prevent="validateQR">
                            <label for="qr_token" class="form-label fw-semibold text-dark mb-2">
                                <i class="fas fa-keyboard me-2"></i>Manual Token Entry
                            </label>
                            <div class="input-group shadow-sm">
                                <input type="text" id="qr_token" class="form-control border py-3"
                                    placeholder="xxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" wire:model="scannedToken">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-check me-2"></i>Submit
                                </button>
                            </div>
                            @error('scannedToken')
                                <div class="alert alert-danger mt-2 py-2 px-3 small">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Details Section --}}
        @php
            $qrCode = App\Models\QRCode::where('id', $this->qrId)->first();
            $storageApp = $qrCode ? $qrCode->storageApplication : null;
        @endphp

        @if ($storageApp)
            <section class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-gradient text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Student Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="bg-light rounded-3 p-4 mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Student Name</small>
                                        <strong>{{ $storageApp->applicant->name }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-id-card text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Matric No</small>
                                        <strong>{{ $storageApp->applicant->matric_no }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-file-alt text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Application ID</small>
                                        <strong>#{{ $storageApp->id }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-door-open text-warning"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Storage Room</small>
                                        <strong>{{ $storageApp->openArea->storageRoom->id ?? $storageApp->locker->storageRoom->id }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if ($storageApp->locker)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-lock text-danger"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Locker</small>
                                            <strong>#{{ $storageApp->locker->id }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($storageApp->openArea)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-box-open text-secondary"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Open Area</small>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-outline-success px-4 py-2 rounded-pill"
                            wire:click="checkin">
                            <i class="fas fa-sign-in-alt me-2"></i>Complete Check-in
                        </button>
                        <button type="button" class="btn btn-outline-warning px-4 py-2 rounded-pill"
                            wire:click="checkout">
                            <i class="fas fa-sign-out-alt me-2"></i>Complete Check-out
                        </button>
                    </div>
                </div>
            </section>
        @endif

        {{-- Recent Logs Section --}}
        <section class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-history me-2 text-primary"></i>Recent Scan Logs
                    </h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $this->recentLogs->count() }} logs</span>
                </div>
            </div>

            <div class="card-body p-0">
                @forelse ($this->recentLogs as $log)
                    <div class="log-item border-bottom p-4 transition-all"
                        onmouseover="this.style.backgroundColor='#f8f9fa'"
                        onmouseout="this.style.backgroundColor='white'">
                        <div class="row align-items-center">
                            <div class="col-lg-2 col-md-3 mb-2 mb-lg-0">
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-semibold">#{{ $log->id }}</span>
                            </div>

                            <div class="col-lg-3 col-md-4 mb-2 mb-lg-0">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-calendar-alt me-2 text-muted"></i>
                                    <div>
                                        <div class="small fw-semibold">{{ $log->created_at->format('M j, Y') }}</div>
                                        <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3 mb-2 mb-lg-0">
                                @php
                                    $statusClass = match (strtolower($log->event_type)) {
                                        'check_in' => 'bg-success',
                                        'check_out' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} px-3 py-2">
                                    <i
                                        class="fas fa-{{ strtolower($log->event_type) === 'check_in' ? 'arrow-right' : 'arrow-left' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $log->event_type)) }}
                                </span>
                            </div>

                            <div class="col-lg-4 col-md-2 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item py-2" href="#">
                                                <i class="fas fa-eye me-2 text-primary"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="#">
                                                <i class="fas fa-download me-2 text-success"></i>Download
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 text-danger" href="#">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No recent logs found</h6>
                        <p class="text-muted mb-0">Scan logs will appear here once you start scanning.</p>
                    </div>
                @endforelse
            </div>

            @if ($this->recentLogs->count() > 0)
                <div class="card-footer bg-white border-top py-3 text-center">
                    <a href="{{ route('scan_log') }}">
                        <button class="btn btn-outline-primary rounded-pill px-4" wire:click="viewAllLogs">
                            <i class="fas fa-list me-2"></i>View All Logs
                        </button>
                    </a>
                </div>
            @endif
        </section>
    </div>

</div>

{{-- HTML5QRCODE --}}
@assets
    <style>
        .log-item:last-child {
            border-bottom: none !important;
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

        #reader {
            width: 500px;
            height: 375px;
            background: linear-gradient(135deg, #042046 0%, #2e4059 100%);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card {
            transition: all 0.3s ease;
        }

        .input-group {
            overflow: hidden;
        }

        .input-group .form-control {
            border-right: 1px solid #dee2e6;
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
@endassets

@script
    <script>
        const scanner = new Html5Qrcode("reader");
        const startbtn = document.getElementById('startBtn');
        const stopbtn = document.getElementById('stopBtn');

        async function startScanner() {
            if (scanner.getState() === 2) return;
            try {
                await scanner.start({
                        facingMode: "environment"
                    }, {
                        fps: 20,
                        qrbox: 250
                    },
                    (decodedText) => {
                        $wire.call('validateQR', decodedText);
                        stopScanner();
                    },
                    (errorMessage) => {
                        console.log("Unable to detect a QR Code.", errorMessage);
                    }
                )
                startbtn.disabled = true;
                stopbtn.disabled = false;
            } catch (e) {
                console.error(e);
            }
        }

        async function stopScanner() {
            if (scanner.getState() === 1) return;
            try {
                await scanner.stop()
                stopbtn.disabled = true;
                startbtn.disabled = false;
            } catch (err) {
                console.error("Unable to stop scanning.", err);
            }
        }

        startbtn.addEventListener('click', startScanner);
        stopbtn.addEventListener('click', stopScanner);
        document.addEventListener('livewire:initialized', () => {
            startScanner();
        });
        document.addEventListener('livewire:navigate', () => {
            stopScanner();
        });
    </script>
@endscript
