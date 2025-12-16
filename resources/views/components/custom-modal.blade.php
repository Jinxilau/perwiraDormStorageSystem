<div x-data="{
    show: false,
    openModal() {
        this.show = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.show = false;
        document.body.style.overflow = '';
    },
}" @display_modal.window="openModal();" @close-modal.window="closeModal()" @keydown.escape.window="closeModal()" x-show='show'
    tabindex="-1" id="modal" :class="{ 'display': show }" style="display: none;" x-transition.scale>

    {{-- Background Overlay --}}
    <div id="overlay" @click="closeModal()" x-show="show" x-transition.opacity></div>

    {{-- Modal --}}
    <div id="modal-content" class="card" x-transition>
        {{-- Title --}}
        <div class="card-header d-flex justify-content-between align-items-center ps-3 pe-3">
            <h5 class="modal-title mb-0">{{ $title }}</h5>

            {{-- Close Button --}}
            <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
        </div>
        
        {{-- Body --}}
        <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
            {{ $slot }}
        </div>
        
        {{-- Footer --}}
        @if (!empty($footer))
            <div class="card-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

{{-- modal.css --}}
