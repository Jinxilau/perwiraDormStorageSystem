<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    @foreach ($this->approvedStorageApplication as $application)
        {{ $application->id }}
    @endforeach
</div>
