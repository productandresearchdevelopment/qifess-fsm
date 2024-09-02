<form id="form-edit-photo" enctype="multipart/form-data" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>

{{--<input id="photo-file" accept="image/*" type="file" style="display:none"/>--}}

<template id="edit-photo.html">
    <ons-page>
        <ons-toolbar modifier="material">
            <div class="left">
                <ons-back-button></ons-back-button>
            </div>
            <div class="center">Edit Photo</div>
        </ons-toolbar>

        <div style="text-align: center; padding: 20px">
            <img id="photo-preview" src="{{ asset('images/loading.gif') }}" style="max-width: 100%; max-height: 80%; margin-bottom: 20px">
            <ons-button modifier="large" onclick="editPhoto.save()">SAVE</ons-button>
        </div>

    </ons-page>
</template>

<script>
    var photoFile = new FileUtil({
        id: 'photo-file',
        fileType: 'image/jpeg',
        autoResize: true,
        maxWidth: 640,
        maxHeight: 480,
        multipleFile: false,
        beforeLoad: function () {
            $('#photo-preview').attr("src", '{{ asset('images/loading.gif') }}');
            document.querySelector('#navigator').pushPage('edit-photo.html', {data: {title: 'Change Photo'}});
        },
        onLoad: function(files){
            $('#photo-preview').attr("src", files[0]);
            editPhoto.photo = files[0];
        }
    });

    var editPhoto = {
        photo: null,

        show: function(){
            photoFile.browse();
        },

        onSubmit: function(){
            $('#form-edit-photo').submit(function (e) {
                main.showLoading();
                var data = new FormData(this);
                data.append("photo", editPhoto.photo);
                $.ajax({
                    url: '{{ route('profile.upload') }}',
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(respon){
                        main.hideLoading();
                        if(respon.success){
                            main.getUser(function(data){
                                profile.backProfile();
                            });
                        }
                        else{
                            alert(respon.message);
                            profile.backProfile();
                        }
                    },
                    error: function(){
                        main.hideLoading();
                        alert('Failed..');
                        profile.backProfile();
                    },
                });
                e.preventDefault();
            });
        },

        save: function(){
            $('#form-edit-photo').submit();
        }
    }

</script>
