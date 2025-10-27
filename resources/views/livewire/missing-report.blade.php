<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <h2 class="card-title text-center mb-2">Report Missing Item</h2>
    <p class="text-muted text-center mb-2 small">
        Follow the steps below to submit your report.
    </p>

    @if ($this->studentApplications->isNotEmpty())
        @if ($showSuccessMessage)
            @include('livewire.includes.report-ok') {{-- Success message --}}
        @else
            {{-- Progress Indicator --}}
            <div class="container mb-2">
                <div class="row justify-content-center">
                    <div class="col-lg-8 d-flex justify-content-center">
                        <ul x-data="{ current: $wire.entangle('currentStep') }"
                            class="steper-step list-unstyled p-0 m-3 position-relative stepper-vertical d-inline-flex justify-content-center">
                            @for ($i = 1; $i <= $totalSteps; $i++)
                                <li class="stepper position-relative pb-1 d-inline-flex"
                                    :class="{
                                        'actived': current == {{ $i }},
                                        'completed': current > {{ $i }},
                                    }">
                                    {{-- Stepper Head --}}
                                    <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center"
                                        style="width: 12vw; min-width: 70px;">
                                        {{-- Stepper Icon --}}
                                        <span
                                            class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                            @if ($this->currentStep <= $i)
                                                {{ $i }}
                                            @endif
                                        </span>
                                        <div class="stepper-content py-0">
                                            <p class="text-muted small mb-0">
                                                @if ($i == 1)
                                                    Select Storage
                                                @elseif($i == 2)
                                                    Select Missing Items
                                                @elseif($i == 3)
                                                    Related Information
                                                @else
                                                    Review & Submit
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    {{-- Stepper Line --}}
                                    @if ($i < $totalSteps)
                                        <span class="stepper-head-line"></span>
                                    @endif
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Forms --}}
            @include('livewire.includes.report-form')
        @endif {{-- Show Success Message --}}
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="mb-0" style="font-size: 64px">❓</p>
                <h5 class="card-title h3">No Existing Storage</h5>
                <p class="text-muted">You can't report a missing item if you never had the item in the first place.</p>
                <div class="mt-3">
                    <a href="{{ route('storage') }}" class="btn btn-primary">
                        Apply for Storage
                    </a>
                </div>
            </div>
        </div>
    @endif

    <x-toast-message></x-toast-message>
</div>
@assets('styles')
    @vite('resources\css\progress_bar.css')
@endassets
