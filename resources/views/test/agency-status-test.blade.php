<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Status Update Test</title>
    <link href="https://cdn.tailwindcss.com/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Agency Status Update Test</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Test Assignment: #{{ $assignment->assignment_ID }}</h2>
            <p><strong>Current Status:</strong> {{ $assignment->assignment_Status }}</p>
            <p><strong>Inquiry:</strong> {{ $assignment->approval->inquiry->inquiry_Title }}</p>

            <form method="POST" action="{{ route('agency.assignments.update-status', $assignment->assignment_ID) }}" class="mt-6" id="testForm">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="status" class="block text-sm font-bold mb-2">Status</label>
                    <select name="status" id="status" class="w-full p-2 border rounded" required>
                        <option value="">Select Status</option>
                        <option value="in_progress">Accept & Start Review</option>
                        <option value="completed">Complete Review</option>
                        <option value="rejected">Reject Assignment</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="comments" class="block text-sm font-bold mb-2">Comments</label>
                    <textarea name="comments" id="comments" rows="3" class="w-full p-2 border rounded"></textarea>
                </div>

                <div id="completion_section" class="mb-4 hidden">
                    <label for="completion_summary" class="block text-sm font-bold mb-2">Completion Summary</label>
                    <textarea name="completion_summary" id="completion_summary" rows="3" class="w-full p-2 border rounded"></textarea>
                </div>

                <div id="rejection_section" class="mb-4 hidden">
                    <label for="rejection_reason" class="block text-sm font-bold mb-2">Rejection Reason</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="w-full p-2 border rounded"></textarea>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Update Status
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add debugging console logs
        console.log('Test page loaded');
        console.log('Form action:', document.getElementById('testForm').action);

        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const completionSection = document.getElementById('completion_section');
            const rejectionSection = document.getElementById('rejection_section');

            console.log('Status changed to:', status);

            // Hide all sections first
            completionSection.classList.add('hidden');
            rejectionSection.classList.add('hidden');

            // Show relevant section
            if (status === 'completed') {
                completionSection.classList.remove('hidden');
                document.getElementById('completion_summary').required = true;
                document.getElementById('rejection_reason').required = false;
            } else if (status === 'rejected') {
                rejectionSection.classList.remove('hidden');
                document.getElementById('rejection_reason').required = true;
                document.getElementById('completion_summary').required = false;
            } else {
                document.getElementById('completion_summary').required = false;
                document.getElementById('rejection_reason').required = false;
            }
        });

        // Add form submission debugging
        document.getElementById('testForm').addEventListener('submit', function(e) {
            console.log('Form submission started');
            console.log('Form data:', new FormData(this));
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);

            // Don't prevent default - let it submit
        });

        // Catch any JavaScript errors
        window.addEventListener('error', function(e) {
            console.error('JavaScript error:', e.error);
            alert('JavaScript Error: ' + e.error.message);
        });
    </script>
</body>
</html>
