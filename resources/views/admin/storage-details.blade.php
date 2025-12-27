@php
    // Determine the layout name based on the authenticated user's role
$layout = auth()->user()->role_id == 1 ? 'layouts.admin' : 'layouts.staff';
@endphp

<x-dynamic-component :component="$layout" title="Storage Details">
    <livewire:storage-details lazy :application="$application" />
</x-dynamic-component>
