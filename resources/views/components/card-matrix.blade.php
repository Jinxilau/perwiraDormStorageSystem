@props(['items' => []])

<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .metric-icon-bg {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="row row-cols-2 row-cols-md-4 row-cols-xl-5 g-3 mb-4">
    @foreach ($items as $item)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-3 hover-lift">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        {{-- Icon with colored background --}}
                        <div class="metric-icon-bg {{ $item['bg'] ?? 'bg-primary bg-opacity-10' }}">
                            <i class="bi {{ $item['icon'] ?? 'bi-bar-chart-fill' }} {{ $item['color'] ?? 'text-primary' }} fs-5"></i>
                        </div>
                    </div>

                    {{-- Value: Large and prominent --}}
                    <div class="h3 fw-bold text-dark mb-1">
                        {{ $item['title'] }}
                    </div>

                    {{-- Label: Small and muted --}}
                    <div class="text-muted fw-medium" style="font-size: 0.75rem;">
                        {{ $item['subtitle'] }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>