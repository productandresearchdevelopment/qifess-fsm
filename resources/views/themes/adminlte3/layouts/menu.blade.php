@foreach ($menus as $menu)
    @if($menu->children && count($menu->children) && (!$menu->device || $menu->device == 2))
        <li class="nav-item has-treeview">
            <a id="menu-{{ $menu->id }}" href="#" class="mainmenu nav-link">
                <i class="nav-icon fa {{ $menu->icon }}"></i>
                <p>
                    {{ $menu->text }}
                    <i class="right fa fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @require('menu', ['menus' => $menu->children])
            </ul>
        </li>
    @elseif((!$menu->device || $menu->device == 2))
        <li class="nav-item">
            <a id="menu-{{ $menu->id }}"
               onclick="activeMenu('{{ $menu->path }}')"
               href="{{ $menu->route ? route($menu->route) : ($menu->url ? $menu->url : '#') }}"
               target="main-frame"
               class="mainmenu nav-link">
                <i class="fa {{ $menu->icon }} nav-icon"></i>
                <p>{{ $menu->text }}</p>
            </a>
        </li>
    @endif
@endforeach
