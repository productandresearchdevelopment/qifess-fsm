ai.ListView = function(option){
    let me = this;

    me.id = ai.autoId('list-view');
    me.store = null;
    me.renderItem = {tpl: null, renders: []};
    me.renderTo = null;
    me.loadMore = true;
    me.pullHook = true;
    me.parent = null;
    me.intro = null;
    me.handler= null;
    me.listeners= [];

    me.options = option;

    me.init = function(option){
        if(!isNull(option) && !isNull(option.renderTo)){
            if (!isNull(option.id)) me.id = option.id;
            if (!isNull(option.store)) me.store = option.store;
            if (!isNull(option.renderTo)) me.renderTo = option.renderTo;
            if (!isNull(option.intro)) me.intro = option.intro;
            if (!isUndef(option.loadMore)) me.loadMore = option.loadMore;
            if (!isNull(option.handler)) me.handler = option.handler;
            if (!isNull(option.listeners)) me.listeners = option.listeners;
            if (!isNull(option.renderItem)) {
                if (!isNull(option.renderItem.tpl)) me.renderItem.tpl = option.renderItem.tpl;
                if (!isNull(option.renderItem.renders)) me.renderItem.renders = option.renderItem.renders;
            }

            me.createPage();

            if (me.store) { me.store.loadTpl = me.loadStoreTpl }
        }
    }

    me.createPage = function(){
        let pullHookTpl = '';
        if(me.pullHook) {
            pullHookTpl = `<ons-pull-hook id="{0}-pull-hook">
                                <ons-icon id="{0}-pull-hook-icon" size="22px" class="pull-hook-content" icon="fa-undo"></ons-icon>
                           </ons-pull-hook>`;
            pullHookTpl = String.format(pullHookTpl, me.id);
        }

        let tpl = `<ons-page class="ai-page" id="{0}"> ` + pullHookTpl + `
                        <div id="{0}-intro" class="ai-page-intro"></div>
                        <ons-list id="{0}-list-node" class="ai-list-node"></ons-list>
                   </ons-page>`;
        $('#'+me.renderTo).append(String.format(tpl, me.id));

        if(me.loadMore) me.loadMore = new me.LoadMore(me);

        $(function () { if(me.pullHook) me.pullHookListener() })
    }

    me.loadStoreTpl = function (data, store, appendMode) {
        let node = $('#' + me.id + '-list-node');
        if(isNull(appendMode)) {
            $('#' + me.id).scrollTop(0)
            node.html('');
        }
        if(store.totalCount) {
            data.forEach(function (rec, index) {
                let recTpl = JSON.parse(JSON.stringify(rec));
                me.renderItem.renders.forEach(function (r) {
                    let result = null;
                    if(typeof r.render == 'function') result = r.render(recTpl);
                    recTpl[r.id] = result;
                });
                let tpl = String.format(me.renderItem.tpl, recTpl);
                tpl = '<ons-list-item modifier="material" id="{0}-list-item-{1}" class="ai-list-item {0}-list-item">' + tpl + '</ons-list-item>';
                tpl = String.format(tpl, me.id, index)
                if (!isNull(appendMode) && appendMode == 'prepend') node.prepend(tpl);
                else node.append(tpl);

                me.listeners.forEach(function (listen) {
                    let el = $(String.format('#{0}-list-item-{1} {2}', me.id, index, listen.target));
                    el.click(function () {
                        listen.action(rec, index);
                    });
                });

                if(typeof me.handler == 'function') {
                    let handler = me.handler;
                    let el = $(String.format('#{0}-list-item-{1}', me.id, index));
                    el.click(function (e) {
                        let targets = $.map(me.listeners, function(o) { return o["target"]; });
                        if(!$(e.target).closest(targets.join(',')).length) {
                            handler(rec, index);
                        }
                    });
                }

            });
        }
        else node.html('<div style="padding: 10px"> No Display Data </div>');

        if (me.loadMore) {
            if (store.data.length < store.totalCount) me.loadMore.show();
            else me.loadMore.hide();
        }
    }

    me.loadInit = function(renderTo){
        if(!isNull(renderTo)){
            me.options.renderTo = renderTo;
            me.init(me.options);
        }
    }

    me.LoadMore = function(parent){
        let me = this;

        me.id = parent.id + '-load-more';

        me.init = function () {
            let tpl = `<div id="{0}" class="after-list ai-load-more">
                           <ons-icon class="ai-load-more-icon" icon="fa-spinner" size="26px" spin></ons-icon>
                       </div>`;
            $('#'+parent.id+' .page__content').append(String.format(tpl, me.id));

            let el = document.getElementById(parent.id);
            el.onInfiniteScroll = function(done) {
                setTimeout(function(){
                    parent.store.loadMore(function(success, data) {
                        done();
                    });
                }, 500);
            }
        }

        me.show = function(){ $('#'+me.id).show()}
        me.hide = function(){ $('#'+me.id).hide()}

        me.init();
    }

    me.pullHookListener = function(){
        let el = document.getElementById(me.id+'-pull-hook');
        let icon = document.getElementById(me.id +'-pull-hook-icon');
        el.addEventListener('changestate', function (event) {
            switch (event.state) {
                case 'initial':
                    icon.setAttribute('icon', 'fa-undo');
                    icon.removeAttribute('rotate');
                    icon.removeAttribute('spin');
                    break;
                case 'preaction':
                    icon.setAttribute('icon', 'fa-undo');
                    icon.setAttribute('rotate', '180');
                    icon.removeAttribute('spin');
                    break;
                case 'action':
                    icon.setAttribute('icon', 'fa-spinner');
                    icon.removeAttribute('rotate');
                    icon.setAttribute('spin', true);
                    break;
            }
        });
        el.onAction = function (done) {
            me.store.load(function () {
                done()
            })
        }
    }

    me.init(option);
}
