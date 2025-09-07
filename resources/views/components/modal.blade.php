@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
    // Map the Tailwind $maxWidth values into our custom class names
    $maxWidth = [
        'sm' => 'sm-max-w-sm',
        'md' => 'sm-max-w-md',
        'lg' => 'sm-max-w-lg',
        'xl' => 'sm-max-w-xl',
        '2xl' => 'sm-max-w-2xl',
    ][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="position-fixed px-4"
    style="display: {{ $show ? 'block' : 'none' }}; top:0; right:0; bottom:0; left:0; overflow-y:auto; padding-top:1.5rem; padding-bottom:1.5rem;"
>
    <!-- Overlay -->
    <div
        x-show="show"
        class="position-fixed"
        style="top:0; right:0; bottom:0; left:0; transition: all 0.15s ease-in-out; transform: none;"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="position-absolute opacity-75" style="top:0; right:0; bottom:0; left:0; background-color:#6b7280; z-index: 9001"></div>
    </div>

    <!-- Modal Content -->
    <div
        x-show="show"
        class="bg-light rounded shadow-lg mb-4 sm-w-100 sm-mx-auto {{ $maxWidth }}"
        style="margin-bottom:1.5rem; border-radius:0.5rem; overflow:hidden; transition:all 0.15s ease-in-out; transform:none; z-index: 9002;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm-translate-y-0 sm-scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm-scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm-scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm-translate-y-0 sm-scale-95"
    >
        {{ $slot }}
    </div>
</div>
