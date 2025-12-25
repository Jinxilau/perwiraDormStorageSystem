<div>
    @include('livewire.includes.header2', [
        'title' => 'Storage Insight',
        'subtitle' => 'Overview of your storage usage and recent activity.',
    ])

    <div class="container-fluid py-4">
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
                        <div wire:ignore x-data="{
                            chart: null,
                            labels: $wire.entangle('labels'),
                            values: $wire.entangle('values'),
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('values', (newValues) => {
                                    if (this.chart) {
                                        this.chart.updateSeries([{ data: newValues }]);
                                    }
                                });
        
                                this.$watch('labels', (newLabels) => {
                                    if (this.chart) {
                                        this.chart.updateOptions({
                                            xaxis: { categories: newLabels }
                                        });
                                    }
                                });
                            },
        
                            renderChart() {
                                if (!this.$refs.chartContainer) return;
        
                                const options = {
                                    chart: {
                                        type: 'bar',
                                        height: 350,
                                        toolbar: { show: true }
                                    },
                                    series: [{
                                        name: 'Storage Applications',
                                        data: this.values || [],
                                    }],
                                    xaxis: {
                                        categories: this.labels || [],
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
                    </div>
                </div>
            </div>
            {{-- Item Size Breakdown --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pie-chart-fill me-2"></i>Item Size Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            labels: $wire.entangle('sizeLabels'),
                            values: $wire.entangle('sizeValues'),
                            chart: null,
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('values', (newValues) => {
                                    if (this.chart) {
                                        const numeric = newValues.map(v => Number(v || 0));
                                        this.chart.updateSeries(numeric);
                                    }
                                });
        
                                this.$watch('labels', (newLabels) => {
                                    if (this.chart) {
                                        this.chart.updateOptions({ labels: newLabels });
                                    }
                                });
                            },
        
                            renderChart() {
                                if (!this.$refs.sizeChart) return;
        
                                const initialValues = (this.values || []).map(v => Number(v || 0));
        
                                const options = {
                                    chart: {
                                        type: 'donut',
                                        height: 350,
                                        redrawOnParentResize: true
                                    },
                                    series: initialValues,
                                    labels: this.labels || [],
                                    legend: { position: 'bottom' },
                                    colors: ['#198754', '#20c997', '#6f42c1', '#fd7e14'],
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                size: '65%',
                                                labels: {
                                                    show: true,
                                                    name: { show: true },
                                                    value: { show: true },
                                                    total: {
                                                        show: true,
                                                        label: 'Total Items',
                                                        formatter: function(w) {
                                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    dataLabels: { enabled: true }
                                };
        
                                this.chart = new ApexCharts(this.$refs.sizeChart, options);
                                this.chart.render();
                            }
                        }">
                            <div x-ref="sizeChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Item Type Breakdown --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3-fill me-2"></i>Item Type Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            labels: $wire.entangle('typeLabels'),
                            values: $wire.entangle('typeValues'),
                            chart: null,
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('values', val => {
                                    if (this.chart) {
                                        this.chart.updateSeries(val.map(v => Number(v || 0)));
                                    }
                                });
        
                                this.$watch('labels', val => {
                                    if (this.chart) {
                                        this.chart.updateOptions({ labels: val });
                                    }
                                });
                            },
        
                            renderChart() {
                                if (!this.$refs.typeChart) return;
        
                                const options = {
                                    chart: { type: 'donut', height: 350 },
                                    series: (this.values || []).map(v => Number(v || 0)),
                                    labels: this.labels || [],
                                    legend: { position: 'bottom' },
                                    colors: ['#0dcaf0', '#6610f2', '#d63384', '#ffc107'],
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
                        }">
                            <div x-ref="typeChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Item Condition Breakdown --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-star-fill me-2"></i>Item Condition Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            labels: $wire.entangle('conditionLabels'),
                            values: $wire.entangle('conditionValues'),
                            chart: null,
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('values', val => {
                                    if (this.chart) {
                                        this.chart.updateSeries(val.map(v => Number(v || 0)));
                                    }
                                });
        
                                this.$watch('labels', val => {
                                    if (this.chart) {
                                        this.chart.updateOptions({ labels: val });
                                    }
                                });
                            },
        
                            renderChart() {
                                if (!this.$refs.groupChart) return;
        
                                const options = {
                                    chart: { type: 'donut', height: 350 },
                                    series: (this.values || []).map(v => Number(v || 0)),
                                    labels: this.labels || [],
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
        
                                this.chart = new ApexCharts(this.$refs.groupChart, options);
                                this.chart.render();
                            }
                        }">
                            <div x-ref="groupChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Check-in vs Check-out per Semester --}}
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i>Check-in vs Check-out per Semester
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            chart: null,
                            checkIn: $wire.entangle('checkInData'),
                            checkOut: $wire.entangle('checkOutData'),
                            labels: $wire.entangle('labels'),
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('checkIn', () => this.updateChart());
                                this.$watch('checkOut', () => this.updateChart());
                                this.$watch('labels', (newLabels) => {
                                    this.chart.updateOptions({ xaxis: { categories: newLabels } });
                                });
                            },
        
                            updateChart() {
                                if (this.chart) {
                                    this.chart.updateSeries([
                                        { name: 'Check-in', data: this.checkIn },
                                        { name: 'Check-out', data: this.checkOut }
                                    ]);
                                }
                            },
        
                            renderChart() {
                                if (!this.$refs.chart) return;
        
                                this.chart = new ApexCharts(this.$refs.chart, {
                                    chart: {
                                        type: 'bar',
                                        height: 350,
                                        toolbar: { show: false }
                                    },
                                    series: [
                                        { name: 'Check-in', data: this.checkIn || @js($checkInData) },
                                        { name: 'Check-out', data: this.checkOut || @js($checkOutData) }
                                    ],
                                    xaxis: {
                                        categories: this.labels || @js($labels),
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
                                });
        
                                this.chart.render();
                            }
                        }">
                            <div x-ref="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Locker vs Open Space Usage per Semester --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Locker vs Open Space Usage
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            chart: null,
                            locker: $wire.entangle('lockerData'),
                            openSpace: $wire.entangle('openSpaceData'),
                            labels: $wire.entangle('labels'),
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('locker', () => this.updateChart());
                                this.$watch('openSpace', () => this.updateChart());
                                this.$watch('labels', (newLabels) => {
                                    if (this.chart) {
                                        this.chart.updateOptions({ xaxis: { categories: newLabels } });
                                    }
                                });
                            },
        
                            updateChart() {
                                if (this.chart) {
                                    this.chart.updateSeries([
                                        { name: 'Locker', data: this.locker },
                                        { name: 'Open Space', data: this.openSpace }
                                    ]);
                                }
                            },
        
                            renderChart() {
                                if (!this.$refs.chart) return;
        
                                const options = {
                                    chart: {
                                        type: 'bar',
                                        stacked: true,
                                        height: 350,
                                        toolbar: { show: false }
                                    },
                                    series: [{
                                            name: 'Locker',
                                            data: this.locker || @js($lockerData)
                                        },
                                        {
                                            name: 'Open Space',
                                            data: this.openSpace || @js($openSpaceData)
                                        }
                                    ],
                                    xaxis: {
                                        categories: this.labels || @js($labels),
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
        
                                this.chart = new ApexCharts(this.$refs.chart, options);
                                this.chart.render();
                            }
                        }">
                            <div x-ref="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Locker Occupancy Rate per Semester --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background-color: #6610f2; color: white;">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>Locker Occupancy Rate
                        </h5>
                    </div>
                    <div class="card-body">
                        <div wire:ignore x-data="{
                            chart: null,
                            rates: $wire.entangle('lockerOccupancyRates'),
                            labels: $wire.entangle('labels'),
        
                            init() {
                                this.$nextTick(() => {
                                    this.renderChart();
                                });
        
                                this.$watch('rates', (newVal) => {
                                    if (this.chart) {
                                        this.chart.updateSeries(newVal.map(v => Number(v || 0)));
                                    }
                                });
                            },
        
                            renderChart() {
                                if (!this.$refs.chart) return;
        
                                const options = {
                                    chart: {
                                        type: 'radialBar',
                                        height: 350,
                                    },
                                    series: (this.rates || []).map(v => Number(v || 0)),
                                    labels: this.labels || [],
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
                                                name: {
                                                    show: true,
                                                },
                                                value: {
                                                    show: true,
                                                    fontSize: '16px',
                                                    formatter: function(val) {
                                                        return val + '%';
                                                    }
                                                },
                                                total: {
                                                    show: true,
                                                    label: 'Average',
                                                    formatter: function(w) {
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
                                        labels: {
                                            useSeriesColors: true,
                                        },
                                        markers: {
                                            size: 0
                                        },
                                        formatter: function(seriesName, opts) {
                                            return seriesName + ':  ' + opts.w.globals.series[opts.seriesIndex] + '%';
                                        },
                                        itemMargin: {
                                            vertical: 3
                                        }
                                    },
                                };
        
                                this.chart = new ApexCharts(this.$refs.chart, options);
                                this.chart.render();
                            }
                        }">
                            <div x-ref="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
