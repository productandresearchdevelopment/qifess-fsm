/*
    created by : ANDIKA
    modified: June 2020
    email: andika2000@gmail.com
    libname: Ceu Ai
*/
function getCurrentPath(){
    let doc = document.currentScript;
    return doc.getAttribute('src').replace('ceai.js','');
}

var ai = {
    currentPath: getCurrentPath(),
    navigatorId: 'ai-navigator',
    increment: 1,
    mask: {},
    require: '*',

    autoId: function(prefix){
        if(isNull(prefix)) prefix =  'ai';
        return prefix + '-' + this.increment++;
    },

    toast: function(text){
        ons.notification.toast(text, { timeout: 2000 })
    },

    create: function(component, option) {
        switch(component) {
            case 'page': return new ai.Page(option);
            case 'actionbar': return new ai.ActionBar(option);
            case 'store': return new ai.Store(option);
            case 'popup': return new ai.Popup(option);
            case 'listview': return new ai.ListView(option);
        }
    },

    showPage: function(id){
        document.getElementById(ai.navigatorId).pushPage(id+'.html');
    },

    getScript: function(uri, callback){
        let path = ai.currentPath;
        if(uri.length) {
            $.getScript(path + 'src/' + uri[0] + '.js', function () {
                if(uri.length > 1){
                    uri.shift();
                    ai.getScript(uri, callback);
                }
                else if(!isNull(callback)) callback();
            });
        }
    },

    ready: function(callback){
        if(this.require == '*') this.require = [
            'mask',
            'page',
            'store',
            'action-bar',
            'list-view',
            'popup',
        ];

        this.getScript(this.require, function(){
            if(!isNull(callback)){
                $(function () { callback() })
            }
        });
    },
};









