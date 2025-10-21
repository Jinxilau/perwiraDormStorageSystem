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
}" @display_modal2.window="openModal();" x-show='show'
    tabindex="-1" id="modal2" :class="{ 'display': show }" style="display: none;" x-transition.scale>

    {{-- Background Overlay --}}
    <div id="overlay2" @click="closeModal()" x-show="show" x-transition.opacity></div>

    {{-- Modal --}}
    <div id="modal-content2" class="card" x-transition>
        {{-- Title --}}
        <div class="card-header">
            {{ $title }}
        </div>
        <div class="card-body" style="min-width: 50vw; max-height: 80vh; overflow-y: auto;">
            {{ $slot }}
        </div>
    </div>
</div>