ai.Navigator = function(option){
    let me = this;
    me.id = ai.autoId('ai-navigator');
    me.mainPage = null;

    me.init = function(option){
        if(typeof option == 'string') {
            me.id = option;
            $('body').append('<template id="'+me.id+'.html"><ons-page id="'+me.id+'" modifier="material" class="ai"></ons-page></template>');
        }
        else if(!isNull(option)) {
            if(!isNull(option.id)) me.id = option.id;

            $('body').append('<template id="'+me.id+'.html"><ons-page id="'+me.id+'" modifier="material" class="ai"></ons-page></template>');
            $(function () {
                if(!isNull(option.actionBar)){
                    if(typeof option.actionBar.init == 'function') {
                        me.actionBar = option.actionBar;
                        me.actionBar.option.renderTo = me.id;
                        me.actionBar.init(me.actionBar.option);
                    }
                    else {
                        option.actionBar.renderTo = me.id;
                        me.actionBar = ai.create('action-bar', option.actionBar);
                    }
                }
            })
        }
    }

    me.html = function(html){ $(function() { $('#' + me.id + ' .page__content').html(html) }) }
    me.append = function(html){ $(function() { $('#' + me.id + ' .page__content').append(html) }) }
    me.prepend = function(html){ $(function() { $('#' + me.id + ' .page__content').prepend(html) }) }

    me.init(option);
}
