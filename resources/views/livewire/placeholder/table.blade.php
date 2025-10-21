<div class="placeholder-glow">
    <div class="row g-3">
        @for ($i = 0; $i < 3; $i++)
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <span class="placeholder col-8"></span>
                        </h5>
                        <p class="card-text">
                            <span class="placeholder col-12"></span>
                            <span class="placeholder col-10"></span>
                            <span class="placeholder col-7"></span>
                        </p>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    <hr class="my-4">
    <table class="table">
        <thead>
            <tr>
                <th><span class="placeholder col-6"></span></th>
                <th><span class="placeholder col-8"></span></th>
                <th><span class="placeholder col-7"></span></th>
                <th><span class="placeholder col-5"></span></th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 8; $i++)
                <tr>
                    <td><span class="placeholder col-10"></span></td>
                    <td><span class="placeholder col-7"></span></td>
                    <td><span class="placeholder col-12"></span></td>
                    <td><span class="placeholder col-9"></span></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
