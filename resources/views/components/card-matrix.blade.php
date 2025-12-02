@props(['items' => []])

<style>
    /* Add this small CSS snippet for the hover effect */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
    @foreach ($items as $item)
        <div class="col">
            {{-- h-100 ensures cards are equal height --}}
            <div class="card h-100 border-1 shadow-sm hover-lift rounded-3"> 
                <div class="card-body p-4 text-center">
                    {{-- Title: Smaller, muted text for the label --}}
                    <h6 class="card-title text-uppercase text-muted mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">
                        {{ $item['subtitle'] }}
                    </h6>
                    
                    {{-- Subtitle: Large, bold text for the value/number --}}
                    <p class="card-text h2 fw-bold text-primary mb-0">
                        {{ $item['title'] }}
                    </p>
                </div>
                {{-- Optional: Add a decorative bottom border --}}
                <div class="card-footer border-0 bg-transparent py-2"></div>
            </div>
        </div>
    @endforeach
</div>