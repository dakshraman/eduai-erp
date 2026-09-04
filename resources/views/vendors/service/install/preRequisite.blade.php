@extends('service::layouts.app_install', ['title' => __('service::install.environment')])

@section('content')
    <div class="card-header">
        <h2>{{ __('service::install.environment_title') }}</h2>
    </div>

    <div class="card-body">
        {{-- Server Requirements --}}
        <h4 class="section-title">{{ __('service::install.server_requirements') }}</h4>

        <div class="check-grid mb-3">
            @foreach ($server_checks as $server)
                @php
                    $isError = ($server['type'] ?? null) === 'error';
                    if ($isError && !$has_false) { $has_false = true; }
                @endphp
                <div class="check-item {{ $isError ? 'fail' : 'pass' }}">
                    @if($isError)
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @endif
                    <span>{{ $server['message'] ?? '' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Folder Requirements --}}
        <h4 class="section-title">{{ __('service::install.folder_permissions') }}</h4>

        <div class="check-grid mb-3">
            @foreach ($folder_checks as $folder)
                @php
                    $isError = ($folder['type'] ?? null) === 'error';
                    if ($isError && !$has_false) { $has_false = true; }
                @endphp
                <div class="check-item {{ $isError ? 'fail' : 'pass' }}">
                    @if($isError)
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    @endif
                    <span>{{ $folder['message'] ?? '' }}</span>
                </div>
            @endforeach
        </div>

        {{-- Result --}}
        @if($has_false)
            <div class="alert alert-danger mt-2">
                <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ __('service::install.requirement_failed') }}</span>
            </div>

            <div class="form-actions">
                <a href="{{ route('service.install') }}" class="btn btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ __('service::install.back_button_content') }}
                </a>
                <a href="{{ route('service.preRequisite') }}" class="btn btn-primary">
                    {{ __('service::install.refresh') }}
                </a>
            </div>
        @else
            <div class="alert alert-success mt-2">
                <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ __('service::install.server_requirements') }} — {{ __('All checks passed. Ready to continue.') }}</span>
            </div>

            <div class="form-actions">
                <a href="{{ route('service.install') }}" class="btn btn-ghost">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ __('service::install.back_button_content') }}
                </a>
                <a href="{{ route('service.license') }}" class="btn btn-primary">
                    {{ __('service::install.lets_go_next') }}
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </a>
            </div>
        @endif
    </div>
@stop
