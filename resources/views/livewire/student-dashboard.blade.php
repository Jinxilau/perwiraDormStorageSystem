<div>
    <style>
        .table-header {
            background-color: #dc3545 !important;
            color: white;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
            border: 2px solid #000;
        }

        .process-cell {
            font-weight: bold;
            font-size: 16px;
        }

        .table {
            border: 2px solid #000;
        }

        .dashboard-notification-item {
            transition: background-color 0.2s;
        }

        .dashboard-notification-item:hover {
            background-color: #f8f9fa;
        }

        .dashboard-notification-unread {
            background-color: #e7f3ff77;
            border-left: 3px solid #0d6efd;
        }

        .view-all-link {
            text-decoration: none;
            font-weight: 500;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        .notification-card-icon {
            width: 40px;
            height: 40px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-view-details {
            font-weight: 500;
            border-width: 2px;
            transition: all 0.3s ease;
        }

        .btn-view-details:hover {
            background: #0d6efd;
            border-color: transparent;
            color: white;
            transform: translateX(4px);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.15;
            }
        }
    </style>

    @include('livewire.includes.student-title2', [
        'title' => 'Student Dashboard',
        'subtitle' => 'Welcome to your dashboard overview',
    ])

    <!-- Storage Guidelines Section -->
    <div class="card shadow-lg mb-5 overflow-hidden border-0 position-relative"
        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #547cc1 100%);">
        <!-- Animated decorative shapes -->
        <div class="position-absolute w-100 h-100 top-0 start-0 overflow-hidden" style="z-index: 1;">
            <!-- Large circle -->
            <div class="position-absolute rounded-circle"
                style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.05); top: -100px; right: -50px; animation: float 6s ease-in-out infinite;">
            </div>

            <!-- Small squares -->
            <div class="position-absolute"
                style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.03); top: 30%; left: 10%; transform: rotate(45deg); animation: rotate 20s linear infinite;">
            </div>

            <div class="position-absolute"
                style="width: 60px; height: 60px; background: rgba(255, 193, 7, 0.08); bottom: 25%; right: 15%; transform: rotate(25deg); animation: rotate 15s linear infinite reverse;">
            </div>

            <!-- Hexagon shape -->
            <div class="position-absolute"
                style="width: 100px; height: 100px; background: rgba(126, 34, 206, 0.1); top: 20%; right: 25%; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); animation: pulse 7s ease-in-out infinite;">
            </div>
        </div>

        <div class="card-body px-4 px-md-5 py-3 position-relative" style="z-index: 2;">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-4 text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="bi bi-book fs-3 me-2"></i>
                        Storage Guidelines
                    </h3>

                    <ul class="mb-4 ps-0 list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <div class="bg-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="min-width: 32px; height: 32px;">
                                <i class="bi bi-check-lg text-dark fw-bold"></i>
                            </div>
                            <span class="text-white">You may keep up to 3 items, and only one storage request.</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <div class="bg-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="min-width: 32px; height: 32px;">
                                <i class="bi bi-check-lg text-dark fw-bold"></i>
                            </div>
                            <span class="text-white">Please select both an open area and a locker.</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <div class="bg-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="min-width: 32px; height: 32px;">
                                <i class="bi bi-check-lg text-dark fw-bold"></i>
                            </div>
                            <span class="text-white">No food, liquids, or perishables.</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <div class="bg-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="min-width: 32px; height: 32px;">
                                <i class="bi bi-check-lg text-dark fw-bold"></i>
                            </div>
                            <span class="text-white">Valuable items stored at own risk.</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <div class="bg-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                                style="min-width: 32px; height: 32px;">
                                <i class="bi bi-check-lg text-dark fw-bold"></i>
                            </div>
                            <span class="text-white">Collection deadline: During early semester.</span>
                        </li>
                    </ul>

                    <a href="{{ route('help_and_support') }}" wire:navigate
                        class="text-warning text-decoration-none fw-semibold d-inline-flex align-items-center position-relative"
                        style="transition: all 0.3s;"
                        onmouseover="this.style.transform='translateX(5px)'; this.style.color='#ffc107'"
                        onmouseout="this.style.transform='translateX(0)'; this.style.color='#ffc107'">
                        Learn More About Storage
                        <i class="fas fa-arrow-right ms-2" style="font-size: 0.9rem;"></i>
                    </a>
                </div>

                <div class="col-lg-4 text-lg-end text-center">
                    <a href="{{ route('storage') }}" class="text-decoration-none d-inline-block w-100">
                        <button
                            class="btn btn-warning btn-lg px-4 py-3 shadow-lg fw-bold w-100 position-relative overflow-hidden"
                            style="transition: all 0.3s; border: none; color: #1e3c72;"
                            onmouseover="this.style.transform='translateY(-3px) scale(1.02)'; this.style.boxShadow='0 12px 30px rgba(255, 193, 7, 0.4)';"
                            onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)';">
                            <i class="fas fa-box me-2"></i>Apply Storage Now!
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Process Details Table -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-calendar-alt text-primary me-2"></i>
                Details of the Storage and Receiving Process
            </h5>
        </div>
        <div class="card-body p-2 py-3">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-bold">Proses</th>
                            <th class="fw-bold">Tarikh</th>
                            <th class="fw-bold">Masa</th>
                            <th class="fw-bold">Rujukan</th>
                            <th class="fw-bold">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold align-middle" rowspan="2">
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                    Penyimpanan Barang
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span>21.7.2025</span>
                                    <small class="text-muted">Hingga</small>
                                    <span>25.7.2025</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="mb-2">
                                    9:00 pagi – 11:00 pagi
                                </div>
                                <div class="mb-2">
                                    2:30 petang – 4:00 petang
                                </div>
                                <small class="text-muted fst-italic">Sila berurusan dengan staf di Pejabat</small>
                            </td>
                            <td class="align-middle">
                                Staf Pejabat KKLK
                            </td>
                            <td class="align-middle" rowspan="3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Stor simpanan barang</div>
                                        <small class="text-muted">E1-06 / E1-03</small>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <span>4.10.2025</span>
                                    <span class="text-muted">&</span>
                                    <span>5.10.2025</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                9:00 pagi – 4:30 Petang
                            </td>
                            <td class="align-middle">
                                Majlis Kepimpinan Kolej (MKP) KKLK
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold align-middle">
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    Pengambilan Barang
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span>6.10.2025</span>
                                    <small class="text-muted">Hingga</small>
                                    <span>10.10.2025</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                9:00 pagi – 11:00 pagi
                            </td>
                            <td class="align-middle">
                                Staf Pejabat KKLK
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Recent Notification --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-bell me-2 text-primary"></i>Recent Notifications</h5>
            <a href="{{ route('student_notification') }}" wire:navigate class="view-all-link text-primary">
                View All
            </a>
        </div>
        <div class="card-body p-0" id="recentNotifications">
            @forelse ($this->notifications as $notification)
                <div
                    class="dashboard-notification-item p-3 border-bottom @if (is_null($notification->read_at)) dashboard-notification-unread @endif">
                    <div class="d-flex gap-3">
                        {{-- Notification Icon --}}
                        <div class="pt-1">
                            <div class="me-2">
                                @php
                                    $badgeClass = 'bg-secondary';
                                    $iconClass = 'bi-bell';
                                    if (isset($notification->data['type'])) {
                                        switch ($notification->data['type']) {
                                            case 'general':
                                                $badgeClass = 'bg-primary';
                                                $iconClass = 'bi-info-circle';
                                                break;
                                            case 'success':
                                                $badgeClass = 'bg-success';
                                                $iconClass = 'bi-check-circle';
                                                break;
                                            case 'announcement':
                                                $badgeClass = 'bg-info';
                                                $iconClass = 'bi-megaphone';
                                                break;
                                            case 'message':
                                                $badgeClass = 'bg-warning';
                                                $iconClass = 'bi-envelope';
                                                break;
                                            case 'warning':
                                                $badgeClass = 'bg-danger';
                                                $iconClass = 'bi-exclamation-triangle';
                                                break;
                                        }
                                    }
                                @endphp
                                <div
                                    class="notification-card-icon {{ $badgeClass }} rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi {{ $iconClass }} fs-5 text-white"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            {{-- Title --}}
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="card-title mb-0 me-2 fw-bold text-dark">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h5>
                            </div>
                            {{-- Message --}}
                            <p class="text-muted mb-2 small">
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            </p>

                            {{-- Footer: Link and Timestamp --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3">
                                <div>
                                    @if (isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}"
                                            class="btn btn-sm btn-outline-primary btn-view-details rounded-pill px-4"
                                            wire:click="markAsRead('{{ $notification->id }}')">
                                            <i class="bi bi-arrow-right-circle me-1"></i>
                                            View Details
                                        </a>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.75rem;">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            <div class="d-flex flex-column align-items-center">
                <div style="font-size: 4rem;">📭</div>
                <h3 class="mt-3">No Notifications</h3>
                <p class="text-muted">You're all caught up! Check back later for new updates.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
