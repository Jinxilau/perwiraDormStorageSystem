<header id="main-header" class="d-flex justify-content-between align-items-center shadow-sm">
    <div id="header-title">
        <h2 id="pageTitle" class="bold mb-0">{{ $title }}</h2>
        <p class="small mb-0">{{ $subtitle }}</p>
    </div>

    <div id="header-actions" class="d-flex align-items-center justify-content-end gap-2">
        <livewire:component.notification-dropdown />
    </div>
</header>
