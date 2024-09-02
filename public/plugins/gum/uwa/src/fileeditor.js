/*
    created by : ANDIKA
    modified: June 2020
    email: andika2000@gmail.com
    libname: Uwa.FileEditor

    --------------------------------------------------------------------------------------
    EXAMPLES CREATE CLASS OBJECT COMPONENT
    --------------------------------------------------------------------------------------
    >   let fileEditor = new Uwa.FileEditor({
            prefixFile: prefixFile,
            maxFile: source.property || 0,
            renderTo: '.display-file',
            files: value,
        });

*/

Uwa.FileEditor = function(property){
    let me = this;
    let prop = property || {};
     
    me.resultFiles = [];

    me.id = Uwa.autoId('fileeditor');
    me.renderTo = prop.renderTo || null;
    me.files = prop.files || [];
    me.prefixFile = prop.prefixFile || '/file';
    me.maxFile = prop.maxFile || 6;

    // FILE UTIL PROPERTY  ---------------------------------------------------------------------------------------------
    me.autoResize = prop.autoResize || true;
    me.maxWidth = prop.maxWidth || 1024;
    me.maxHeight = prop.maxHeight || 768;
    me.multipleFile = (me.maxFile == 1) ? false : true;
    me.fileType = prop.fileType || '*';

    me.fileUtil = new FileUtil({
        id: Uwa.autoId('inputfileeditor'),
        fileType: me.fileType,
        autoResize: me.autoResize,
        maxWidth: me.maxWidth,
        maxHeight: me.maxHeight,
        multipleFile: me.multipleFile
    })

    me.init = function(){
        if(typeof me.files == 'string') me.resultFiles.push(me.files);
        else me.resultFiles = me.files;
        if(me.renderTo) me.setRenderTo(me.renderTo, me.resultFiles);
    }

    me.setRenderTo = function(renderTo, files){
        setTimeout(function () {
            me.renderTo = renderTo;
            $(me.renderTo).html('<div id="'+me.id+'" class="uwa-fileeditor-container '+me.id+'"></div>');
            me.resultFiles = files;
            me.load(me.resultFiles);
        }, 200)
    }

    me.load = function(){
        let html = [];

        // CREATE NODE FILE --------------------------------------------------------
        let tplFile = `<div class=" box {type} ">
                            <img class="thumb" src="{src}" alt=" ">
                            <div class="delete">
                                <input type="hidden" value="{index}">
                            </div>
                       </div>`;
        for(let i=0; i < me.resultFiles.length; i++){
            let file = me.resultFiles[i];
            let type = "";
            let src = "";

            if(typeof file == "object") {
                if(file.type == 'image') src = me.prefixFile + '/' + file.id;
                else type = "type "+file.extension;
            }
            else if(file.substr(0,4) == 'data'){
                src = file;
                type = " type ";
                type += file.substring(file.indexOf('/')+1,file.indexOf(';'))
            }
            html.push(String.format(tplFile, {src: src, index: i, type: type}));
            if((i+1) >= me.maxFile && me.maxFile) i = me.resultFiles.length;
        }

        // BUTTON ADD FILE ---------------------------------------------------------
        if(!me.maxFile || me.resultFiles.length < me.maxFile){
            html.push('<div class="box add"></div>');
        }

        $('#'+me.id).html(html.join(''));

        $('#'+me.id+' .add').click(me.add)
        $('#'+me.id+' .delete').click(me.delete)
    }

    me.add = function(){
        me.fileUtil.load(function (files) {
            me.resultFiles = me.resultFiles.concat(files);
            me.load();
        })
    }

    me.delete = function(){
        let con = confirm("Delete File?");
        if(con) {
            let index = $(this).children().val();
            me.resultFiles.splice(index, 1);
            me.load();
        }
    }

    me.init()
}

