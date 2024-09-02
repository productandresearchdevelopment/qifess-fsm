<nav class="main-header navbar navbar-expand navbar-white navbar-light text-sm">
    {{--  ICON MENU SIDE BAR  --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
    </ul>

    {{--  PROFILE EDITOR  --}}
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a href="#" class="nav-link" data-toggle="dropdown">
                <img src="{{ $userphoto }}" class="img-circle elevation-1" style="width: 30px; height: 30px; margin: -5px 5px 0px 0px">
                <span class="hidden-xs">{{ $user->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="margin-top: 5px">
                <div class="dropdown-item dropdown-header" style="background-color: #EEEEEE">
                    <img src="{{ $userphoto }}" class="img-circle elevation-3" data-toggle="modal" data-target="#profile-photo-edit" style="width: 80px; height: 80px; margin: 20px 0px 10px; cursor: pointer">
                    <div>{{ $user->role->name }}</div>
                    <div>{{ $user->name }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#profile-form-edit"><i class="fa fa-edit mr-2"></i> Edit Profile</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#profile-form-password"><i class="fa fa-key mr-2"></i> Change Password</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item"><i class="fa fa-lock mr-2"></i> Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
            </div>
        </li>
    </ul>
</nav>
