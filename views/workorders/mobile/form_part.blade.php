<style>

</style>

<template modifier="material" id="form-part.html">
    <ons-page modifier="material" id="form-part" class="ai">
        <ons-toolbar id="form-part-toolbar" modifier="material">
            <div class="left">
                <ons-back-button modifier="material"></ons-back-button>
            </div>
            <div id="form-part-title" class="center">Add Part</div>
        </ons-toolbar>

        <div id="form-part-content" class="ai-form">
            <div style="padding: 20px 20px">
                <div id="form-part-code">
                    <div class="group-title">Part Number</div>
                    <ons-input id="form-part-input-code" modifier="material underbar"></ons-input>
                </div>

                <div id="form-part-name">
                    <div class="group-title">Name</div>
                    <ons-input id="form-part-input-name" modifier="material underbar"></ons-input>
                </div>

                <div id="form-part-sn">
                    <div class="group-title">Serial Number</div>
                    <ons-input id="form-part-input-serial" modifier="material underbar"></ons-input>
                </div>

                <div id="form-part-model">
                    <div class="group-title">Model</div>
                    <ons-input id="form-part-input-model" modifier="material underbar"></ons-input>
                </div>

                <div class="group-title">Note / Description</div>
                <textarea id="form-part-input-description" class="textarea" rows="6"></textarea>

                <div class="group-title">Photos</div>
                <div id="form-part-photos"></div>

                <ons-button id="form-part-submit" modifier="material" style="width: 100%; margin: 10px 0px">Add</ons-button>
            </div>
        </div>
    </ons-page>
</template>

<script>
    Uwa.ready();
    var FormPart = function(store){
        let me = this;

        me.fileEditor = null;

        me.show = function(){
            ai.showPage('form-part');
        }

        me.hide = function () {
            $('#form-part ons-back-button').trigger('click');
        }

        me.renderPhotos = function(files){
            files = files || [];
            me.fileEditor = new Uwa.FileEditor({
                prefixFile: '{{ route('upload.file') }}',
                maxFile: 4,
                renderTo: '#form-part-photos',
                fileType: '.jpg,.jpeg',
                files: files,
            });
        }

        me.create = function(type, data){
            me.show();
            me.renderPhotos();
            $(function () {
                $('#form-part-title').html('ADD '+type);
                $('#form-part-submit').html('ADD '+type);
                $('#form-part-submit').click(function () {
                    me.save(type, data.id);
                })
            });
        }

        me.edit = function(data){
            me.show();
            $(function () {
                $('#form-part-title').html('UPDATE '+data.type);
                $('#form-part-submit').html('UPDATE '+data.type);

                $('#form-part-input-code').val(data.code);
                $('#form-part-input-name').val(data.name);
                $('#form-part-input-serial').val(data.serial);
                $('#form-part-input-model').val(data.model);
                $('#form-part-input-description').val(data.description);
                me.renderPhotos(data.files);
                $(function () {
                    $('#form-part-submit').click(function () {
                        me.save(data.type, data.wo_id, data.id);
                    })
                });
            });
        }

        me.save = function(type, wo, id){
            id = id || '0';
            $.ajax({
                url: '{{ route('wo.push.part') }}/'+ id,
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    wo_id: wo,
                    type: type,
                    code: $('#form-part-input-code').val(),
                    name: $('#form-part-input-name').val(),
                    serial: $('#form-part-input-serial').val(),
                    model: $('#form-part-input-model').val(),
                    description: $('#form-part-input-description').val(),
                    files: JSON.stringify(me.fileEditor.resultFiles)
                },
                success: function (res) {
                    if(res.success){
                        me.hide();
                        store.load();
                        detailWo.load();
                    }
                    else ons.notification.alert(res.message);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    ons.notification.alert(errorThrown);
                }
            });
        }
    }
</script>



