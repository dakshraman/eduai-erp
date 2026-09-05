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
    <div class="installer-wrapper">
        <div class="installer-container">
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
