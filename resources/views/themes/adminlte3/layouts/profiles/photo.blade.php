<div class="modal fade" id="profile-photo-edit" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content box">
            <div class="modal-header">
                <h4 class="modal-title">Change Photo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="text-align: center">
                <img id="photo-preview" src="{{ $userphoto }}" style="max-height: 200px" onclick="photoFile.browse()">
                <form id="form-edit-photo" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
                {{--  <input id="photo-file" accept="image/*" type="file" >--}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"  onclick="submitFormEditPhoto()">Save changes</button>
            </div>
            <div id="profile-form-photo-mask" class="overlay" style="display: none;">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
</div>

<script>
    var photoFile = new FileUtil({
        id: 'profile-photo-file',
        fileType: 'image/jpeg',
        autoResize: true,
        maxWidth: 640,
        maxHeight: 480,
        multipleFile: false,
        onLoad: function(files){
            $('#photo-preview').attr("src", files[0]);
        }
    });

    function submitFormEditPhoto(){
        $('#form-edit-photo').submit(function (e) {
            var data = new FormData(this);
            data.append("photo", photoFile.get());
            $.ajax({
                url: '{{ route('profile.upload') }}',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(respon){
                    if(respon.success) window.location = '{{ Request::url() }}';
                    else{
                        $('#profile-form-photo-mask').hide();
                        alert(respon.message);
                    }
                },
                error: function(){
                    alert('Failed..');
                    $('#profile-form-photo-mask').hide();
                },
            });
            e.preventDefault();
        });

        $('#form-edit-photo').submit();
        $('#profile-form-photo-mask').show();
    }
</script>
