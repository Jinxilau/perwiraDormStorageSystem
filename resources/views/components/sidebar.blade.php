@props([
    'items' => [],
])
<aside id="sidebar" class="col-md-3 p-0 mb-4 mb-md-0 custom-vh-100 shadow-lg">
    <nav>
        <div class="list-group">
            @foreach ($items as $item)
                <a id="sidebarcontent" href="{{ $item['url'] }}" wire:navigate
                    class="list-group-item list-group-item-action {{ request()->is($item['path']) ? 'active' : '' }}">
                    <i class="ms-2 bi {{ $item['icon'] }} h4"></i>
                    <span class="ms-2">{{ $item['title'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</aside>

@push('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('toggle-btn');
            const mainContent = document.getElementById('maincontent');

            sidebar.classList.toggle('close');
            toggleButton.classList.toggle('rotate');
            mainContent.classList.toggle('col-md-9');

            const icon = toggleButton.querySelector('i');
            if (sidebar.classList.contains('close')) {
                icon.classList.replace('bi-list', 'bi-x-lg');
            } else {
                icon.classList.replace('bi-x-lg', 'bi-list');
            }
        }
    </script>
@endpush
