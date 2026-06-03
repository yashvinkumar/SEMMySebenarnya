@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


<div class="alert alert-warning" role="alert">
    <strong>Notification:</strong> {{ $message ?? 'You have a new update on your inquiry.' }}
</div>
