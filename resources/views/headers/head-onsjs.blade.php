<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

        @include('headers.head')

        <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/fontawesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/Ionicons/css/ionicons.min.css') }}">

        <link rel="stylesheet" href="{{ asset('plugins/onsen/css/onsenui.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/onsen/css/onsen-css-components.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/onsen/css/theme.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('css/ons-style.css') }}"> --}}


        <script src="{{ asset('plugins/onsen/js/onsenui.min.js') }}"></script>

        <link rel="stylesheet" href="{{ asset('plugins/gum/ceuai/ceai.css') }}">
        <script src="{{ asset('plugins/gum/ceuai/ceai.js') }}"></script>

        @yield('head')
    </head>

    <body>
        @yield('content')
    </body>
</html>
