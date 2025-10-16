@props(['items' => []])
<div class="row row-cols-md-3 mb-3">
    @foreach ($items as $item)
        <div class="col mx-auto">
            <div class="card text-bg-light p-2 mb-3 border-secondary text-center">
                <div class="card-body shadow">
                    <h5 class="card-title h3">{{ $item['title'] }}</h5>
                    <p class="card-text">{{ $item['subtitle'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
