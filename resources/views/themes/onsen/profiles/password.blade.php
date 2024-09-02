<template id="edit-password.html">
    <ons-page id="password">
        <ons-toolbar modifier="material">
            <div class="left">
                <ons-back-button></ons-back-button>
            </div>
            <div class="center">Change Password</div>
        </ons-toolbar>

        <ons-page>
            <form id="form-edit-password" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <ons-list style="background: #FFFFFF">
                    <ons-list-item class="input-items">
                        <div class="left"><ons-icon icon="fa-key" class="list-item__icon"></ons-icon></div>
                        <label class="center">
                            <ons-input name="old" id="input-old" type="password" class="input" maxlength="50" placeholder="Old Password" float></ons-input>
                        </label>
                    </ons-list-item>
                    <ons-list-item class="input-items">
                        <div class="left"><ons-icon icon="fa-key" class="list-item__icon"></ons-icon></div>
                        <label class="center">
                            <ons-input name="new" id="input-new" type="password" class="input" maxlength="50" placeholder="New Password" float></ons-input>
                        </label>
                    </ons-list-item>
                    <ons-list-item class="input-items">
                        <div class="left"><ons-icon icon="fa-key" class="list-item__icon"></ons-icon></div>
                        <label class="center">
                            <ons-input name="confirm" id="input-confirm" type="password" class="input" maxlength="50" placeholder="Confirm Password" float></ons-input>
                        </label>
                    </ons-list-item>


                    <ons-list-item class="input-items" style="padding-right: 10px">
                        <ons-button modifier="large" onclick="editPassword.save()">Save Password</ons-button>
                    </ons-list-item>
                </ons-list>
            </form>
        </ons-page>

        <script>
            ons.getScriptPage().onInit = function (){
                editPassword.onSubmit();
            }
        </script>

    </ons-page>
</template>

<script>
    var editPassword = {
        show: function(){
            document.querySelector('#navigator').pushPage('edit-password.html', {data: {title: 'Change Password'}});
        },

        onSubmit: function(){
            $('#form-edit-password').submit(function (e) {
                main.showLoading();
                var data = new FormData(this);
                $.ajax({
                    url: '{{ route('profile.password') }}',
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(respon){
                        main.hideLoading();
                        if(respon.success) profile.backProfile();
                        else{
                            alert(respon.message);
                        }
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
            $('#form-edit-password').submit();
        }
    };



</script>
