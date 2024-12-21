@extends('layouts.auth')

@section('content')
    <div class="card card-outline card-secondary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>Reset Password</b></a>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <p class="login-box-msg">Enter your email address to receive a password reset link.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="social-auth-links text-center mt-2 mb-3">
                    <button type="submit" class="btn btn-secondary btn-block">Send Password Reset Link</button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}">Remembered your password? Login</a>
                </div>
            </form>
        </div>
    </div>
@endsection