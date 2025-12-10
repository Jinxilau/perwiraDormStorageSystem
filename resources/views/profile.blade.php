<x-layouts.student title="Profile">
    @include('livewire.includes.student-title2', [
        'title' => 'Profile',
        'subtitle' => 'Manage and view your personal information.',
    ])

    <div class="pb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">

                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="row">
                                <div class="col-lg-8">
                                    <livewire:profile.update-profile-information-form />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="row">
                                <div class="col-lg-8">
                                    <livewire:profile.update-password-form />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="row">
                                <div class="col-lg-8">
                                    <livewire:profile.delete-user-form />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.student>
