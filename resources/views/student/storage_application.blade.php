@extends('layouts.student')

@section('title', 'Student - Storage Application')
{{-- Assets --}}
@section('assets')
@vite('resources\css\storage_application.css') 
@endsection

@section('content')
<h3 class="text-center">Storage Application Process</h3>
@livewire('storage-application')
@endsection