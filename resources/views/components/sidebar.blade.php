@props([
    'items' => [],
])
<aside id="sidebar" class="col-md-3 p-0 mb-4 mb-md-0 custom-vh-100 shadow border-end border-secondary-subtle">
    <nav>
        <div class="list-group">
            @foreach ($items as $item)
                <a id="sidebarcontent" href="{{ $item['url'] }}" wire:navigate
                    class="list-group-item list-group-item-action {{ request()->is($item['path'], $item['path'] . '/*') ? 'active' : '' }}">
                    <i class="ms-2 bi {{ $item['icon'] }} h4"></i>
                    <span class="ms-2">{{ $item['title'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</aside>
