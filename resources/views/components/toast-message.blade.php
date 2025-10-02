<div x-data="{ show: false, message: '', type: 'success' }"
    @show_toast.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type;
        setTimeout(() => show = false, 4000);
    "
    x-show="show" x-transition:enter="fade-in" x-transition:leave="fade-out" class="position-fixed bottom-0 start-0 p-3"
    style="z-index: 1050; display: none;">
    <div class="toast show" role="alert">
        <div class="toast-header"
            :class="{
                'bg-success': type === 'success',
                'bg-danger': type === 'fail'
            }">
            <!-- Change color based on type -->
            <div :class="{
                'bg-success': type === 'success',
                'bg-danger': type === 'fail'
            }"
                class="rounded me-2" style="width: 20px; height: 20px;">
            </div>

            <!-- Change title based on type -->
            <strong class="me-auto" x-text="type === 'success' ? 'Success' : 'Error'"></strong>

            <button type="button" class="btn-close" @click="show = false"></button>
        </div>
        <div class="toast-body" x-text="message"></div>
    </div>
</div>
