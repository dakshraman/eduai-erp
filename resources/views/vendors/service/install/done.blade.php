@extends('service::layouts.app', ['title' => __('service::install.done')])

@section('content')
    <div class="card-header">
        <h2>{{ config('spondonit.branding.welcome_title', __('service::install.welcome_title')) }}</h2>
    </div>

    <div class="card-body text-center">
        <div class="hero-icon success">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>

        <p class="text-secondary">{{ __('service::install.confirm_description') }}</p>

        {{-- Credential card — password is masked, copyable --}}
        <div class="credential-card">
            <div class="credential-row">
                <span class="credential-label">{{ __('service::install.email') }}</span>
                <span class="credential-value">{{ $user }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">{{ __('service::install.password') }}</span>
                <span class="credential-value credential-masked">••••••••</span>
            </div>
        </div>

        <button type="button"
                class="btn btn-ghost btn-sm btn-copy"
                data-clipboard="{{ $user }}&#10;{{ $pass }}"
                title="{{ __('Copy credentials to clipboard') }}">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"/><path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/></svg>
            <span class="copy-label">{{ __('Copy Credentials') }}</span>
        </button>

        <div class="form-actions form-actions-center mt-3">
            <a href="{{ url('/') }}" class="btn btn-primary">
                {{ __('service::install.goto_home') }}
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </a>
        </div>
    </div>
@stop
