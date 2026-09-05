@extends('service::layouts.app_install', ['title' => __('service::install.license')])

@section('content')
    <div class="card-header">
        <h2>{{ __('service::install.license_verification') }}</h2>
        <p>{{ __('service::install.sub_title') }}</p>
    </div>

    <div class="card-body">
        <form method="post"
              action="{{ route('service.license') }}"
              id="license_form"
              data-ajax-form
              data-preloader-message="{{ __('We are validating your license. Please do not refresh or close the browser.') }}">

            <div class="form-group">
                <label class="required" for="access_code">{{ __('service::install.access_code') }}</label>
                <input type="text"
                       class="form-control"
                       name="access_code"
                       id="access_code"
                       required
                       autofocus
                       value="{{ old('access_code', request('access_code')) }}"
                       placeholder="{{ __('service::install.access_code') }}">
                @if(request('message'))
                    <span class="form-error">{{ request('message') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="envato_email">{{ __('service::install.envato_email') }}</label>
                <input type="email"
                       class="form-control"
                       name="envato_email"
                       id="envato_email"
                       required
                       value="{{ old('envato_email', request('envato_email')) }}"
                       placeholder="{{ __('service::install.envato_email') }}">
            </div>

            <div class="form-group">
                <label class="required" for="installed_domain">{{ __('service::install.installed_domain') }}</label>
                <input type="text"
                       class="form-control"
                       name="installed_domain"
                       id="installed_domain"
                       required
                       readonly
                       value="{{ appUrl() }}">
            </div>

            @if($reinstall)
                <div class="form-group">
                    <label class="checkbox-wrapper">
                        <input name="re_install" type="checkbox">
                        <span class="checkbox-box">
                            <svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg>
                        </span>
                        <span>{{ __('Re-install System') }}</span>
                    </label>
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('service.preRequisite') }}" class="btn btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ __('service::install.back_button_content') }}
                </a>

                <button type="submit" class="btn btn-primary btn-submit">
                    {{ __('service::install.lets_go_next') }}
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
                <button type="button" class="btn btn-primary btn-loading hidden" disabled>
                    <span class="spinner spinner-sm"></span>
                    {{ __('service::install.submitting') }}
                </button>
            </div>
        </form>
    </div>
@stop

