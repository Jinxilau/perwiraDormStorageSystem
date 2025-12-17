@props(['items' => []])

<style>
    /* Add this small CSS snippet for the hover effect */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
</style>

{{-- Changed grid to fit 2 on mobile, 4 on desktop, 5 on huge screens --}}
<div class="row row-cols-2 row-cols-md-4 row-cols-xl-5 g-3 mb-4">
    @foreach ($items as $item)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-3 hover-lift">
                {{-- Reduced padding to p-3 --}}
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            {{-- Label: Small and uppercase --}}
                            <div class="text-secondary fw-semibold text-uppercase"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                {{ $item['subtitle'] }}
                            </div>

                            {{-- Value: Bold but not huge (h4 instead of h2) --}}
                            <div class="h4 fw-bold text-dark mb-0 mt-1">
                                {{ $item['title'] }}
                            </div>
                        </div>

                        {{-- Optional: A subtle visual cue/icon on the right --}}
                        <div class="{{ $item['color'] ?? 'text-primary' }} opacity-25">
                            <i class="bi {{ $item['icon'] ?? 'bi-bar-chart-fill' }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
