/*
    created by : ANDIKA
    modified: June 2020
    email: andika2000@gmail.com
    libname: UwaBobot
*/
function getCurrentPath(){
    let doc = document.currentScript;
    return doc.getAttribute('src').replace('uwa.js','');
}

function dd(text){
    if(!isUndef(text)) console.log(text);
    else console.log('ok');
}

var Uwa = {
    currentPath: getCurrentPath(),
    increment: 1,
    require: '*',

    getScript: function(uri, callback){
        let path = this.currentPath;
        if(uri.length) {
            $.getScript(path + 'src/' + uri[0] + '.js', function () {
                if(uri.length > 1){
                    uri.shift();
                    Uwa.getScript(uri, callback);
                }
                else if(!isNull(callback)) callback();
            });
        }
    },

    ready: function(callback){
        if(this.require == '*') this.require = [
            'fileeditor',
            'grid-property',
        ];
        this.getScript(this.require, function(){
            if(!isNull(callback)){
                $(function () { callback() })
            }
        });
    },

    create: function(component, property) {
        switch(component) {
            case 'gridproperty': return new Uwa.GridProperty(property);
            case 'fileeditor': return new Uwa.FileEditor(property);
        }
    },

    autoId: function(prefix){
        if(isNull(prefix)) prefix =  'ai';
        return prefix + '-' + this.increment++;
    },

    dom: function(id, getElementById){
        if(!isUndef(getElementById) || getElementById) id = '#'+id;
        return $(id);
    }
};
