<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Test - MySebenarnya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-database text-primary"></i>
                    Database Connection Test - MySebenarnya
                </h1>

                @if(isset($error))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Database Error:</strong> {{ $error }}
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Database Connected Successfully!</strong>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-users"></i> Public Users
                                    </h5>
                                    <h2>{{ isset($inquiries) ? $inquiries->pluck('user')->filter()->count() : 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-clipboard-list"></i> Inquiries
                                    </h5>
                                    <h2>{{ isset($inquiries) ? $inquiries->count() : 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-building"></i> Agencies
                                    </h5>
                                    <h2>{{ isset($agencies) ? $agencies->count() : 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-tie"></i> MCMC Staff
                                    </h5>
                                    <h2>{{ isset($staff) ? $staff->count() : 0 }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($inquiries) && $inquiries->count() > 0)
                        <!-- Inquiries Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i>
                                    All Inquiries from Database
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>User</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Assignments</th>
                                                <th>Progress</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inquiries as $inquiry)
                                                <tr>
                                                    <td>{{ $inquiry->inquiry_ID }}</td>
                                                    <td>{{ $inquiry->inquiry_Title }}</td>
                                                    <td>
                                                        @if($inquiry->user)
                                                            <strong>{{ $inquiry->user->user_Name }}</strong><br>
                                                            <small class="text-muted">{{ $inquiry->user->user_Email }}</small>
                                                        @else
                                                            <span class="text-muted">No User</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ ucfirst($inquiry->inquiry_Category) }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusColor = match($inquiry->inquiry_Status) {
                                                                'submitted' => 'warning',
                                                                'pending' => 'warning',
                                                                'under_review' => 'info',
                                                                'assigned_to_agency' => 'success',
                                                                'approved' => 'success',
                                                                'closed' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColor }}">
                                                            {{ ucfirst(str_replace('_', ' ', $inquiry->inquiry_Status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($inquiry->assignments && $inquiry->assignments->count() > 0)
                                                            @foreach($inquiry->assignments as $assignment)
                                                                <div class="mb-1">
                                                                    <small class="text-success">
                                                                        <i class="fas fa-building"></i>
                                                                        {{ $assignment->agency->agency_Name ?? 'Unknown Agency' }}
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fas fa-minus"></i> Not Assigned
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($inquiry->progressRecords && $inquiry->progressRecords->count() > 0)
                                                            <span class="badge bg-info">
                                                                {{ $inquiry->progressRecords->count() }} Updates
                                                            </span>
                                                        @else
                                                            <span class="text-muted">No Progress</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $inquiry->inquiry_Created_At ? $inquiry->inquiry_Created_At->format('M d, Y H:i') : 'N/A' }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Agencies Table -->
                        @if(isset($agencies) && $agencies->count() > 0)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-building"></i>
                                        Available Agencies
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Created</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($agencies as $agency)
                                                    <tr>
                                                        <td>{{ $agency->agency_ID }}</td>
                                                        <td>{{ $agency->agency_Name }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ ucfirst($agency->agency_Type) }}</span>
                                                        </td>
                                                        <td>{{ $agency->agency_Email }}</td>
                                                        <td>{{ $agency->agency_Phone }}</td>
                                                        <td>
                                                            <small>{{ $agency->agency_Created_At ? $agency->agency_Created_At->format('M d, Y') : 'N/A' }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>No Data Found</h5>
                                <p class="text-muted">The database is connected but no inquiries were found.</p>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="/test-db" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Test DB Connection API
                    </a>
                    <a href="/mcmc/inquiries/assign-inquiries" class="btn btn-success">
                        <i class="fas fa-tasks"></i> Go to Assignment Interface
                    </a>
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
