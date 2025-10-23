<div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="display-5 fw-bold text-dark">Notifications</h2>
            <p class="text-muted">You have {{ count($this->notifications) }} new alerts</p>
        </div>

        @if ($this->notifications->where('read_at', null)->count() > 0)
            <button wire:click="markAllAsRead" class="btn btn-primary btn-sm">
                Mark All as Read
            </button>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            @forelse ($this->notifications as $notification)
                <div
                    class="card mb-3 {{ is_null($notification->read_at) ? 'border-start border-primary border-4' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start">
                                    <!-- Notification Icon -->
                                    <div class="me-3">
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            $icon = '🔔';

                                            if (isset($notification->data['type'])) {
                                                switch ($notification->data['type']) {
                                                    case 'general':
                                                        $badgeClass = 'bg-primary';
                                                        $icon = '🤖';
                                                        break;
                                                    case 'success':
                                                        $badgeClass = 'bg-success';
                                                        $icon = '✓';
                                                        break;
                                                    case 'announcement':
                                                        $badgeClass = 'bg-info';
                                                        $icon = '📢';
                                                        break;
                                                    case 'message':
                                                        $badgeClass = 'bg-warning';
                                                        $icon = '✉️';
                                                        break;
                                                    case 'warning':
                                                        $badgeClass = 'bg-danger';
                                                        $icon = '⚠️';
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-circle p-2"
                                            style="font-size: 1.2rem;">
                                            {{ $icon }}
                                        </span>
                                    </div>

                                    <div class="flex-grow-1">
                                        <!-- Title -->
                                        <h5 class="card-title mb-2">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                            @if (is_null($notification->read_at))
                                                <span class="badge bg-primary ms-2">New</span>
                                            @endif
                                        </h5>

                                        <!-- Message -->
                                        <p class="card-text text-muted mb-2">
                                            {{ $notification->data['message'] ?? ($notification->data['body'] ?? 'You have a new notification.') }}
                                        </p>

                                        <!-- Link if available -->
                                        @if (isset($notification->data['url']))
                                            <a href="{{ $notification->data['url'] }}"
                                                class="btn btn-link btn-sm p-0 text-decoration-none"
                                                wire:click="markAsRead('{{ $notification->id }}')">
                                                View Details
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        @endif

                                        <!-- Timestamp -->
                                        <div class="text-muted small mt-2">
                                            <i class="bi bi-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex align-items-center gap-2">
                                @if (is_null($notification->read_at))
                                    <button wire:click="markAsRead('{{ $notification->id }}')"
                                        class="btn btn-sm btn-outline-primary" title="Mark as read"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                @endif

                                <button wire:click="deleteNotification('{{ $notification->id }}')"
                                    wire:confirm="Are you sure you want to delete this notification?"
                                    class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="tooltip">
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
</div>

@assets
    <style>
        .border-4 {
            border-width: 4px !important;
        }
    </style>
@endassets
