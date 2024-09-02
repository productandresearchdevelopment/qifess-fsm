<div class="modal fade" id="profile-form-password" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content box">
            <div class="modal-header">
                <h4 class="modal-title">Change Password</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>

            </div>
            <div class="modal-body">
                <form id="form-edit-password" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="input-name">Old Password</label>
                            <input class="form-control" id="input-pld" placeholder="Old Password" type="password" name="old">
                        </div>

                        <div class="form-group">
                            <label for="input-name">New Password</label>
                            <input class="form-control" id="input-new" placeholder="New Password" type="password" name="new">
                        </div>

                        <div class="form-group">
                            <label for="input-name">Confirm</label>
                            <input class="form-control" id="input-confirm" placeholder="Confirm" type="password" name="confirm">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitFormEditPassword()">Change Password</button>
            </div>
            <div id="profile-form-password-mask" class="overlay" style="display: none;">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
</div>

<script>
    $('#form-edit-password').submit(function (e) {
        var data = new FormData(this);
        $.ajax({
            url: '{{ route('profile.password') }}',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function(respon){
                if(respon.success) $('#profile-form-password').modal("hide");
                else{
                    $('#profile-form-password-mask').hide();
                    alert(respon.message);
                }
            },
            error: function(){
                alert('Failed..');
                $('#profile-form-password-mask').hide();
            },
        });
        e.preventDefault();
    });

    function submitFormEditPassword(){
        $('#form-edit-password').submit();
        $('#profile-form-password-mask').show();
    }
</script>
