@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


<div class="mt-3">
    @foreach($progress as $entry)
        <div class="card mb-3">
            <div class="card-body">
                <small class="text-muted">{{ $entry->progress_Updated_At }}</small>
                <h5 class="card-title">{{ ucfirst(str_replace('_', ' ', $entry->progress_Status)) }}</h5>
                <p class="card-text">{{ $entry->progress_Remarks }}</p>
            </div>
        </div>
    @endforeach
</div>
