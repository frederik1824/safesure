@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 min-h-screen" style="background-color: #f8fafc !important;">
    <div class="max-w-7xl mx-auto">
        <!-- Render the new Sync Control Center -->
        @livewire('sync-control-center')
    </div>
</div>
@endsection
