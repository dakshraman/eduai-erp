@extends('service::layouts.app_install', ['title' => __('service::install.database')])

@section('content')
    <div class="card-header">
        <h2>{{ __('service::install.database_title') }}</h2>
    </div>

    <div class="card-body">
        <h4 class="section-title">{{ __('service::install.database_setup') }}</h4>

        <form method="post"
              action="{{ route('service.database') }}"
              id="database_form"
              data-ajax-form
              data-preloader-message="{{ __('We are validating your database. Please do not refresh or close the browser.') }}">

            @if(config('spondonit.support_multi_connection', false))
                <div class="form-group">
                    <label class="required" for="db_connection">{{ __('service::install.db_connection') }}</label>
                    <select class="form-control"
                            name="db_connection"
                            id="db_connection"
                            required>
                        <option value="mysql" {{ config('database.default', 'mysql') === 'mysql' ? 'selected' : '' }}>
                            {{ __('service::install.mysql') }}
                        </option>
                        <option value="pgsql" {{ config('database.default', 'mysql') === 'pgsql' ? 'selected' : '' }}>
                            {{ __('service::install.pgsql') }}
                        </option>
                    </select>
                    <span class="form-hint">{{ __('service::install.db_connection_help') }}</span>
                </div>
            @endif

            <div class="form-group">
                <label class="required" for="db_host">{{ __('service::install.db_host') }}</label>
                <input type="text"
                       class="form-control"
                       name="db_host"
                       id="db_host"
                       required
                       placeholder="{{ __('service::install.db_host') }}"
                       value="{{ config('database.connections.mysql.host', 'localhost') }}">
            </div>

            <div class="form-group">
                <label class="required" for="db_port">{{ __('service::install.db_port') }}</label>
                <input type="text"
                       class="form-control"
                       name="db_port"
                       id="db_port"
                       required
                       placeholder="{{ __('service::install.db_port') }}"
                       value="{{ config('database.connections.mysql.port', '3306') }}">
            </div>

            <div class="form-group">
                <label class="required" for="db_database">{{ __('service::install.db_database') }}</label>
                <input type="text"
                       class="form-control"
                       name="db_database"
                       id="db_database"
                       required
                       autofocus
                       placeholder="{{ __('service::install.db_database') }}"
                       value="{{ config('database.connections.mysql.database') }}">
            </div>

            <div class="form-group">
                <label class="required" for="db_username">{{ __('service::install.db_username') }}</label>
                <input type="text"
                       class="form-control"
                       name="db_username"
                       id="db_username"
                       required
                       placeholder="{{ __('service::install.db_username') }}"
                       value="{{ config('database.connections.mysql.username') }}">
            </div>

            <div class="form-group">
                <label for="db_password">{{ __('service::install.db_password') }}</label>
                <input type="password"
                       class="form-control"
                       name="db_password"
                       id="db_password"
                       placeholder="{{ __('service::install.db_password') }}"
                       value="{{ config('database.connections.mysql.password') }}">
            </div>

            <div class="form-group">
                <label class="checkbox-wrapper">
                    <input name="force_migrate" type="checkbox">
                    <span class="checkbox-box">
                        <svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg>
                    </span>
                    <span class="text-danger font-medium">{{ __('Force Delete Previous Tables') }}</span>
                </label>
                <span class="form-hint text-danger">{{ __('Warning: This will permanently delete all existing tables in the database.') }}</span>
            </div>

            <div class="form-actions">
                <a href="{{ route('service.license') }}" class="btn btn-ghost">
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

