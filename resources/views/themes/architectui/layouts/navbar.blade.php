<div class="app-header">
    <div class="app-header__logo">
        <div class="logo-src" style="background-image: none !important; font-size: 20px; margin-top: -10px">
            <img src="{{ asset('images/logoqifess.png') }}" alt="Logo" style="max-height: 24px;">
        </div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>

    <div class="app-header__menu">
        <span>
            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                <i class="fa fa-ellipsis-v fa-w-6"></i>
            </a>
            @require('profile')
        </span>
    </div>

    <div class="app-header__content">
        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div data-toggle="dropdown" class="widget-content-left  ml-3 header-user-info">
                            <div class="widget-heading" style="cursor: pointer">{{ $user->name }}</div>
                        </div>
                        <div data-toggle="dropdown" class="widget-content-right header-user-info ml-3" data-target>
                            <img src="{{ $userphoto }}" class="user-photo-circle" data-toggle="modal" data-target="#profile-photo-edit" style="margin: 10px 0px; cursor: pointer">
                        </div>
                        @require('profile')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


