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

    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/adminlte3/plugins/Ionicons/css/ionicons.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/onsen/css/onsenui.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/onsen/css/onsen-css-components.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/onsen/css/theme.css') }}">
    <script src="{{ asset('plugins/onsen/js/onsenui.min.js') }}"></script>

    <style>
        .main-frame{border: none; width: 100%; height: calc(100% - 0px); overflow: auto;}
        .user-info{text-align: center; padding: 20px; background: #FFAB0A;}
        .circle-image{width: 70px; height: 70px; border-radius: 50%;  margin: 5px;}
        .user-info-name{font-size: 15px; color: #FFFFFF; text-decoration: none; text-transform: uppercase}
        .menu-item{text-decoration: none;}
        .page__content{background: #FFFFFF !important;}
        .input{width: 100%; margin-right: 20px}
        .input-items{padding-top: 5px; padding-bottom: 5px;}
        .form-photo-items{}
        ons-input .text-input, ons-search-input .search-input,
        .toolbar__title, .page--material__content, .list-title,
        input, body, div{
            font-family: Quicksand,helvetica,tahoma,verdana,sans-serif;
        }
        .toolbar.toolbar--material+.page__background+.page__content{overflow: hidden !important;}
    </style>

    <script>
        var user = null;
        var main = {
            toggleMenu: function() {
                document.getElementById('splitter').left.toggle();
            },

            closeMenu: function(){
                document.getElementById('sidemenu').close();
            },

            showLoading: function() {
                var dialog = document.getElementById('loading');
                if (dialog) dialog.show();
                else ons.createElement('loading.html', { append: true }).then(function(dialog) { dialog.show(); });
            },

            hideLoading: function() {
                let modal = document.getElementById('loading');
                if(modal) modal.hide();
            },

            activeMenu: function(name){
                main.showLoading();
                main.closeMenu();
                $('#title-navbar').html(name);
            },

            getUser: function(action){
                $.ajax({
                    url: '{{ route('profile.data') }}',
                    type: 'GET',
                    success: function(data){
                        user = data;
                        if(action != undefined){
                            action(data);
                        }
                    }
                });
            },

            setUser: function(){
                if(user){
                    let name = user.name;
                    let photo = '{{ asset('images/nouser.png') }}';
                    if(user.photo) photo = '{{ route('upload.file') }}/'+user.photo;

                    $('#user-info-photo').attr('src', photo);
                    $('#user-info-name').html(name);
                }
            }
        };

        var trigger = function(){
            main.hideLoading();
        };

        $( document ).ready(function() {
            main.activeMenu('{{ $module ? $module->text : '' }}');
            main.getUser();
            editPhoto.onSubmit();
        });

    </script>
</head>

<body>

<ons-navigator id="navigator" animation="slide" swipeable swipe-target-width="80px">
    <ons-page>
        <ons-splitter id="splitter">
            <ons-splitter-side id="sidemenu" page="sidemenu.html" swipeable side="left" collapse="" width="260px"></ons-splitter-side>
            <ons-splitter-content page="content.html"></ons-splitter-content>
        </ons-splitter>

        <script>
            ons.getScriptPage().onShow = function (){
                main.setUser();
            }
        </script>

    </ons-page>
</ons-navigator>

<template id="content.html">
    <ons-page id="content-page">
        <ons-toolbar modifier="material noshadow" style="background: #3195dd">
            <div id="title-navbar" class="center" style="color: #FFFFFF">{{ config('site.title') }}</div>
            <div class="left">
                <ons-toolbar-button onclick="main.toggleMenu()">
                    <ons-icon style="color: #FFFFFF" icon="ion-navicon, material:md-menu"></ons-icon>
                </ons-toolbar-button>
            </div>
        </ons-toolbar>

        <iframe id="mainframe" name="main-frame" class="main-frame" src="{{ $moduleUrl ? $moduleUrl : '-' }}"></iframe>

    </ons-page>
</template>

<template id="sidemenu.html">
    <ons-page>
        <div class="user-info">
            <img id="user-info-photo" src="{{ $userphoto }}" class="circle-image" onclick="profile.editProfile()">
            <br>
            <a href="#" id="user-info-name" class="user-info-name" onclick="profile.editProfile()">{{ $user->name }}</a>
            <br>
            <div class="user-info-name" style="font-size: 13px; margin-top: 5px; color: #333333" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">LOGOUT</div>
        </div>

        @require('menu', ['menus' => $treeMenu])

    </ons-page>
</template>

<template id="loading.html">
    <ons-modal id="loading">
        <ons-progress-circular indeterminate></ons-progress-circular>
    </ons-modal>
</template>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

@require('profiles/profile')

</body>

</html>
