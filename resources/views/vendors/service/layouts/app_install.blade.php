<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $basePath = config('spondonit.branding.asset_path', 'vendor/spondonit');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset($basePath . '/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset($basePath . '/css/installer.css') }}">
    @stack('css')
</head>

<body>
    {{-- Preloader — shown on form submit, not on page load --}}
    <div class="preloader" id="preloader">
        <div class="spinner"></div>
        <p class="preloader-text">{{ __('service::install.installation_processing') }}</p>
    </div>

    <div class="installer-wrapper">
        <div class="installer-container">
            {{-- Step Indicator --}}
            @php
                $steps = [
                    ['route' => 'service.install', 'label' => __('service::install.welcome')],
                    ['route' => 'service.preRequisite', 'label' => __('service::install.environment')],
                    ['route' => 'service.license', 'label' => __('service::install.license')],
                    ['route' => 'service.database', 'label' => __('service::install.database')],
                    ['route' => 'service.user', 'label' => __('service::install.admin_setup')],
                    ['route' => 'service.done', 'label' => __('service::install.done')],
                ];

                $currentRoute = request()->route()?->getName();
                $routeNames = array_column($steps, 'route');
                $currentIndex = array_search($currentRoute, $routeNames);
            @endphp

            <ol class="stepper">
                @foreach($steps as $index => $step)
                    <li class="{{ $index < $currentIndex ? 'completed' : '' }}{{ $index === $currentIndex ? 'active' : '' }}">
                        {{ $step['label'] }}
                    </li>
                @endforeach
            </ol>

            <div class="card">
                @yield('content')
            </div>
        </div>
    </div>

    @if(session('message'))
        <div id="session-flash"
             data-message="{{ e(session('message')) }}"
             data-type="{{ e(session('status', 'error')) }}"
             class="hidden"></div>
    @endif

    <script src="{{ asset($basePath . '/js/installer.js') }}"></script>
    @stack('js')

</body>

</html>
