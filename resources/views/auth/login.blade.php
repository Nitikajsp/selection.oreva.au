@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
  
@endpush

@section('content')
<div class="container-xxl login-form">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card login-card">
                <div class="card-body">
                    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

                    @if (session('msg'))
                        <div class="alert alert-success text-center">{{ session('msg') }}</div>
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
                    
                    <form method="POST" action="{{ route('login') }}" id="formAuthentication" class="mb-3">
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
                        <div class="mb-3 form-password-toggle">  
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input id="password" type="password" class="form-control border @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary d-grid w-100 rounded">{{ __('Login') }}</button>
                        </div>

                        <div class="text-center">
                            <span>New on our platform?</span>
                            <a href="{{ route('register') }}"><span>Create an account</span></a>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
