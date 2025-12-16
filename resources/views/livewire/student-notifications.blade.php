<div class="container">
    @php
        $unread = $this->notifications->whereNull('read_at')->count();
    @endphp
    @include('livewire.includes.student-title2', [
        'title' => 'Notifications',
        'subtitle' => 'You have ' . $unread . ' new alerts',
    ])

    <div class="d-flex justify-content-between align-items-center mb-4">
        <button wire:click="markAllAsRead" class="btn btn-primary btn-mark-all rounded-pill px-4 py-2 shadow-sm"
            @if ($unread <= 0) disabled @endif>
            <i class="bi bi-check-all me-2"></i>Mark All as Read
        </button>

        {{ $this->notifications->links('vendor.livewire.bootstrap') }}
    </div>

    {{-- Notifications List --}}
    <div class="row">
        <div class="col-12">
            @forelse ($this->notifications as $notification)
                <div
                    class="notification-card card mb-4 {{ is_null($notification->read_at) ? 'unread-notification' : '' }}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start">
                                    {{-- Notification Icon --}}
                                    <div class="me-4">
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
                                            <i class="bi {{ $iconClass }} fs-4 text-white"></i>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1">
                                        {{-- Title --}}
                                        <div class="d-flex align-items-center mb-2">
                                            <h5 class="card-title mb-0 me-2 fw-bold text-dark">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h5>
                                            @if (is_null($notification->read_at))
                                                <span class="badge bg-info rounded-pill pulse-badge">New</span>
                                            @endif
                                        </div>

                                        {{-- Message --}}
                                        <p class="card-text text-secondary mb-3"
                                            style="line-height: 1.7; font-size: 0.95rem;">
                                            {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                        </p>

                                        {{-- Footer: Link and Timestamp --}}
                                        <div
                                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3">
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

                                            <div class="text-muted small d-flex align-items-center">
                                                <i class="bi bi-clock me-2"></i>
                                                <span
                                                    class="fw-medium">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex align-items-start gap-2 ms-3">
                                @if (is_null($notification->read_at))
                                    <button wire:click="markAsRead('{{ $notification->id }}')"
                                        class="btn btn-sm btn-outline-success rounded-circle action-btn"
                                        title="Mark as read" data-bs-toggle="tooltip">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                @endif
                                <button wire:click="deleteNotification('{{ $notification->id }}')"
                                    wire:confirm="Are you sure you want to delete this notification?"
                                    class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="Delete"
                                    data-bs-toggle="tooltip">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div style="font-size: 4rem;">📭</div>
                        <h3 class="mt-3">No Notifications</h3>
                        <p class="text-muted">You're all caught up! Check back later for new updates.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    {{ $this->notifications->links('vendor.livewire.bootstrap') }}

    <style>
        .icon-wrapper {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-mark-all {
            font-weight: 500;
            border: none;
            background: #0d6efd;
            transition: all 0.3s ease;
        }

        .btn-mark-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Notification Card Styling */
        .notification-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 6px solid transparent;
            border-radius: 16px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .notification-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .unread-notification {
            border-left-color: #667eea;
            background: linear-gradient(to right, rgba(102, 126, 234, 0.04), white);
            box-shadow: 0 2px 12px rgba(102, 126, 234, 0.15);
        }

        /* Notification Icon */
        .notification-card-icon {
            width: 56px;
            height: 56px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Badge Animation */
        .pulse-badge {
            font-weight: 600;
            padding: 0.4em 0.8em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: translateY(0);
            }

            50% {
                opacity: 0.5;
                transform: translateY(-2px);
            }
        }

        /* Action Buttons */
        .action-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .action-btn:hover {
            transform: scale(1.15) rotate(5deg);
        }

        .btn-outline-success:hover {
            background: green;
            border-color: transparent;
        }

        .btn-outline-danger:hover {
            background: red;
            border-color: transparent;
        }

        /* View Details Button */
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

        /* Empty State */
        .empty-state-card {
            border-radius: 16px;
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: #0d6efd;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-mark-all {
                width: 100%;
            }

            .notification-card-icon {
                width: 48px;
                height: 48px;
            }

            .action-btn {
                width: 36px;
                height: 36px;
            }
        }
    </style>
</div>
