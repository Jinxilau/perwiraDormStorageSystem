{{-- Search property and Clear method must be implemented --}}

<div class="card border-0 shadow-sm mb-4" x-data="{ filtersOpen: false }">
    {{-- Header: Search Bar --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-4 px-4">
        <div class="d-flex gap-3 align-items-center">

            {{-- Search Input (Always Visible) --}}
            <div class="position-relative flex-grow-1">
                <i class="bi bi-search text-muted position-absolute"
                    style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                <input wire:model.live="search" type="text" class="form-control form-control-lg border-0 bg-light ps-5"
                    placeholder="Search by name, matric no, or ID..." style="border-radius: 10px;">
            </div>

            {{-- The Toggle Button --}}
            <button @click="filtersOpen = !filtersOpen"
                class="btn btn-lg d-flex align-items-center gap-2 transition-all"
                :class="filtersOpen ? 'btn-danger shadow-sm' : 'btn-primary'"
                style="border-radius: 10px; min-width: 120px; justify-content: center;">

                {{-- Icon Swapping Logic --}}
                <i class="bi" :class="filtersOpen ? 'bi-x-lg' : 'bi-funnel-fill'"></i>

                {{-- Text Swapping Logic --}}
                <span class="fw-bold fs-6" x-text="filtersOpen ? 'Close' : 'Filters'"></span>
            </button>
        </div>
    </div>

    {{-- Body: Filters --}}
    <div x-show="filtersOpen" x-cloak x-collapse.duration.500ms style="display: none;">

        <div class="card-body p-4 border-top mt-3 mx-4"> {{-- Section Label --}}
            <h6 class="text-uppercase text-muted fw-bold mb-3 text-center" style="font-size: 0.75rem; letter-spacing: 1px;">
                <i class="bi bi-funnel-fill me-1"></i> Filter Options
            </h6>

            <div class="row g-3">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer: Clear Filters --}}
        <div class="card-footer bg-white border-top-0 pb-4 pt-0 d-flex justify-content-end">
            <button class="btn btn-link text-danger text-decoration-none fw-bold small"
                wire:click.prevent="clearFilters" @click="allCol = true; toggleAll()">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </button>
        </div>
    </div>
</div>
