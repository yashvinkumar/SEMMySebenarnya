@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
@endpush

@section('title')
    Assignment Management - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('mcmc.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('mcmc.inquiries.list') }}" class="nav-link">
        <i class="fas fa-clipboard-list mr-2" aria-hidden="true"></i>
        Inquiries
    </a>
    <a href="{{ route('mcmc.assignments.list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-share-alt mr-2" aria-hidden="true"></i>
        Assignments
    </a>
    <a href="{{ route('mcmc.users') }}" class="nav-link">
        <i class="fas fa-users mr-2" aria-hidden="true"></i>
        Manage Users
    </a>
    <a href="{{ route('mcmc.reports.index') }}" class="nav-link">
        <i class="fas fa-chart-bar mr-2" aria-hidden="true"></i>
        Reports
    </a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Assignment List -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Assignment List</h2>
        </div>
        <div class="card-body">
            <div class="text-center py-12">
                <i class="fas fa-share-alt text-gray-400 text-4xl mb-4" aria-hidden="true"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No assignments found</h3>
                <p class="text-gray-500 mb-4">There are no inquiry assignments to display at this time.</p>
            </div>
        </div>
    </div>
</div>
@endsection
