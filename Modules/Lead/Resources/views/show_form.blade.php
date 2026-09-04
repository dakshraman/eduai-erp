<?php
$setting = $formData->generalSettings;

App::setLocale(getUserLanguage());

if (isset($setting->copyright_text)) {
    $copyright_text = $setting->copyright_text;
} else {
    $copyright_text = 'Copyright © ' . date('Y') . ' All rights reserved | This template is made with by Codethemes';
}
if ($setting->logo) {
    $logo = $setting->logo;
} else {
    $logo = 'public/uploads/settings/logo.png';
}

$ttl_rtl = userRtlLtl();

if ($setting->favicon) {
    $favicon = $setting->favicon;
} else {
    $favicon = 'public/uploads/settings/favicon.png';
}

$login_background = App\Models\SmBackgroundSetting::where([['is_default', 1], ['title', 'Lead Form Background']])
    ->where('school_id', app('school')->id)
    ->first();

if (empty($login_background)) {
    $css = 'background: url(' . url('public/backEnd/img/in_registration.png') . ')  no-repeat center; background-size: cover; ';
} else {
    if (!empty($login_background->image)) {
        $css = "background: url('" . url($login_background->image) . "')  no-repeat center;  background-size: cover;";
    } else {
        $css = 'background:' . $login_background->color;
    }
}
?>


        <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @if (isset($ttl_rtl) && $ttl_rtl == 1) dir="rtl" class="rtl" @endif>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ assetPath($favicon) }}" type="image/png"/>
    <title>{{ @$setting->school_name ? @$setting->school_name : 'Infix Edu ERP' }}
        | @lang('student.student_registration') </title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{ url('/') }}/public/backEnd/vendors/css/bootstrap.css"/>
    <link rel="stylesheet" href="{{ url('/') }}/public/backEnd/vendors/css/themify-icons.css"/>
    <link rel="stylesheet" href="{{ url('/public/') }}/landing/css/toastr.css">
    <link rel="stylesheet" href="{{ url('/') }}/public/backEnd/vendors/css/nice-select.css"/>
    <link rel="stylesheet" href="{{ url('/') }}/public/backEnd/vendors/js/select2/select2.css"/>
    <link rel="stylesheet" href="{{ url('/') }}/public/backEnd/vendors/css/fastselect.min.css"/>
    <link rel="stylesheet" href="{{ url('public/backEnd/') }}/vendors/css/toastr.min.css"/>
    <link rel="stylesheet" href="{{ url('public/backEnd/') }}/vendors/css/bootstrap-datepicker.min.css"/>
    <link rel="stylesheet" href="{{ url('public/backEnd/') }}/vendors/css/bootstrap-datetimepicker.min.css"/>
    @if (userRtlLtl() == 1)
        <style>
            html[dir="rtl"] .loader_style_parent_reg {
                padding-left: 25px;
                position: absolute;
                left: 10px;
                top: 5px;
            }

            html[dir="rtl"] .input-right-icon button {
                margin-left: 0;
                left: 0;
                margin-right: auto;
            }

            html[dir="rtl"] .input-right-icon button i {
                left: 22px;
                display: inline-block !important;
            }

            html[dir="rtl"] .input-right-icon button {
                margin-left: 0;
                left: 0;
                margin-right: auto;
                position: absolute;
                left: 0;
            }

            html[dir="rtl"] .mr-20 {
                margin-right: 0px;
                margin-left: 20px;
            }

            html[dir="rtl"] .ml-30 {
                margin-left: 0;
                margin-right: 30px;
            }

            html[dir="rtl"] .primary_input_field:focus ~ label,
            .primary_input_field.read-only-input ~ label,
            html[dir="rtl"] .has-content.primary_input_field ~ label {
                text-align: right !important;
            }

            html[dir="rtl"] .primary_input_field ~ label {
                left: auto;
                right: 0 !important;
                text-align: right;
            }
        </style>

        <link rel="stylesheet" href="{{ assetPath('public/backEnd/css/rtl/style.css') }}"/>
    @else
        <link rel="stylesheet" href="{{ assetPath('public/backEnd/css/style.css') }}"/>
    @endif
    <link rel="stylesheet" href="{{ assetPath('modules/lead/css/style.css') }}">
    <style>
        .single_registration_area .form-group .form-control {
            padding: 0px 0px 20px;
        }

        .single_registration_area .nice-select.niceSelect {
            padding: 0 0px 13px;
        }

        .primary_input_field {
            padding-left: 0px;
        }

        .login-area input.common-checkbox + label {
            display: block;
            cursor: pointer;
        }

        .login-area input.common-checkbox {
            display: none;
        }

        .login-area input.common-checkbox + label:before {
            content: "";
            border: 1px solid var(--base_color);
            border-radius: 2px;
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            width: 14px;
            height: 14px;
            line-height: 15px;
            padding-left: 0px;
            margin-right: 14px;
            vertical-align: bottom;
            color: transparent;
            position: relative;
            top: -6px;
            -webkit-transition: all 0.4s ease 0s;
            -moz-transition: all 0.4s ease 0s;
            -o-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }

        .login-area input.common-checkbox + label:active:before {
            transform: scale(0);
        }

        .login-area input.common-checkbox:checked + label:before {
            content: "\e64d";
            border: 0px;
            font-family: "themify";
            border-radius: 2px;
            display: inline-block;
            font-size: 16px;
            font-weight: 600;
            width: 14px;
            height: 14px;
            line-height: 15px;
            padding-left: 0px;
            margin-right: 14px;
            vertical-align: bottom;
            color: var(--base_color);
            position: relative;
            top: -6px;
            -webkit-transition: all 0.4s ease 0s;
            -moz-transition: all 0.4s ease 0s;
            -o-transition: all 0.4s ease 0s;
            transition: all 0.4s ease 0s;
        }

        .login-area input.common-checkbox:disabled + label:before {
            transform: scale(1);
            border-color: var(--border_color);
        }

        .login-area input.common-checkbox:checked:disabled + label:before {
            transform: scale(1);
            background-color: #bfb;
            border-color: var(--border_color);
        }

        .login-area input[type="checkbox"]:checked + label:before {
            left: 0;
            transform: rotate(0deg);
        }

        .login-area input[type="checkbox"] + label {
            padding-left: 0px;
        }

        .login-area input[type="checkbox"] + label:last-child {
            margin-bottom: 0;
            font-size: 14px;
            color: #282545;
        }
    </style>

</head>

<body class="reg_bg" style="{!! $css !!}}">
<!--================ Start Login Area =================-->
<div class="reg_bg">

</div>
<section class="login-area  registration_area ">
    <div class="container">
        <div class="registration_area_logo">

            @if ($with_logo != null)

                @if (!empty($setting->logo))
                    <img src="{{ assetPath($setting->logo) }}" alt="Login Panel">
                @endif
            @endif
        </div>
        @if (\Session::has('success'))
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-12">
                    <div class="text-center white-box single_registration_area">
                        <h3>{!! \Session::get('success') !!}</h3>
                        <a href="{{ url('/') }}" class="primary-btn small fix-gr-bg">
                            @lang('common.home')
                        </a>
                    </div>

                </div>
            </div>
        @else
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-12">
                    <div class="white-box single_registration_area">
                        {{ html()->form('POST', route('lead.store-form-data'))->attributes(['class' => 'form-horizontal', 'files' => true, 'enctype' => 'multipart/form-data'])->open() }}

                        @if ($errors->any())
                            <div>
                                <ul class="mt-1 text-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <input type="hidden" name="form_builder_id" value="{{ $formData->id }}">

                        <div class="row">

                            @foreach ($form as $item)
                                @php
                                    $required = property_exists($item, 'required');
                                    if (property_exists($item, 'name')) {
                                        $facultyId = $item->name == 'un_faculty_id' ? 'select_faculty' : '';
                                        $departmentDiv = $item->name == 'un_department_id' ? 'select_dept_div' : '';
                                        $departmentId = $item->name == 'un_department_id' ? 'select_dept' : '';
                                    } else {
                                        $facultyId = '';
                                        $departmentDiv = '';
                                        $departmentId = '';
                                    }

                                @endphp
                                @if ($item->type == 'header' || $item->type == 'paragraph')
                                    <div class="col-lg-12">
                                        <{{ $item->subtype }}>{{ $item->label }} </{{ $item->subtype }}>
                        </div>
                        @elseif($item->type == 'file')
                            <div class="col-lg-6">
                                <div class="row no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="primary_input ">
                                            <input class="primary_input_field" type="text"
                                                   id="placeholderPhoto"
                                                   placeholder="{{ $item->label }} {{ $required ? '*' : '' }}"
                                                   {{ $required ? 'required' : '' }} readonly="">

                                            @if ($errors->has('file_input'))
                                                <span class="text-danger d-block">
                                                                <strong>{{ @$errors->first('file_input') }}
                                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                   for="photo">@lang('common.browse')</label>
                                            <input type="file" class="d-none"
                                                   value="{{ old('photo') }}" name="{{ $item->name }}"
                                                   id="photo">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @elseif($item->type == 'text' || $item->type == 'number' || $item->type == 'email')
                            <div class="col-lg-12">
                                <div class="form-group input-group">
                                    <input class="{{ $item->className }}" type="{{ $item->type }}"
                                           name="{{ $item->name }}" value="{{ old($item->name) }}"
                                           placeholder="{{ $item->label }} {{ $required ? '*' : '' }}"
                                            {{ $required ? 'required' : '' }} />
                                </div>

                            </div>
                        @elseif($item->type == 'select')
                            <div class="col-lg-12" id="{{ $departmentDiv }}">
                                <div class="primary_input ">
                                    <select class="{{ $item->className }}" {{ $required ? 'required' : '' }}
                                    name="{{ $item->name }}"
                                            id="{{ $departmentId . '' . $facultyId }}">
                                        <option
                                                data-display="{{ $item->label }} {{ $required ? '*' : '' }}"
                                                value="">{{ $item->label }} {{ $required ? '*' : '' }}
                                        </option>
                                        @foreach ($item->values as $value)
                                            <option value="{{ $value->value }}"
                                                    {{ old($item->name) == $value->value ? 'selected' : '' }}>
                                                {{ $value->label }}</option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>
                        @elseif($item->type == 'textarea')
                            <div class="col-lg-12 mb-10">
                                <div class="primary_input ">
                                                <textarea class="{{ $item->className }}" cols="0" rows="4"
                                                          name="{{ $item->name }}"
                                                          placeholder="{{ $item->label }} {{ $required ? '*' : '' }}" {{ $required ? 'required' : '' }}>{{ old($item->name) }}</textarea>

                                </div>
                            </div>
                        @elseif($item->type == 'radio-group')
                            <div class="col-lg-12 mb-10">
                                <label for="">{{ $item->label }}</label>
                                <div class="d-flex radio-btn-flex">
                                    @foreach ($item->values as $value)
                                        <div class="mr-30">
                                            <input type="radio" name="{{ $item->name }}"
                                                   id="radio_{{ $value->label }}"
                                                   value="{{ $value->value }}"
                                                   class="common-radio relationButton" checked>
                                            <label
                                                    for="radio_{{ $value->label }}">{{ $value->label }}</label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @elseif($item->type == 'checkbox-group')
                            <div class="col-lg-12 mb-10">
                                <label>{{ @$item->label }}</label>
                                @foreach ($item->values as $value)
                                    <div class="primary_input">
                                        <input type="checkbox" id="checkbox_{{ $value->label }}"
                                               class="common-checkbox" name="{{ @$item->name }}[]"
                                               value="{{ @$value->label }}">
                                        <label
                                                for="checkbox_{{ $value->label }}">{{ @$value->label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                        @endif
                        @endforeach


                    </div>


                    <div class="row mt-40">
                        <div class="col-lg-12">

                            <div class="login_button text-center">
                                <button type="submit" class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                        title=""
                                        style="background: {{ $formData->webForm->submit_button_background }};color:{{ $formData->webForm->submit_button_background_text }}">
                                    <span class="ti-check"></span>
                                    {{ $formData->webForm->submit_button_text }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{ html()->form()->close() }}

                </div>
            </div>
    </div>
    @endif
    </div>
    </form>
</section>
<!--================ Start End Login Area =================-->
<!--================ Footer Area =================-->
<footer class="footer_area registration_footer">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <p></p>
            </div>
        </div>
    </div>
</footer>
<!--================ End Footer Area =================-->
<script src="{{ assetPath('public/backEnd/vendors/js/jquery-3.2.1.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/vendors/js/popper.js') }}"></script>
<script src="{{ assetPath('public/backEnd/vendors/js/bootstrap.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/vendors/js/nice-select.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/login.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/validate.js') }}"></script>
<script src="{{ assetPath('public/backEnd/vendors/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/main.js') }}"></script>
<script src="{{ assetPath('public/backEnd/js/custom.js') }}"></script>
<script src="{{ assetPath('/public/js/registration_custom.js') }}"></script>
<script type="text/javascript" src="{{ assetPath('public/backEnd/vendors/js/toastr.min.js') }}"></script>
{!! Toastr::message() !!}
@yield('script')
@if (moduleStatusCheck('University'))
    <script src="{{ assetPath('modules/university/js/app.js') }}"></script>
@endif
</body>

</html>
