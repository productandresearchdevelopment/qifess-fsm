<style>
    .edit-photo-icon{
        background: #0d5183; color: #FFFFFF;
        padding: 4px 5px 5px 5px; border-radius: 50%;
        border: 2px solid #EEEEEE;
        position: absolute;
        top: 110px;
        right: calc(50% - 50px);
    }
</style>

<script>
    var profile = {
        editProfile: function(){
            main.closeMenu();
            document.querySelector('#navigator').pushPage('edit-profile.html', {data: {title: 'Edit Profile'}});
        },

        backProfile: function(){
            document.querySelector('#navigator').removePage();
        },

        setUser: function(){
            if(user){
                let photo = '{{ asset('images/nouser.png') }}';
                if(user.photo) photo = '{{ route('upload.file') }}/'+user.photo;

                $('#edit-profile-photo').attr('src', photo);

                $('#input-name').val(user.name);
                $('#input-email').val(user.email);
                $('#input-phone').val(user.phone);
            }
        },

        onSubmit: function(){
            $('#form-edit-profile').submit(function (e) {
                main.showLoading();
                var data = new FormData(this);
                $.ajax({
                    url: '{{ route('profile.edit') }}',
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(respon){
                        main.hideLoading();
                        if(respon.success) {
                            main.getUser(function(){
                                profile.backProfile();
                                main.setUser();
                            });
                        }
                        else alert(respon.message);
                    },
                    error: function(){
                        main.hideLoading();
                        alert('Failed..');
                    },
                });
                e.preventDefault();
            });
        },

        save: function(){
            $('#form-edit-profile').submit();
        }
    }
</script>

<template id="edit-profile.html">
    <ons-page>
        <ons-toolbar modifier="material">
            <div class="left"> <ons-back-button></ons-back-button> </div>
            <div class="center">Edit Profile</div>
        </ons-toolbar>

        <ons-page>
            <div style="padding: 30px; background: #FFFFFF; text-align: center">
                <img id="edit-profile-photo" src="{{ $userphoto }}" style="width: 100px; height: 100px" class="circle-image" onclick="editPhoto.show()">
                <ons-icon icon="fa-pencil-alt" class="edit-photo-icon"></ons-icon>
                <br>
                <ons-button style="background: #CC0000; margin-top: 20px" onclick="editPassword.show()">Change Password</ons-button>
            </div>

            <form id="form-edit-profile" enctype="multipart/form-data" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <ons-list>
                    <ons-list-header>Profile ({{ Auth::user()->username }})</ons-list-header>

                    <ons-list-item class="input-items">
                        <div class="left">
                            <ons-icon icon="fa-user-circle-o" class="list-item__icon"></ons-icon>
                        </div>
                        <label class="center">
                            <ons-input name="name" id="input-name" class="input" maxlength="50" placeholder="Name" value="{{ Auth::user()->name }}" float></ons-input>
                        </label>
                    </ons-list-item>

                    <ons-list-item class="input-items">
                        <div class="left">
                            <ons-icon icon="fa-envelope" class="list-item__icon"></ons-icon>
                        </div>
                        <label class="center">
                            <ons-input name="email" id="input-email" class="input" type="email" maxlength="150" placeholder="Email" value="{{ Auth::user()->email }}" float></ons-input>
                        </label>
                    </ons-list-item>
                    <ons-list-item class="input-items">
                        <div class="left">
                            <ons-icon icon="fa-phone" class="list-item__icon"></ons-icon>
                        </div>
                        <label class="center">
                            <ons-input name="phone"  id="input-phone" class="input" type="text" maxlength="20" placeholder="Phone" value="{{ Auth::user()->phone }}" float></ons-input>
                        </label>
                    </ons-list-item>
                    <ons-list-item class="input-items" style="padding-right: 10px">
                        <ons-button modifier="large" onclick="profile.save()">UPDATE PROFILE</ons-button>
                    </ons-list-item>
                </ons-list>
            </form>
        </ons-page>

        <script>
            ons.getScriptPage().onShow = function (){
                profile.setUser();
            }

            ons.getScriptPage().onInit = function (){
                profile.onSubmit();
            }
        </script>
    </ons-page>
</template>



@require('password')
@require('photo')
