@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Inquiry Progress Monitoring</h2>

    @if(isset($reportData))
        <div class="mb-4">
            <h4>Agency Performance</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Agency ID</th>
                        <th>Total Updates</th>
                        <th>Last Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $report)
                        <tr>
                            <td>{{ $report->agency_ID }}</td>
                            <td>{{ $report->total_updates }}</td>
                            <td>{{ $report->last_update }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(isset($inquiries))
        <h4>Inquiry List</h4>
        <table class="table table-striped">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->inquiry_Title }}</td>
                        <td>{{ $inquiry->inquiry_Status }}</td>
                        <td>{{ $inquiry->inquiry_Created_At }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
