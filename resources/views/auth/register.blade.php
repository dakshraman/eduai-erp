@php
    $gs = generalSetting();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset($gs->favicon ?? 'public/backEnd/img/favicon.png') }}" type="image/png"/>
    <title>Register - {{ $gs->school_name ?? config('app.name') }}</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{ assetPath('public/backEnd/') }}/vendors/css/bootstrap.css"/>
    <link rel="stylesheet" href="{{ assetPath('public/backEnd/') }}/vendors/css/themify-icons.css"/>
    <link rel="stylesheet" href="{{ assetPath('public/backEnd/') }}/vendors/css/toastr.min.css"/>
    <x-root-css/>
</head>

<body class="login admin login_screen_body" style="background: url({{ url('public/backEnd/img/login-bg.png') }}) no-repeat center; background-size: cover;">
<style>
    .login_screen_body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 30px 0;
        grid-gap: 20px;
    }

    body { height: 100%; }

    .register-form .form-group {
        margin-bottom: 1.25rem;
    }

    .register-form .form-group label {
        font-weight: 600;
        margin-bottom: 0.35rem;
        display: block;
    }

    .register-form .form-control {
        height: 44px;
        border-radius: 6px;
    }

    .register-form .input-group-addon {
        background: none;
        border: none;
        padding: 0;
    }

    @media (max-width: 991px) {
        .login.admin .login-height .form-wrap {
            padding: 50px 8px;
        }
        .login-area .login-height {
            min-height: auto;
        }
    }
</style>

<section class="login-area up_login login_screen">
    <div class="container">
        <input type="hidden" id="url" value="{{ url('/') }}">
        <div class="row login-height justify-content-center align-items-center mb-30 mt-30">
            <div class="col-lg-6 col-md-8">
                <div class="form-wrap text-center">
                    <div class="logo-container">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset($gs->logo ?? 'public/backEnd/img/logo.png') }}" alt="" class="logoimage">
                        </a>
                    </div>

                    <h5 class="text-uppercase">Create Your School Account</h5>

                    @if(session('message-success'))
                        <p class="text-success">{{ session('message-success') }}</p>
                    @endif
                    @if(session('message-danger'))
                        <p class="text-danger">{{ session('message-danger') }}</p>
                    @endif

                    <form method="POST" class="register-form" action="{{ route('register.store') }}">
                        @csrf

                        <div class="form-group text-left">
                            <label for="school_name">School Name</label>
                            <input class="form-control{{ $errors->has('school_name') ? ' is-invalid' : '' }}"
                                   type="text" name="school_name" id="school_name"
                                   placeholder="Enter school name" value="{{ old('school_name') }}" required autofocus/>
                            @if ($errors->has('school_name'))
                                <span class="text-danger text-left mb-15" role="alert">
                                    {{ $errors->first('school_name') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group text-left">
                            <label for="school_code">School Code</label>
                            <input class="form-control{{ $errors->has('school_code') ? ' is-invalid' : '' }}"
                                   type="text" name="school_code" id="school_code"
                                   placeholder="e.g. myacademy" value="{{ old('school_code') }}" required/>
                            @if ($errors->has('school_code'))
                                <span class="text-danger text-left mb-15" role="alert">
                                    {{ $errors->first('school_code') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group text-left">
                            <label for="full_name">Admin Full Name</label>
                            <input class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}"
                                   type="text" name="full_name" id="full_name"
                                   placeholder="Enter admin full name" value="{{ old('full_name') }}" required/>
                            @if ($errors->has('full_name'))
                                <span class="text-danger text-left mb-15" role="alert">
                                    {{ $errors->first('full_name') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group text-left">
                            <label for="email">Admin Email</label>
                            <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   type="email" name="email" id="email"
                                   placeholder="Enter admin email" value="{{ old('email') }}" required/>
                            @if ($errors->has('email'))
                                <span class="text-danger text-left mb-15" role="alert">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group text-left">
                            <label for="password">Password</label>
                            <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   type="password" name="password" id="password"
                                   placeholder="Enter password" required/>
                            @if ($errors->has('password'))
                                <span class="text-danger text-left mb-15" role="alert">
                                    {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group text-left">
                            <label for="password_confirmation">Confirm Password</label>
                            <input class="form-control"
                                   type="password" name="password_confirmation" id="password_confirmation"
                                   placeholder="Confirm password" required/>
                        </div>

                        <div class="form-group mt-30 mb-30 flex-fill">
                            <button type="submit" class="primary-btn fix-gr-bg w-100">
                                <span class="ti-user mr-2"></span>
                                Register
                            </button>
                        </div>
                    </form>

                    <p class="mt-10">
                        Already have an account? <a href="{{ route('login') }}">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer_area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <p class="mb-0">{!! $gs->copyright_text ?? '' !!}</p>
            </div>
        </div>
    </div>
</footer>

<script src="{{ assetPath('public/backEnd/') }}/vendors/js/jquery-3.2.1.min.js"></script>
<script src="{{ assetPath('public/backEnd/') }}/vendors/js/popper.js"></script>
<script src="{{ assetPath('public/backEnd/') }}/vendors/js/bootstrap.min.js"></script>
<script src="{{ assetPath('public/backEnd/') }}/vendors/js/toastr.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#school_name').on('keyup', function () {
            var name = $(this).val();
            var code = name.toLowerCase().replace(/[^a-z0-9]/g, '').substring(0, 8);
            $('#school_code').val(code);
        });
    });
</script>

{!! Toastr::message() !!}

</body>
</html>
