<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ $settings->business_name ?? 'Stage Decor Management' }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('falcon/assets/img/favicons/favicon.ico') }}">
    <script src="{{ asset('falcon/assets/js/config.js') }}"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('falcon/assets/css/theme.min.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('falcon/assets/css/user.min.css') }}" rel="stylesheet" id="user-style-default">
</head>
<body class="bg-card-gradient min-vh-100 d-flex flex-center">
    <div class="container py-5">
        <div class="row flex-center">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-2" style="width: 60px; height: 60px;">
                        <span class="fas fa-crown fs-2 text-warning"></span>
                    </div>
                    <h3 class="fw-bolder text-primary mb-1">{{ $settings->business_name ?? 'Stage Decor' }}</h3>
                    <p class="text-600 mb-0">Stage Decoration Booking &amp; Management System</p>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-sm-5">
                        <div class="row flex-between-center mb-3">
                            <div class="col-auto">
                                <h5 class="mb-0 text-900">Sign In</h5>
                            </div>
                            <div class="col-auto fs-10 text-600">
                                <span>Authorized staff only</span>
                            </div>
                        </div>

                        @if(session('success'))
                        <div class="alert alert-success py-2 small mb-3">
                            {{ session('success') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger py-2 small mb-3">
                            {{ $errors->first() }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email Address</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', 'admin@stagedecor.pk') }}" required autofocus />
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" value="password123" required />
                            </div>

                            <div class="row flex-between-center mb-4">
                                <div class="col-auto">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" checked />
                                        <label class="form-check-label mb-0 fs-10" for="remember">Remember me</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-block w-100 mt-2" type="submit" name="submit">
                                    <span class="fas fa-sign-in-alt me-1"></span> Sign In
                                </button>
                            </div>
                        </form>

                        <div class="bg-light p-3 rounded-2 mt-4 border">
                            <h6 class="fs-10 text-uppercase fw-bold text-700 mb-2">Default Credentials:</h6>
                            <div class="fs-11 mb-1">
                                <span class="badge badge-subtle-primary me-1">Admin</span>
                                <code>admin@stagedecor.pk</code> / <code>password123</code>
                            </div>
                            <div class="fs-11">
                                <span class="badge badge-subtle-secondary me-1">Staff</span>
                                <code>staff@stagedecor.pk</code> / <code>password123</code>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3 text-500 fs-10">
                    {{ $settings->business_name ?? 'Stage Decor' }} &bull; Timezone: Pakistan Standard Time
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('falcon/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/fontawesome/all.min.js') }}"></script>
</body>
</html>
