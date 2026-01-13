<div>
    @include('livewire.includes.header2', [
        'title' => 'Storage Insight',
        'subtitle' => 'Overview of your storage usage and recent activity.',
    ])

    <style>
        .card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
            border-bottom: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            padding: 0.625rem 0.875rem;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .btn {
            border-radius: 8px;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .spinner-border-sm {
            width: 1.2rem;
            height: 1.2rem;
        }

        .card-title {
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .bi {
            font-size: 1.1rem;
        }

        .shadow-sm {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        }

        .bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        .bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        }

        .bg-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        }

        .bg-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
        }

        .bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        }

        .bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
        }

        .bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #565e64 100%) !important;
        }

        .container-fluid {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .row.g-4 {
            margin-top: 0;
        }

        .h-100 {
            height: 100% !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading overlay */
        [wire\:loading] {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Chart containers */
        [x-ref] {
            min-height: 350px;
        }

        /* Export and Print Buttons */
        .btn-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #565e64 100%);
            border: none;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #565e64 0%, #3d4449 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        /* Metric Cards */
        .metric-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .metric-card:hover::before {
            opacity: 1;
        }

        .metric-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .metric-card .card-body {
            padding: 1.5rem;
        }

        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .metric-card:hover .metric-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .metric-icon i {
            font-size: 1.75rem;
        }

        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .metric-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Print Styles */
        @media print {

            .btn,
            [wire\:loading] {
                display: none !important;
            }

            .card {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .metric-card,
            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            .card-header {
                background: #f8f9fa !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Animation for metric values */
        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .metric-value {
            animation: countUp 0.6s ease-out;
        }

        /* Chart Actions Buttons */
        .chart-actions .btn {
            border-radius: 6px;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            transition: all 0.3s ease;
        }

        .chart-actions .btn-light:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .chart-actions .btn-dark:hover {
            background-color: #1a1d20;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        @media print {
            .chart-actions {
                display: none !important;
            }
        }
    </style>

    <div class="container-fluid py-4">
        {{-- Export and Print Buttons --}}
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <button wire:click="exportExcel" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </button>
            <button wire:click="exportPdf" class="btn btn-danger">
                <i class="bi bi-file-pdf me-2"></i>Export PDF
            </button>
        </div>

        {{-- Filters Section --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel-fill me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Semester Filter --}}
                    <div class="col-md-3">
                        <label for="semesterFilter" class="form-label">Semester</label>
                        <select wire:model.live="selectedSemester" id="semesterFilter" class="form-select">
                            <option value="all">All Semesters</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}">
                                    {{ $semester->academic_year }} - Semester {{ $semester->semester_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date From Filter --}}
                    <div class="col-md-3">
                        <label for="dateFrom" class="form-label">Date From</label>
                        <input type="date" wire:model.blur="dateFrom" id="dateFrom" class="form-control">
                    </div>

                    {{-- Date To Filter --}}
                    <div class="col-md-3">
                        <label for="dateTo" class="form-label">Date To</label>
                        <input type="date" wire:model.blur="dateTo" id="dateTo" class="form-control">
                    </div>

                    {{-- Storage Room Filter --}}
                    <div class="col-md-3">
                        <label for="roomFilter" class="form-label">Storage Room</label>
                        <select wire:model.live="selectedRoom" id="roomFilter" class="form-select">
                            <option value="all">All Rooms</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12">
                        <button wire:click="resetFilters" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div wire:loading wire:target="selectedSemester,dateFrom,dateTo,selectedRoom,resetFilters"
            class="alert alert-info">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Updating charts...
            </div>
        </div>
        
        {{-- Key Metrics Section --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card metric-card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="metric-icon bg-primary-light mb-3">
                            <i class="bi bi-file-earmark-text text-primary"></i>
                        </div>
                        <h6 class="metric-label text-muted mb-2">Total Applications</h6>
                        <h3 class="metric-value text-primary mb-0">{{ $metrics['totalApplications'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card metric-card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="metric-icon bg-success-light mb-3">
                            <i class="bi bi-box-seam text-success"></i>
                        </div>
                        <h6 class="metric-label text-muted mb-2">Active Stored Items</h6>
                        <h3 class="metric-value text-success mb-0">{{ $metrics['activeItems'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card metric-card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="metric-icon bg-info-light mb-3">
                            <i class="bi bi-door-closed text-info"></i>
                        </div>
                        <h6 class="metric-label text-muted mb-2">Locker Occupancy</h6>
                        <h3 class="metric-value text-info mb-0">{{ $metrics['lockerOccupancyRate'] }}%</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card metric-card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="metric-icon bg-warning-light mb-3">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                        </div>
                        <h6 class="metric-label text-muted mb-2">Unclaimed Items</h6>
                        <h3 class="metric-value text-warning mb-0">{{ $metrics['unclaimedItems'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row g-4">
            {{-- Storage Demand per Semester --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart-fill me-2"></i>Storage Demand per Semester
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (empty($storageDemandLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($storageDemandLabels),
                                values: @js($storageDemandValues),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.chartContainer) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'bar',
                                            height: 350,
                                            toolbar: { show: true },
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: [{
                                            name: 'Storage Applications',
                                            data: this.values,
                                        }],
                                        xaxis: {
                                            categories: this.labels,
                                        },
                                        colors: ['#0d6efd'],
                                        plotOptions: {
                                            bar: {
                                                borderRadius: 4,
                                                columnWidth: '60%'
                                            }
                                        },
                                        dataLabels: { enabled: false }
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.chartContainer, options);
                                    this.chart.render();
                                }
                            }">
                                <div x-ref="chartContainer"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Item Size Breakdown --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pie-chart-fill me-2"></i>Item Size Breakdown
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-size-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($sizeLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($sizeLabels),
                                values: @js($sizeValues),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'item-size-breakdown.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.sizeChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'donut',
                                            height: 350,
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: this.values,
                                        labels: this.labels,
                                        legend: { position: 'bottom' },
                                        colors: ['#198754', '#20c997', '#6f42c1', '#fd7e14'],
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    size: '65%',
                                                    labels: {
                                                        show: true,
                                                        total: {
                                                            show: true,
                                                            label: 'Total Items',
                                                            formatter: (w) => {
                                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.sizeChart, options);
                                    this.chart.render();
                                }
                            }" @download-size-chart.window="downloadPng()">
                                <div x-ref="sizeChart"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Item Type Breakdown --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3-fill me-2"></i>Item Type Breakdown
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-type-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body">
                        @if (empty($typeLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($typeLabels),
                                values: @js($typeValues),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'item-type-breakdown.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.typeChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'donut',
                                            height: 350,
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: this.values,
                                        labels: this.labels,
                                        legend: { position: 'bottom' },
                                        colors: ['#0dcaf0', '#6610f2', '#d63384', '#ffc107', '#198754', '#fd7e14', '#dc3545'],
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    size: '65%',
                                                    labels: {
                                                        show: true,
                                                        total: {
                                                            show: true,
                                                            label: 'Total Items',
                                                            formatter: (w) => {
                                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.typeChart, options);
                                    this.chart.render();
                                }
                            }" @download-type-chart.window="downloadPng()">
                                <div x-ref="typeChart"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Item Condition Breakdown --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-star-fill me-2"></i>Item Condition Breakdown
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-condition-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($conditionLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($conditionLabels),
                                values: @js($conditionValues),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'item-condition-breakdown.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.conditionChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'donut',
                                            height: 350,
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: this.values,
                                        labels: this.labels,
                                        legend: { position: 'bottom' },
                                        colors: ['#ffc107', '#fd7e14', '#dc3545', '#198754'],
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    size: '65%',
                                                    labels: {
                                                        show: true,
                                                        total: {
                                                            show: true,
                                                            label: 'Total Items',
                                                            formatter: (w) => {
                                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.conditionChart, options);
                                    this.chart.render();
                                }
                            }">
                                <div x-ref="conditionChart" @download-condition-chart.window="downloadPng()"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Check-in vs Check-out per Semester --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i>Check-in vs Check-out per Semester
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-check-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($checkLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($checkLabels),
                                checkIn: @js($checkInData),
                                checkOut: @js($checkOutData),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'check-in-vs-check-out.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.checkChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'bar',
                                            height: 350,
                                            toolbar: { show: false },
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: [
                                            { name: 'Check-in', data: this.checkIn },
                                            { name: 'Check-out', data: this.checkOut }
                                        ],
                                        xaxis: {
                                            categories: this.labels,
                                        },
                                        plotOptions: {
                                            bar: {
                                                horizontal: false,
                                                columnWidth: '50%',
                                                borderRadius: 4
                                            }
                                        },
                                        dataLabels: { enabled: false },
                                        legend: { position: 'top' },
                                        colors: ['#0d6efd', '#dc3545']
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.checkChart, options);
                                    this.chart.render();
                                }
                            }">
                                <div x-ref="checkChart" @download-check-chart.window="downloadPng()"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Locker vs Open Space Usage per Semester --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Locker vs Open Space Usage
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-usage-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($usageLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($usageLabels),
                                locker: @js($lockerData),
                                openSpace: @js($openSpaceData),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'locker-vs-open-space-usage.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.stackedChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'bar',
                                            stacked: true,
                                            height: 350,
                                            toolbar: { show: false },
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: [
                                            { name: 'Locker', data: this.locker },
                                            { name: 'Open Space', data: this.openSpace }
                                        ],
                                        xaxis: {
                                            categories: this.labels,
                                        },
                                        legend: { position: 'top' },
                                        plotOptions: {
                                            bar: {
                                                dataLabels: {
                                                    total: {
                                                        enabled: true,
                                                        style: { fontWeight: 900 }
                                                    }
                                                }
                                            }
                                        },
                                        dataLabels: { enabled: false },
                                        colors: ['#6c757d', '#198754']
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.stackedChart, options);
                                    this.chart.render();
                                }
                            }" @download-usage-chart.window="downloadPng()">
                                <div x-ref="stackedChart"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Locker Occupancy Rate per Semester --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #6610f2 0%, #520dc2 100%); color: white;">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>Locker Occupancy Rate
                        </h5>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light" @click="$dispatch('download-occupancy-chart')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($occupancyLabels))
                            <div class="alert alert-warning">No data available for the selected filters.</div>
                        @else
                            <div wire:ignore x-data="{
                                chart: null,
                                labels: @js($occupancyLabels),
                                rates: @js($lockerOccupancyRates),
                            
                                init() {
                                    this.$nextTick(() => {
                                        this.renderChart();
                                    });
                                },
                            
                                downloadPng() {
                                    this.chart.dataURI().then(({ imgURI }) => {
                                        const link = document.createElement('a');
                                        link.href = imgURI;
                                        link.download = 'locker-occupancy-rate.png';
                                        link.click();
                                    });
                                },
                            
                                renderChart() {
                                    if (!this.$refs.radialChart) return;
                            
                                    const options = {
                                        chart: {
                                            type: 'radialBar',
                                            height: 350,
                                            animations: {
                                                enabled: true,
                                                speed: 800
                                            }
                                        },
                                        series: this.rates,
                                        labels: this.labels,
                                        plotOptions: {
                                            radialBar: {
                                                offsetY: 0,
                                                startAngle: 0,
                                                endAngle: 270,
                                                hollow: {
                                                    margin: 5,
                                                    size: '30%',
                                                    background: 'transparent',
                                                },
                                                dataLabels: {
                                                    name: { show: true },
                                                    value: {
                                                        show: true,
                                                        fontSize: '16px',
                                                        formatter: (val) => val + '%'
                                                    },
                                                    total: {
                                                        show: true,
                                                        label: 'Average',
                                                        formatter: (w) => {
                                                            const sum = w.globals.series.reduce((a, b) => a + b, 0);
                                                            return Math.round(sum / w.globals.series.length) + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                                        legend: {
                                            show: true,
                                            floating: true,
                                            fontSize: '14px',
                                            position: 'left',
                                            offsetX: 0,
                                            offsetY: 15,
                                            labels: { useSeriesColors: true },
                                            markers: { size: 0 },
                                            formatter: (seriesName, opts) => {
                                                return seriesName + ':  ' + opts.w.globals.series[opts.seriesIndex] + '%';
                                            },
                                            itemMargin: { vertical: 3 }
                                        }
                                    };
                            
                                    this.chart = new ApexCharts(this.$refs.radialChart, options);
                                    this.chart.render();
                                }
                            }" @download-usage-chart.window="downloadPng()">
                                <div x-ref="radialChart"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
