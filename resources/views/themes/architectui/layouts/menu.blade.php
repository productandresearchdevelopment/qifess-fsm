@foreach ($menus as $menu)
    @if($menu->children && count($menu->children) && (!$menu->device || $menu->device == 2) && !$menu->route && !$menu->url && !$level)
        <li class="app-sidebar__heading">{{ $menu->text }}</li>
        @require('menu', ['menus' => $menu->children, 'level' => $level+1])
    @elseif($menu->children && count($menu->children) && (!$menu->device || $menu->device == 2) && $level)
        <li id="menu-{{ $menu->id }}" class="mainmenu">
            <a href="#">
                <i class="metismenu-icon fa {{ $menu->icon }}"></i>
                {{ $menu->text }}
                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
            </a>
            <ul class="mm-show">@require('menu', ['menus' => $menu->children, 'level' => $level+1])</ul>
        </li>
    @elseif((!$menu->device || $menu->device == 2))
        <li>
            <a
                id="menu-{{ $menu->id }}"
                onclick="activeMenu('{{ $menu->path }}')"
                target="main-frame"
                class="mainmenu"
                href="{{ $menu->route ? route($menu->route).$menu->param : ($menu->url ? $menu->url.$menu->param : '#') }}">
                <i class="metismenu-icon fa {{ $menu->icon }}"></i>
                {{ $menu->text }}
            </a>
        </li>
    @endif
@endforeach
