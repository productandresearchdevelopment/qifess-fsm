<div role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
    <div class="user-photo-container">
        <img src="{{ $userphoto }}" class="user-photo-circle" data-toggle="modal" data-target="#edit-photo" style="width: 80px; height: 80px; margin: 10px 0px; cursor: pointer">
        <div><b>{{ $user->username }}</b></div>
        <div style="font-size: 11px">{{ $user->role->name }}</div>
    </div>
    <button type="button" tabindex="0" class="dropdown-item" data-toggle="modal" data-target="#edit-password">Change Password</button>
    <button type="button" tabindex="0" class="dropdown-item" data-toggle="modal" data-target="#edit-profile">Edit Profile</button>
    <button type="button" tabindex="0" class="dropdown-item" onclick="logout()">Logout</button>
</div>
