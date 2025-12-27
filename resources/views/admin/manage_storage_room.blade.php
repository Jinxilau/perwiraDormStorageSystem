@php
    // Determine the layout name based on the authenticated user's role
$layout = auth()->user()->role_id == 1 ? 'layouts.admin' : 'layouts.staff';
@endphp

<x-dynamic-component :component="$layout" title="Storage Room">
    <livewire:manage-storage-room lazy />
</x-dynamic-component>
