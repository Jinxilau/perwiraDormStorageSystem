<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <h2 class="card-title text-center mb-2">Report Missing Item</h2>

    @if ($showSuccessMessage)
        @include('livewire.includes.report-ok') {{-- Success message --}}
    @else
        {{-- Progress Indicator --}}
        <div class="container mb-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 d-flex justify-content-center">
                    <ul
                        class="steper-step list-unstyled p-0 m-0 position-relative stepper-vertical d-inline-flex justify-content-center">
                        @for ($i = 1; $i <= $totalSteps; $i++)
                            <li
                                class="stepper position-relative pb-1 {{ $currentStep >= $i ? 'actived' : '' }} {{ $i < $currentStep ? 'completed' : '' }} d-inline-flex">
                                {{-- Stepper Head --}}
                                <div class="d-flex flex-column align-items-center justify-content-center align-items-center position-relative text-center"
                                    style="width: 12vw; min-width: 70px;">
                                    {{-- Stepper Icon --}}
                                    <span
                                        class="stepper-head-icon d-flex align-items-center justify-content-center fw-bold rounded-circle">
                                        {{ $i }}
                                    </span>
                                    <div class="stepper-content py-0">
                                        <p class="text-muted small mb-0">
                                            @if ($i == 1)
                                                Storage & Item
                                            @elseif($i == 2)
                                                Additional Information
                                            @else
                                                Verify Contact Information
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

</div>
@push('styles')
    @vite('resources\css\storage_application.css')
@endpush
