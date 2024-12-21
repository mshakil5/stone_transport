@extends('frontend.layouts.app')

@section('content')

<div class="container mt-5 mb-5">
    <div class="form-box">
        <div class="form-tab">
            <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link">Register</a>
                </li>  
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    @if (session('message'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-danger">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form name="registerForm" id="registerForm" method="POST" action="{{ route('register') }}">
                         @csrf
                        <div class="form-group mt-2">
                            <label for="name" class="black">Your Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="" value="{{ old('name') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-2">
                            <label for="email" class="black">Your Email Address<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="" value="{{ old('email') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="black">Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="black">Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-outline-primary-2" id="loginButton">
                                <span>Register</span>
                                <i class="icon-long-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection