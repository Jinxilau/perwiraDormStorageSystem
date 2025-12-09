<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    @include('livewire.includes.header2', [
        'title' => 'Storage Insight',
        'subtitle' => 'Overview of your storage usage and recent activity.',
    ])

    <div>
        <dirv id="storageApplicationStatusChart"></div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            const chart = new ApexCharts(document.querySelector('#storageApplicationStatusChart'), {
                chart: {
                    type: 'bar'
                },
                series: [{
                    name: 'Demand',
                    data: @json($values)
                }],
                xaxis: {
                    categories: @json($labels)
                }
            });
            chart.render();
        });
    </script>
</div>
