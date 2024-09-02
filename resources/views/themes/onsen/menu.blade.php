@foreach ($menus as $menu)
    @if($menu->children && count($menu->children) && $menu->device < 2)
        <ons-list-title>{{ $menu->text }}</ons-list-title>
        <ons-list>
            @require('menu', ['menus' => $menu->children])
        </ons-list>
    @elseif($menu->device < 2)
        <a class="menu-item" onclick="main.activeMenu('{{$menu->text}}')" href="{{ $menu->route ? route($menu->route) : ($menu->url ? $menu->url : '#') }}" target="main-frame" >
            <ons-list-item style="background: #FFFFFF">
                <div class="left">
                    <ons-icon fixed-width class="list-item__icon" icon="{{ $menu->icon }}"></ons-icon>
                </div>
                <div class="center">{{ $menu->text }}</div>
            </ons-list-item>
        </a>
    @endif
@endforeach
