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

    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    @include('livewire.includes.header2', [
        'title' => 'Dashboard',
        'subtitle' => 'Manage your storage system efficiently.',
    ])

    <div class="container">
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

</div>
