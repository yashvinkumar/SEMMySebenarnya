<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email Address</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-envelope-open me-2"></i>Verify Your Email Address</h4>
                    </div>
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Email Verification Required</strong><br>
                            Before proceeding, please check your email for a verification link.
                            If you didn't receive the email, you can request another one below.
                        </div>

                        <div class="text-center mb-4">
                            <i class="fas fa-envelope fa-4x text-primary mb-3"></i>
                            <p class="text-muted">
                                We've sent a verification link to your email address.<br>
                                Please click the link in the email to verify your account.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Resend Verification Email
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-question-circle me-1"></i>
                        Having trouble? Check your spam folder or contact support.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
