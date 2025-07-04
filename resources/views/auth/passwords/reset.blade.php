@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
@endpush

@section('content')
<div class="container-xxl register-form">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <div class="card register-card">
                <div class="card-body register-card">
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
                    
                   <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="col-md-6 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <input id="email" type="email" class="form-control border  @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <input id="password" type="password" class="form-control border @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="col-md-6 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <input id="password-confirm" type="password" class="form-control border" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-10 offset-md-1">
                                <button type="submit" class="btn btn-primary rounded ">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
