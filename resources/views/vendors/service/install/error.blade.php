@extends('service::layouts.app', ['title' => __('service::install.error')])

@section('content')
    <div class="card-header">
        <h2>{{ $title ?? __('service::install.error') }}</h2>
    </div>

    <div class="card-body text-center">
        <div class="hero-icon error">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        </div>

        <p class="text-secondary">{{ $message }}</p>

        <div class="form-actions form-actions-center mt-3">
            <a href="{{ url('/') }}" class="btn btn-primary">
                {{ __('service::install.goto_home') }}
            </a>
        </div>
    </div>
@stop
