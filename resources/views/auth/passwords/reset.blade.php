@php
    $logo = Utility::get_superadmin_logo();
    $logos=\App\Models\Utility::get_file('uploads/logo/');
@endphp

@extends('layouts.auth')

@section('page-title')
    {{ __('Set a New Password') }}
@endsection

@section('content')

    <div class="auth-wrapper auth-v3">
        <div class="bg-auth-side bg-primary"></div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md navbar-light default">
                <div class="container-fluid pe-2">
                    <a class="navbar-brand" href="#">
                        <img src="{{ $logos.$logo }}" alt="logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                        <ul class="navbar-nav ms-auto me-auto mb-2 mb-lg-0">
                            
                        </ul>
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">

                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="card">
                <div class="row align-items-center text-start">
                    <div class="col-xl-6">
                        <div class="card-body">
                            <div class="">
                                <h2 class="mb-3 f-w-600">{{ __('Set a New Password') }}</h2>
                            </div>
                            <form method="POST" action="{{ route('password.update') }}">
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                @csrf

                                <div class="">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">{{ __('Enter Email address') }}</label>
                                        
                                        <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" placeholder="{{ __('Enter Your Email') }}" required="" value="{{ old('email') }}">
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('email') }}
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">{{ __('Enter Password') }}</label>
                                        <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" placeholder="{{ __('Enter Password') }}" required="" value="{{ old('password') }}">
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('password') }}
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password_confirmation" class="form-label">{{ __('Enter Confirm Password') }}</label>
                                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Enter Confirm Password') }}" required="">
                                        <div class="invalid-feedback d-block">
                                            {{ $errors->first('password_confirmation') }}
                                        </div>
                                    </div>
                                                        
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-submit btn-block mt-2">{{ __('Reset Password') }}</button>
                                        <a href="{{ route('login') }}" class="d-block mt-2"><small>{{ __('Sign In') }}</small>
                                        </a>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-xl-6 img-card-side">
                        <div class="auth-img-content">
                            <img src="{{ asset('assets/images/auth/img-auth-3.svg') }}" alt="" class="img-fluid">
                            <h3 class="text-white mb-4 mt-5">{{ __('“Attention is the new currency”')}}</h3>
                            <p class="text-white">
                                {{ __('The more effortless the writing looks, the more effort the writer actually put into the process.')}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted">{{env('FOOTER_TEXT')}}</p> 
                        </div>
                        <div class="col-6 text-end">
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
