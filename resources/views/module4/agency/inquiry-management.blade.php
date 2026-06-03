@extends('layouts.dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-theme.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Navigation link styling */
        .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
        }

        .nav-link:hover {
            background-color: #f3f4f6;
            color: #374151;
        }

        .nav-link.active {
            background-color: #dbeafe;
            color: #2563eb;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('title')
    Inquiry Progress Management - MySebenarnya
@endsection

@section('nav-links')
    <a href="{{ route('agency.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('agency.assignments.list') }}" class="nav-link">
        <i class="fas fa-tasks mr-2" aria-hidden="true"></i>
        My Assignments
    </a>
    <a href="{{ route('agency.progress.inquiry-list') }}" class="nav-link active text-primary fw-semibold">
        <i class="fas fa-chart-line mr-2" aria-hidden="true"></i>
        Progress
    </a>
    <a href="{{ route('agency.profile') }}" class="nav-link">
        <i class="fas fa-user mr-2" aria-hidden="true"></i>
        Profile
    </a>
@endsection

@section('user-menu-items')
    <a href="{{ route('agency.profile') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-user mr-3" aria-hidden="true"></i>
        Profile
    </a>
    <a href="{{ route('agency.settings') }}"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200" role="menuitem">
        <i class="fas fa-cog mr-3" aria-hidden="true"></i>
        Settings
    </a>
@endsection

@section('content')
    <div class="py-8" style="color:#000 !important;">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Inquiry Progress Management</h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('agency.progress.inquiry-list') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="progress_status" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Progress Status</label>
                        <select id="progress_status" name="progress_status" class="form-select w-full text-black"
                            style="color:#000 !important;">
                            <option value="">All Progress Status</option>
                            <option value="received" {{ request('progress_status') == 'received' ? 'selected' : '' }}>
                                Received</option>
                            <option value="under_investigation"
                                {{ request('progress_status') == 'under_investigation' ? 'selected' : '' }}>Under
                                Investigation</option>
                            <option value="investigating"
                                {{ request('progress_status') == 'investigating' ? 'selected' : '' }}>Investigating
                            </option>
                            <option value="completed" {{ request('progress_status') == 'completed' ? 'selected' : '' }}>
                                Completed</option>
                            <option value="closed" {{ request('progress_status') == 'closed' ? 'selected' : '' }}>Closed
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Year</label>
                        <select id="year" name="year" class="form-select w-full text-black"
                            style="color:#000 !important;">
                            <option value="">All Years</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-sm font-bold mb-2"
                            style="color:#000 !important;">Month</label>
                        <select id="month" name="month" class="form-select w-full text-black"
                            style="color:#000 !important;">
                            <option value="">All Months</option>
                            <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>January</option>
                            <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>February</option>
                            <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>March</option>
                            <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>April</option>
                            <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>May</option>
                            <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>June</option>
                            <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>July</option>
                            <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>August</option>
                            <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>October</option>
                            <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>December</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary me-2" style="white-space: nowrap; height: 38px; display: flex; align-items: center;">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="{{ route('agency.progress.inquiry-list') }}" class="btn btn-outline-secondary" style="white-space: nowrap; height: 38px; display: flex; align-items: center; text-decoration: none;">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>

            @if ($assignments->isEmpty())
                <div class="alert alert-info">No inquiries assigned to your agency yet.</div>
            @else
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inquiry ID</th>
                                    <th>Title</th>
                                    <th>Submitted By</th>
                                    <th>Category</th>
                                    <th>Inquiry Status</th>
                                    <th>Assignment Status</th>
                                    <th>Progress Status</th>
                                    <th>Assigned Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignments as $assignment)
                                    @php
                                        $inquiry = $assignment->inquiry;
                                    @endphp
                                    <tr>
                                        <td>{{ $inquiry->inquiry_ID }}</td>
                                        <td>
                                            <a href="#" data-bs-toggle="collapse"
                                                data-bs-target="#inquiryDetails{{ $inquiry->inquiry_ID }}">
                                                {{ $inquiry->inquiry_Title }}
                                            </a>
                                            <div class="collapse mt-2" id="inquiryDetails{{ $inquiry->inquiry_ID }}">
                                                <div class="card card-body">
                                                    <strong>Description:</strong> {{ $inquiry->inquiry_Description }}<br>
                                                    <strong>Attachment:</strong>
                                                    @if ($inquiry->inquiry_Attachment_URL)
                                                        <a href="{{ asset('storage/' . $inquiry->inquiry_Attachment_URL) }}"
                                                            target="_blank">View</a>
                                                    @else
                                                        None
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $inquiry->user->user_Name }}</td>
                                        <td>{{ $inquiry->inquiry_Category }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $inquiry->inquiry_Status == 'pending' ? 'warning' : ($inquiry->inquiry_Status == 'completed' ? 'success' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $assignment->assignment_Status == 'pending' ? 'warning' : ($assignment->assignment_Status == 'completed' ? 'success' : 'primary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $assignment->assignment_Status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $latestProgress = $inquiry
                                                    ->progressRecords()
                                                    ->latest('progress_Updated_At')
                                                    ->first();
                                            @endphp
                                            @if ($latestProgress)
                                                <span
                                                    class="badge bg-{{ $latestProgress->progress_Status == 'received'
                                                        ? 'info'
                                                        : ($latestProgress->progress_Status == 'under_investigation'
                                                            ? 'warning'
                                                            : ($latestProgress->progress_Status == 'investigating'
                                                                ? 'primary'
                                                                : ($latestProgress->progress_Status == 'completed'
                                                                    ? 'success'
                                                                    : 'secondary'))) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $latestProgress->progress_Status)) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Progress</span>
                                            @endif
                                        </td>
                                        <td>{{ $assignment->assignment_Date ? $assignment->assignment_Date->format('Y-m-d') : '-' }}
                                        </td>
                                        <td>
                                            <!-- Update Status Modal Trigger -->
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal{{ $inquiry->inquiry_ID }}">
                                                Update Status
                                            </button>

                                            <!-- Update Status Modal -->
                                            <div class="modal fade" id="updateStatusModal{{ $inquiry->inquiry_ID }}"
                                                tabindex="-1"
                                                aria-labelledby="updateStatusModalLabel{{ $inquiry->inquiry_ID }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form
                                                        action="{{ route('agency.progress.submit-update', $assignment->assignment_ID) }}"
                                                        method="POST" enctype="multipart/form-data"
                                                        class="modal-content">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="updateStatusModalLabel{{ $inquiry->inquiry_ID }}">
                                                                Update
                                                                Inquiry Status</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Hidden assignment ID -->
                                                            <input type="hidden" name="assignment_ID"
                                                                value="{{ $assignment->assignment_ID }}">

                                                            <div class="mb-3">
                                                                <label for="progress_Status"
                                                                    class="form-label">Status</label>
                                                                <select name="progress_Status" id="progress_Status"
                                                                    class="form-select" required>
                                                                    <option value="">Select status</option>
                                                                    <option value="under_investigation">Under Investigation
                                                                    </option>
                                                                    <option value="verified_true">Verified as True</option>
                                                                    <option value="identified_fake">Identified as Fake
                                                                    </option>
                                                                    <option value="rejected">Rejected</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="progress_Remarks"
                                                                    class="form-label">Investigation
                                                                    Notes</label>
                                                                <textarea name="progress_Remarks" id="progress_Remarks" class="form-control" rows="3" required></textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="supporting_file" class="form-label">Supporting
                                                                    Document
                                                                    (Optional)
                                                                </label>
                                                                <input type="file" name="supporting_file"
                                                                    id="supporting_file" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success">Submit
                                                                Update</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- End Modal -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
