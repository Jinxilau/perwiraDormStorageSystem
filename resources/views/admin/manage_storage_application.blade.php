@php
    // Determine the layout name based on the authenticated user's role
$layout = auth()->user()->role_id == 1 ? 'layouts.admin' : 'layouts.staff';
@endphp

<x-dynamic-component :component="$layout" title="Manage Storage Application">
    <livewire:manage-storage-application />
</x-dynamic-component>
