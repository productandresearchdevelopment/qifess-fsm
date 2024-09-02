<style>
    .nav-legacy.nav-sidebar .nav-item > .nav-link.active{background-color: #EEEEEE}
    .nav-legacy.nav-sidebar .nav-item > .nav-link{background-color: #FAFAFA}
    .nav-legacy.nav-sidebar .nav-item > .nav-link{background-color: #fff}

</style>

<aside class="main-sidebar elevation-1 sidebar-light-warning">
    <a href="{{ config('site.web') }}" class="brand-link text-sm">
        <img src="{{ asset('images/logo-mini.png') }}" class="brand-image">
        <span class="brand-text font-weight-light">{{ config('site.title') }}</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ $userphoto }}" class="img-circle elevation-1" style="width: 40px; height: 40px">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user->name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent nav-legacy" data-widget="treeview" role="menu" data-accordion="false">
                @require('menu', ['menus' => $treeMenu])
            </ul>
        </nav>
    </div>
</aside>
