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
}" @display_modal2.window="openModal();" @close-modal.window="closeModal()"
    @keydown.escape.window="closeModal()" x-show='show' tabindex="-1" id="modal2" :class="{ 'display': show }"
    style="display: none;" x-transition.scale>

    {{-- Background Overlay --}}
    <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

    {{-- Modal --}}
    <div id="modal-content2" class="card" x-transition>
        {{-- Title --}}
        <div class="card-header d-flex justify-content-between align-items-center ps-3 pe-3">
            <h5 class="modal-title mb-0">{{ $title }}</h5>

            {{-- Bootstrap Native Close Button --}}
            <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
        </div>

        {{-- Body --}}
        <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">
            {{ $slot }}
        </div>

        @if (!empty($footer))
            <div class="card-footer d-flex justify-content-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
