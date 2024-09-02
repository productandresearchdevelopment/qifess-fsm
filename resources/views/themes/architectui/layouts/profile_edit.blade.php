<div class="modal fade" id="edit-profile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit-profile" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="input-name">Name</label>
                            <input class="form-control" id="input-name" placeholder="Name" name="name" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="input-email">Email</label>
                            <input class="form-control" id="input-email" placeholder="Email" name="email" value="{{ Auth::user()->email }}">
                        </div>

                        <div class="form-group">
                            <label for="input-phone">Phone</label>
                            <input class="form-control" id="input-phone" placeholder="Phone Number" name="phone" value="{{ Auth::user()->phone }}">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitFormEditProfile()">Change Password</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#form-edit-profile').submit(function (e) {
        var data = new FormData(this);
        $.ajax({
            url: '{{ route('profile.edit') }}',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function(respon){
                if(respon.success) window.location = '{{ Request::url() }}';
                else{
                    $('#profile-form-edit-mask').hide();
                    alert(respon.message);
                }
            },
            error: function(){
                alert('Failed..');
                $('#profile-form-edit-mask').hide();
            },
        });
        e.preventDefault();
    });

    function submitFormEditProfile(){
        $('#form-edit-profile').submit();
        $('#profile-form-edit-mask').show();
    }
</script>
