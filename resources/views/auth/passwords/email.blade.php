@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div class="container-xxl reset-password-form">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card reset-password-card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success text-center">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
                    @endif

                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="javascript:void(0)" class="app-brand-link gap-2">
                            <span style="background: black !important; border-radius: 5px;" class="p-3 w-75 text-center m-auto">
                                <img src="{{ asset('img/dashboardlogo.svg') }}" alt="Logo" class="w-100">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    
                    <form method="POST" action="{{ route('password.email') }}" id="formAuthentication" class="mb-3">
                        
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control border @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary d-grid w-100 rounded">{{ __('Send Password Reset Link') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
