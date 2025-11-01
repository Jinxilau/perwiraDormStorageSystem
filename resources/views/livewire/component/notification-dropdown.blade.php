<div x-data="{ open: false, show: false, notification: null }"
    @show_notification.window="show = true; notification = $event.detail.notification; setTimeout(() => show = false, 5000)"
    class="notification-dropdown">
    <button class="notification-button position-relative" @click="open = !open">
        <span class="notification-icon">🔔</span>
        @if ($this->countUnread > 0)
            <span class="notification-badge">
                {{ $this->countUnread > 99 ? '99+' : $this->countUnread }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @click.outside="open = false" @keydown.escape.window="open = false" x-transition
        class="notification-dropdown-menu" style="display: none">

        <div class="notification-header">
            <span class="fw-bold text-dark">Notifications</span>
            @if ($this->countUnread > 0)
                <button wire:click="markAllAsRead" class="mark-all-btn" @click="setTimeout(() => open = false, 300)">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="notification-list">
            @forelse ($this->unreadNotifications as $notification)
                <div class="notification-item" wire:key="notification-{{ $notification->id }}">
                    <div class="notification-content">
                        <div class="notification-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                        </div>
                        <div class="notification-text">
                            <p class="mb-0 fw-bold">{{ $notification->data['title'] }}</p>
                            {{ $notification->data['message'] }}
                            @if (isset($notification->created_at))
                                <span class="notification-time">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button wire:click="markAsRead('{{ $notification->id }}')" class="notification-mark-read"
                        title="Mark as read">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </button>
                </div>
            @empty
                <div class="notification-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                        viewBox="0 0 16 16" opacity="0.5">
                        <path
                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                    </svg>
                    <p class="mb-0 mt-2">No new notifications</p>
                </div>
            @endforelse
        </div>

        @php
            $role = auth()->user()->role_id;
            $route = $role === 1 ? 'admin_notification' : ($role === 2 ? 'staff_notification' : 'student_notification');
        @endphp
        @if ($this->unreadNotifications->count() > 0)
            <div class="notification-footer">
                <a href="{{ route($route) }}" class="view-all-link">
                    View all notifications
                </a>
            </div>
        @endif
    </div>

    {{-- Latest Notification --}}
    <div class="position-absolute end-0" style="z-index: 9999; top: calc(100% + 0.5rem); ">
        <template x-if="show">
            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-full" class="toast show mb-2" role="alert"
                aria-live="assertive" aria-atomic="true">

                <div class="toast-header">
                    <i class="bi bi-bell-fill me-2"></i>
                    <strong class="me-auto">New Notification</strong>
                    @if ($this->braodcastNotificationId)
                        <button type="button" class="btn-close"
                            @click="show = false; $wire.markAsRead(notification.id)"></button>
                    @endif
                </div>

                <div class="toast-body">
                    <p class="fw-semibold text-dark mb-1" x-text="notification.title"></p>
                    <p class="text-muted small mb-0" x-text="notification.message"></p>
                </div>
            </div>
        </template>
    </div>
</div>
