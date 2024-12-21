@extends('layouts.auth')

@section('content')
    <div class="card card-outline card-secondary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>Login</b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            @if (isset($message))
                <span class="login-head" role="alert">
                    <strong>
                        <p style="color: red">{{ $message }}</p>
                    </strong>
                </span>
            @endif

            @if (session('message'))
                <span class="login-head" role="alert">
                    <strong>
                        <p style="color: red">{{ session('message') }}</p>
                    </strong>
                </span>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required autocomplete="email" autofocus>
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

                <div class="input-group mb-3">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="social-auth-links text-center mt-2 mb-3">
                    <button type="submit" class="btn btn-secondary btn-block">Sign In</button>
                </div>

                <div class="text-center">
                    <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                </div>
            </form>
        </div>
    </div>
@endsection