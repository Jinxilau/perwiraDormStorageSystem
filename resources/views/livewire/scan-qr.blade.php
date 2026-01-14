<div x-data
    x-on:scroll-to-details.window="
        $nextTick(() => {
            document.getElementById('student-details')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
        })
    ">
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

                {{-- Scanner --}}
                <div x-data="{
                    scanner: null,
                    scanning: false,
                    isTransitioning: false, // Guard variable
                    errorMessage: '',
                
                    init() {
                        // Delay slightly to ensure Livewire has finished DOM manipulation
                        this.$nextTick(() => this.start());
                    },
                
                    async start() {
                        // Prevent overlapping calls
                        if (this.scanning || this.isTransitioning) return;
                
                        const container = document.getElementById('reader');
                        if (!container) return;
                
                        // CRITICAL FIX: Clear the container's innerHTML to prevent double UI
                        if (container.innerHTML !== '') {
                            container.innerHTML = '';
                        }
                
                        if (!this.scanner) {
                            this.scanner = new Html5Qrcode('reader');
                        }
                
                        this.isTransitioning = true;
                        this.errorMessage = '';
                
                        try {
                            await this.scanner.start({ facingMode: 'environment' }, { fps: 20, qrbox: 250 },
                                (decodedText) => this.onSuccess(decodedText),
                                () => {}
                            );
                            this.scanning = true;
                        } catch (e) {
                            console.error('Scanner start failed', e);
                            this.errorMessage = 'Camera access denied or not found.';
                        } finally {
                            this.isTransitioning = false;
                        }
                    },
                
                    async stop() {
                        // 1. Guard against null scanner
                        if (!this.scanner) {
                            console.warn('Scanner does not exist, cannot stop.');
                            this.scanning = false;
                            return;
                        }
                
                        // 2. Guard against state transitions
                        if (this.isTransitioning) return;
                
                        this.isTransitioning = true;
                
                        try {
                            // 3. Only call stop if the library is in a 'SCANNING' state (State 2)
                            // This prevents the 'Cannot stop a non-running scanner' error
                            if (this.scanner.getState() === 2) {
                                await this.scanner.stop();
                            }
                        } catch (e) {
                            console.error('Stop failed', e);
                        } finally {
                            this.scanning = false;
                            this.isTransitioning = false;
                        }
                    },
                
                    async onSuccess(token) {
                        // Optional: Add a small haptic feedback or sound here
                        this.$wire.call('validateQR', token);
                        await this.stop();
                    },
                
                    async destroy() {
                        // This runs automatically when Alpine removes the element
                        if (this.scanner) {
                            try {
                                if (this.scanner.getState() === 2) {
                                    await this.scanner.stop();
                                }
                                this.scanner.clear();
                            } catch (e) {}
                        }
                    }
                }" x-on:livewire:navigated.window="destroy();">

                    {{-- Error Display --}}
                    <template x-if="errorMessage">
                        <div class="alert alert-danger mb-3" x-text="errorMessage"></div>
                    </template>

                    {{-- Scanner Display --}}
                    <div class="d-flex justify-content-center mb-4">
                        <div id="reader" wire:ignore></div>
                    </div>

                    {{-- Controls --}}
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <button type="button" class="btn btn-success px-4 py-2 rounded-pill shadow-sm" @click="start"
                            :disabled="scanning || isTransitioning">
                            <i class="fas fa-play me-2"></i>Start Scanning
                        </button>

                        <button type="button" class="btn btn-danger px-4 py-2 rounded-pill shadow-sm" @click="stop"
                            :disabled="!scanning || isTransitioning">
                            <span x-show="!isTransitioning"><i class="fas fa-stop"></i> Stop</span>
                            <span x-show="isTransitioning">Processing...</span>
                        </button>
                    </div>
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
        @if ($qrCode && $qrCode->storageApplication)
            @php
                $storageApp = $qrCode->storageApplication;
            @endphp
            <section id="student-details"
                class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4 student-details-card">
                <div class="card-header bg-primary text-white position-relative overflow-hidden">
                    <div class="header-pattern"></div>
                    <h5 class="mb-0 fw-bold d-flex align-items-center position-relative">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <span>Storage Details</span>
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Student Information Card --}}
                    <div class="info-section mb-4">
                        <h6 class="section-title mb-3">
                            <i class="fas fa-user-graduate me-2"></i>Student Information
                        </h6>
                        <div class="info-card bg-gradient-light rounded-4 p-4 shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="info-label">Full Name</label>
                                        <div class="info-value">{{ $storageApp->applicant->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="info-label">Matric Number</label>
                                        <div class="info-value">{{ $storageApp->applicant->matric_no }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="info-label">Gender</label>
                                        <div class="info-value">
                                            <span class="badge bg-primary bg-opacity-15 px-3 py-2">
                                                <i
                                                    class="fas fa-{{ $storageApp->applicant->gender === 'Male' ? 'mars' : 'venus' }} me-1"></i>
                                                {{ ucfirst($storageApp->applicant->gender) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <label class="info-label">Year of Study</label>
                                        <div class="info-value">
                                            <span class="badge bg-success bg-opacity-15 px-3 py-2">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                Year {{ $storageApp->applicant->year_of_study }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QR & Storage Information --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-qrcode me-2"></i>QR Code Status
                                </h6>
                                <div class="info-card bg-gradient-light rounded-4 p-4 shadow-sm">
                                    <div class="info-item mb-3">
                                        <label class="info-label">Current Status</label>
                                        <div class="info-value">
                                            @php
                                                $statusConfig = match ($qrCode->status) {
                                                    'checked_in' => [
                                                        'class' => 'success',
                                                        'icon' => 'check-circle',
                                                        'text' => 'Checked In',
                                                    ],
                                                    'checked_out' => [
                                                        'class' => 'warning',
                                                        'icon' => 'clock',
                                                        'text' => 'Checked Out',
                                                    ],
                                                    default => [
                                                        'class' => 'secondary',
                                                        'icon' => 'circle',
                                                        'text' => ucfirst($qrCode->status),
                                                    ],
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusConfig['class'] }} px-4 py-2 fs-6">
                                                <i class="fas fa-{{ $statusConfig['icon'] }} me-2"></i>
                                                {{ $statusConfig['text'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label">Total Scans</label>
                                        <div class="info-value">
                                            <span class="scan-count">{{ $qrCode->scanned_count }}</span>
                                            <small class="text-muted ms-2">times scanned</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-warehouse me-2"></i>Storage Location
                                </h6>
                                <div class="row info-card bg-gradient-light rounded-4 p-4 shadow-sm">
                                    <div class="info-item mb-3 col-md-4">
                                        <label class="info-label">Storage Room</label>
                                        <div class="info-value">
                                            <span class="room-badge">
                                                <i class="fas fa-door-open me-2"></i>
                                                {{ $storageApp->storageRoom->room_name }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($storageApp->locker)
                                        <div class="info-item col-md-4">
                                            <label class="info-label">Locker Number</label>
                                            <div class="info-value">
                                                <span class="locker-badge">
                                                    <i class="fas fa-lock me-2"></i>
                                                    #{{ $storageApp->locker->code }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($storageApp->openArea)
                                        <div class="info-item col-md-4">
                                            <label class="info-label">Storage Type</label>
                                            <div class="info-value">
                                                <span class="locker-badge">
                                                    <i class="fas fa-box-open me-1"></i>
                                                    Open Area
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Check-in Information Section --}}
                    @if ($qrCode->status === 'checked_in')
                        <div class="info-section mb-4">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Check-in Information
                            </h6>
                            <div class="checkin-card bg-gradient-success rounded-4 p-4 shadow-sm border-success">
                                <div class="row g-4">
                                    @if ($qrCode->storage_zone)
                                        <div class="col-md-6">
                                            <div class="checkin-info-item">
                                                <label class="checkin-label">
                                                    <i class="fas fa-map-marker-alt me-2"></i>Storage Zone
                                                </label>
                                                <div class="checkin-value">
                                                    <span class="zone-badge">
                                                        <i class="fas fa-location-dot me-2"></i>
                                                        {{ $qrCode->storage_zone }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($qrCode->checkin_item_photo)
                                        <div class="col-md-6">
                                            <div class="checkin-info-item">
                                                <label class="checkin-label">
                                                    <i class="fas fa-camera me-2"></i>Storage Photo
                                                </label>
                                                <div class="checkin-value">
                                                    <a href="{{ Storage::disk('public')->url($qrCode->checkin_item_photo) }}"
                                                        target="_blank"
                                                        class="btn btn-light btn-sm px-4 py-2 shadow-sm photo-btn">
                                                        <i class="fas fa-image me-2"></i>
                                                        View Photo
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="checkin-info-item">
                                            <label class="checkin-label">
                                                <i class="fas fa-clock me-2"></i>Last Check-in Time
                                            </label>
                                            <div class="checkin-value">
                                                <span class="time-badge">
                                                    <i class="far fa-calendar-alt me-2"></i>
                                                    {{ $qrCode->updated_at->format('F j, Y - g:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($qrCode->checkin_item_photo)
                                        <div class="col-12">
                                            <div class="photo-preview-section mt-3">
                                                <img src="{{ Storage::disk('public')->url($qrCode->checkin_item_photo) }}"
                                                    alt="Storage Location"
                                                    class="storage-photo img-fluid rounded-3 shadow">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Stored Items Section --}}
                    @if ($storageApp->storedItems->count() > 0)
                        <div class="info-section mb-4">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-boxes me-2"></i>Stored Items
                                <span class="badge bg-primary ms-2">{{ $storageApp->storedItems->count() }}</span>
                            </h6>
                            <div class="items-grid">
                                @foreach ($storageApp->storedItems as $item)
                                    <div class="item-card bg-white rounded-4 p-4 shadow-sm border">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="item-header flex-grow-1">
                                                <h6 class="item-name mb-1">{{ $item->item_name }}</h6>
                                                <span class="text-muted small">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                                                </span>
                                            </div>
                                            @if ($item->item_photo_path)
                                                <a href="{{ Storage::disk('public')->url($item->item_photo_path) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="bi bi-image me-1"></i>
                                                    Photo
                                                </a>
                                            @endif
                                        </div>

                                        <div class="item-details mb-3">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <small class="text-muted d-block mb-1">Size</small>
                                                    <span class="badge bg-light text-dark border px-3 py-2">
                                                        <i class="fas fa-ruler-combined me-1"></i>
                                                        {{ ucfirst($item->estimated_size) }}
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block mb-1">Condition</small>
                                                    @php
                                                        $conditionConfig = match ($item->item_condition) {
                                                            'fragile' => [
                                                                'class' => 'danger',
                                                                'icon' => 'exclamation-triangle-fill',
                                                            ],
                                                            'bulky' => ['class' => 'warning', 'icon' => 'box-fill'],
                                                            'boxed' => [
                                                                'class' => 'success',
                                                                'icon' => 'check2-square',
                                                            ],
                                                            default => ['class' => 'secondary', 'icon' => 'tag'],
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $conditionConfig['class'] }} px-3 py-2">
                                                        <i class="bi bi-{{ $conditionConfig['icon'] }} me-1"></i>
                                                        {{ ucfirst($item->item_condition) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($item->item_description)
                                            <div class="item-description">
                                                <small class="text-muted d-block mb-1">Description</small>
                                                <p class="mb-0 small text-dark">{{ $item->item_description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="action-section">
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('check_in_application', $qrCode->storageApplication->id) }}"
                                class="btn btn-success px-5 py-3 rounded-pill shadow-sm action-btn"
                                @disabled(!$qrCode || $qrCode->status === 'checked_in') wire:click="checkin" wire:loading.attr="disabled">
                                <span wire:loading.remove class="d-flex align-items-center">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    <span>Complete Check-in</span>
                                </span>
                                <span wire:loading>
                                    <span class="d-flex align-items-center">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                            aria-hidden="true"></span>
                                        <span>Processing...</span>
                                    </span>
                                </span>
                            </a>

                            <button type="button"
                                class="btn btn-warning px-5 py-3 rounded-pill shadow-sm action-btn text-white"
                                @disabled(!$qrCode || $qrCode->status !== 'checked_in') wire:click="checkout" wire:loading.attr="disabled">
                                <span wire:loading.remove class="d-flex align-items-center">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>Complete Check-out</span>
                                </span>
                                <span wire:loading wire:target="checkout">
                                    <span class="d-flex align-items-center">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                            aria-hidden="true"></span>
                                        <span>Processing...</span>
                                    </span>
                                </span>
                            </button>
                        </div>
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

    {{-- LOADING --}}
    <div wire:loading wire:target="validateQR">
        <div
            class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center z-1050">
            <div class="bg-white p-4 rounded shadow-lg text-center border">
                <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="fw-bold text-muted">Processing...</div>
            </div>
        </div>
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

        /* Card Styling */
        .student-details-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideInUp 0.6s ease-out;
        }

        .student-details-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1.5rem 3rem rgba(0, 0, 0, 0.2) !important;
        }

        /* Header Styling */

        .header-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(circle, white 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Section Styling */
        .section-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        .info-section {
            margin-bottom: 2rem;
        }

        /* Info Card Styling */
        .bg-gradient-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .info-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        /* Info Item Styling */
        .info-item {
            padding: 0.5rem 0;
        }

        .info-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Special Badges */
        .room-badge,
        .locker-badge {
            display: inline-flex;
            align-items: center;
            background: #3459ff;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .scan-count {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        /* Items Grid */
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .item-card {
            transition: all 0.3s ease;
            border: 2px solid #667eea !important;
        }

        .item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }

        .item-name {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .item-description {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.75rem;
            border-left: 3px solid #667eea;
        }

        /* Action Buttons */
        .action-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }

        .action-btn {
            transition: all 0.3s ease;
            border: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .action-btn:hover:not(:disabled) {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.25) !important;
        }

        .action-btn:active:not(:disabled) {
            transform: translateY(-2px) scale(1);
        }

        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .items-grid {
                grid-template-columns: 1fr;
            }

            .action-btn {
                width: 100%;
                padding: 1rem 2rem !important;
            }

            .room-badge,
            .locker-badge {
                font-size: 0.95rem;
                padding: 0.6rem 1.2rem;
            }

            .scan-count {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 0.9rem;
            }
        }

        /* Badge Improvements */
        .badge {
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }
    </style>
@endassets
