<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if (isset($companyData))
            {{ $companyData['company_name'] }}
        @else
            {{ env('APP_NAME') }}
        @endif
    </title>
    <script src="{{ asset('js/app.js') }}"></script>
    <link href="{{ asset('css/css.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/nunito.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>

<body class="body">
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
