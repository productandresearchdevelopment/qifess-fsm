var FileUtil = function (property) {
    let me = this;

    let prop = property || {};

    me.resultFiles = [];

    me.id = prop.id || null;
    me.inputId = prop.inputId || null;
    me.name = prop.name || 'file';
    me.fileType = prop.fileType || '*';
    me.autoResize = !isUndef(prop.autoResize) ? prop.autoResize : true;
    me.maxWidth = prop.maxWidth || 1024;
    me.maxHeight = prop.maxHeight || 768;
    me.multipleFile = prop.multipleFile || false;

    me.beforeLoad = prop.beforeLoad || null;
    me.onLoad = prop.onLoad || null;

    let init = function(property){
        if(!me.inputId){
            let tpl = String.format('<input id="{id}" accept="{type}" type="file" style="display:none" {multiple}/>', {
                id: me.id,
                type: me.fileType,
                multiple: (me.multipleFile ? 'multiple' : '')
            });
            $('body').append(tpl);
            $(function () {
                me.setChangeListener(me.id, me.onLoad, me.autoResize);
            })
        }
        else {
            me.id = me.inputId;
        }
    }

    me.load = function(action){
        me.browse(me.id, action, me.autoResize);
    }

    me.browse = function(id, action, autoResize){
        id = id || me.id;
        action = action || me.onLoad;
        autoResize = autoResize || me.autoResize;
        if(id){
            me.setChangeListener(id, action, autoResize);
            $('#'+id).trigger('click');
        }
        else console.error('FileUtil "Not Set ID"');
    }

    me.setChangeListener = function(id, action, autoResize){
        if(action) {
            $('#'+id).off('change');
            $('#'+id).on('change', function (e) {
                me.resultFiles = [];
                let files = e.target.files;
                if(files){
                    if (!isNull(me.beforeLoad)) me.beforeLoad(files, me)
                    for (let i = 0; i < files.length; i++) {
                        let index = i + 1;
                        let file = files[i];
                        if (file) {
                            let filetype = file.type.substr(0, 5);
                            let reader = new FileReader();
                            reader.onload = function (e) {
                                let src = e.target.result;
                                if (autoResize && filetype == 'image') {
                                    if (index < files.length) me.resize(src, file, me.resultFiles);
                                    else me.resize(src, file, me.resultFiles, function (result) {
                                        setTimeout(function () {
                                            action(result, me);
                                        }, 200);
                                    });
                                }
                                else {
                                    me.resultFiles.push(src);
                                    if (index >= files.length) {
                                        setTimeout(function () {
                                            action(me.resultFiles, me);
                                        }, 1000);
                                    }
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                }
                else {
                    action(null, me);
                    console.error('element is not "input file"');
                }
            });
        }
    }

    me.resize = function(src, file, result, action){
        let img = document.createElement("img");
        let canvas = document.createElement("canvas");
        let MAX_WIDTH = me.maxWidth;
        let MAX_HEIGHT = me.maxHeight;

        img.src = src;
        setTimeout(function () {
            let width = img.width;
            let height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            }
            else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }

            canvas.width = width;
            canvas.height = height;

            let ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, width, height);

            result.push(canvas.toDataURL(file.type));

            if(!isNull(action)) action(result);
        }, 500);
    }

    me.files = function(){
        return me.resultFiles;
    }
    me.result = me.files;

    me.file = function(){
        return me.resultFiles.length ? me.resultFiles[0] : null;
    }
    me.get = me.file

    init(prop);
}



