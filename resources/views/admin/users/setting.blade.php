@extends('layouts.admin')

@section('page-title')
    {{ __('Settings ') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection

@php
    $logos=\App\Models\Utility::get_file('uploads/logo/');
    $SITE_RTL = Utility::getSettingValByName('SITE_RTL');
    if($SITE_RTL == ''){
        $SITE_RTL == 'off';
    }
    $color = 'theme-3';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }
    $cust_theme_bg = 'on';
    if (!empty($setting['cust_theme_bg']))
    {
        $cust_theme_bg = $setting['cust_theme_bg'];
    }
    $EmailTemplates     = App\Models\EmailTemplate::all();

    $file_type = config('files_types');
    $setting = App\Models\Utility::settings();

    $local_storage_validation    = $setting['local_storage_validation'];
    $local_storage_validations   = explode(',', $local_storage_validation);

    $s3_storage_validation    = $setting['s3_storage_validation'];
    $s3_storage_validations   = explode(',', $s3_storage_validation);

    $wasabi_storage_validation    = $setting['wasabi_storage_validation'];
    $wasabi_storage_validations   = explode(',', $wasabi_storage_validation);

@endphp
@if($color == 'theme-1')
    <style>
        .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
            border-color: #51459d !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
            border-color: #51459d !important;
        }
        .btn.btn-outline-success{
            color: #51459d;
            border-color: #51459d !important;
        }
    </style>
@endif

@if($color == 'theme-2')
    <style>
        .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
                border-color: #1F3996 !important;

            }

            .btn-outline-success:hover
            {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
                border-color: #1F3996 !important;
            }
            .btn.btn-outline-success{
                color: #1F3996;
                border-color: #1F3996 !important;
            }
    </style>
@endif

@if($color == 'theme-4')
    <style>
        .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background-color: #584ed2 !important;
            border-color: #584ed2 !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background-color: #584ed2 !important;
            border-color: #584ed2 !important;
        }
        .btn.btn-outline-success{
            color: #584ed2;
            border-color: #584ed2 !important;
        }
    </style>
@endif

@if($color == 'theme-3')
    <style>
    .btn-check:checked + .btn-outline-success, .btn-check:active + .btn-outline-success, .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;

        }

        .btn-outline-success:hover
        {
            color: #ffffff;
            background-color: #6fd943 !important;
            border-color: #6fd943 !important;
        }
        .btn.btn-outline-success{
            color: #6fd943;
            border-color: #6fd943 !important;
        }
    </style>

@endif

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#logo-settings" class="list-group-item list-group-item-action border-0 active">{{ __('Logo Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-settings" class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (\Auth::user()->parent == 0)
                            <a href="#email-notification-settings" class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @endif
                            <a href="#pusher-settings" class="list-group-item list-group-item-action border-0">{{ __('Pusher Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#recaptcha-settings" class="list-group-item list-group-item-action border-0">{{ __('ReCaptcha Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#ticket-fields-settings" class="list-group-item list-group-item-action border-0">{{ __('Ticket Fields Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#company-settings" class="list-group-item list-group-item-action border-0">{{__('Company Settings')}} <div
                                class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#storage-settings" class="list-group-item list-group-item-action border-0">{{ __('Storage Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="logo-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Logo settings') }}</h5>
                        </div>
                        <div class="card-body">
                            {{ Form::open(['route' => 'admin.settings.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Dark Logo') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content logo-set-bg  text-center py-2">
                                                            {{-- <a href="{{ asset(Storage::url('logo/logo-dark.png')) }}" target="_blank">
                                                                <img src="{{ asset(Storage::url('logo/logo-dark.png')) }}" class="big-logo" id="blah2" alt=""/>
                                                            </a> --}}
                                                            <a href="{{$logos.'logo-dark.png'}}" target="_blank">
                                                                <img id="blah2" alt="your image" src="{{$logos.'logo-dark.png'}}" width="150px" class="logo logo-sm">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="choose-files mt-4">
                                                            <label for="logo" class="form-label d-block">
                                                                <div class="bg-primary m-auto">
                                                                    <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    <input type="file" name="logo" id="logo" class="form-control file" data-filename="company_logo_update" onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">
                                                                </div>
                                                                {{-- <input type="file" class="form-control file" name="logo" id="logo" data-filename="edit-logo"> --}}
                                                            </label>
                                                            <p class="edit-logo"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Light Logo') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content  logo-set-bg text-center py-2">
                                                            <a href="{{$logos.'logo-light.png'}}" target="_blank">
                                                                <img id="blah3" alt="your image" src="{{$logos.'logo-light.png'}}" width="150px" class="logo logo-sm img_setting" style="filter: drop-shadow(2px 3px 7px #011c4b);">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="choose-files mt-4">
                                                            <label for="white_logo" class="form-label d-block">
                                                                <div class=" bg-primary m-auto">
                                                                    <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    <input type="file" name="white_logo" id="white_logo" class="form-control file" data-filename="company_logo_update" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                                                                </div>
                                                                {{-- <input type="file" class="form-control file" name="white_logo" id="white_logo" data-filename="edit-white_logo"> --}}
                                                            </label>
                                                            <p class="edit-white_logo"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Favicon') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content logo-set-bg text-center py-2">
                                                            <a href="{{$logos.'favicon.png'}}" target="_blank">
                                                                <img id="blah" alt="your image" src="{{$logos.'favicon.png'}}" width="80px" class="big-logo img_setting">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="choose-files mt-4">
                                                            <label for="favicon" class="form-label d-block">
                                                                <div class=" bg-primary m-auto">
                                                                    <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    <input type="file" name="favicon" id="favicon" class="form-control file" data-filename="company_logo_update" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                                </div>
                                                            </label>
                                                            <p class="edit-favicon"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-body setting-card p-3 mt-3">
                                                <div class="row">
                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                        <div class="form-group">
                                                            {{ Form::label('APP_NAME', __('App Name'), ['class' => 'form-label']) }}
                                                            {{ Form::text('APP_NAME', env('APP_NAME'), ['class' => 'form-control', 'placeholder' => __('App Name')]) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                        <div class="form-group">
                                                            {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-label']) }}
                                                            {{ Form::text('footer_text',!empty($setting['FOOTER_TEXT']) ? $setting['FOOTER_TEXT'] : "", ['class' => 'form-control','placeholder' => __('Footer Text')]) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                        <div class="form-group">
                                                            {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                            <div class="changeLanguage">
                                                                <select name="default_language" id="default_language"
                                                                    class="form-select">
                                                                    @php
                                                                        $default_lan = !empty($setting['DEFAULT_LANG']) ? $setting['DEFAULT_LANG'] : 'en';
                                                                    @endphp
                                                                    @foreach ($lang as $lan)
                                                                        <option value="{{ $lan }}"
                                                                            @if ($default_lan == $lan) selected @endif>
                                                                            {{ Str::upper($lan) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-xl-12 col-md-12">
                                                        <div class="row">
                                                            <div class="col-3 switch-width">
                                                                <div class="form-group ml-2 mr-3">
                                                                    <label class="form-label">{{ __('Enable RTL') }}</label>
                                                                    {{-- <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="SITE_RTL" id="SITE_RTL"
                                                                            {{ $SITE_RTL == 'on' ? 'checked ="checked"' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="SITE_RTL"></label>
                                                                    </div> --}}
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="site_rtl" id="site_rtl" value='on'
                                                                            {{ !empty($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label" for="site_rtl"></label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-3 switch-width">
                                                                <div class="form-group mr-3">
                                                                    <label class="form-label">{{ __('FAQ') }}</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="faq" id="faq"
                                                                            {{ !empty($setting['FAQ']) && $setting['FAQ'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            {{-- {{ !empty(env('FAQ')) && env('FAQ') == 'on' ? 'checked="checked"' : '' }}> --}}
                                                                        <label class="custom-control-label" for="faq"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 switch-width">
                                                                <div class="form-group mr-3">
                                                                    <label
                                                                        class="form-label">{{ __('Knowledge Base') }}</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="knowledge" id="knowledge"
                                                                            {{ !empty($setting['Knowlwdge_Base']) && $setting['Knowlwdge_Base'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            {{-- {{ !empty(env('Knowlwdge_Base')) && env('Knowlwdge_Base') == 'on' ? 'checked="checked"' : '' }}> --}}
                                                                        <label class="custom-control-label"
                                                                            for="knowledge"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 switch-width">
                                                                <div class="form-group mr-3">
                                                                    {{ Form::label('gdpr_cookie', 'GDPR Cookie', ['class' => 'form-label']) }}
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary"
                                                                            class="custom-control-input gdpr_fulltime gdpr_type" name="gdpr_cookie" id="gdpr_cookie"
                                                                            {{ !empty($setting['gdpr_cookie']) && $setting['gdpr_cookie'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label" for="gdpr_cookie"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 col-xl-12 col-md-12">
                                                        <div class="row">
                                                            <div class="form-group col-md-12">
                                                                {{ Form::label('cookie_text', __('GDPR Cookie Text'), ['class' => 'fulltime']) }}
                                                                <input type="text" name="cookie_text" class="form-control fulltime" value="{{ !empty($setting['cookie_text']) && $setting['cookie_text'] ? $setting['cookie_text'] : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                                                    <div class="setting-card setting-logo-box p-3">
                                                        <div class="row">
                                                            <div class="pct-body">
                                                                <div class="row">
                                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                                        <h6 class="mt-2">
                                                                            <i data-feather="credit-card"
                                                                                class="me-2"></i>{{ __('Primary color settings') }}
                                                                        </h6>
                                                                        <hr class="my-2" />
                                                                        <div class="theme-color themes-color">
                                                                            <a href="#!" class="{{($setting['color'] == 'theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                                            <input type="radio" class="theme_color" name="color" value="theme-1" style="display: none;">
                                                                            <a href="#!" class="{{($setting['color'] == 'theme-2') ? 'active_color' : ''}}"  data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                                            <input type="radio" class="theme_color" name="color" value="theme-2" style="display: none;">
                                                                            <a href="#!" class="{{($setting['color'] == 'theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                                            <input type="radio" class="theme_color" name="color" value="theme-3" style="display: none;">
                                                                            <a href="#!" class="{{($setting['color'] == 'theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                                            <input type="radio" class="theme_color" name="color" value="theme-4" style="display: none;">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                                        <h6 class="mt-2 ">
                                                                            <i data-feather="layout"
                                                                                class="me-2"></i>{{ __('Sidebar settings') }}
                                                                        </h6>
                                                                        <hr class="my-2" />
                                                                        {{-- <div class="form-check form-switch">
                                                                            <input type="checkbox" class="form-check-input"
                                                                                id="cust-theme-bg" name="cust_theme_bg"
                                                                                {{ !empty($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                                                            <label class="form-check-label f-w-600 pl-1"
                                                                                for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                                        </div> --}}

                                                                        <div class="form-check form-switch mt-2 ">
                                                                            <input type="checkbox" class="form-check-input" id="cust_theme_bg" name="cust_theme_bg"@if($cust_theme_bg == 'on') checked @endif/>

                                                                            <label class="form-check-label f-w-600 pl-1" for="cust_theme_bg">{{__('Transparent layout')}}</label>
                                                                        </div>


                                                                    </div>

                                                                    <div class="col-lg-4 col-xl-4 col-md-4">
                                                                        <h6 class="mt-2">
                                                                            <i data-feather="sun"
                                                                                class="me-2"></i>{{ __('Layout settings') }}
                                                                        </h6>
                                                                        <hr class="my-2" />
                                                                        <div class="form-check form-switch mt-2">
                                                                            <input type="checkbox" class="form-check-input"
                                                                                id="cust-darklayout" name="cust_darklayout"
                                                                                {{ !empty($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                                                            <label class="form-check-label f-w-600 pl-1"
                                                                                for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-12 text-right text-end float-end">
                                        <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary btn-block btn-submit">
                                    </div>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    <div id="email-settings" class="card">
                        <div class="card-header">
                            <h5 class="mb-2">{{ __('Email Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            {{ Form::open(['route' => 'admin.email.settings.store', 'method' => 'post']) }}
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_driver', env('MAIL_DRIVER'), ['class' => 'form-control','placeholder' => __('Enter Mail Driver')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_host', env('MAIL_HOST'), ['class' => 'form-control ','placeholder' => __('Enter Mail Driver')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_port', env('MAIL_PORT'), ['class' => 'form-control','placeholder' => __('Enter Mail Port')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_username', env('MAIL_USERNAME'), ['class' => 'form-control','placeholder' => __('Enter Mail Username')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_password', env('MAIL_PASSWORD'), ['class' => 'form-control','placeholder' => __('Enter Mail Password')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_encryption', env('MAIL_ENCRYPTION'), ['class' => 'form-control','placeholder' => __('Enter Mail Encryption')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_from_address', env('MAIL_FROM_ADDRESS'), ['class' => 'form-control','placeholder' => __('Enter Mail From Address')]) }}
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                        {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                        {{ Form::text('mail_from_name', env('MAIL_FROM_NAME'), ['class' => 'form-control','placeholder' => __('Enter Mail Encryption')]) }}
                                    </div>
                                </div>
                                <div class="col-lg-12 ">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <a href="#" data-url="{{ route('admin.test.email') }}"
                                                data-title="{{ __('Send Test Mail') }}"
                                                class="btn btn-primary btn-submit text-white send_email">
                                                {{ __('Send Test Mail') }}
                                            </a>
                                        </div>
                                        <div class="form-group col-md-6 float-end text-end">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-primary btn-block btn-submit text-white">
                                        </div>
                                    </div>
                                </div>
                            {{ Form::close() }}

                        </div>
                    </div>

                    <!--Email Notification Setting-->
                    <div id="email-notification-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Email Notification Settings') }}</h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                @foreach ($EmailTemplates as $EmailTemplate)

                                    <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                        <div class="list-group">
                                            <div class="list-group-item form-switch form-switch-right">
                                                <label class="form-label" style="margin-left:5%;">{{ $EmailTemplate->name }}</label>

                                                <input class="form-check-input email-template-checkbox" id="email_tempalte_{{$EmailTemplate->template->id}}" type="checkbox" @if($EmailTemplate->template->is_active == 1) checked="checked" @endif type="checkbox" value="{{$EmailTemplate->template->is_active}}"
                                                    data-url="{{route('status.email.language',[$EmailTemplate->template->id])}}" />
                                                <label class="form-check-label" for="email_tempalte_{{$EmailTemplate->template->id}}"></label>

                                                {{-- <label class="form-check-label form-switch form-switch-right">
                                                    <input type="checkbox" class="form-check-input " id="email_tempalte_{{$EmailTemplate->template->id}}"
                                                    @if($EmailTemplate->template->is_active == 1) checked="checked" @endif type="checkbox" value="{{$EmailTemplate->template->is_active}}"
                                                    data-url="{{route('status.email.language',[$EmailTemplate->template->id])}}"/>
                                                    <span class="slider1 round"></span>
                                                </label> --}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- <div class="card-footer p-0">
                                <div class="col-sm-12 mt-3 px-2">
                                    <div class="text-end">
                                        <input class="btn btn-print-invoice  btn-primary " type="submit" value="{{__('Save Changes')}}">
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    <div id="pusher-settings" class="card">
                        <form method="POST" action="{{ route('admin.pusher.settings.store') }}" accept-charset="UTF-8">
                            @csrf
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="mb-0">{{ __('Pusher Settings') }}</h5>
                                    </div>
                                    <div class="col switch-width text-end">
                                        <div class="form-group mb-0">
                                            {{-- <label class="form-label">{{ __('Enable Chat') }}</label> --}}
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" data-toggle="switchbutton"
                                                    data-onstyle="primary" class=""
                                                    name="enable_chat" id="enable_chat"
                                                    {{ Utility::getSettingValByName('CHAT_MODULE') == 'yes' ? 'checked="checked"' : '' }}>
                                                <label class="custom-control-label" for="enable_chat"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                        <label for="pusher_app_id"
                                            class="form-label">{{ __('Pusher App Id') }}</label>
                                        <input class="form-control" placeholder="Enter Pusher App Id"
                                            name="pusher_app_id" type="text" value="{{ !empty($setting['PUSHER_APP_ID']) ? $setting['PUSHER_APP_ID'] : ""  }}" id="pusher_app_id">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                        <label for="pusher_app_key"
                                            class="form-label">{{ __('Pusher App Key') }}</label>
                                        <input class="form-control " placeholder="Enter Pusher App Key"
                                            name="pusher_app_key" type="text" value="{{ !empty($setting['PUSHER_APP_KEY']) ? $setting['PUSHER_APP_KEY'] : ""  }}" id="pusher_app_key">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                        <label for="pusher_app_secret"
                                            class="form-label">{{ __('Pusher App Secret') }}</label>
                                        <input class="form-control " placeholder="Enter Pusher App Secret"
                                            name="pusher_app_secret" type="text" value="{{ !empty($setting['PUSHER_APP_SECRET']) ? $setting['PUSHER_APP_SECRET'] : ""  }}" id="pusher_app_secret">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                        <label for="pusher_app_cluster"
                                            class="form-label">{{ __('Pusher App Cluster') }}</label>
                                        <input class="form-control " placeholder="Enter Pusher App Cluster"
                                            name="pusher_app_cluster" type="text" value="{{ !empty($setting['PUSHER_APP_CLUSTER']) ? $setting['PUSHER_APP_CLUSTER'] : ""  }}" id="pusher_app_cluster">
                                    </div>
                                    <div class="col-lg-12  text-right text-end float-end">
                                        <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary btn-block btn-submit text-white">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="recaptcha-settings" class="card pb-4">
                        <form method="POST" action="{{ route('admin.recaptcha.settings.store') }}" accept-charset="UTF-8">
                            @csrf
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5>{{ __('ReCaptcha Settings') }}</h5>
                                            <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                target="_blank" class="text-blue">
                                                <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                            </a>
                                        </div>
                                        <div class="col switch-width text-end">
                                            <div class="form-group mb-0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" value="yes"
                                                        name="recaptcha_module" id="recaptcha_module"
                                                        {{ env('RECAPTCHA_MODULE') == 'yes' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label" for="recaptcha_module"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_key"
                                                class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                            <input class="form-control"
                                                placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                name="google_recaptcha_key" type="text"
                                                value="{{ env('NOCAPTCHA_SITEKEY') }}" id="google_recaptcha_key">
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="google_recaptcha_secret"
                                                class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                            <input class="form-control "
                                                placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                name="google_recaptcha_secret" type="text"
                                                value="{{ env('NOCAPTCHA_SECRET') }}" id="google_recaptcha_secret">
                                        </div>
                                    </div>
                                    <div class="col-lg-12  text-right text-end float-end">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-primary btn-block btn-submit text-white">
                                    </div>
                                </div>
                        </form>
                    </div>

                    <div id="ticket-fields-settings" class="card">
                        <div class="custom-fields" data-value="{{ json_encode($customFields) }}">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="">
                                    <h5 class="">{{ __('Ticket Fields Settings') }}</h5>
                                    <label class="form-check-label pe-5 text-muted"
                                        for="enable_chat">{{ __('You can easily change order of fields using drag & drop.') }}</label>
                                </div>
                                <button data-repeater-create type="button"
                                    class="btn btn-sm btn-primary btn-icon m-1 float-end ms-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ __('Create Custom Field') }}">
                                    <i class="ti ti-plus mr-1"></i>
                                </button>
                            </div>
                            <div class="card-body table-border-style">
                                <form method="post" action="{{ route('admin.custom-fields.store') }}">
                                    @csrf
                                    <div class="table-responsive m-0 custom-field-table">

                                        <table class="table dataTable-table" id="pc-dt-simple" data-repeater-list="fields">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th></th>
                                                    <th>{{ __('Labels') }}</th>
                                                    <th>{{ __('Placeholder') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('Require') }}</th>
                                                    <th>{{ __('Width') }}</th>
                                                    <th class="text-right">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr data-repeater-item>
                                                    <td><i class="ti ti-arrows-maximize sort-handler"></i></td>
                                                    <td>
                                                        <input type="hidden" name="id" id="id" />
                                                        <input type="text" name="name" class="form-control mb-0"
                                                            required />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="placeholder" class="form-control mb-0"
                                                            required />
                                                    </td>
                                                    <td>
                                                        <select class="form-control select-field field_type mr-2"
                                                            name="type">
                                                            @foreach (\App\Models\CustomField::$fieldTypes as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="form-control select-field field_type"
                                                            name="is_required">
                                                            <option value="1">{{ __('Yes') }}</option>
                                                            <option value="0">{{ __('No') }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select-field" name="width">
                                                            <option value="3">25%</option>
                                                            <option value="4">33%</option>
                                                            <option value="6">50%</option>
                                                            <option value="8">66%</option>
                                                            <option value="12">100%</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <a data-repeater-delete class="delete-icon"><i
                                                                class="fas fa-trash text-danger"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="text-right text-end float-end p-4">
                                            <button class="btn btn-primary btn-block btn-submit"
                                                type="submit">{{ __('Save Changes') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="company-settings">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Company Settings') }}</h5>
                            </div>
                            {{Form::model($setting,array('route'=>'company.settings','method'=>'post'))}}
                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_name *',__('Company Name *'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_name',null,array('class'=>'form-control '))}}

                                        @error('company_name')

                                        <span class="invalid-company_name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_address',__('Address'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_address',null,array('class'=>'form-control '))}}
                                        @error('company_address')
                                        <span class="invalid-company_address" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_city',__('City'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_city',null,array('class'=>'form-control '))}}
                                        @error('company_city')
                                        <span class="invalid-company_city" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_state',__('State'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_state',null,array('class'=>'form-control '))}}
                                        @error('company_state')
                                        <span class="invalid-company_state" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_zipcode',__('Zip/Post Code'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_zipcode',null,array('class'=>'form-control'))}}
                                        @error('company_zipcode')
                                        <span class="invalid-company_zipcode" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_country',__('Country'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_country',null,array('class'=>'form-control '))}}
                                        @error('company_country')
                                        <span class="invalid-company_country" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_telephone',__('Telephone'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_telephone',null,array('class'=>'form-control'))}}
                                        @error('company_telephone')
                                        <span class="invalid-company_telephone" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_email',__('System Email *'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_email',null,array('class'=>'form-control'))}}
                                        @error('company_email')
                                        <span class="invalid-company_email" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{Form::label('company_email_from_name',__('Email (From Name) *'),['class'=>'col-form-label']) }}
                                        {{Form::text('company_email_from_name',null,array('class'=>'form-control '))}}
                                        @error('company_email_from_name')
                                        <span class="invalid-company_email_from_name" role="alert"><strong class="text-danger">{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn-submit btn btn-primary" type="submit">
                                    {{__('Save Changes')}}
                                </button>
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>

                    <div id="storage-settings" class="card mb-3">
                        {{ Form::open(array('route' => 'storage.setting.store', 'enctype' => "multipart/form-data")) }}
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        <h5 class="">{{ __('Storage Settings') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="local-outlined" autocomplete="off" {{  $setting['storage_setting'] == 'local'?'checked':'' }} value="local" checked>
                                        <label class="btn btn-outline-success" for="local-outlined">{{ __('Local') }}</label>
                                    </div>
                                    <div  class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="s3-outlined" autocomplete="off" {{  $setting['storage_setting']=='s3'?'checked':'' }}  value="s3">
                                        <label class="btn btn-outline-success" for="s3-outlined"> {{ __('AWS S3') }}</label>
                                    </div>

                                    <div  class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting" id="wasabi-outlined" autocomplete="off" {{  $setting['storage_setting']=='wasabi'?'checked':'' }} value="wasabi">
                                        <label class="btn btn-outline-success" for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                    </div>
                                </div>
                                <div  class="mt-2">
                                <div class="local-setting row  {{ $setting['storage_setting']=='local'?' ':'d-none' }} ">
                                    <div class="form-group col-8 switch-width">
                                        {{Form::label('local_storage_validation',__('Only Upload Files'),array('class'=>' form-label')) }}
                                        <select name="local_storage_validation[]" class="form-control" id="choices-multiple-remove-button"  multiple>
                                            @foreach($file_type as $f)
                                                <option @if (in_array($f, $local_storage_validations)) selected @endif>{{$f}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)')}}</label>
                                            <input type="number" name="local_storage_max_upload_size" class="form-control" value="{{(!isset($setting['local_storage_max_upload_size']) || is_null($setting['local_storage_max_upload_size'])) ? '' : $setting['local_storage_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="s3-setting row {{ $setting['storage_setting']=='s3'?' ':'d-none' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_key">{{ __('S3 Key') }}</label>
                                                <input type="text" name="s3_key" class="form-control" value="{{(!isset($setting['s3_key']) || is_null($setting['s3_key'])) ? '' : $setting['s3_key']}}" placeholder="{{ __('S3 Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_secret">{{ __('S3 Secret') }}</label>
                                                <input type="text" name="s3_secret" class="form-control" value="{{(!isset($setting['s3_secret']) || is_null($setting['s3_secret'])) ? '' : $setting['s3_secret']}}" placeholder="{{ __('S3 Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_region">{{ __('S3 Region') }}</label>
                                                <input type="text" name="s3_region" class="form-control" value="{{(!isset($setting['s3_region']) || is_null($setting['s3_region'])) ? '' : $setting['s3_region']}}" placeholder="{{ __('S3 Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                <input type="text" name="s3_bucket" class="form-control" value="{{(!isset($setting['s3_bucket']) || is_null($setting['s3_bucket'])) ? '' : $setting['s3_bucket']}}" placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_url">{{ __('S3 URL')}}</label>
                                                <input type="text" name="s3_url" class="form-control" value="{{(!isset($setting['s3_url']) || is_null($setting['s3_url'])) ? '' : $setting['s3_url']}}" placeholder="{{ __('S3 URL')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_endpoint">{{ __('S3 Endpoint')}}</label>
                                                <input type="text" name="s3_endpoint" class="form-control" value="{{(!isset($setting['s3_endpoint']) || is_null($setting['s3_endpoint'])) ? '' : $setting['s3_endpoint']}}" placeholder="{{ __('S3 Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            <div>
                                                <label class="form-label" for="s3_storage_validation">{{ __('Only Upload Files')}}</label>
                                            </div>
                                            <select class="form-control" name="s3_storage_validation[]" id="choices-multiple-remove-button1" placeholder="This is a placeholder" multiple>
                                                @foreach($file_type as $f)
                                                    <option @if (in_array($f, $s3_storage_validations)) selected @endif>{{$f}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="s3_max_upload_size">{{ __('Max upload size ( In KB)')}}</label>
                                                <input type="number" name="s3_max_upload_size" class="form-control"
                                                value="{{(!isset($setting['s3_max_upload_size']) || is_null($setting['s3_max_upload_size'])) ? '' : $setting['s3_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="wasabi-setting row {{  $setting['storage_setting']=='wasabi'?' ':'d-none' }}">
                                    <div class=" row ">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_key">{{ __('Wasabi Key') }}</label>
                                                <input type="text" name="wasabi_key" class="form-control" value="{{(!isset($setting['wasabi_key']) || is_null($setting['wasabi_key'])) ? '' : $setting['wasabi_key']}}" placeholder="{{ __('Wasabi Key') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                <input type="text" name="wasabi_secret" class="form-control" value="{{(!isset($setting['wasabi_secret']) || is_null($setting['wasabi_secret'])) ? '' : $setting['wasabi_secret']}}" placeholder="{{ __('Wasabi Secret') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="s3_region">{{ __('Wasabi Region') }}</label>
                                                <input type="text" name="wasabi_region" class="form-control" value="{{(!isset($setting['wasabi_region']) || is_null($setting['wasabi_region'])) ? '' : $setting['wasabi_region']}}" placeholder="{{ __('Wasabi Region') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                <input type="text" name="wasabi_bucket" class="form-control" value="{{(!isset($setting['wasabi_bucket']) || is_null($setting['wasabi_bucket'])) ? '' : $setting['wasabi_bucket']}}" placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_url">{{ __('Wasabi URL')}}</label>
                                                <input type="text" name="wasabi_url" class="form-control" value="{{(!isset($setting['wasabi_url']) || is_null($setting['wasabi_url'])) ? '' : $setting['wasabi_url']}}" placeholder="{{ __('Wasabi URL')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_root">{{ __('Wasabi Root')}}</label>
                                                <input type="text" name="wasabi_root" class="form-control" value="{{(!isset($setting['wasabi_root']) || is_null($setting['wasabi_root'])) ? '' : $setting['wasabi_root']}}" placeholder="{{ __('Wasabi Bucket') }}">
                                            </div>
                                        </div>
                                        <div class="form-group col-8 switch-width">
                                            {{Form::label('wasabi_storage_validation',__('Only Upload Files'),array('class'=>'form-label')) }}

                                            <select name="wasabi_storage_validation[]" class="form-control" id="choices-multiple-remove-button2" multiple>
                                                @foreach($file_type as $f)
                                                    <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>{{$f}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="wasabi_root">{{ __('Max upload size ( In KB)')}}</label>
                                                <input type="number" name="wasabi_max_upload_size" class="form-control" value="{{(!isset($setting['wasabi_max_upload_size']) || is_null($setting['wasabi_max_upload_size'])) ? '' : $setting['wasabi_max_upload_size']}}" placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
                            </div>
                        {{Form::close()}}
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/repeater.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })

        $(".list-group-item").click(function(){
            $('.list-group-item').filter(function(){
                return this.href == id;
            }).parent().removeClass('text-primary');
        });

    </script>
    <script>
        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>

        $(document).ready(function() {
            if ($('.gdpr_fulltime').is(':checked')) {
                $('.fulltime').show();
            } else {
                $('.fulltime').hide();
            }

            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {
                    $('.fulltime').show();
                } else {
                    $('.fulltime').hide();
                }
            });
        });
    </script>
<script>
    var multipleCancelButton = new Choices(
               '#choices-multiple-remove-button', {
                   removeItemButton: true,
               }
           );

           var multipleCancelButton = new Choices(
               '#choices-multiple-remove-button1', {
                   removeItemButton: true,
               }
           );

           var multipleCancelButton = new Choices(
               '#choices-multiple-remove-button2', {
                   removeItemButton: true,
               }
           );
   </script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function(){
            $('.list-group-item').filter(function(){
                return this.href == id;
            }).parent().removeClass('text-primary');
        });

        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }

        $(document).on('change','[name=storage_setting]',function(){
        if($(this).val() == 's3'){
            $('.s3-setting').removeClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').addClass('d-none');
        }else if($(this).val() == 'wasabi'){
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').removeClass('d-none');
            $('.local-setting').addClass('d-none');
        }else{
            $('.s3-setting').addClass('d-none');
            $('.wasabi-setting').addClass('d-none');
            $('.local-setting').removeClass('d-none');
        }
    });
    </script>

    <script>
        $(document).on("click", '.send_email', function(e) {

            e.preventDefault();
            var title = $(this).attr('data-title');

            var size = 'md';
            var url = $(this).attr('data-url');
            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');

                $.post(url, {
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val(),
                }, function(data) {
                    $('#commonModal .modal-body').html(data);
                });
            }
        });
        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            $("#email_sending").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                beforeSend: function() {
                    $('#test_email .btn-create').attr('disabled', 'disabled');
                },
                success: function(data) {
                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sending").hide();
                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        });

        // $(document).on('change','.SITE_RTL',function(){
        //     $()
        // });
        $(document).ready(function() {
            var $dragAndDrop = $("body .custom-fields tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeater = $('.custom-fields').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                    var eleId = $(this).find('input[type=hidden]').val();
                    if (eleId > 6 || eleId == '') {
                        $(this).find(".field_type option[value='file']").remove();
                        $(this).find(".field_type option[value='select']").remove();
                    }
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });

            var value = $(".custom-fields").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

            $.each($('[data-repeater-item]'), function(index, val) {
                var elementId = $(this).find('input[type=hidden]').val();
                if (elementId <= 6) {
                    $.each($(this).find('.field_type'), function(index, val) {
                        $(this).prop('disabled', 'disabled');
                    });
                    $(this).find('.delete-icon').remove();
                }
            });
        });


    </script>

    <script type="text/javascript">
        $(document).on("click", ".email-template-checkbox", function () {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'post',
                success: function (response) {
                    if (response.is_success) {
                        show_toastr('Success', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('Error', response.error, 'error');
                    } else {
                        show_toastr('Error', response, 'error');
                    }
                }
            })
        });
    </script>

@endpush
