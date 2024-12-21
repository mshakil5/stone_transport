@extends('layouts.auth')

@section('content')
    <div class="card card-outline card-secondary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>Verify Your Email Address</b></a>
        </div>
        <div class="card-body">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
            <p>{{ __('If you did not receive the email') }},</p>

            <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-secondary btn-block">{{ __('Click here to request another') }}</button>
            </form>
        </div>
    </div>
@endsection