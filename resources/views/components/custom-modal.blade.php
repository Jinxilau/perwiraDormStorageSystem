@props(['title' => 'Modal'])
<div x-data="{ show: false }" @display_modal.window="show = true" x-show='show'
    x-effect="document.body.style.overflow = show ? 'hidden': ''" tabindex="-1" id="modal"
    :class="{ 'display': show }" style="display: none;" x-transition.scale>

    {{-- Background Overlay --}}
    <div id="overlay" @click="show = false" x-show="show" x-transition.opacity></div>

    {{-- Modal --}}
    <div class="card" x-transition>
        {{-- Title --}}
        @if ($title)
            <div class="card-header">
                {{ $title }}
            </div>
        @endif
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- modal.css --}}
