<?php
    $userphoto = ($user->photo) ?  route('upload.file', $user->photo) : asset('images/nouser.png');
    $moduleUrl = null;
    if($module = $user->lastModule ? $user->lastModule : ($user->role->module ? $user->role->module : null)){
        $moduleUrl = $module->route ? route($module->route) : ($module->url ? $module->url : '#');
    }
?>
<!DOCTYPE html>
<html>
<head>
    @include('headers.head')

    <script src="{{ asset('plugins/adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/adminlte3/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('plugins/adminlte3/dist/js/adminlte.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <style>
        .x-body {font-family: Quicksand,helvetica,tahoma,verdana,sans-serif; font-size:13px;}
        .main-frame{position: absolute; border: none; width: 100%; height: 100%; overflow: auto; top: 0; bottom: 0;}
        .overlay{
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255,255,255,0.75);
            background-image: url({{ asset('images/loading.gif') }});
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>

<body class="x-body hold-transition sidebar-mini layout-fixed sidebar-collapse">

    <div class="wrapper">

        @require('layouts.navbar')

        @require('layouts.sidebar')

        <div class="content-wrapper" style="position: relative; border: none; min-height: 550px inherit;">
            <iframe id="mainframe" name="main-frame" class="main-frame" src="{{ $moduleUrl ? $moduleUrl : '-' }}"></iframe>
            <div id="loading-overlay" class="overlay" style="display: none"></div>
        </div>

        @require('layouts.footer')

        @require('layouts/profiles/profile')
        @require('layouts/profiles/password')
        @require('layouts/profiles/photo')

    </div>

    <script type="text/javascript">
        function activeMenu(path){
            $('#loading-overlay').show();
            $('.mainmenu').removeClass("active");
            let ids = path.split('/');
            ids.forEach(function(id){
                if(id) $('#menu-' + id).addClass("active");
            });
        }

        $( document ).ready(function() {
            activeMenu('{{ $module ? $module->path : '' }}');
        });

        var trigger = function(){
            $('#loading-overlay').hide();
        }

    </script>

</body>
</html>
