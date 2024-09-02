ai.ActionBar = function(option){
    let me = this;

    me.id = ai.autoId('ai-actionbar');
    me.store = null;
    me.page = null;
    me.title = null;
    me.renderTo = null;
    me.search = null;
    me.menu = null;
    me.action = null;


    me.option = option;

    me.init = function(option){
        if(!isNull(option) && (!isNull(option.renderTo) || !isNull(option.page))) {
            if (!isNull(option.id)) me.id = option.id;
            if (!isNull(option.store)) me.store = option.store;
            if (!isNull(option.title)) me.title = option.title;
            if (!isNull(option.menu)) me.menu = option.menu;
            if (!isNull(option.renderTo)) me.renderTo = option.renderTo;
            if (!isNull(option.page)) me.page = option.page;

            let tpl = `<ons-toolbar  modifier="material" id="{0}" class="ai-actionbar">
                            <div class="left"></div>
                            <div class="center"> <div id="{0}-title" class="ai-actionbar-title"></div> </div>
                            <div class="right"></div>
                       </ons-toolbar>`;

            $('#' + me.renderTo).prepend(String.format(tpl, me.id));

            if (me.title) me.showTitle(me.title);

            if (option.action) me.action = new me.actionButton(me, option.action);

            if (option.search) me.search = new me.searchBar(me, option.search);

            if (option.menu) me.menu = new me.menuBar(me, option.menu);
        }
    }

    me.loadInit = function(renderTo){
        if(!isNull(renderTo)) {
            me.option.renderTo = renderTo;
            me.init(me.option);
        }
    }

    me.setTitle = function(title){
        $('#'+me.id+ '-title').html(title);
    }

    me.showTitle = function(title){
        if(!isNull(title)) me.setTitle(title);
        $('#'+me.id+'-title').show();
    }

    me.hideTitle = function(){
        $('#'+me.id+'-title').hide();
    }

    me.actionButton = function(parent, option){
        let me = this;

        me.id = ai.autoId('actionbar-action-btn');
        me.icon = 'fa-arrow-left';

        me.handler = function(){

        };

        me.init = function(option){
            if(typeof option == 'object'){
                if(!isNull(option.id)) me.id = option.id;
                if(!isNull(option.icon)) me.icon = option.icon;
                if(!isNull(option.handler)) me.handler = option.handler;
            }

            if(option) {
                $('#'+parent.id+' .left').append('<ons-icon id="'+me.id+'" class="ai-actionbar-menu-btn" icon="'+me.icon+'"></ons-icon>');
                $(function () { $('#'+me.id).click(me.handler) })
            }
        }

        me.show = function(){ $('#'+me.id).show() }

        me.hide = function(){ $('#' + me.id).hide() }

        me.init(option);
    }

    me.searchBar = function(parent, option){
        let me = this;

        me.id = parent.id + '-search';
        me.btnId = me.id+'-search-btn';
        me.icon = 'fa-search';
        me.store = null;
        me.handler = null;

        me.init = function(option){
            if(!parent.title && parent.action) parent.title = "Search";

            if(!isNull(option.id)) me.id = option.id;
            if(!isNull(option.icon)) me.icon = option.icon;
            if(!isNull(option.handler)) me.handler = option.handler;

            me.store = (isUndef(option.store)) ? parent.store : option.store;

            let serachTpl = String.format('<ons-search-input modifier="material" id="{0}" modifier="material" class="ai-search-input" placeholder="Search"></ons-search-input>', me.id)
            $('#'+parent.id+' .center').append(serachTpl);

            let btnTpl = String.format('<ons-icon icon="{1}" id="{0}" class="ai-actionbar-search-btn"></ons-icon>', me.btnId, me.icon);
            $('#'+parent.id+' .right').prepend(btnTpl);

            $(function () {
                $('#'+me.btnId).click(me.show);
                $('#'+me.id+' input').on('blur', me.hide);
                $('#'+me.id+' input').on('search', function(){
                    let val = me.get();
                    if(me.store){
                        me.store.extraParams({query: val});
                        me.store.load(function(success, result){
                            if(parent.action) me.hide();
                            else $('#'+me.id+' input').blur();

                            if(me.handler) me.handler(val, success, result);
                        });
                    }
                    else if(me.handler) {
                        if(parent.action) me.hide();
                        else $('#'+me.id+' input').blur();
                        me.handler(val);
                    }
                });
                $('#'+parent.id+'-title').click(me.show);

                if(!parent.title) me.show();
                else me.hide();
            })

        }

        me.show = function(){
            $('#'+me.id).show();
            $('#'+me.id+' input').focus();
            $('#'+me.btnId).hide();
            parent.hideTitle();
            if(!isNull(parent.action)) parent.action.hide();
        }

        me.hide = function(){
            if(parent.title){
                $('#' + me.id).hide();
                $('#' + me.btnId).show();
                let val = me.get();
                parent.showTitle(val ? val : parent.title);
                if (!isNull(parent.action)) parent.action.show();
            }
        }

        me.get = function(){ return $('#'+me.id+' input').val(); }

        me.set = function(value){ $('#'+me.id+' input').val(value); }

        me.init(option);
    }

    me.menuBar = function(parent, option){
        let me = this;

        me.id = parent.id + '-menu';
        me.popupId = me.id + '-popup';
        me.store = null;
        me.popup = null;
        me.icon = 'fa-ellipsis-v';

        me.init = function(option) {
            let popup = {};

            if (!isNull(option.id)) {
                me.id = option.id;
                me.id + '-popup';
            }
            if (!isNull(me.popupId)) me.popupId = option.popupId;
            if (!isNull(option.icon)) me.icon = option.icon;
            if (!isNull(option.type)) me.type = option.type;

            me.store = (isUndef(option.store)) ? parent.store : option.store;

            $('#'+parent.id+' .right').append('<ons-icon id="'+me.id+'" class="ai-actionbar-menu-btn" icon="'+me.icon+'"></ons-icon>');

            if(!isNull(option.items)) {
                popup.id = me.popupId;
                popup.buttonId = me.id;
                popup.type = me.type;
                popup.items = [];
                option.items.forEach(function (item) {
                    if(!isNull(item.type)){
                        switch(item.type) {
                            case 'filter' : popup.items = popup.items.concat(me.createFilter(item)); break;
                            case 'sort' : popup.items = popup.items.concat(me.createSort(item)); break;
                        }
                    }
                    else popup.items.push(item);
                });
            }

            me.popup = new ai.Popup(popup);
        }

        me.show = function(){
            $('#'+me.id).show();
        }

        me.hide = function(){
            $('#'+me.id).hide();
        }

        me.createFilter = function(opt){
            let result = [];

            let id = (isNull(opt.id)) ? ai.autoId(me.id+'-filters') : opt.id;
            let text = (isNull(opt.text)) ? 'Filter' : opt.text;
            let icon = (isNull(opt.icon)) ? 'fa-filter' : opt.icon;
            let prefix = (isNull(opt.prefixText)) ? 'Filter By' : opt.prefixText;
            let store = (isUndef(opt.store)) ? me.store : opt.store;

            result.push({id: id, text: text, icon: icon});

            opt.items.forEach(function (item) {
                let filters = {}

                // FILTER ITEM
                filters.id = isNull(item.id) ? ai.autoId('filter-item') : item.id;
                //filters.iconRight = 'fa-chevron-right';
                filters.value = isNull(item.value) ? null : item.value;
                filters.text = isNull(item.text) ? null : prefix + ' ' +item.text;
                filters.items = [];

                // CHILDS ITEMS ALL FILTER (CLEAR)
                let name = isNull(item.name) ? null : item.name;
                let displayKey = isNull(item.displayKey) ? 'name' : item.displayKey;
                let valueKey = isNull(item.valueKey) ? 'id' : item.valueKey;

                let createHandler = function(child){
                    return function(val, obj){
                        if(store){
                            let param = {};
                            param[name] = val;
                            store.extraParams(param);
                            store.load(function(success, data, obj){
                                if(!isNull(item.handler)){ item.handler(val, child, item, obj, success, data); }
                            });
                        }
                        else if(!isNull(item.handler)) item.handler(val, child, item, obj);
                    };
                }

                filters.items = [{
                    id: filters.id+'-default',
                    text: isNull(item.clearFilterText) ? '<b> ALL '+ item.text.toUpperCase() +'</b>' : item.clearFilterText,
                    value: null,
                    handler: createHandler(this)
                }];

                // CHILD ITEMS
                item.items.forEach(function(child) {
                    let filtersItem = {};
                    filtersItem.id = ai.autoId('filter-item');
                    filtersItem.value = isNull(child[valueKey]) ? child : child[valueKey];
                    filtersItem.text = isNull(child[displayKey]) ? child : child[displayKey];
                    filtersItem.handler = createHandler(child);

                    filters.items.push(filtersItem);
                })

                result.push(filters);
            })

            return result;

        }

        me.createSort = function(opt){
            let result = [];

            let id = (isNull(opt.id)) ? ai.autoId(me.id+'-sort') : opt.id;
            let text = (isNull(opt.text)) ? 'Sort By' : opt.text;
            let icon = (isNull(opt.icon)) ? ['fa-sort-amount-asc', 'fa-chevron-right'] : opt.icon;
            let store = (isUndef(opt.store)) ? me.store : opt.store;

            result.push({id: id, text: text, icon: icon});

            opt.items.forEach(function(item) {
                let sort = {};
                sort.id = ai.autoId('filter-item');
                sort.value = {
                    sort: isNull(item.sort) ? null : item.sort,
                    dir: isNull(item.dir) ? null : item.dir,
                };
                sort.text = isNull(item.text) ? null : item.text;
                sort.handler = function(val, obj){
                    if(store){
                        store.extraParams(val);
                        store.load(function(success, data, obj){
                            if(!isNull(item.handler)){ item.handler(val, item, obj, success, data); }
                        });
                    }
                    else if(!isNull(item.handler)) item.handler(val, item, obj);
                };

                result.push(sort);
            })

            return result;
        }

        me.init(option);
    }

    me.init(option);
}
