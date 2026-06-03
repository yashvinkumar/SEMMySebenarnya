@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush


<div class="card mb-4">
    <div class="card-header">
        Inquiry Information
    </div>
    <div class="card-body">
        <p><strong>Title:</strong> {{ $inquiry->inquiry_Title }}</p>
        <p><strong>Category:</strong> {{ $inquiry->inquiry_Category }}</p>
        <p><strong>Status:</strong> {{ $inquiry->inquiry_Status }}</p>
        <p><strong>Submitted at:</strong> {{ $inquiry->inquiry_Created_At }}</p>
        @if($inquiry->inquiry_Attachment_URL)
            <p><strong>Attachment:</strong> <a href="{{ route('inquiry.attachment', $inquiry->inquiry_ID) }}" class="btn btn-link">Download</a></p>
        @endif
    </div>
</div>
