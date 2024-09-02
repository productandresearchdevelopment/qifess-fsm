<?php
    $userphoto = ($user->photo) ?  route('upload.file', $user->photo) : asset('images/nouser.png');
    $moduleUrl = null;
    if($module = $user->lastModule ? $user->lastModule : ($user->role->module ? $user->role->module : null)){
        $moduleUrl = $module->route ? route($module->route) : ($module->url ? $module->url : '#');
    }
?>

<!doctype html>
<html>
    <head>
        <link href="{{ asset('plugins/architechui/architectui-html-free/main.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('plugins/architechui/architectui-html-free/assets/scripts/main.js') }}"></script>
        @include('headers.head')
        <style>
            .main-frame{top: 0px; height: calc(100% - 40px); border: 0; border-left: 1px solid #fafafa;}
            .x-body {font-family: Quicksand,helvetica,tahoma,verdana,sans-serif; font-size:13px;}
            .overlay{
                position: absolute;
                top: 0px; bottom: 0px;
                left: 0px; right: 0px;
                height: calc(100% - 40px);
                background-color: rgba(255,255,255,0.5);
                background-image: url({{ asset('images/loading.gif') }});
                background-repeat: no-repeat;
                background-position: center center;
            }
            .app-footer, .app-footer .app-footer__inner { height: 40px; background: #FAFBFC!important;}
            .app-footer{border-top: 1px solid #FAFAFA; border-left: 1px solid #FAFAFA}
            .user-photo-circle{border-radius: 50%; width:40px; height: 40px; border: 1px solid #FAFAFA}
            .user-photo-container .user-photo-circle{width:40px; height: 40px; border: 1px solid #eee}
            .user-photo-container{text-align: center; padding: 20px; background-color: #FAFAFA; margin-top: -10px; margin-bottom: 10px; border-radius: 2px 2px 0px 0px;}
            @media screen and (max-width: 1000px) {
                .main-container {
                    position: absolute !important;
                    height: calc(100% - 60px) !important;
                    width: 100%;
                }
                .fixed-footer .app-footer .app-footer__inner{margin-left: 0px}
            }
        </style>
    </head>
    <body style="overflow: hidden">
        <div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
            @require('layouts.navbar')
            <div class="scrollbar-container"></div>
            <div class="app-main">
                @require('layouts.sidebar')

                <div class="app-main__outer main-container" style="position: relative !important;">

                    <iframe id="mainframe" name="main-frame" class="main-frame scrollbar-container" src="{{ $moduleUrl ? $moduleUrl : '-' }}"></iframe>

                    <div id="loading-overlay" class="overlay">&nbsp;</div>

                    @require('layouts.footer')
                </div>
            </div>
        </div>

        @require('layouts.profile_edit');
        @require('layouts.profile_edit_password');
        @require('layouts.profile_edit_photo');

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

        <script type="text/javascript">
            function activeMenu(path){
                $('#loading-overlay').show();
                $('.mainmenu').removeClass("mm-active");
                let ids = path.split('/');
                ids.forEach(function(id){
                    if(id) $('#menu-' + id).addClass("mm-active");
                });
            }

            function logout(){
                document.getElementById('logout-form').submit();
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
