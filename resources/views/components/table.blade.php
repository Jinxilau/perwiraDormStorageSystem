<div class="table-responsive">
    <table class="table">
        <thead class="table-dark">
            <tr>
                {{ $header }}
            </tr>
        </thead>
        <tbody class="table-group-divider">
            {{ $slot }}
        </tbody>
    </table>
    {{ $pagination }}
</div>
