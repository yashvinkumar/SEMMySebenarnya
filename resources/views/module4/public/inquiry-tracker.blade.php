@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Inquiry Tracker</h2>

    @include('module4.partials.inquiry-details', ['inquiry' => $inquiry])

    <div class="mt-4">
        <h4>Progress Timeline</h4>
        @include('module4.components.status-display', ['progress' => $inquiry->progress])
    </div>
</div>
@endsection
