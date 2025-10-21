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
}" @display_modal.window="openModal();" x-show='show'
    tabindex="-1" id="modal" :class="{ 'display': show }" style="display: none;" x-transition.scale>

    {{-- Background Overlay --}}
    <div id="overlay" @click="closeModal()" x-show="show" x-transition.opacity></div>

    {{-- Modal --}}
    <div id="modal-content" class="card" x-transition>
        {{-- Title --}}
        <div class="card-header">
            {{ $title }}
        </div>
        <div class="card-body" style="max-height: 80vh; overflow-y: auto;">
            {{ $slot }}
        </div>
    </div>
</div>

{{-- modal.css --}}
