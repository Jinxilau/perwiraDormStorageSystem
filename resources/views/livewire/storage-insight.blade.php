<div>
    @include('livewire.includes.header2', [
        'title' => 'Storage Insight',
        'subtitle' => 'Overview of your storage usage and recent activity.',
    ])

    <div>
        <h2 class="text-xl font-semibold mb-4">Storage Demand per Semester</h2>
        <div x-data="{
            chart: null,
            labels: $wire.entangle('labels'),
            values: $wire.entangle('values'),
        
            init() {
                this.renderChart();
        
                // Watch Livewire updates
                this.$watch('values', (newValues) => {
                    this.chart.updateSeries([{ data: newValues }]);
                });
        
                this.$watch('labels', (newLabels) => {
                    this.chart.updateOptions({
                        xaxis: { categories: newLabels }
                    });
                });
            },
        
            renderChart() {
                const options = {
                    chart: {
                        type: 'bar',
                        height: 350,
                    },
                    series: [{
                        name: 'Storage Applications',
                        data: this.values,
                    }],
                    xaxis: {
                        categories: this.labels,
                    },
                };
        
                this.chart = new ApexCharts(
                    this.$refs.chartContainer,
                    options
                );
        
                this.chart.render();
            }
        }">
            <div x-ref="chartContainer"></div>
        </div>


        <h2 class="text-xl font-semibold mt-8 mb-4">Item Size Breakdown</h2>

        <div x-data="{
            labels: $wire.entangle('sizeLabels'),
            values: $wire.entangle('sizeValues'),
            chart: null,
        
            init() {
                // ensure values are numbers
                this.values = this.values.map(v => Number(v || 0));
        
                const options = {
                    chart: { type: 'donut', height: 350 },
                    series: this.values,
                    labels: this.labels,
                    legend: { position: 'bottom' },
        
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: { show: true },
                                    value: { show: true },
                                    // use arrow function so `this` is Alpine component
                                    total: {
                                        show: true,
                                        label: 'Total Items',
                                        formatter: () => {
                                            return this.values.reduce((a, b) => Number(a) + Number(b), 0);
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
        
                this.$watch('values', (newValues) => {
                    const numeric = newValues.map(v => Number(v || 0));
                    this.chart.updateSeries(numeric);
                });
        
                this.$watch('labels', (newLabels) => {
                    this.chart.updateOptions({ labels: newLabels });
                });
            }
        }">
            <div x-ref="sizeChart"></div>
        </div>

        <h2 class="text-xl font-semibold mt-10 mb-4">Item Type Breakdown</h2>

        <div x-data="{
            labels: @entangle('typeLabels'),
            values: @entangle('typeValues'),
            chart: null,
        
            init() {
                this.values = this.values.map(v => Number(v || 0));
        
                const options = {
                    chart: { type: 'donut', height: 350 },
                    series: this.values,
                    labels: this.labels,
                    legend: { position: 'bottom' },
        
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Items',
                                        formatter: () => {
                                            return this.values.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
        
                this.chart = new ApexCharts(this.$refs.typeChart, options);
                this.chart.render();
        
                // reactive update
                this.$watch('values', val => {
                    const num = val.map(v => Number(v || 0));
                    this.chart.updateSeries(num);
                });
        
                this.$watch('labels', val => {
                    this.chart.updateOptions({ labels: val });
                });
            }
        }" x-init="init()">
            <div x-ref="typeChart"></div>
        </div>


    </div>

</div>
