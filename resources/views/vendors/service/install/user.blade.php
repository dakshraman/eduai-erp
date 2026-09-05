@extends('service::layouts.app_install', ['title' => config('spondonit.branding.admin_setup_title', __('service::install.admin_setup'))])

@section('content')
    <div class="card-header">
        <h2>{{ config('spondonit.branding.admin_setup_title', __('service::install.admin_setup')) }}</h2>
    </div>

    <div class="card-body">
        <form method="post"
              action="{{ route('service.user') }}"
              id="user_form"
              data-ajax-form
              data-preloader-message="{{ __('We are installing your system. This may take some time. Please do not refresh or close the browser.') }}">

            <div class="form-group">
                <label class="required" for="email">{{ __('service::install.email') }}</label>
                <input type="email"
                       class="form-control"
                       name="email"
                       id="email"
                       required
                       placeholder="{{ __('service::install.email') }}">
            </div>

            <div class="form-group">
                <label class="required" for="password">{{ __('service::install.password') }}</label>
                <input type="password"
                       class="form-control"
                       name="password"
                       id="password"
                       required
                       minlength="8"
                       placeholder="{{ __('service::install.password') }}">
                <span class="form-hint">{{ config('spondonit.branding.min_password_chars', __('service::install.the_password_must_be_at_least_8_characters')) }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="password_confirmation">{{ __('service::install.password_confirmation') }}</label>
                <input type="password"
                       class="form-control"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       minlength="8"
                       data-match="#password"
                       placeholder="{{ config('spondonit.branding.same_as_password', __('service::install.password_confirmation')) }}">
            </div>

            @if(config('app.app_sync'))
                <div class="form-group">
                    <label class="checkbox-wrapper">
                        <input name="seed" type="checkbox">
                        <span class="checkbox-box">
                            <svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg>
                        </span>
                        <span>{{ config('spondonit.branding.with_demo_data', __('Install With Demo Data')) }}</span>
                    </label>
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('service.license') }}" class="btn btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ __('service::install.back_button_content') }}
                </a>

                <button type="submit" class="btn btn-primary btn-submit">
                    {{ __('service::install.ready_to_go') }}
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

