ai.Page = function(option){
    let me = this;
    me.id = ai.autoId('ai-page');
    me.mainPage = false,
    me.actionBar = null;
    me.listView = null;

    me.init = function(option){
        if(!isNull(option.mainPage)) me.mainPage = option.mainPage;
        if(typeof option == 'string') {
            me.id = option;
            me.createPageTpl();
        }
        else if(!isNull(option)) {
            if(!isNull(option.id)) me.id = option.id;

            me.createPageTpl();

            $(function () { me.loadComponent(option) })
        }
    }

    me.loadComponent = function(option){
        if(!isNull(option.actionBar)) me.actionBar = option.actionBar.loadInit(me.id, me);
        if(!isNull(option.listView)) me.listView = option.listView.loadInit(me.id);
    }

    me.createPageTpl = function(){
        let tpl = String.format('<ons-navigator modifier="material" animation="slide" swipeable id="{0}"></ons-navigator>', ai.navigatorId);
        $('body').append(tpl);
        $('body').append(String.format(`<template id="{0}.html"> <ons-page id="{0}" modifier="material" class="ai"></ons-page> </template>`, me.id));
        if(me.mainPage) {
            ai.showPage(me.id);
        }
    }

    me.html = function(html){ $(function() { $('#' + me.id + ' .page__content').html(html) }) }

    me.append = function(html){ $(function() { $('#' + me.id + ' .page__content').append(html) }) }

    me.prepend = function(html){ $(function() { $('#' + me.id + ' .page__content').prepend(html) }) }

    me.show = function(){

    }

    me.init(option);
}

