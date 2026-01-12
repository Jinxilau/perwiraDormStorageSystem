<div>
    <style>
        .text-indigo {
            color: #6610f2 !important;
        }

        .bg-indigo {
            background-color: #6610f2 !important;
        }

        .border-indigo {
            border-color: #6610f2 !important;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .stat-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-approved {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-rejected {
            background-color: #f8d7da;
            color: #842029;
        }

        .badge-investigating {
            background-color: #cfe2ff;
            color: #084298;
        }

        .badge-found {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-lost {
            background-color: #f8d7da;
            color: #842029;
        }

        .badge-dismissed {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .action-btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.813rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .section-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.25rem;
        }

        .view-all-btn {
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .view-all-btn:hover {
            transform: translateX(3px);
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: #6c757d;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .table-header {
            background-color: #dc3545 !important;
            color: white;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            vertical-align: middle;
        }

        /* Activity Timeline */
        .activity-item {
            padding: 1.25rem 1.5rem;
            display: flex;
            gap: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon.bg-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .activity-icon.bg-info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }

        .activity-icon.bg-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .activity-icon.bg-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .activity-description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .activity-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
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
        {{-- Key Metrics Section --}}
        <div class="row g-4 mb-4">
            {{-- Pending Applications --}}
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-warning">{{ $pendingApplicationsCount }}</div>
                                <div class="stat-label">Pending Applications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Storage Users --}}
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-success">{{ $activeStorageUsers }}</div>
                                <div class="stat-label">Active Storage Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Missing Reports --}}
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-danger">{{ $pendingMissingReports }}</div>
                                <div class="stat-label">Pending Reports</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Storage Capacity --}}
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-primary">{{ $lockerCapacityPercentage }}%</div>
                                <div class="stat-label">Locker Occupied</div>
                                <small class="text-muted">{{ $occupiedLockers }}/{{ $totalLockers }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Open Area Items --}}
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-box-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-success">{{ $totalItemsInOpenArea }}</div>
                                <div class="stat-label">Open Area Items</div>
                                <small class="text-muted">Total physical items</small>
                            </div>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Applications:</span>
                                <span class="fw-bold">{{ $occupiedOpenAreasCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100 border-start border-indigo border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-indigo bg-opacity-10 text-white me-3">
                                <i class="bi bi-boxes"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="stat-value text-indigo">{{ $totalActiveItems }}</div>
                                <div class="stat-label">Total Items Stored</div>
                                <small class="text-muted">Across all zones</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                @php
                                    $lockerWeight =
                                        $totalActiveItems > 0 ? ($totalItemsInLockers / $totalActiveItems) * 100 : 0;
                                    $openWeight =
                                        $totalActiveItems > 0 ? ($totalItemsInOpenArea / $totalActiveItems) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $lockerWeight }}%"></div>
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $openWeight }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2" style="font-size: 0.75rem;">
                                <span><i class="bi bi-circle-fill text-primary me-1"></i> Lockers</span>
                                <span><i class="bi bi-circle-fill text-success me-1"></i> Open Area</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Check-in/Check-out Activity Section --}}
        <div class="card section-card mb-4">
            <div class="section-header bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-qr-code-scan me-2"></i>Recent Check-in/Check-out Activity
                    </h5>
                    <a href="{{ route('scan_log') }}" wire:navigate class="view-all-btn text-white">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0" id="recentNotifications">
                @forelse ($this->recentScanLogs as $scanLog)
                    @php
                        $student = $scanLog->qrCode->storageApplication->applicant;
                    @endphp
                    <div class="scan-log-item border-bottom p-3">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="mb-1 fw-semibold">{{ $student->name }}</h6>
                                <p class="text-muted small m-0">
                                    <i class="bi bi-card-text me-1"></i>{{ $student->matric_no }}
                                </p>
                                <p class="text-muted small m-0">
                                    <i class="bi bi-envelope me-1"></i>{{ $student->email }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Event Type</small>
                                <span class="status-badge badge-{{ str_replace('_', '-', $scanLog->event_type) }}">
                                    <i
                                        class="bi bi-{{ $scanLog->event_type === 'check_in' ? 'box-arrow-in-right' : 'box-arrow-left' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $scanLog->event_type)) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Semester</small>
                                <span class="fw-semibold">
                                    {{ $scanLog->semester->academic_year }} -
                                    {{ $scanLog->semester->semester_no }}
                                </span>
                            </div>
                            <div class="col-md-2 text-end">
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock me-1"></i>{{ $scanLog->created_at->diffForHumans() }}
                                </small>
                                <small class="text-muted d-block">
                                    {{ $scanLog->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">📱</div>
                        <h5>No Scan Activity</h5>
                        <p class="text-muted">No check-in or check-out events have been recorded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Activity Timeline Section --}}
        <div class="card section-card mb-4">
            <div class="section-header bg-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Activity Timeline
                    </h5>
                    <a href="{{ route('activity_log') }}" wire:navigate class="view-all-btn text-white">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse ($this->recentActivityLogs as $activity)
                    <div class="activity-timeline">
                        <div class="activity-item">
                            @php
                                $iconClass = 'bg-secondary';
                                $icon = 'bi-circle-fill';

                                // Determine icon and color based on action
                                if (str_contains(strtolower($activity->action), 'create')) {
                                    $iconClass = 'bg-success text-white';
                                    $icon = 'bi-plus-circle-fill';
                                } elseif (str_contains(strtolower($activity->action), 'update')) {
                                    $iconClass = 'bg-info';
                                    $icon = 'bi-pencil-fill';
                                } elseif (str_contains(strtolower($activity->action), 'delete')) {
                                    $iconClass = 'bg-danger text-white';
                                    $icon = 'bi-trash-fill';
                                } elseif (str_contains(strtolower($activity->action), 'approve')) {
                                    $iconClass = 'bg-success text-white';
                                    $icon = 'bi-check-circle-fill';
                                } elseif (str_contains(strtolower($activity->action), 'reject')) {
                                    $iconClass = 'bg-danger text-white';
                                    $icon = 'bi-x-circle-fill';
                                } elseif (str_contains(strtolower($activity->action), 'opened')) {
                                    $iconClass = 'bg-success text-white';
                                    $icon = 'bi-door-open';
                                } elseif (str_contains(strtolower($activity->action), 'closed')) {
                                    $iconClass = 'bg-warning';
                                    $icon = 'bi-door-closed';
                                }
                            @endphp

                            <div class="activity-icon {{ $iconClass }}">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold activity-title">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </h6>
                                            <p class="text-muted mb-2 small activity-description">
                                                {{ $activity->description }}
                                            </p>
                                        </div>
                                        <small class="text-muted ms-3">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="d-flex gap-3 flex-wrap small text-muted activity-meta">
                                        @if ($activity->user)
                                            <span>
                                                <i class="bi bi-person me-1"></i>
                                                {{ $activity->user->name }}
                                            </span>
                                        @endif
                                        @if ($activity->ip_address)
                                            <span>
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $activity->ip_address }}
                                            </span>
                                        @endif
                                        @if ($activity->semester)
                                            <span>
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $activity->semester->academic_year }} -
                                                {{ $activity->semester->semester_no }}
                                            </span>
                                        @endif
                                        <span>
                                            <i class="bi bi-tag me-1"></i>
                                            {{ class_basename($activity->subject_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h5>No Activity Logs</h5>
                        <p class="text-muted">No system activities have been recorded yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Storage Applications Section --}}
        <div class="card section-card mb-4">
            <div class="section-header bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-file-earmark-text me-2"></i>Recent Storage Applications
                    </h5>
                    <a href="{{ route('manage_storage_application') }}" wire:navigate
                        class="view-all-btn text-white">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse ($this->recentApplications as $application)
                    <div class="border-bottom p-3">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="mb-1 fw-semibold">{{ $application->applicant->name }}</h6>
                                <p class="text-muted small m-0">
                                    <i class="bi bi-envelope me-1"></i>{{ $application->applicant->email }}
                                </p>
                                <p class="text-muted small m-0">
                                    <i class="bi bi-card-text me-1"></i>{{ $application->applicant->matric_no }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Storage Type</small>
                                <span class="fw-semibold">
                                    @if ($application->locker_id)
                                        <i class="bi bi-lock me-1"></i>Locker
                                    @else
                                        <i class="bi bi-box me-1"></i>Open Area
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-2">
                                <span class="status-badge badge-{{ $application->storage_application_status }}">
                                    {{ ucfirst($application->storage_application_status) }}
                                </span>
                            </div>
                            <div class="col-md-3 text-end">
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-clock me-1"></i>{{ $application->created_at->diffForHumans() }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('storage_application_detail', $application->id) }}"
                                        wire:navigate class="btn btn-outline-primary action-btn">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h5>No Storage Applications</h5>
                        <p class="text-muted">No applications have been submitted yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Missing Reports Section --}}
        <div class="card section-card mb-4">
            <div class="section-header bg-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-search me-2"></i>Recent Missing Item Reports
                    </h5>
                    <a href="{{ route('manage_missing') }}" wire:navigate class="view-all-btn text-white">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse ($this->recentMissingReports as $report)
                    @php
                        $student = $report->storedItems->first()?->storageApplication->applicant;
                        $application = $report->storedItems->first()?->storageApplication;
                    @endphp
                    <div class="border-bottom p-3">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6 class="mb-1 fw-semibold">Report #{{ $report->id }}</h6>
                                <p class="text-muted small">
                                    <i class="bi bi-person me-1"></i>{{ $student->name ?? 'Unknown User' }}
                                </p>
                                <p class="text-muted small">
                                    <i class="bi bi-card-text me-1"></i>{{ $student->matric_no ?? 'Unknown User' }}
                                </p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Last Seen</small>
                                <span class="fw-semibold">
                                    @if ($report->last_seen_type === 'specific_date')
                                        {{ \Carbon\Carbon::parse($report->last_seen_date)->format('M d, Y') }}
                                    @elseif($report->last_seen_type === 'range')
                                        {{ ucfirst(str_replace('_', ' ', $report->last_seen_range)) }}
                                    @else
                                        Don't remember
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-2">
                                <span class="status-badge badge-{{ $report->status }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                            <div class="col-md-3 text-end">
                                <small class="text-muted d-block mb-2">
                                    <i class="bi bi-clock me-1"></i>{{ $report->created_at->diffForHumans() }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('missing_report_detail', $report->id) }}" wire:navigate
                                        class="btn btn-outline-primary action-btn">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if ($report->last_seen_location)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <strong>Location:</strong> {{ Str::limit($report->last_seen_location, 60) }}
                                </small>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">🔍</div>
                        <h5>No Missing Reports</h5>
                        <p class="text-muted">No missing item reports have been filed.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Notifications Section --}}
        <div class="card section-card">
            <div class="section-header bg-info">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-bell me-2"></i>Recent Notifications
                    </h5>
                    <a href="{{ route('admin_notification') }}" wire:navigate class="view-all-btn text-white">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0" id="recentNotifications">
                @forelse ($this->notifications as $notification)
                    <div
                        class="dashboard-notification-item p-3 border-bottom @if (is_null($notification->read_at)) dashboard-notification-unread @endif">
                        <div class="d-flex gap-3">
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
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 me-2 fw-bold text-dark">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h6>
                                </div>
                                <p class="text-muted mb-2 small">
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                </p>
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
                                    <div class="d-flex align-items-center gap-1 text-muted"
                                        style="font-size: 0.75rem;">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <h5>No Notifications</h5>
                        <p class="text-muted">You're all caught up! Check back later for new updates.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
