<div x-data="{ show: false }" class="position-relative d-inline-block">
    {{-- Toggle Button --}}
    <button type="button" @click.prevent="show = !show"
        class="bg-light border-0 text-start d-flex justify-content-between align-items-center">
        {{ $toggleButtonSlot }}
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="show" @click.away="show = false" x-transition.opacity.duration.200ms
        class="position-absolute bg-white border rounded-3 shadow p-3 mt-1 z-3"
        style="display: none; z-index: 1050; right: 0; min-width: 150px;">

        {{ $slot }}
    </div>
</div>
